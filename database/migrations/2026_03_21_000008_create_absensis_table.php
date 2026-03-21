<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('perangkat_id')->nullable()->constrained('perangkats')->nullOnDelete();
            $table->morphs('subject'); // subject_type + subject_id (Murid or Guru)
            $table->enum('tipe', ['masuk', 'pulang']);
            $table->enum('status', ['hadir', 'terlambat', 'alpha'])->default('alpha');
            $table->enum('metode', ['rfid', 'fingerprint', 'manual'])->default('rfid');
            $table->dateTime('waktu');
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->index(['subject_type', 'subject_id', 'tipe', 'waktu']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
