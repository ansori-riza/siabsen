<?php

use App\Http\Controllers\Api\AbsensiController;
use App\Http\Controllers\Api\DeviceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Routes untuk ESP32/IoT Device
| Authentication via X-Device-Key header
|
*/

// Device routes (require X-Device-Key header)
Route::post('/device/heartbeat', [DeviceController::class, 'heartbeat'])
    ->name('api.device.heartbeat');

Route::get('/device/sync', [DeviceController::class, 'sync'])
    ->name('api.device.sync');

// Absensi routes (require X-Device-Key header)
Route::post('/absensi', [AbsensiController::class, 'store'])
    ->name('api.absensi.store');

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ]);
})->name('api.health');