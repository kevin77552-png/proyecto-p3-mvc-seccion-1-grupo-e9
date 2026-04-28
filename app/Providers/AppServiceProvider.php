<?php

namespace App\Providers;

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
        // Ensure PHP runtime uses the application's configured timezone so
        // Carbon::now() and other date functions are consistent with
        // `config('app.timezone')` even if php.ini has a different value.
        date_default_timezone_set(config('app.timezone'));
    }
}
