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
        Schema::create('open_houses', function (Blueprint $table) {
            $table->id();
            $table->string('nama_event');
            $table->date('tanggal_event');
            $table->text('lokasi_event');
            $table->text('deskripsi_event')->nullable();
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('open_houses');
    }
};
