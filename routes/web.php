<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\MuridController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\JadwalSekolahController;
use App\Http\Controllers\PerangkatController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\CustomLoginController;
use Illuminate\Support\Facades\Route;

// Redirect root to login page
Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/admin');
    }
    return redirect('/login');
});

// Custom Login Routes
Route::get('/login', [CustomLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [CustomLoginController::class, 'login']);

// Logout Routes - support both GET and POST
Route::get('/logout', [CustomLoginController::class, 'logout'])->name('logout');
Route::post('/logout', [CustomLoginController::class, 'logout']);

// Dashboard
Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Master Data Routes
Route::middleware(['auth'])->group(function () {
    // Sekolah
    Route::resource('sekolah', SekolahController::class);
    
    // Guru
    Route::resource('guru', GuruController::class);
    
    // Murid
    Route::resource('murid', MuridController::class);
    
    // Kelas
    Route::resource('kelas', KelasController::class);
    
    // Jadwal Sekolah
    Route::resource('jadwal-sekolah', JadwalSekolahController::class);
    
    // Perangkat
    Route::resource('perangkat', PerangkatController::class);
    
    // Absensi
    Route::resource('absensi', AbsensiController::class);
    Route::get('absensi/monitoring', [AbsensiController::class, 'monitoring'])->name('absensi.monitoring');
    
    // User Management (Super Admin only)
    Route::resource('user', UserController::class)->middleware('role:super_admin');
});
