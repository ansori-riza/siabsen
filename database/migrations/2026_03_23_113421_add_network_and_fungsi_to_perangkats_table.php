<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perangkats', function (Blueprint $table) {
            $table->enum('tipe_fungsi', ['in', 'out', 'both'])->default('both')->after('vendor_type');
            $table->string('ip_address', 45)->nullable()->after('tipe_fungsi');
            $table->integer('port')->nullable()->after('ip_address');
        });
    }

    public function down(): void
    {
        Schema::table('perangkats', function (Blueprint $table) {
            $table->dropColumn(['tipe_fungsi', 'ip_address', 'port']);
        });
    }
};