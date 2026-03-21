<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('absensis', 'waktu') && !Schema::hasColumn('absensis', 'waktu_absen')) {
            Schema::table('absensis', function (Blueprint $table) {
                $table->renameColumn('waktu', 'waktu_absen');
            });
        }

        if (Schema::hasColumn('absensis', 'metode')) {
            DB::table('absensis')->where('metode', 'RFID')->update(['metode' => 'rfid']);
            DB::table('absensis')->where('metode', 'system')->update(['metode' => 'manual']);

            Schema::table('absensis', function (Blueprint $table) {
                $table->enum('metode', ['rfid', 'fingerprint', 'manual'])->default('rfid')->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('absensis', 'metode')) {
            Schema::table('absensis', function (Blueprint $table) {
                $table->enum('metode', ['RFID', 'fingerprint', 'manual', 'system'])->default('RFID')->change();
            });

            DB::table('absensis')->where('metode', 'rfid')->update(['metode' => 'RFID']);
            DB::table('absensis')->where('metode', 'manual')->where('keterangan', 'like', 'Auto-generated:%')->update(['metode' => 'system']);
        }

        if (Schema::hasColumn('absensis', 'waktu_absen') && !Schema::hasColumn('absensis', 'waktu')) {
            Schema::table('absensis', function (Blueprint $table) {
                $table->renameColumn('waktu_absen', 'waktu');
            });
        }
    }
};
