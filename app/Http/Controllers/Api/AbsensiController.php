<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Guru;
use App\Models\JadwalSekolah;
use App\Models\Murid;
use App\Models\Perangkat;
use App\Services\AbsensiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AbsensiController extends Controller
{
    public function __construct(
        private AbsensiService $absensiService
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rfid_uid' => 'nullable|string|required_without:fingerprint_id',
            'fingerprint_id' => 'nullable|integer|required_without:rfid_uid',
            'tipe' => 'required|in:masuk,pulang',
            'device_id' => 'required|string',
        ]);

        $perangkat = Perangkat::where('device_id', $validated['device_id'])
            ->where('device_key', $request->header('X-Device-Key'))
            ->first();

        if (!$perangkat) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat tidak valid',
                'feedback' => [
                    'lcd' => 'Error: Device',
                    'buzzer' => 'long',
                    'led' => 'red'
                ]
            ], 401);
        }

        $perangkat->update(['last_ping' => now(), 'status' => 'online']);

        $user = null;
        $role = null;

        if (!empty($validated['rfid_uid'])) {
            $user = Murid::where('rfid_uid', $validated['rfid_uid'])->first()
                ?? Guru::where('rfid_uid', $validated['rfid_uid'])->first();
            $role = $user instanceof Guru ? 'guru' : 'murid';
        }

        if (!$user && !empty($validated['fingerprint_id'])) {
            $user = Guru::where('fingerprint_id', $validated['fingerprint_id'])->first();
            $role = 'guru';
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'RFID/Fingerprint tidak dikenal',
                'feedback' => [
                    'lcd' => 'Tidak Dikenal',
                    'buzzer' => 'long',
                    'led' => 'red'
                ]
            ], 404);
        }

        $jadwal = JadwalSekolah::where('hari', now()->locale('id')->dayName)
            ->where('role_target', $role)
            ->where('is_active', true)
            ->first();

        if (!$jadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada jadwal hari ini',
                'feedback' => [
                    'lcd' => 'Tidak Ada Jadwal',
                    'buzzer' => 'double',
                    'led' => 'yellow'
                ]
            ], 400);
        }

        $waktuSekarang = now()->format('H:i:s');
        $jamMasukDenganToleransi = Carbon::parse($jadwal->jam_masuk)
            ->addMinutes($jadwal->toleransi_menit)
            ->format('H:i:s');

        $status = $waktuSekarang > $jamMasukDenganToleransi ? 'terlambat' : 'hadir';

        $metode = !empty($validated['rfid_uid']) ? 'RFID' : 'fingerprint';

        $existingAbsensi = Absensi::where('subject_type', get_class($user))
            ->where('subject_id', $user->id)
            ->where('tipe', $validated['tipe'])
            ->whereDate('waktu_absen', today())
            ->first();

        if ($existingAbsensi) {
            return response()->json([
                'success' => false,
                'message' => 'Sudah absen ' . $validated['tipe'] . ' hari ini',
                'feedback' => [
                    'lcd' => 'Sudah Absen',
                    'buzzer' => 'short',
                    'led' => 'yellow'
                ]
            ], 400);
        }

        $absensi = Absensi::create([
            'sekolah_id' => $user->sekolah_id,
            'perangkat_id' => $perangkat->id,
            'subject_type' => get_class($user),
            'subject_id' => $user->id,
            'tipe' => $validated['tipe'],
            'status' => $status,
            'metode' => $metode,
            'waktu_absen' => now(),
        ]);

        $nama = $user->nama;
        $statusText = $status === 'hadir' ? 'HADIR' : 'TERLAMBAT';
        $emoji = $status === 'hadir' ? '✓' : '!';

        return response()->json([
            'success' => true,
            'nama' => $nama,
            'role' => $role,
            'status' => $status,
            'metode' => $metode,
            'waktu' => now()->format('H:i'),
            'feedback' => [
                'lcd' => "{$nama} {$emoji} {$statusText} - " . now()->format('H:i'),
                'buzzer' => $status === 'hadir' ? 'short' : 'double',
                'led' => $status === 'hadir' ? 'green' : 'yellow'
            ]
        ]);
    }

    public function heartbeat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => 'required|string',
        ]);

        $perangkat = Perangkat::where('device_id', $validated['device_id'])
            ->where('device_key', $request->header('X-Device-Key'))
            ->first();

        if (!$perangkat) {
            return response()->json(['success' => false], 401);
        }

        $perangkat->update([
            'last_ping' => now(),
            'status' => 'online',
        ]);

        return response()->json([
            'success' => true,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}