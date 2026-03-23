<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('murids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->string('nis', 20)->unique();
            $table->string('nama');
            $table->string('rfid_uid', 20)->unique()->nullable();
            $table->unsignedSmallInteger('fingerprint_id')->unique()->nullable();
            $table->enum('jenis_kelamin', ['l', 'p'])->default('l');
            $table->date('tanggal_lahir')->nullable();
            $table->string('foto')->nullable();
            $table->string('nama_ortu')->nullable();
            $table->string('hp_ortu', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('murids');
    }
};
