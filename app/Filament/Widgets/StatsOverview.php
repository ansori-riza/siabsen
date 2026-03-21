<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Murid;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = now()->toDateString();

        $muridHadir = Absensi::where('subject_type', 'App\Models\Murid')
            ->whereIn('status', ['hadir', 'terlambat'])
            ->whereDate('waktu_absen', $today)
            ->where('tipe', 'masuk')
            ->count();

        $muridAlpha = Absensi::where('subject_type', 'App\Models\Murid')
            ->where('status', 'alpha')
            ->whereDate('waktu_absen', $today)
            ->where('tipe', 'masuk')
            ->count();

        $totalMurid = Murid::where('is_active', true)->count();

        $guruHadir = Absensi::where('subject_type', 'App\Models\Guru')
            ->whereIn('status', ['hadir', 'terlambat'])
            ->whereDate('waktu_absen', $today)
            ->where('tipe', 'masuk')
            ->count();

        $guruAlpha = Absensi::where('subject_type', 'App\Models\Guru')
            ->where('status', 'alpha')
            ->whereDate('waktu_absen', $today)
            ->where('tipe', 'masuk')
            ->count();

        $totalGuru = Guru::where('is_active', true)->count();

        return [
            Stat::make('Murid Hadir', $muridHadir)
                ->description("{$muridAlpha} Alpha dari {$totalMurid} murid")
                ->color($muridHadir > 0 ? 'success' : 'danger'),

            Stat::make('Guru Hadir', $guruHadir)
                ->description("{$guruAlpha} Alpha dari {$totalGuru} guru")
                ->color($guruHadir > 0 ? 'success' : 'warning'),

            Stat::make('Total Kehadiran', $muridHadir + $guruHadir)
                ->description('Hari ini')
                ->color('primary'),
        ];
    }
}
