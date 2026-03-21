<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use App\Models\Perangkat;
use App\Models\Sekolah;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Hitung untuk hari ini
        $today = now()->toDateString();

        // Murid
        $muridHadir = Absensi::whereDate('waktu_absen', $today)
            ->where('subject_type', 'App\Models\Murid')
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();

        $muridAlpha = Absensi::whereDate('waktu_absen', $today)
            ->where('subject_type', 'App\Models\Murid')
            ->where('status', 'alpha')
            ->count();

        $muridTerlambat = Absensi::whereDate('waktu_absen', $today)
            ->where('subject_type', 'App\Models\Murid')
            ->where('status', 'terlambat')
            ->count();

        // Guru
        $guruHadir = Absensi::whereDate('waktu_absen', $today)
            ->where('subject_type', 'App\Models\Guru')
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();

        $guruAlpha = Absensi::whereDate('waktu_absen', $today)
            ->where('subject_type', 'App\Models\Guru')
            ->where('status', 'alpha')
            ->count();

        // Device
        $deviceOnline = Perangkat::where('status', 'online')->count();
        $deviceOffline = Perangkat::where('status', 'offline')->count();

        return [
            Stat::make(Sekolah::getStudentLabel() . ' Hadir', $muridHadir)
                ->description("Terlambat: {$muridTerlambat}")
                ->descriptionIcon('heroicon-m-clock')
                ->color('success'),

            Stat::make(Sekolah::getStudentLabel() . ' Tidak Hadir', $muridAlpha)
                ->description('Alpha/Izin')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make(Sekolah::getGuruLabel() . ' Hadir', $guruHadir)
                ->description("Tidak Hadir: {$guruAlpha}")
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('Device Online', $deviceOnline)
                ->description("Offline: {$deviceOffline}")
                ->descriptionIcon('heroicon-m-wifi')
                ->color($deviceOffline > 0 ? 'warning' : 'success'),
        ];
    }
}
