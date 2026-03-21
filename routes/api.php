<?php

use App\Http\Controllers\Api\AbsensiController;
use Illuminate\Support\Facades\Route;

Route::post('/v1/absensi', [AbsensiController::class, 'store'])
    ->middleware('device.key');

Route::post('/v1/perangkat/heartbeat', [AbsensiController::class, 'heartbeat'])
    ->middleware('device.key');