@extends('layouts.adminlte')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['totalGuru'] ?? 0 }}</h3>
                <p>Total Guru</p>
            </div>
            <div class="icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <a href="{{ route('guru.index') }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['totalMurid'] ?? 0 }}</h3>
                <p>Total Murid</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <a href="{{ route('murid.index') }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['totalKelas'] ?? 0 }}</h3>
                <p>Total Kelas</p>
            </div>
            <div class="icon">
                <i class="fas fa-school"></i>
            </div>
            <a href="{{ route('kelas.index') }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['totalAbsensi'] ?? 0 }}</h3>
                <p>Total Absensi</p>
            </div>
            <div class="icon">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <a href="{{ route('absensi.index') }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-1"></i>
                    Selamat Datang di SiAbsen
                </h3>
            </div>
            <div class="card-body">
                <p>Sistem Absensi Sekolah berbasis web ini digunakan untuk mengelola:</p>
                <ul>
                    <li><strong>Master Data:</strong> Guru, Murid, dan Kelas</li>
                    <li><strong>Absensi:</strong> Monitoring absensi harian</li>
                    <li><strong>Pengaturan:</strong> Jadwal sekolah dan perangkat</li>
                </ul>
                <p class="mt-3">
                    <strong>Sekolah:</strong> {{ $sekolah->nama ?? 'Sekolah Contoh' }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection