<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * One-time data fix: renumber gapped auto-increment ids to 1..N (in current id order)
 * and remap every column that references them.
 *
 * Laravel system tables (migrations, sessions, cache, jobs, password_reset_tokens,
 * personal_access_tokens, failed_jobs) are never renumbered. Only id / *_id columns are
 * written; tracking_no, payment_id and every other column are left exactly as they are.
 */
class RenumberIds extends Command
{
    protected $signature = 'app:renumber-ids {--dry-run : Run everything and all checks, then roll back}';

    protected $description = 'One-time: renumber gapped ids to 1..N and remap every reference';

    /**
     * table => list of [referencing table, referencing column, extra where (column => value) or null].
     */
    private const PLAN = [
        'users' => [
            ['orders', 'user_id', null],
            ['carts', 'user_id', null],
            ['wishlists', 'user_id', null],
            // Polymorphic Sanctum tokens owned by users (the table itself is not renumbered).
            ['personal_access_tokens', 'tokenable_id', ['tokenable_type', 'App\\Models\\User']],
        ],
        'catagories' => [
            ['products', 'catagory_id', null],
            ['brands', 'catagory_id', null],
        ],
        'brands' => [],
        'colors' => [
            ['product_colors', 'color_id', null],
        ],
        'products' => [
            ['product_images', 'product_id', null],
            ['product_colors', 'product_id', null],
            ['order_items', 'product_id', null],
            ['carts', 'product_id', null],
            ['wishlists', 'product_id', null],
        ],
        'product_images' => [],
        'product_colors' => [
            ['order_items', 'product_color_id', null],
            ['carts', 'product_color_id', null],
        ],
        'orders' => [
            ['order_items', 'order_id', null],
        ],
        'order_items' => [],
        'carts' => [],
        'wishlists' => [],
        'contact_messages' => [],
        'settings' => [],
    ];

