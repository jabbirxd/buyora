<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\Admin\OrderController;


Auth::routes();
// home route
Route::get('/',[App\Http\Controllers\Frontend\FrontendController::class,'index']);
Route::get('/contact',[App\Http\Controllers\Frontend\FrontendController::class,'contact']);
Route::get('/about-us', [App\Http\Controllers\Frontend\FrontendController::class, 'aboutUs']);
Route::get('/collections',[App\Http\Controllers\Frontend\FrontendController::class,'catagories']);

// Featured Products & Trending Products (must come before {catagory_slug} route)
Route::get('/collections/featured', [App\Http\Controllers\Frontend\FrontendController::class, 'featuredProducts']);
Route::get('/collections/trending', [App\Http\Controllers\Frontend\FrontendController::class, 'trendingProducts']);

// Collections by category (more general route, must come after specific routes)
Route::get('/collections/{catagory_slug}',[App\Http\Controllers\Frontend\FrontendController::class,'products']);
Route::get('/collections/{catagory_slug}/{product_slug}',[App\Http\Controllers\Frontend\FrontendController::class,'productView']);

// New Arrivals
Route::get('/new-arrivals',[App\Http\Controllers\Frontend\FrontendController::class,'newArrivals']);

// Storefront search
Route::get('/search', [App\Http\Controllers\Frontend\FrontendController::class, 'search']);

// Contact form: every submission is stored in contact_messages (admin: /admin/contact-messages)
Route::post('/contact', [App\Http\Controllers\Frontend\FrontendController::class, 'contactStore']);


