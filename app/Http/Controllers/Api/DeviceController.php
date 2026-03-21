<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalSekolah;
use App\Models\Perangkat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Heartbeat dari ESP32 untuk update status online
     */
    public function heartbeat(Request $request): JsonResponse
    {
        $deviceKey = $request->header('X-Device-Key');
        
        $perangkat = Perangkat::where('device_key', $deviceKey)
            ->where('is_active', true)
            ->first();

        if (!$perangkat) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found',
            ], 401);
        }

        $perangkat->update([
            'status' => 'online',
            'last_ping' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Heartbeat received',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Sync jadwal dan whitelist untuk ESP32
     */
    public function sync(Request $request): JsonResponse
    {
        $deviceKey = $request->header('X-Device-Key');
        
        $perangkat = Perangkat::where('device_key', $deviceKey)
            ->where('is_active', true)
            ->first();

        if (!$perangkat) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found',
            ], 401);
        }

        // Ambil jadwal hari ini
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

        $jadwals = JadwalSekolah::where('hari', $hariIni)
            ->where('is_active', true)
            ->get();

        // Ambil whitelist RFID untuk ESP32
        $rfidWhitelist = $this->getRfidWhitelist($perangkat->sekolah_id);

        return response()->json([
            'success' => true,
            'device_id' => $perangkat->id,
            'device_name' => $perangkat->nama,
            'jadwal' => $jadwals->map(function ($jadwal) {
                return [
                    'role_target' => $jadwal->role_target,
                    'hari' => $jadwal->hari,
                    'jam_masuk' => $jadwal->jam_masuk,
                    'jam_pulang' => $jadwal->jam_pulang,
                    'toleransi_menit' => $jadwal->toleransi_menit,
                ];
            }),
            'rfid_whitelist' => $rfidWhitelist,
        ]);
    }

    /**
     * Get whitelist RFID untuk ESP32 cache
     */
    private function getRfidWhitelist(int $sekolahId): array
    {
        $whitelist = [];

        // Guru RFID
        $gurus = \App\Models\Guru::where('sekolah_id', $sekolahId)
            ->where('is_active', true)
            ->whereNotNull('rfid_uid')
            ->select('id', 'rfid_uid', 'nama', 'fingerprint_id')
            ->get();

        foreach ($gurus as $guru) {
            $whitelist[] = [
                'type' => 'guru',
                'rfid_uid' => $guru->rfid_uid,
                'fingerprint_id' => $guru->fingerprint_id,
                'name' => $guru->nama,
            ];
        }

        // Murid RFID
        $murids = \App\Models\Murid::where('sekolah_id', $sekolahId)
            ->where('is_active', true)
            ->whereNotNull('rfid_uid')
            ->select('id', 'rfid_uid', 'nama')
            ->get();

        foreach ($murids as $murid) {
            $whitelist[] = [
                'type' => 'murid',
                'rfid_uid' => $murid->rfid_uid,
                'name' => $murid->nama,
            ];
        }

        return $whitelist;
    }
}