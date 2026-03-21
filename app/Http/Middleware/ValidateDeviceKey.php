<?php

namespace App\Http\Middleware;

use App\Models\Perangkat;
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
                'message' => 'Missing X-Device-Key header',
            ], 401);
        }

        $perangkat = Perangkat::where('device_key', $deviceKey)
            ->where('is_active', true)
            ->first();

        if (!$perangkat) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive device key',
            ], 401);
        }

        // Attach perangkat to request for controller access
        $request->attributes->set('perangkat', $perangkat);

        return $next($request);
    }
}