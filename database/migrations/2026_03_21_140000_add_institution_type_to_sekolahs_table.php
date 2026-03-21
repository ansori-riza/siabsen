<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->string('institution_type', 20)
                ->default('sekolah_umum')
                ->after('kepala_sekolah');
        });
    }

    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn('institution_type');
        });
    }
};
