<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->string('source')->nullable()->after('metode');
        });

        DB::table('absensis')
            ->where('keterangan', 'like', 'Auto-generated:%')
            ->update(['source' => 'system']);
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
