<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->string('nip', 20)->unique();
            $table->string('nama');
            $table->string('rfid_uid', 20)->unique()->nullable();
            $table->unsignedSmallInteger('fingerprint_id')->unique()->nullable();
            $table->enum('employment_type', ['tetap', 'tidak_tetap', 'kontrak', 'part_time', 'lainnya'])->default('tidak_tetap');
            $table->string('employment_detail')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('hp', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('foto')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};
