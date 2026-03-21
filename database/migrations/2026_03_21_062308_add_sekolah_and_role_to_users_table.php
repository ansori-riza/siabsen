<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('sekolah_id')->nullable()->after('id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('guru_id')->nullable()->after('sekolah_id')->constrained('gurus')->nullOnDelete();
            $table->string('role', 20)->default('operator')->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['sekolah_id']);
            $table->dropForeign(['guru_id']);
            $table->dropColumn(['sekolah_id', 'guru_id', 'role']);
        });
    }
};
