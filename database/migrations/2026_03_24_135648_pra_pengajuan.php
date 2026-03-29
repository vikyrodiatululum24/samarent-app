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
        Schema::create('pra_pengajuans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pic');
            $table->string('no_wa');
            $table->string('project');
            $table->string('up');
            $table->string('up_lainnya')->nullable();
            $table->string('provinsi');
            $table->string('kota');
            $table->date('tanggal_masuk_finance')->nullable();
            $table->date('tanggal_otorisasi')->nullable();
            $table->date('tanggal_pengerjaan')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pra_pengajuans');
    }
};
