<?php

use App\Console\Commands\InitDailyAbsensi;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Register custom commands
Artisan::command('absensi:init-daily', function () {
    $this->call(InitDailyAbsensi::class);
})->describe('Initialize daily absensi records');

// Schedule: BP4 - Positive Attendance Default
// Set all murid & guru to "alpha" at 00:01 every day
Schedule::command('absensi:init-daily')
    ->dailyAt('00:01')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// Schedule: Device status cleanup
// Mark devices as offline if no heartbeat for 5 minutes
Schedule::call(function () {
    \App\Models\Perangkat::where('status', 'online')
        ->where('last_ping', '<', now()->subMinutes(5))
        ->update(['status' => 'offline']);
})->everyFiveMinutes();