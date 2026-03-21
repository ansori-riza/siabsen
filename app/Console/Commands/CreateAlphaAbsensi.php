<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Murid;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CreateAlphaAbsensi extends Command
{
    protected $signature = 'absensi:create-alpha {--date=} {--sekolah_id=}';
    protected $description = 'Create alpha absensi records for all murid and guru';

    public function handle(): int
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : now();
        $sekolahId = $this->option('sekolah_id');

        $this->info("Creating alpha absensi for {$date->toDateString()}");

        $muridQuery = Murid::where('is_active', true);
        $guruQuery = Guru::where('is_active', true);

        if ($sekolahId) {
            $muridQuery->where('sekolah_id', $sekolahId);
            $guruQuery->where('sekolah_id', $sekolahId);
        }

        $murids = $muridQuery->get();
        $gurus = $guruQuery->get();

        $createdCount = 0;

        foreach ($murids as $murid) {
            $existing = Absensi::where('subject_type', Murid::class)
                ->where('subject_id', $murid->id)
                ->where('tipe', 'masuk')
                ->whereDate('waktu_absen', $date)
                ->first();

            if (!$existing) {
                Absensi::create([
                    'sekolah_id' => $murid->sekolah_id,
                    'subject_type' => Murid::class,
                    'subject_id' => $murid->id,
                    'tipe' => 'masuk',
                    'status' => 'alpha',
                    'metode' => 'manual',
                    'source' => 'system',
                    'waktu_absen' => $date->copy()->setTime(0, 0, 1),
                ]);
                $createdCount++;
            }
        }

        foreach ($gurus as $guru) {
            $existing = Absensi::where('subject_type', Guru::class)
                ->where('subject_id', $guru->id)
                ->where('tipe', 'masuk')
                ->whereDate('waktu_absen', $date)
                ->first();

            if (!$existing) {
                Absensi::create([
                    'sekolah_id' => $guru->sekolah_id,
                    'subject_type' => Guru::class,
                    'subject_id' => $guru->id,
                    'tipe' => 'masuk',
                    'status' => 'alpha',
                    'metode' => 'manual',
                    'source' => 'system',
                    'waktu_absen' => $date->copy()->setTime(0, 0, 1),
                ]);
                $createdCount++;
            }
        }

        $this->info("Created {$createdCount} alpha absensi records");

        return self::SUCCESS;
    }
}
