<?php

namespace App\Services;

use App\Enums\MetodeAbsensi;
use App\Enums\StatusAbsensi;
use App\Enums\TipeAbsensi;
use App\Models\Absensi;
use App\Models\Guru;
use App\Models\JadwalSekolah;
use App\Models\Murid;
use App\Models\Perangkat;
use App\Models\Sekolah;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AbsensiService
{
    private const DOUBLE_TAP_COOLDOWN_SECONDS = 2;

    public function prosesTap(
        ?string $rfidUid,
        ?int $fingerprintId,
        string $tipe,
        ?string $deviceId = null
    ): array {
        $subject = $this->identifikasiSubject($rfidUid, $fingerprintId);

        if (!$subject) {
            return $this->errorResponse('RFID/Fingerprint tidak dikenal');
        }

        $sekolah = $subject->sekolah;
        $jadwal = $this->getJadwal($sekolah, $subject);
        $status = $this->tentukanStatus($jadwal, $tipe);
        $metode = $this->tentukanMetode($rfidUid, $fingerprintId);

        $result = $this->simpanAbsensi($subject, $sekolah, $tipe, $status, $metode, $deviceId);

        if (!$result['success']) {
            return $result;
        }

        return $this->successResponse($subject, $status, $metode, $result['waktu']);
    }

    private function identifikasiSubject(?string $rfidUid, ?int $fingerprintId): Murid|Guru|null
    {
        if ($rfidUid) {
            $murid = Murid::where('rfid_uid', $rfidUid)->first();
            if ($murid) {
                return $murid;
            }

            return Guru::where('rfid_uid', $rfidUid)->first();
        }

        if ($fingerprintId) {
            return Guru::where('fingerprint_id', $fingerprintId)->first();
        }

        return null;
    }

    private function getJadwal(Sekolah $sekolah, Murid|Guru $subject): ?JadwalSekolah
    {
        $roleTarget = $subject instanceof Guru ? 'guru' : 'murid';
        $hariIni = now()->dayOfWeek;

        return JadwalSekolah::where('sekolah_id', $sekolah->id)
            ->where('role_target', $roleTarget)
            ->where('hari', $hariIni)
            ->where('is_active', true)
            ->first();
    }

    public function tentukanStatus(?JadwalSekolah $jadwal, string $tipe): StatusAbsensi
    {
        if ($tipe === 'pulang') {
            return StatusAbsensi::HADIR;
        }

        if (!$jadwal) {
            return StatusAbsensi::HADIR;
        }

        $waktuSekarang = now()->format('H:i:s');
        $batasTerlambat = Carbon::parse($jadwal->jam_masuk)
            ->addMinutes($jadwal->toleransi_menit)
            ->format('H:i:s');

        return $waktuSekarang > $batasTerlambat
            ? StatusAbsensi::TERLAMBAT
            : StatusAbsensi::HADIR;
    }

    private function tentukanMetode(?string $rfidUid, ?int $fingerprintId): MetodeAbsensi
    {
        if ($fingerprintId) {
            return MetodeAbsensi::FINGERPRINT;
        }

        if ($rfidUid) {
            return MetodeAbsensi::RFID;
        }

        return MetodeAbsensi::MANUAL;
    }

    private function simpanAbsensi(
        Murid|Guru $subject,
        Sekolah $sekolah,
        string $tipe,
        StatusAbsensi $status,
        MetodeAbsensi $metode,
        ?string $deviceId
    ): array {
        $sekarang = now();
        $tanggal = $sekarang->toDateString();
        $waktu = $sekarang->format('H:i:s');

        // Anti double tap check
        $existingTap = Absensi::where('subject_type', get_class($subject))
            ->where('subject_id', $subject->id)
            ->where('tanggal', $tanggal)
            ->where('tipe', $tipe)
            ->where('tap_terakhir', '>', $sekarang->copy()->subSeconds(self::DOUBLE_TAP_COOLDOWN_SECONDS))
            ->first();

        if ($existingTap) {
            return [
                'success' => false,
                'message' => 'Double tap detected',
                'waktu' => $waktu,
            ];
        }

        // Find perangkat
        $perangkat = null;
        if ($deviceId) {
            $perangkat = Perangkat::where('device_key', $deviceId)->first();
        }

        $absensi = Absensi::create([
            'sekolah_id' => $sekolah->id,
            'perangkat_id' => $perangkat?->id,
            'subject_type' => get_class($subject),
            'subject_id' => $subject->id,
            'tipe' => $tipe,
            'tanggal' => $tanggal,
            'waktu' => $waktu,
            'status' => $status->value,
            'metode' => $metode->value,
            'device_id' => $deviceId,
            'tap_terakhir' => $sekarang,
        ]);

        return [
            'success' => true,
            'waktu' => $waktu,
            'absensi_id' => $absensi->id,
        ];
    }

    private function successResponse(
        Murid|Guru $subject,
        StatusAbsensi $status,
        MetodeAbsensi $metode,
        string $waktu
    ): array {
        $role = $subject instanceof Guru ? 'guru' : 'murid';
        $roleLabel = $subject instanceof Guru ? 'Pak/Bu' : '';

        $feedback = $this->generateFeedback($subject->nama, $status, $waktu, $metode);

        return [
            'success' => true,
            'nama' => $subject->nama,
            'role' => $role,
            'status' => $status->value,
            'metode' => $metode->value,
            'waktu' => substr($waktu, 0, 5),
            'feedback' => $feedback,
        ];
    }

    private function errorResponse(string $message): array
    {
        return [
            'success' => false,
            'pesan' => $message,
            'feedback' => [
                'lcd_text' => 'Tidak dikenal ❌',
                'buzzer' => 'beep_long',
                'led_color' => 'red',
            ],
        ];
    }

    private function generateFeedback(
        string $nama,
        StatusAbsensi $status,
        string $waktu,
        MetodeAbsensi $metode
    ): array {
        $emoji = match($status) {
            StatusAbsensi::HADIR => '✅',
            StatusAbsensi::TERLAMBAT => '⚠️',
            StatusAbsensi::ALPHA => '❌',
        };

        $metodeText = match($metode) {
            MetodeAbsensi::FINGERPRINT => ' (Fingerprint)',
            MetodeAbsensi::RFID => ' (RFID)',
            default => '',
        };

        $lcdText = sprintf(
            '%s %s %s - %s%s',
            $nama,
            $emoji,
            strtoupper($status->label()),
            substr($waktu, 0, 5),
            $metodeText
        );

        $buzzer = match($status) {
            StatusAbsensi::HADIR => 'beep_short',
            StatusAbsensi::TERLAMBAT => 'beep_double',
            StatusAbsensi::ALPHA => 'beep_long',
        };

        $ledColor = match($status) {
            StatusAbsensi::HADIR => 'green',
            StatusAbsensi::TERLAMBAT => 'yellow',
            StatusAbsensi::ALPHA => 'red',
        };

        return [
            'lcd_text' => $lcdText,
            'buzzer' => $buzzer,
            'led_color' => $ledColor,
        ];
    }

    public function generateAlphaRecords(Sekolah $sekolah, Carbon $tanggal = null): int
    {
        $tanggal = $tanggal ?? now();
        $hariIni = $tanggal->dayOfWeek;

        $count = 0;

        // Get active murids
        $murids = Murid::where('sekolah_id', $sekolah->id)
            ->where('is_active', true)
            ->get();

        foreach ($murids as $murid) {
            $jadwal = $this->getJadwal($sekolah, $murid);
            if (!$jadwal || $jadwal->hari !== $hariIni) {
                continue;
            }

            $exists = Absensi::where('subject_type', Murid::class)
                ->where('subject_id', $murid->id)
                ->where('tanggal', $tanggal->toDateString())
                ->where('tipe', 'masuk')
                ->exists();

            if (!$exists) {
                Absensi::create([
                    'sekolah_id' => $sekolah->id,
                    'subject_type' => Murid::class,
                    'subject_id' => $murid->id,
                    'tipe' => 'masuk',
                    'tanggal' => $tanggal->toDateString(),
                    'waktu' => '00:00:00',
                    'status' => StatusAbsensi::ALPHA->value,
                    'metode' => MetodeAbsensi::MANUAL->value,
                ]);
                $count++;
            }
        }

        // Get active gurus
        $gurus = Guru::where('sekolah_id', $sekolah->id)
            ->where('is_active', true)
            ->get();

        foreach ($gurus as $guru) {
            $jadwal = $this->getJadwal($sekolah, $guru);
            if (!$jadwal || $jadwal->hari !== $hariIni) {
                continue;
            }

            $exists = Absensi::where('subject_type', Guru::class)
                ->where('subject_id', $guru->id)
                ->where('tanggal', $tanggal->toDateString())
                ->where('tipe', 'masuk')
                ->exists();

            if (!$exists) {
                Absensi::create([
                    'sekolah_id' => $sekolah->id,
                    'subject_type' => Guru::class,
                    'subject_id' => $guru->id,
                    'tipe' => 'masuk',
                    'tanggal' => $tanggal->toDateString(),
                    'waktu' => '00:00:00',
                    'status' => StatusAbsensi::ALPHA->value,
                    'metode' => MetodeAbsensi::MANUAL->value,
                ]);
                $count++;
            }
        }

        return $count;
    }
}
