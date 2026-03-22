<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ForceAssetUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Force URL root untuk Filament assets
        $appUrl = config('app.url');
        
        if ($appUrl) {
            URL::forceRootUrl($appUrl);
            
            // Force HTTPS jika APP_URL menggunakan https
            if (str_starts_with($appUrl, 'https://')) {
                URL::forceScheme('https');
            }
        }
        
        return $next($request);
    }
}
