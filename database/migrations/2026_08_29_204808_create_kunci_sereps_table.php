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
        Schema::create('kunci_sereps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('data_units')->cascadeOnDelete();
            $table->string('no_kunci')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('status_kunci')->default('tersedia');
            $table->string('tanggal_masuk')->nullable();
            $table->string('tanggal_keluar')->nullable();
            $table->string('diambil_oleh')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kunci_sereps');
    }
};
