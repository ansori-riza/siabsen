<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Perangkat;
use App\Services\AbsensiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AbsensiController extends Controller
{
    public function __construct(
        private AbsensiService $absensiService
    ) {}

    public function absen(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'rfid_uid' => 'nullable|string|max:255',
            'fingerprint_id' => 'nullable|integer|min:1|max:162',
            'tipe' => 'required|in:masuk,pulang',
            'device_id' => 'nullable|string|max:255',
            'timestamp' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'pesan' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!$request->rfid_uid && !$request->fingerprint_id) {
            return response()->json([
                'success' => false,
                'pesan' => 'RFID UID atau Fingerprint ID wajib diisi',
                'feedback' => [
                    'lcd_text' => 'Data tidak lengkap ❌',
                    'buzzer' => 'beep_long',
                    'led_color' => 'red',
                ],
            ], 422);
        }

        $result = $this->absensiService->prosesTap(
            rfidUid: $request->rfid_uid,
            fingerprintId: $request->fingerprint_id,
            tipe: $request->tipe,
            deviceId: $request->device_id,
        );

        $statusCode = $result['success'] ? 200 : ($result['pesan'] === 'RFID/Fingerprint tidak dikenal' ? 404 : 200);

        return response()->json($result, $statusCode);
    }

    public function heartbeat(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => 'Device ID required'], 422);
        }

        $device = Perangkat::where('device_key', $request->device_id)->first();

        if (!$device) {
            return response()->json(['success' => false, 'error' => 'Device not found'], 404);
        }

        $device->markOnline();

        return response()->json([
            'success' => true,
            'device' => [
                'id' => $device->id,
                'nama' => $device->nama,
                'status' => 'online',
            ],
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => 'Device ID required'], 422);
        }

        $device = Perangkat::where('device_key', $request->device_id)->first();

        if (!$device) {
            return response()->json(['success' => false, 'error' => 'Device not found'], 404);
        }

        // Get relevant config for this device
        $sekolah = $device->sekolah;
        $hariIni = now()->dayOfWeek;

        $jadwalMurid = $sekolah->jadwalSekolahs()
            ->where('role_target', 'murid')
            ->where('hari', $hariIni)
            ->where('is_active', true)
            ->first();

        $jadwalGuru = $sekolah->jadwalSekolahs()
            ->where('role_target', 'guru')
            ->where('hari', $hariIni)
            ->where('is_active', true)
            ->first();

        return response()->json([
            'success' => true,
            'sekolah' => [
                'nama' => $sekolah->nama,
                'theme_color' => $sekolah->theme_color,
            ],
            'jadwal' => [
                'murid' => $jadwalMurid ? [
                    'jam_masuk' => $jadwalMurid->jam_masuk,
                    'jam_pulang' => $jadwalMurid->jam_pulang,
                    'toleransi_menit' => $jadwalMurid->toleransi_menit,
                ] : null,
                'guru' => $jadwalGuru ? [
                    'jam_masuk' => $jadwalGuru->jam_masuk,
                    'jam_pulang' => $jadwalGuru->jam_pulang,
                    'toleransi_menit' => $jadwalGuru->toleransi_menit,
                ] : null,
            ],
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
