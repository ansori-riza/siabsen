<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perangkats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->string('nama');
            $table->string('lokasi')->nullable();
            $table->string('device_key', 64)->unique();
            $table->enum('tipe', ['gerbang', 'kelas'])->default('gerbang');
            $table->enum('status', ['online', 'offline'])->default('offline');
            $table->timestamp('last_ping')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perangkats');
    }
};