    public function handle(): int
    {
        // 1. Build old => new maps and print them.
        $maps = [];
        foreach (array_keys(self::PLAN) as $table) {
            $ids = DB::table($table)->orderBy('id')->pluck('id')->all();
            $maps[$table] = $ids ? array_combine($ids, range(1, count($ids))) : [];
        }

        foreach ($maps as $table => $map) {
            if (! $map) {
                $this->line("<comment>{$table}</comment>: empty, skipped");
                continue;
            }
            $changed = array_filter($map, fn ($new, $old) => $new != $old, ARRAY_FILTER_USE_BOTH);
            $pairs = implode(', ', array_map(fn ($o, $n) => "{$o}->{$n}", array_keys($map), $map));
            $this->line("<comment>{$table}</comment> (" . count($map) . ' rows, ' . count($changed) . " changing): {$pairs}");
        }

        if (! array_filter($maps, fn ($m) => array_filter($m, fn ($n, $o) => $n != $o, ARRAY_FILTER_USE_BOTH))) {
            $this->info('All ids are already sequential. Nothing to do.');
            return self::SUCCESS;
        }

        $dryRun = (bool) $this->option('dry-run');
        if (! $dryRun && $this->ask('Type "yes" to renumber the live database') !== 'yes') {
            $this->warn('Aborted. Nothing was changed.');
            return self::FAILURE;
        }

        // 2. Snapshot every affected table, and compute exactly what each row must look like afterwards.
        $affected = $this->affectedTables();
        $expected = [];
        foreach ($affected as $table) {
            $rows = DB::table($table)->orderBy('id')->get()->map(fn ($r) => (array) $r)->all();
            $expected[$table] = $this->applyMaps($table, $rows, $maps);
        }

        // 3. Renumber inside one transaction, with FK checks off (so RESTRICT/CASCADE rules never fire).
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        try {
            DB::beginTransaction();

            foreach (self::PLAN as $table => $refs) {
                $changed = array_filter($maps[$table], fn ($new, $old) => $new != $old, ARRAY_FILTER_USE_BOTH);
                if (! $changed) {
                    continue;
                }
                // Ascending order is collision-free: every new id <= its old id.
                $this->remapColumn($table, 'id', $changed, null, true);
                foreach ($refs as [$refTable, $refColumn, $where]) {
                    $this->remapColumn($refTable, $refColumn, $changed, $where, false);
                }
            }

            $failures = $this->verify($affected, $expected);
            if ($failures) {
                DB::rollBack();
                $this->error('Verification FAILED, everything rolled back:');
                foreach ($failures as $f) {
                    $this->error('  - ' . $f);
                }
                return self::FAILURE;
            }

            if ($dryRun) {
                DB::rollBack();
                $this->info('Dry run: all checks passed, changes rolled back.');
                return self::SUCCESS;
            }

            DB::commit();
        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            $this->error('Error, everything rolled back: ' . $e->getMessage());
            return self::FAILURE;
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        // 4. Reset counters. ALTER TABLE implicitly commits in MySQL, so it must run after the
        //    transaction; it changes no row data.
        foreach ($maps as $table => $map) {
            if ($map) {
                $next = max($map) + 1;
                DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = {$next}");
                $this->line("{$table}: AUTO_INCREMENT = {$next}");
            }
        }

        $this->info('Done. Row counts unchanged, every row matches its expected renumbered form, no orphaned references.');
        return self::SUCCESS;
    }

    /** Every table whose id or reference columns may change. */
    private function affectedTables(): array
    {
        $tables = array_keys(self::PLAN);
        foreach (self::PLAN as $refs) {
            foreach ($refs as [$refTable]) {
                $tables[] = $refTable;
            }
        }
        return array_values(array_unique($tables));
    }

    /** Apply the old=>new maps to a table's rows in PHP (the expected result), re-sorted by id. */
    private function applyMaps(string $table, array $rows, array $maps): array
    {
        foreach ($rows as &$row) {
            if (isset($maps[$table]) && array_key_exists($row['id'], $maps[$table])) {
                $row['id'] = $maps[$table][$row['id']];
            }
            foreach (self::PLAN as $parent => $refs) {
                foreach ($refs as [$refTable, $refColumn, $where]) {
                    if ($refTable !== $table || $row[$refColumn] === null) {
                        continue;
                    }
                    if ($where && $row[$where[0]] !== $where[1]) {
                        continue;
                    }
                    if (array_key_exists($row[$refColumn], $maps[$parent])) {
                        $row[$refColumn] = $maps[$parent][$row[$refColumn]];
                    }
                }
            }
        }
        unset($row);
        usort($rows, fn ($a, $b) => $a['id'] <=> $b['id']);
        return $rows;
    }

    /** UPDATE t SET col = CASE col WHEN old THEN new ... END WHERE col IN (...) as one statement. */
    private function remapColumn(string $table, string $column, array $map, ?array $where, bool $orderById): void
    {
        $cases = [];
        $bindings = [];
        foreach ($map as $old => $new) {
            $cases[] = 'WHEN ? THEN ?';
            array_push($bindings, $old, $new);
        }
        $in = implode(',', array_fill(0, count($map), '?'));
        $sql = "UPDATE `{$table}` SET `{$column}` = CASE `{$column}` " . implode(' ', $cases) . " END WHERE `{$column}` IN ({$in})";
        $bindings = array_merge($bindings, array_keys($map));
        if ($where) {
            $sql .= " AND `{$where[0]}` = ?";
            $bindings[] = $where[1];
        }
        if ($orderById) {
            $sql .= ' ORDER BY `id` ASC';
        }
        DB::update($sql, $bindings);
    }

    /** Returns a list of failed checks (empty = all good). */
    private function verify(array $affected, array $expected): array
    {
        $failures = [];

        foreach ($affected as $table) {
            $after = DB::table($table)->orderBy('id')->get()->map(fn ($r) => (array) $r)->all();
            if (count($after) !== count($expected[$table])) {
                $failures[] = "{$table}: row count changed (" . count($expected[$table]) . ' -> ' . count($after) . ')';
                continue;
            }
            foreach ($after as $i => $row) {
                if ($row != $expected[$table][$i]) {
                    $failures[] = "{$table}: row with new id {$row['id']} does not match its expected renumbered data";
                }
            }
        }

        foreach (self::PLAN as $parent => $refs) {
            foreach ($refs as [$refTable, $refColumn, $where]) {
                $q = DB::table($refTable)->whereNotNull($refColumn)
                    ->whereNotIn($refColumn, DB::table($parent)->select('id'));
                if ($where) {
                    $q->where($where[0], $where[1]);
                }
                $orphans = $q->pluck($refColumn)->unique()->implode(',');
                if ($orphans !== '') {
                    $failures[] = "{$refTable}.{$refColumn}: orphaned references to {$parent} ids [{$orphans}]";
                }
            }
        }

        return $failures;
    }
}
