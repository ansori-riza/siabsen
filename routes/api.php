<?php

use App\Http\Controllers\Api\AbsensiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Device API Routes (RFID/Fingerprint Hardware)
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->group(function () {
    // Device authentication via X-Device-Key header
    Route::middleware('device.key')->group(function () {
        Route::post('/absensi', [AbsensiController::class, 'absen']);
        Route::post('/perangkat/heartbeat', [AbsensiController::class, 'heartbeat']);
        Route::get('/perangkat/sync', [AbsensiController::class, 'sync']);
    });
});

/*
|--------------------------------------------------------------------------
| Admin API Routes (Authenticated)
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', function () {
        return response()->json(['message' => 'Dashboard API - coming soon']);
    });

    Route::get('/monitor', function () {
        return response()->json(['message' => 'Monitor API - coming soon']);
    });
});
