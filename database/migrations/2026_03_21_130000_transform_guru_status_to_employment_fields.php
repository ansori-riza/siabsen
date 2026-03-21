<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('gurus', 'employment_type')) {
            Schema::table('gurus', function (Blueprint $table) {
                $table->enum('employment_type', ['tetap', 'tidak_tetap', 'kontrak', 'part_time', 'lainnya'])
                    ->default('tidak_tetap')
                    ->after('fingerprint_id');
            });
        }

        if (! Schema::hasColumn('gurus', 'employment_detail')) {
            Schema::table('gurus', function (Blueprint $table) {
                $table->string('employment_detail')->nullable()->after('employment_type');
            });
        }

        if (Schema::hasColumn('gurus', 'status')) {
            DB::table('gurus')
                ->where('status', 'pns')
                ->update([
                    'employment_type' => 'tetap',
                    'employment_detail' => DB::raw("COALESCE(NULLIF(employment_detail, ''), 'Guru Tetap')"),
                ]);

            DB::table('gurus')
                ->where('status', 'honor')
                ->update([
                    'employment_type' => 'tidak_tetap',
                    'employment_detail' => DB::raw("COALESCE(NULLIF(employment_detail, ''), 'Guru Tidak Tetap')"),
                ]);

            Schema::table('gurus', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('gurus', 'status')) {
            Schema::table('gurus', function (Blueprint $table) {
                $table->enum('status', ['pns', 'honor'])->default('honor')->after('fingerprint_id');
            });
        }

        if (Schema::hasColumn('gurus', 'employment_type')) {
            DB::table('gurus')
                ->where('employment_type', 'tetap')
                ->update(['status' => 'pns']);

            DB::table('gurus')
                ->whereIn('employment_type', ['tidak_tetap', 'kontrak', 'part_time', 'lainnya'])
                ->update(['status' => 'honor']);

            if (Schema::hasColumn('gurus', 'employment_detail')) {
                Schema::table('gurus', function (Blueprint $table) {
                    $table->dropColumn('employment_detail');
                });
            }

            Schema::table('gurus', function (Blueprint $table) {
                $table->dropColumn('employment_type');
            });
        }
    }
};
