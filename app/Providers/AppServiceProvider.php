<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Belt-and-suspenders alongside public/.htaccess's HTTPS redirect —
        // guarantees every generated URL (route(), asset(), redirects) is
        // https:// even if a request somehow reaches the app over plain
        // HTTP (e.g. an internal artisan/cron context with no live request
        // to infer the scheme from).
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }
    }
}
