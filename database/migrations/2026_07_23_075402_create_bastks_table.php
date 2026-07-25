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
        Schema::create('bastk', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->nullable()->unique();
            $table->string('kepada')->nullable();
            $table->string('alamat')->nullable();
            $table->string('no_hp')->nullable();
            $table->foreignId('unit_id')->constrained('data_units')->cascadeOnDelete();
            $table->date('tgl_serah')->nullable();
            $table->date('tgl_kembali')->nullable();
            $table->string('nama_penyerah')->nullable();
            $table->string('nama_penerima')->nullable();
            $table->json('kondisi_unit')->nullable();
            $table->text('exchange')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bastk');
    }
};