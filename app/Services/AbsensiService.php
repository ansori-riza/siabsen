<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\JadwalSekolah;
use App\Models\Murid;
use Illuminate\Support\Carbon;

class AbsensiService
{
    public function tentukanStatus(JadwalSekolah $jadwal, string $tipe, Carbon $waktuAbsen): string
    {
        if ($tipe === 'pulang') {
            return 'hadir';
        }

        $jamMasukDenganToleransi = Carbon::parse($jadwal->jam_masuk)
            ->addMinutes($jadwal->toleransi_menit);

        if ($waktuAbsen->greaterThan($jamMasukDenganToleransi)) {
            return 'terlambat';
        }

        return 'hadir';
    }

    public function cekSudahAbsen($user, string $tipe, ?Carbon $tanggal = null): bool
    {
        $tanggal = $tanggal ?? now();

        return Absensi::where('subject_type', get_class($user))
            ->where('subject_id', $user->id)
            ->where('tipe', $tipe)
            ->whereDate('waktu_absen', $tanggal)
            ->exists();
    }

    public function createAbsensi($user, string $tipe, string $metode, int $perangkatId = null): Absensi
    {
        $role = $user instanceof Guru ? 'guru' : 'murid';

        $jadwal = JadwalSekolah::where('hari', now()->locale('id')->dayName)
            ->where('role_target', $role)
            ->where('is_active', true)
            ->first();

        $status = $jadwal
            ? $this->tentukanStatus($jadwal, $tipe, now())
            : 'hadir';

        return Absensi::create([
            'sekolah_id' => $user->sekolah_id,
            'perangkat_id' => $perangkatId,
            'subject_type' => get_class($user),
            'subject_id' => $user->id,
            'tipe' => $tipe,
            'status' => $status,
            'metode' => $metode,
            'waktu_absen' => now(),
        ]);
    }
}