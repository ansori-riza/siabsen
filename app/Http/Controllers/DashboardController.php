<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Murid;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $sekolah = Sekolah::first();
        $stats = [
            'total_guru' => Guru::count(),
            'total_murid' => Murid::count(),
            'total_kelas' => Kelas::count(),
            'absensi_hari_ini' => Absensi::whereDate('tanggal', today())->count(),
        ];
        
        return view('dashboard.index', compact('sekolah', 'stats'));
    }
}
