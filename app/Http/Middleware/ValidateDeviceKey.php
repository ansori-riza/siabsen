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
                'pesan' => 'X-Device-Key header required',
            ], 401);
        }

        $device = Perangkat::where('device_key', $deviceKey)->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'pesan' => 'Device not authorized',
            ], 401);
        }

        // Mark device as online
        $device->markOnline();

        return $next($request);
    }
}
