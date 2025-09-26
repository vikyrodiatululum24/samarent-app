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
        Schema::table('data_units', function (Blueprint $table) {
            $table->string('warna')->nullable();
            $table->string('tahun')->nullable();
            $table->string('bpkb')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_units', function (Blueprint $table) {
            $table->dropColumn(['warna', 'tahun', 'bpkb']);
        });
    }
};
