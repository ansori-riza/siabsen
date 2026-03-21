<?php

namespace Tests\Feature;

use App\Enums\MetodeAbsensi;
use App\Enums\UserRole;
use App\Filament\Resources\AbsensiResource;
use App\Filament\Resources\AbsensiResource\Pages\ListAbsensis;
use App\Models\Absensi;
use App\Models\JadwalSekolah;
use App\Models\Kelas;
use App\Models\Murid;
use App\Models\Perangkat;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OperationalVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_access_works_for_operator_pembina_and_pimpinan(): void
    {
        $sekolah = $this->createSekolah();

        foreach ([UserRole::OPERATOR, UserRole::PEMBINA, UserRole::PIMPINAN] as $role) {
            $user = User::create([
                'name' => ucfirst($role->value),
                'email' => $role->value.'@example.test',
                'password' => 'password',
                'sekolah_id' => $sekolah->id,
                'role' => $role->value,
            ]);

            $this->actingAs($user)
                ->get('/admin')
                ->assertOk();

            auth()->logout();
        }
    }

    public function test_end_to_end_absensi_flow_masuk_pulang_manual_koreksi_and_export(): void
    {
        $sekolah = $this->createSekolah();
        $kelas = Kelas::create([
            'sekolah_id' => $sekolah->id,
            'nama' => '7A',
            'tingkat' => 7,
            'kapasitas' => 32,
        ]);

        $murid = Murid::create([
            'sekolah_id' => $sekolah->id,
            'kelas_id' => $kelas->id,
            'nis' => 'NIS001',
            'nama' => 'Santri Test',
            'rfid_uid' => 'RFID001',
            'jenis_kelamin' => 'l',
            'is_active' => true,
        ]);

        $perangkat = Perangkat::create([
            'sekolah_id' => $sekolah->id,
            'nama' => 'Gate Device',
            'lokasi' => 'Gerbang',
            'device_key' => 'DEVICE-KEY-001',
            'tipe' => 'gerbang',
            'status' => 'offline',
            'is_active' => true,
        ]);

        JadwalSekolah::create([
            'sekolah_id' => $sekolah->id,
            'role_target' => 'murid',
            'hari' => now()->dayOfWeek,
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '15:00:00',
            'toleransi_menit' => 10,
            'is_active' => true,
        ]);

        // Tap masuk terlambat
        $this->travelTo(now()->setTime(7, 20));
        $this->postJson('/api/absensi', [
            'rfid_uid' => $murid->rfid_uid,
            'tipe' => 'masuk',
        ], [
            'X-Device-Key' => $perangkat->device_key,
        ])->assertOk()->assertJsonPath('status', 'terlambat');

        // Tap pulang
        $this->travelTo(now()->setTime(15, 10));
        $this->postJson('/api/absensi', [
            'rfid_uid' => $murid->rfid_uid,
            'tipe' => 'pulang',
        ], [
            'X-Device-Key' => $perangkat->device_key,
        ])->assertOk()->assertJsonPath('status', 'hadir');

        $masuk = Absensi::where('subject_type', Murid::class)->where('subject_id', $murid->id)->where('tipe', 'masuk')->firstOrFail();
        $pulang = Absensi::where('subject_type', Murid::class)->where('subject_id', $murid->id)->where('tipe', 'pulang')->firstOrFail();

        $this->assertSame('terlambat', $masuk->status);
        $this->assertSame('hadir', $pulang->status);

        // Koreksi manual oleh operator
        $operator = User::create([
            'name' => 'Operator',
            'email' => 'operator-flow@example.test',
            'password' => 'password',
            'sekolah_id' => $sekolah->id,
            'role' => UserRole::OPERATOR->value,
        ]);

        $this->actingAs($operator);
        $this->assertTrue(AbsensiResource::canEdit($masuk));

        $masuk->update([
            'status' => 'izin',
            'metode' => MetodeAbsensi::MANUAL->value,
            'keterangan' => 'Koreksi manual operator',
        ]);

        $this->assertDatabaseHas('absensis', [
            'id' => $masuk->id,
            'status' => 'izin',
            'metode' => MetodeAbsensi::MANUAL->value,
        ]);

        // Export laporan CSV hari ini
        Livewire::actingAs($operator)
            ->test(ListAbsensis::class)
            ->call('exportTodayCsv')
            ->assertFileDownloaded();
    }

    public function test_domain_labels_are_consistent_for_pondok_context(): void
    {
        $sekolah = $this->createSekolah('pondok');

        $user = User::create([
            'name' => 'Operator Pondok',
            'email' => 'operator-pondok@example.test',
            'password' => 'password',
            'sekolah_id' => $sekolah->id,
            'role' => UserRole::OPERATOR->value,
        ]);

        $this->actingAs($user);

        $dictionary = Sekolah::getLabelDictionary();

        $this->assertSame('Ustadz/Pengajar', $dictionary['guru_label']);
        $this->assertSame('Musyrif Kelas', $dictionary['class_guardian_label']);
        $this->assertSame('Murid', $dictionary['student_label']);
    }

    public function test_timezone_and_schedule_use_asia_jakarta_for_late_alpha_calculation(): void
    {
        $this->assertSame('Asia/Jakarta', config('app.timezone'));

        $command = app()->make(\Illuminate\Console\Scheduling\Schedule::class)
            ->events()[0] ?? null;

        $this->assertNotNull($command);
        $this->assertSame('Asia/Jakarta', $command->timezone);
    }

    private function createSekolah(string $institutionType = 'sekolah_umum'): Sekolah
    {
        return Sekolah::create([
            'nama' => 'Sekolah Test',
            'npsn' => '12345678',
            'institution_type' => $institutionType,
            'is_active' => true,
        ]);
    }
}
