<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Guru;
use App\Models\JadwalSekolah;
use App\Models\Murid;
use App\Models\Perangkat;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AbsensiController extends Controller
{
    /**
     * API untuk ESP32/Perangkat melakukan absensi
     * Method: POST /api/absensi
     * Headers: X-Device-Key: {device_key}
     * Body: { rfid_uid: string | fingerprint_id: int, tipe: 'masuk'|'pulang' }
     */
    public function store(Request $request): JsonResponse
    {
        // Validasi device key
        $deviceKey = $request->header('X-Device-Key');
        $perangkat = Perangkat::where('device_key', $deviceKey)
            ->where('is_active', true)
            ->first();

        if (!$perangkat) {
            return $this->errorResponse('Device tidak terdaftar', 401);
        }

        // Update status perangkat
        $perangkat->update([
            'status' => 'online',
            'last_ping' => now(),
        ]);

        // Validasi input
        $validated = $request->validate([
            'rfid_uid' => 'nullable|string|max:20',
            'fingerprint_id' => 'nullable|integer',
            'tipe' => 'required|in:masuk,pulang',
        ]);

        // Minimal salah satu harus ada
        if (empty($validated['rfid_uid']) && empty($validated['fingerprint_id'])) {
            return $this->errorResponse('RFID UID atau Fingerprint ID harus diisi', 400);
        }

        // Cari user berdasarkan RFID atau Fingerprint
        $user = $this->findUser($validated);

        if (!$user) {
            return $this->errorResponse('RFID/Fingerprint tidak dikenal', 404);
        }

        // Cek anti-double tap (2 detik cooldown)
        $recentAbsensi = Absensi::where('subject_type', get_class($user))
            ->where('subject_id', $user->id)
            ->where('tipe', $validated['tipe'])
            ->where('waktu_absen', '>=', now()->subSeconds(2))
            ->first();

        if ($recentAbsensi) {
            return $this->successResponse($user, 'already_tapped', $validated['tipe']);
        }

        // Tentukan status berdasarkan jadwal
        $status = $this->determineStatus($user, $validated['tipe']);

        // Simpan absensi
        $absensi = Absensi::create([
            'sekolah_id' => $perangkat->sekolah_id,
            'perangkat_id' => $perangkat->id,
            'subject_type' => get_class($user),
            'subject_id' => $user->id,
            'tipe' => $validated['tipe'],
            'status' => $status,
            'metode' => $validated['rfid_uid'] ? 'RFID' : 'fingerprint',
            'waktu_absen' => now(),
        ]);

        return $this->successResponse($user, $status, $validated['tipe'], $absensi->metode);
    }

    /**
     * Cari user berdasarkan RFID atau Fingerprint
     */
    private function findUser(array $data): ?object
    {
        // Cari berdasarkan RFID
        if (!empty($data['rfid_uid'])) {
            // Cari di Guru dulu
            $guru = Guru::where('rfid_uid', $data['rfid_uid'])->where('is_active', true)->first();
            if ($guru) {
                return $guru;
            }

            // Cari di Murid
            $murid = Murid::where('rfid_uid', $data['rfid_uid'])->where('is_active', true)->first();
            if ($murid) {
                return $murid;
            }
        }

        // Cari berdasarkan Fingerprint (hanya untuk Guru di Phase 1)
        if (!empty($data['fingerprint_id'])) {
            $guru = Guru::where('fingerprint_id', $data['fingerprint_id'])->where('is_active', true)->first();
            if ($guru) {
                return $guru;
            }
        }

        return null;
    }

    /**
     * Tentukan status berdasarkan jadwal
     */
    private function determineStatus(object $user, string $tipe): string
    {
        if ($tipe === 'pulang') {
            return 'hadir';
        }

        // Cari jadwal hari ini
        $hariIndonesia = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $hariIni = $hariIndonesia[now()->format('l')];
        $roleTarget = $user instanceof Guru ? 'guru' : 'murid';

        $jadwal = JadwalSekolah::where('hari', $hariIni)
            ->where('role_target', $roleTarget)
            ->where('is_active', true)
            ->first();

        if (!$jadwal) {
            return 'hadir'; // Default jika tidak ada jadwal
        }

        // Cek terlambat
        $jamMasuk = Carbon::parse($jadwal->jam_masuk);
        $jamMasukDenganToleransi = $jamMasuk->copy()->addMinutes($jadwal->toleransi_menit);
        $waktuAbsen = now();

        if ($waktuAbsen->gt($jamMasukDenganToleransi)) {
            return 'terlambat';
        }

        return 'hadir';
    }

    /**
     * Response sukses dengan feedback untuk ESP32
     */
    private function successResponse(object $user, string $status, string $tipe, string $metode = 'RFID'): JsonResponse
    {
        $statusLabels = [
            'hadir' => 'HADIR',
            'terlambat' => 'TERLAMBAT',
            'already_tapped' => 'SUDAH TAP',
        ];

        $statusEmojis = [
            'hadir' => '✅',
            'terlambat' => '⚠️',
            'already_tapped' => '⏰',
        ];

        $lcdText = sprintf(
            "%s %s %s - %s (%s)",
            $user->nama,
            $statusEmojis[$status] ?? '✅',
            $statusLabels[$status] ?? 'HADIR',
            now()->format('H:i'),
            $metode
        );

        $buzzer = match ($status) {
            'hadir' => 'beep_short',
            'terlambat' => 'beep_double',
            'already_tapped' => 'beep_long',
            default => 'beep_short',
        };

        $ledColor = match ($status) {
            'hadir' => 'green',
            'terlambat' => 'yellow',
            'already_tapped' => 'red',
            default => 'green',
        };

        return response()->json([
            'success' => true,
            'nama' => $user->nama,
            'status' => $status === 'already_tapped' ? 'hadir' : $status,
            'metode' => $metode,
            'waktu' => now()->format('H:i'),
            'feedback' => [
                'lcd_text' => $lcdText,
                'buzzer' => $buzzer,
                'led_color' => $ledColor,
            ],
        ]);
    }

    /**
     * Response error dengan feedback untuk ESP32
     */
    private function errorResponse(string $message, int $code): JsonResponse
    {
        return response()->json([
            'success' => false,
            'pesan' => $message,
            'feedback' => [
                'lcd_text' => 'Tidak dikenal ❌',
                'buzzer' => 'beep_long',
                'led_color' => 'red',
            ],
        ], $code);
    }
}