Route::middleware(['auth'])->group(function(){
    Route::get('/wishlist',[App\Http\Controllers\Frontend\WishlistController::class,'index'])->name('wishlist');
    Route::get('/cart',[App\Http\Controllers\Frontend\CartController::class,'index'])->name('cart');
    Route::get('/checkout',[App\Http\Controllers\Frontend\CheckoutController::class,'index'])->name('checkout');
    // Online payment start (needs the logged-in session: reads session('pending_checkout'))
    Route::get('/payment/sslcommerz/initiate', [App\Http\Controllers\Frontend\PaymentController::class, 'sslcommerzInitiate'])->name('payment.initiate');
    Route::get('/payment/aamarpay/initiate', [App\Http\Controllers\Frontend\PaymentController::class, 'aamarpayInitiate'])->name('payment.aamarpay.initiate');
    Route::get('/orders',[App\Http\Controllers\Frontend\OrderController::class,'index']);
    Route::get('orders/{orderId}',[App\Http\Controllers\Frontend\OrderController::class,'show']);
    // User Profile Management
    Route::get('/profile', [App\Http\Controllers\Frontend\ProfileController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [App\Http\Controllers\Frontend\ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [App\Http\Controllers\Frontend\ProfileController::class, 'updatePassword'])->name('profile.password');
    
});

// 
Route::get('thank-you',[App\Http\Controllers\Frontend\FrontendController::class,'thankyou'])->name('thank-you');

// Payment gateway callbacks (SSLCommerz + aamarPay).
// These are cross-site POSTs from the gateway, so the browser does NOT send the SameSite=Lax
// login cookie with them: they cannot sit behind 'auth', and they run without the session
// middleware so Laravel does not issue a fresh session cookie that would log the customer out.
// Each one finds its order via the cached pending checkout (keyed by tran_id) and only creates
// an order after server-side verification with the gateway.
Route::controller(App\Http\Controllers\Frontend\PaymentController::class)
    ->withoutMiddleware([
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
    ])
    ->group(function () {
        Route::post('/payment/sslcommerz/success', 'sslcommerzSuccess')->name('payment.sslcommerz.success');
        Route::post('/payment/sslcommerz/fail', 'sslcommerzFail')->name('payment.sslcommerz.fail');
        Route::post('/payment/sslcommerz/cancel', 'sslcommerzCancel')->name('payment.sslcommerz.cancel');
        Route::post('/payment/sslcommerz/ipn', 'sslcommerzIpn')->name('payment.sslcommerz.ipn'); // server-to-server
        Route::post('/payment/aamarpay/success', 'aamarpaySuccess')->name('payment.aamarpay.success');
        Route::post('/payment/aamarpay/fail', 'aamarpayFail')->name('payment.aamarpay.fail');
        Route::post('/payment/aamarpay/cancel', 'aamarpayCancel')->name('payment.aamarpay.cancel');
    });

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::prefix('admin')->middleware(['auth', 'isAdmin'])->group(function(){
     route::get('dashboard',[App\Http\Controllers\Admin\DashboardController::class, 'index']);
     // Settings page
     Route::get('settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings.index');
     Route::post('settings', [App\Http\Controllers\Admin\SettingController::class, 'store'])->name('admin.settings.store');
     // User management
     Route::controller(App\Http\Controllers\Admin\UserController::class)->group(function () {
         Route::get('users', 'index')->name('admin.users.index');
         Route::get('users/create', 'create')->name('admin.users.create');
         Route::post('users', 'store')->name('admin.users.store');
         Route::get('users/{user_id}', 'show')->name('admin.users.show');
         Route::get('users/{user_id}/edit', 'edit')->name('admin.users.edit');
         Route::put('users/{user_id}', 'update')->name('admin.users.update');
         Route::delete('users/{user_id}', 'destroy')->name('admin.users.destroy');
     });

    //Catagory group route
    Route::controller(App\Http\Controllers\Admin\CatagoryController::class)->group(function () {
        Route::get('/catagory','index');
        Route::get('/catagory/create','create');
        Route::post('/catagory', 'store');
        Route::get('/catagory/{catagory}/edit', 'edit');
        Route::put('/catagory/{catagory}', 'update');
    });
    //Product group route
    Route::controller(App\Http\Controllers\Admin\ProductController::class)->group(function () {
        Route::get('/products','index')->name('admin.products.index');
        Route::get('/products/create','create')->name('admin.products.create');
        Route::post('/products', 'store')->name('admin.products.store');
        Route::get('/products/{product}/edit', 'edit')->name('admin.products.edit');
        Route::put('/products/{product}', 'update')->name('admin.products.update');
        Route::delete('/products/{product_id}/delete', 'destroy')->name('admin.products.destroy');
        Route::post('/products/{product_id}/toggle-status', 'toggleStatus')->name('admin.products.toggle-status');

        Route::delete('product-image/{product_image_id}/delete','destroyImage')->name('admin.product-image.destroy');

        Route::post('product-color/{prod_color_id}','updateProductColorQty');
        Route::delete('product-color/{prod_color_id}/delete','deleteProdColor')->name('admin.product-color.destroy');

    });
    //Color group route
    Route::controller(App\Http\Controllers\Admin\ColorController::class)->group(function () {
        Route::get('/colors','index')->name('admin.colors.index');
        Route::get('/colors/create','create')->name('admin.colors.create');
        Route::post('/colors', 'store')->name('admin.colors.store');
        Route::get('/colors/{color}/edit', 'edit')->name('admin.colors.edit');
        Route::put('/colors/{color}', 'update')->name('admin.colors.update');
        Route::delete('/colors/{color_id}/delete', 'destroy')->name('admin.colors.destroy');
        Route::post('/colors/{color_id}/toggle-status', 'toggleStatus')->name('admin.colors.toggle-status');
    });


    



    Route::get('/brands',App\Livewire\Admin\Brand\Index::class);

});
Route::prefix('admin')->middleware(['auth', 'isAdmin'])->group(function(){
    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
    Route::put('/orders/{order}', [OrderController::class, 'updateStatus'])->name('admin.orders.update');
    // Messages sent through the storefront contact form
    Route::get('/contact-messages', [App\Http\Controllers\Admin\ContactMessageController::class, 'index'])->name('admin.contact-messages.index');
    Route::get('/contact-messages/{id}', [App\Http\Controllers\Admin\ContactMessageController::class, 'show'])->name('admin.contact-messages.show');
    Route::delete('/contact-messages/{id}/delete', [App\Http\Controllers\Admin\ContactMessageController::class, 'destroy'])->name('admin.contact-messages.destroy');
});
