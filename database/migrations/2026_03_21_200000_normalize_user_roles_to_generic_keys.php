<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $roleMap = [
            'operator_pondok' => 'operator',
            'musyrif' => 'pembina',
            'pengasuh' => 'pengelola',
            'mudir' => 'pimpinan',
            'wali_kelas' => 'pembina',
            'kepala_sekolah' => 'pimpinan',
        ];

        foreach ($roleMap as $from => $to) {
            DB::table('users')
                ->where('role', $from)
                ->update(['role' => $to]);
        }
    }

    public function down(): void
    {
        $rollbackMap = [
            'pembina' => 'wali_kelas',
            'pimpinan' => 'kepala_sekolah',
        ];

        foreach ($rollbackMap as $from => $to) {
            DB::table('users')
                ->where('role', $from)
                ->update(['role' => $to]);
        }
    }
};
