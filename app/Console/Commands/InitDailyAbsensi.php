<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Murid;
use App\Models\Sekolah;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Command untuk inisialisasi absensi harian (Positive Attendance Default)
 * Best Practice BP4: Semua murid & guru default "alpha" di awal hari
 * Di-run setiap hari jam 00:01 via scheduler
 */
class InitDailyAbsensi extends Command
{
    protected $signature = 'absensi:init-daily {--dry-run : Jangan simpan ke database}';
    protected $description = 'Inisialisasi record absensi alpha untuk semua murid dan guru aktif';

    public function handle(): int
    {
        $this->info('Memulai inisialisasi absensi harian...');
        $isDryRun = $this->option('dry-run');

        $sekolahs = Sekolah::where('is_active', true)->get();
        $totalCreated = 0;

        foreach ($sekolahs as $sekolah) {
            $this->info("Sekolah: {$sekolah->nama}");

            // Inisialisasi untuk murid
            $murids = Murid::where('sekolah_id', $sekolah->id)
                ->where('is_active', true)
                ->get();

            foreach ($murids as $murid) {
                $created = $this->createAlphaRecord($murid, 'murid', $isDryRun);
                if ($created) $totalCreated++;
            }

            $this->info("  - Murid: {$murids->count()} record");

            // Inisialisasi untuk guru
            $gurus = Guru::where('sekolah_id', $sekolah->id)
                ->where('is_active', true)
                ->get();

            foreach ($gurus as $guru) {
                $created = $this->createAlphaRecord($guru, 'guru', $isDryRun);
                if ($created) $totalCreated++;
            }

            $this->info("  - Guru: {$gurus->count()} record");
        }

        $this->info("Total record alpha dibuat: {$totalCreated}");
        Log::info("InitDailyAbsensi: {$totalCreated} records created" . ($isDryRun ? ' (DRY RUN)' : ''));

        return self::SUCCESS;
    }

    private function createAlphaRecord($user, string $type, bool $isDryRun): bool
    {
        // Cek apakah sudah ada record untuk hari ini
        $exists = Absensi::where('subject_type', get_class($user))
            ->where('subject_id', $user->id)
            ->where('tipe', 'masuk')
            ->whereDate('waktu_absen', today())
            ->exists();

        if ($exists) {
            return false; // Sudah ada, skip
        }

        if ($isDryRun) {
            return true; // Simulasi saja
        }

        Absensi::create([
            'sekolah_id' => $user->sekolah_id,
            'subject_type' => get_class($user),
            'subject_id' => $user->id,
            'tipe' => 'masuk',
            'status' => 'alpha',
            'metode' => 'manual',
            'waktu_absen' => today()->startOfDay(),
            'keterangan' => 'Auto-generated: Belum absen',
        ]);

        return true;
    }
}
