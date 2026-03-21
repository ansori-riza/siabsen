<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Buat sekolah default
        $sekolah = Sekolah::create([
            'nama' => 'Sekolah Contoh',
            'npsn' => '12345678',
            'alamat' => 'Jl. Pendidikan No. 1',
            'kepala_sekolah' => 'Bapak Kepala Sekolah',
            'theme_color' => '#1971C2',
            'is_active' => true,
        ]);

        // Buat admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@siabsen.test',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'sekolah_id' => $sekolah->id,
        ]);

        $this->command->info('Admin user created: admin@siabsen.test / password');
        $this->command->info('Sekolah default created: Sekolah Contoh');
    }
}