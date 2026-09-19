<?php

namespace App\Providers;

use App\Models\DynamicTable;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
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
        Schema::defaultStringLength(191);

        // Use Bootstrap 5 pagination
        Paginator::useBootstrapFive();

        // Explicit route model binding for 'table' parameter
        Route::model('table', DynamicTable::class);

        // Share the main RV Rising site root URL with every view.
        // APP_URL is "<host>/pricing" — stripping the suffix gives the parent site root,
        // which is used to load shared CSS, fonts, images and to link back to the main site.
        $appUrl   = rtrim((string) config('app.url'), '/');
        $siteRoot = rtrim(preg_replace('#/pricing$#', '', $appUrl), '/');
        View::share('siteRoot', $siteRoot ?: $appUrl);

        // Cache-buster for the shared main-site style.css so the Laravel pricing
        // pages always pull the latest stylesheet (matches the ?v=<filemtime>
        // pattern used by includes/header.php on the main site).
        $cssFile = base_path('../assets/css/style.css');
        View::share('mainCssVer', file_exists($cssFile) ? filemtime($cssFile) : time());

        // Force the URL generator to use APP_URL as the root. The /pricing shim
        // rewrites REQUEST_URI before Laravel boots, so without this Laravel
        // would build URLs like http://localhost/package/x instead of
        // http://localhost/rvrising-php/pricing/package/x.
        if ($appUrl !== '') {
            URL::forceRootUrl($appUrl);
        }
    }
}
