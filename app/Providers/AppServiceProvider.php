<?php

namespace App\Providers;

use App\Models\Catagory;
use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         Paginator::useBootstrapFive();

         // Share the single site-settings row with every view, so the
         // footer, contact page and page title/meta read what the admin
         // saves in Site Settings. Loaded once per request; null when
         // the table is empty or unavailable, so every use falls back
         // to the hardcoded default with ??.
         View::composer('*', function ($view) {
             static $setting = null;
             static $loaded = false;

             if (! $loaded) {
                 $loaded = true;
                 try {
                     $setting = Setting::first();
                 } catch (\Throwable $e) {
                     $setting = null;
                 }
             }

             $view->with('setting', $setting);
         });

         // Share the visible categories (status 0) with every view, so the
         // navbar, footer and homepage cards follow what the admin manages
         // in the dashboard. Loaded once per request; an empty collection
         // when the table is unavailable.
         View::composer('*', function ($view) {
             static $navCategories = null;

             if ($navCategories === null) {
                 try {
                     $navCategories = Catagory::where('status', 0)->orderBy('name')->get();
                 } catch (\Throwable $e) {
                     $navCategories = collect();
                 }
             }

             $view->with('navCategories', $navCategories);
         });
    }
}
