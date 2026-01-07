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
        // Force HTTPS when behind a proxy (ngrok, load balancer, etc.)
        if (request()->header('X-Forwarded-Proto') === 'https' || 
            str_contains(request()->header('Host', ''), 'ngrok') ||
            app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
