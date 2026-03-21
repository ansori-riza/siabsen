<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sekolahs', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('npsn', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('kepala_sekolah')->nullable();
            $table->string('logo')->nullable();
            $table->string('theme_color', 7)->default('#1971C2');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sekolahs');
    }
};
