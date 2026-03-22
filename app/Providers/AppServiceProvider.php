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
        // Force URL root untuk mengatasi issue Filament asset URL
        $appUrl = config('app.url');
        
        if ($appUrl) {
            URL::forceRootUrl($appUrl);
            
            // Force HTTPS jika APP_URL menggunakan https
            if (str_starts_with($appUrl, 'https://')) {
                URL::forceScheme('https');
            }
        }
    }
}
