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
        // Ensure PHP uses the application timezone so helpers like now() and
        // \Carbon\Carbon::now() return times in the expected timezone on
        // hosts (like Hostinger) that may be configured to UTC.
        $timezone = config('app.timezone') ?? env('APP_TIMEZONE', 'Asia/Jakarta');
        if ($timezone) {
            date_default_timezone_set($timezone);
        }

        // Set Carbon locale to Indonesian
        \Carbon\Carbon::setLocale(config('app.locale', 'id'));
    }
}
