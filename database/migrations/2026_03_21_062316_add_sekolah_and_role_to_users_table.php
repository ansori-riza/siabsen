<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('sekolah_id')->nullable()->after('id')->constrained('sekolahs')->nullOnDelete();
            $table->foreignId('guru_id')->nullable()->after('sekolah_id')->constrained('gurus')->nullOnDelete();
            $table->string('role', 20)->default('operator')->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['sekolah_id']);
            $table->dropForeign(['guru_id']);
            $table->dropColumn(['sekolah_id', 'guru_id', 'role']);
        });
    }
};