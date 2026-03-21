<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateDeviceKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $deviceKey = $request->header('X-Device-Key');

        if (!$deviceKey) {
            return response()->json([
                'success' => false,
                'message' => 'Device key tidak ditemukan'
            ], 401);
        }

        return $next($request);
    }
}