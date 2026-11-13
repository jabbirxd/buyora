<?php

namespace Tests\Feature;

use App\Models\Color;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AuditFixesTest extends TestCase
{
    /**
     * Test CRIT-03: GET delete routes are no longer allowed (return 405 Method Not Allowed)
     */
    public function test_crit_03_get_delete_routes_return_405(): void
    {
        $admin = User::where('roll_as', 1)->first() ?? User::factory()->create(['roll_as' => 1]);

        // GET on product delete URL returns 405
        $response = $this->actingAs($admin)->get('/admin/products/99999/delete');
        $response->assertStatus(405);

        // GET on color delete URL returns 405
        $response = $this->actingAs($admin)->get('/admin/colors/99999/delete');
        $response->assertStatus(405);

        // GET on product-image delete URL returns 405
        $response = $this->actingAs($admin)->get('/admin/product-image/99999/delete');
        $response->assertStatus(405);

        // GET on product-color delete URL returns 405
        $response = $this->actingAs($admin)->get('/admin/product-color/99999/delete');
        $response->assertStatus(405);
    }

    /**
     * Test CRIT-03: Blade templates contain DELETE forms with CSRF and method Spoofing
     */
    public function test_crit_03_blade_views_use_delete_forms(): void
    {
        $admin = User::where('roll_as', 1)->first();
        $this->actingAs($admin);

        // Products index
        $response = $this->get('/admin/products');
        $response->assertStatus(200);
        $response->assertSee('value="DELETE"', false);
        $this->assertDoesNotMatchRegularExpression('/<a\s+[^>]*href=["\'][^"\']*\/delete["\']/', $response->getContent());

        // Colors index
        $response = $this->get('/admin/colors');
        $response->assertStatus(200);
        $response->assertSee('value="DELETE"', false);
        $this->assertDoesNotMatchRegularExpression('/<a\s+[^>]*href=["\'][^"\']*\/delete["\']/', $response->getContent());

        // Product edit
        $product = Product::first();
        if ($product) {
            $response = $this->get('/admin/products/' . $product->id . '/edit');
            $response->assertStatus(200);
            $response->assertSee('type: "DELETE"', false);
        }
    }

    /**
     * Test CRIT-04: Migration files exist and guard clauses prevent errors
     */
    public function test_crit_04_migrations_exist_and_guarded(): void
    {
        $this->assertFileExists(database_path('migrations/2026_01_29_161038_add_is_featured_to_products_table.php'));
        $this->assertFileExists(database_path('migrations/2026_06_08_054200_create_settings_table.php'));

        $this->assertTrue(Schema::hasTable('settings'));
        $this->assertTrue(Schema::hasColumn('products', 'is_featured'));

        $settingCols = Schema::getColumnListing('settings');
        $this->assertContains('website_name', $settingCols);
        $this->assertContains('website_url', $settingCols);
        $this->assertContains('page_title', $settingCols);
        $this->assertContains('meta_keyword', $settingCols);
        $this->assertContains('meta_description', $settingCols);
        $this->assertContains('address', $settingCols);
        $this->assertContains('phone1', $settingCols);
        $this->assertContains('phone2', $settingCols);
        $this->assertContains('email1', $settingCols);
        $this->assertContains('email2', $settingCols);
    }

    /**
     * Test CRIT-06: Product page renders safely when color is deleted (color_id is null)
     */
    public function test_crit_06_null_safe_color_rendering(): void
    {
        $product = Product::with('catagory')->first();
        $this->assertNotNull($product);

        DB::beginTransaction();
        try {
            // Simulate deleted color (FK set null)
            $pc = ProductColor::where('product_id', $product->id)->first();
            if (!$pc) {
                $pc = ProductColor::create([
                    'product_id' => $product->id,
                    'color_id' => null,
                    'quantity' => 5,
                ]);
            } else {
                $pc->update(['color_id' => null]);
            }

            $url = '/collections/' . $product->catagory->slug . '/' . $product->slug;
            $response = $this->get($url);
            $response->assertStatus(200);
            $response->assertSee('Default');
            $response->assertSee('#cccccc');
        } finally {
            DB::rollBack();
        }
    }

    /**
     * Test HIGH-01: Admin can update order status and Orders link is in sidebar
     */
    public function test_high_01_admin_can_update_order_status(): void
    {
        $admin = User::where('roll_as', 1)->first();
        $order = Order::first();
        $this->assertNotNull($order);

        $originalStatus = $order->status_message;

        try {
            // Test valid status update via PUT
            $response = $this->actingAs($admin)->put('/admin/orders/' . $order->id, [
                'order_status' => 'processing',
            ]);
            $response->assertSessionHas('message');
            $this->assertEquals('processing', $order->fresh()->status_message);

            // Test invalid status validation error
            $response = $this->actingAs($admin)->put('/admin/orders/' . $order->id, [
                'order_status' => 'invalid_fake_status',
            ]);
            $response->assertSessionHasErrors('order_status');

            // Test admin order show page has status update form
            $response = $this->actingAs($admin)->get('/admin/orders/' . $order->id);
            $response->assertStatus(200);
            $response->assertSee('Update Order Status');
            $response->assertSee('name="order_status"', false);
            $response->assertSee('value="PUT"', false);

            // Test admin sidebar includes uncommented Orders link
            $response = $this->actingAs($admin)->get('/admin/dashboard');
            $response->assertStatus(200);
            $response->assertSee('admin/orders');
            $response->assertSee('Orders');
        } finally {
            $order->update(['status_message' => $originalStatus]);
        }
    }

    /* ------------------------------------------------------------------
     | Batch 2. These tests run against the real database, so every test
     | that writes wraps its work in a transaction and rolls it back.
     | ------------------------------------------------------------------ */

    /**
     * MED-04: a missing category or product slug is a 404, not a redirect back.
     */
    public function test_med_04_missing_slugs_return_404(): void
    {
        $this->get('/collections/no-such-category-zzz')->assertStatus(404);
        $this->get('/collections/no-such-category-zzz/no-such-product-zzz')->assertStatus(404);

        $product = Product::with('catagory')->where('status', '0')->first();
        $this->assertNotNull($product);
        $this->get('/collections/' . $product->catagory->slug . '/no-such-product-zzz')->assertStatus(404);

        // A real product still loads.
        $this->get('/collections/' . $product->catagory->slug . '/' . $product->slug)->assertStatus(200);
    }

    /**
     * MED-01: an admin cannot delete or demote their own account.
     */
    public function test_med_01_admin_cannot_delete_or_demote_self(): void
    {
        $admin = User::where('roll_as', 1)->first();
        $this->assertNotNull($admin);

        DB::beginTransaction();
        try {
            // Self-delete is blocked.
            $response = $this->actingAs($admin)->delete('/admin/users/' . $admin->id);
            $response->assertRedirect();
            $response->assertSessionHas('error');
            $this->assertNotNull(User::find($admin->id));

            // Self-demote is blocked.
            $response = $this->actingAs($admin)->put('/admin/users/' . $admin->id, [
                'name' => $admin->name,
                'email' => $admin->email,
                'roll_as' => 0,
            ]);
            $response->assertSessionHasErrors('roll_as');
            $this->assertEquals(1, (int) User::find($admin->id)->roll_as);

            // An admin can still edit their own name while staying admin.
            $response = $this->actingAs($admin)->put('/admin/users/' . $admin->id, [
                'name' => $admin->name . ' X',
                'email' => $admin->email,
                'roll_as' => 1,
            ]);
            $response->assertSessionHasNoErrors();
            $this->assertEquals($admin->name . ' X', User::find($admin->id)->name);

            // Deleting ANOTHER user still works.
            $other = User::create([
                'name' => 'Temp Delete Me',
                'email' => 'temp-delete-me@example.test',
                'password' => bcrypt('secret-secret'),
            ]);
            $this->actingAs($admin)->delete('/admin/users/' . $other->id)->assertSessionHas('status');
            $this->assertNull(User::find($other->id));
        } finally {
            DB::rollBack();
        }
    }

    /**
     * MED-03: the contact form stores every submission and the admin can list them.
     */
    public function test_med_03_contact_form_stores_message(): void
    {
        $this->assertTrue(Schema::hasTable('contact_messages'));

        DB::beginTransaction();
        try {
            $response = $this->post('/contact', [
                'name' => 'Audit Tester',
                'email' => 'audit-tester@example.test',
                'message' => 'Hello from the audit test 7391.',
            ]);
            $response->assertSessionHas('message', 'Thank you for contacting us!');
            $this->assertDatabaseHas('contact_messages', [
                'name' => 'Audit Tester',
                'email' => 'audit-tester@example.test',
                'message' => 'Hello from the audit test 7391.',
            ]);

            // Invalid submissions are rejected and not stored.
            $before = DB::table('contact_messages')->count();
            $this->post('/contact', ['name' => 'X', 'email' => 'not-an-email', 'message' => ''])
                ->assertSessionHasErrors(['email', 'message']);
            $this->assertEquals($before, DB::table('contact_messages')->count());

            // Admin list page shows it; guests cannot open it.
            $admin = User::where('roll_as', 1)->first();
            $this->actingAs($admin)->get('/admin/contact-messages')
                ->assertStatus(200)
                ->assertSee('Hello from the audit test 7391.');
        } finally {
            DB::rollBack();
        }

        auth()->logout();
        $this->get('/admin/contact-messages')->assertRedirect();
    }

    /**
     * MED-05: Today's Orders counts orders created today.
     */
    public function test_med_05_dashboard_counts_todays_orders(): void
    {
        $admin = User::where('roll_as', 1)->first();

        DB::beginTransaction();
        try {
            Order::create([
                'user_id' => $admin->id,
                'tracking_no' => 'buyora-audittest',
                'fullname' => 'Audit Tester',
                'email' => 'audit-tester@example.test',
                'phone' => '01712345678',
                'pincode' => '1207',
                'address' => 'House 1, Dhaka',
                'status_message' => 'in progress',
                'payment_mode' => 'Cash On Delivery',
            ]);

            $expected = Order::whereDate('created_at', now()->toDateString())->count();
            $this->assertGreaterThanOrEqual(1, $expected);

            $this->actingAs($admin)->get('/admin/dashboard')
                ->assertStatus(200)
                ->assertViewHas('todayOrder', $expected);
        } finally {
            DB::rollBack();
        }
    }

    /**
     * HIGH-08: the header cart count listens for the 'cart-updated' event.
     */
    public function test_high_08_cart_count_refreshes_on_cart_updated(): void
    {
        $user = User::first();
        $product = Product::first();

        DB::beginTransaction();
        try {
            \App\Models\Cart::where('user_id', $user->id)->delete();

            $component = \Livewire\Livewire::actingAs($user)
                ->test(\App\Livewire\Frontend\Cart\CartCount::class)
                ->assertSet('cartCount', 0);

            \App\Models\Cart::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 1]);

            $component->dispatch('cart-updated')->assertSet('cartCount', 1);
        } finally {
            DB::rollBack();
        }

        // No component still uses the old, mismatched event names.
        foreach (['Frontend/Cart/CartShow.php', 'Frontend/Cart/CartCount.php', 'Frontend/Product/View.php'] as $file) {
            $this->assertStringNotContainsStringIgnoringCase('cartAddedUpdated', file_get_contents(app_path('Livewire/' . $file)));
        }
    }

    /**
     * MED-07: saving a product with a colour it already has updates that row instead of duplicating it.
     */
    public function test_med_07_product_update_does_not_duplicate_colors(): void
    {
        $admin = User::where('roll_as', 1)->first();
        $color = Color::first();
        $product = Product::first();
        $this->assertNotNull($color);
        $this->assertNotNull($product);

        DB::beginTransaction();
        try {
            ProductColor::where('product_id', $product->id)->where('color_id', $color->id)->delete();
            ProductColor::create(['product_id' => $product->id, 'color_id' => $color->id, 'quantity' => 3]);
            $otherRows = ProductColor::where('product_id', $product->id)->where('color_id', '!=', $color->id)->count();

            $payload = $product->only([
                'catagory_id', 'name', 'slug', 'brand', 'small_description', 'description',
                'original_price', 'selling_price', 'quantity', 'meta_title', 'meta_keyword', 'meta_description',
            ]) + [
                'colors' => [$color->id => $color->id],
                'colorquantity' => [$color->id => 77],
            ];

            $this->actingAs($admin)->put('/admin/products/' . $product->id, $payload)
                ->assertSessionHasNoErrors()
                ->assertSessionHas('message', 'Product Updated Successfully.');

            $rows = ProductColor::where('product_id', $product->id)->where('color_id', $color->id)->get();
            $this->assertCount(1, $rows);
            $this->assertEquals(77, (int) $rows->first()->quantity);

            // Colours that were not submitted are left alone.
            $this->assertEquals($otherRows, ProductColor::where('product_id', $product->id)->where('color_id', '!=', $color->id)->count());
        } finally {
            DB::rollBack();
        }
    }

    /**
     * HIGH-06: product image validation is keyed on 'image' (the real form field name).
     */
    public function test_high_06_product_image_validation_uses_image_key(): void
    {
        $rules = (new \App\Http\Requests\ProductFormRequest())->rules();
        $this->assertArrayHasKey('image', $rules);
        $this->assertArrayHasKey('image.*', $rules);
        $this->assertArrayNotHasKey('images', $rules);

        $imageRules = ['image' => $rules['image'], 'image.*' => $rules['image.*']];

        $bad = ['image' => [\Illuminate\Http\UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf')]];
        $this->assertTrue(validator($bad, $imageRules)->fails());

        $tooBig = ['image' => [\Illuminate\Http\UploadedFile::fake()->image('big.png')->size(3000)]];
        $this->assertTrue(validator($tooBig, $imageRules)->fails());

        $good = ['image' => [\Illuminate\Http\UploadedFile::fake()->image('ok.png')]];
        $this->assertFalse(validator($good, $imageRules)->fails());
    }

    /**
     * MED-10: checkout accepts real Bangladeshi phone numbers and postcodes.
     */
    public function test_med_10_checkout_phone_and_pincode_rules(): void
    {
        $all = (new \App\Livewire\Frontend\Checkout\CheckoutShow())->rules();
        $rules = ['phone' => $all['phone'], 'pincode' => $all['pincode']];

        $valid = [
            ['01712345678', '1207'], ['+8801712345678', '9100'], ['+880 1712-345678', '900100'],
            ['8801712345678', 'SW1A 1AA'], ['017-1234-5678', '12-07'],
        ];
        foreach ($valid as [$phone, $pincode]) {
            $this->assertFalse(validator(compact('phone', 'pincode'), $rules)->fails(), "$phone / $pincode should pass");
        }

        $invalid = [
            ['12345', '1207'], ['hello', '1207'], ['01112345678', '1207'], ['017123456789012', '1207'],
            ['01712345678', '12'], ['01712345678', '12345678901'], ['01712345678', '12#4'],
        ];
        foreach ($invalid as [$phone, $pincode]) {
            $this->assertTrue(validator(compact('phone', 'pincode'), $rules)->fails(), "$phone / $pincode should fail");
        }
    }

    /**
     * Item 20: the gateway city comes from the customer's address, not a hardcoded 'Khulna'.
     */
    public function test_payment_city_comes_from_address(): void
    {
        $city = fn ($a) => \App\Http\Controllers\Frontend\PaymentController::cityFromAddress($a);

        $this->assertSame('Dhaka', $city('House 12, Road 5, Dhanmondi, Dhaka'));
        $this->assertSame('Dhaka', $city('House 12, Road 5, Dhanmondi, Dhaka-1207, Bangladesh'));
        $this->assertSame('Chattogram', $city("Flat 3B, Agrabad\nChattogram 4100"));
        $this->assertSame('Sylhet', $city('Zindabazar, Sylhet, 3100'));
        $this->assertSame('Khulna', $city('Khulna'));

        $source = file_get_contents(app_path('Http/Controllers/Frontend/PaymentController.php'));
        $this->assertStringNotContainsString("'Khulna'", $source);
    }

    /**
     * HIGH-05: preview dumps are not written into public/.
     */
    public function test_high_05_no_preview_files_in_public(): void
    {
        $this->assertSame([], glob(public_path('_preview_*.html')));
    }

    /**
     * LOW-07: a customer lands on the storefront after login, an admin on the dashboard.
     */
    public function test_low_07_login_redirects(): void
    {
        DB::beginTransaction();
        try {
            $customer = User::create([
                'name' => 'Temp Customer',
                'email' => 'temp-customer@example.test',
                'password' => bcrypt('secret-secret'),
            ]);
            $this->post('/login', ['email' => $customer->email, 'password' => 'secret-secret'])->assertRedirect('/');
            auth()->logout();

            $admin = User::create([
                'name' => 'Temp Admin',
                'email' => 'temp-admin@example.test',
                'password' => bcrypt('secret-secret'),
            ]);
            DB::table('users')->where('id', $admin->id)->update(['roll_as' => 1]);
            $this->post('/login', ['email' => $admin->email, 'password' => 'secret-secret'])->assertRedirect('admin/dashboard');
        } finally {
            DB::rollBack();
        }
    }
}
