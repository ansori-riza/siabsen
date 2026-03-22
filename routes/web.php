<?php

use App\Http\Controllers\Auth\CustomLoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Custom Login Routes (bypass Livewire/Filament login issues)
Route::get('/login', [CustomLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [CustomLoginController::class, 'login']);
Route::post('/logout', [CustomLoginController::class, 'logout'])->name('logout');

// Also map /admin/login to custom login for Filament compatibility
Route::get('/admin/login', [CustomLoginController::class, 'showLoginForm']);
Route::post('/admin/login', [CustomLoginController::class, 'login']);
