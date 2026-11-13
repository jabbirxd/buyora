<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PreviewDumpTest extends TestCase
{
    public function test_dump_authenticated_pages(): void
    {
        $user = User::find(10);
        $this->actingAs($user);

        $pages = [
            'cart' => '/cart',
            'checkout' => '/checkout',
            'orders' => '/orders',
            'profile' => '/profile',
            'admin' => '/admin/dashboard',
        ];

        // Never write these into public/: they contain logged-in pages and would be web-accessible.
        $previewDir = storage_path('app/previews');
        File::ensureDirectoryExists($previewDir);

        foreach ($pages as $name => $url) {
            $html = $this->get($url)->getContent();
            file_put_contents($previewDir . DIRECTORY_SEPARATOR . '_preview_' . $name . '.html', $html);
        }

        $this->assertTrue(true);
    }
}
