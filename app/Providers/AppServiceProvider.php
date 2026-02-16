<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        if (app()->runningInConsole() === false) {
            \Log::info('Checking Request Scheme:', [
                'scheme' => request()->getScheme(),
                'isSecure' => request()->isSecure(),
                'x-forwarded-proto' => request()->header('x-forwarded-proto'),
                'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ]);
        }
        // URL::forceScheme('https');
    }
}
