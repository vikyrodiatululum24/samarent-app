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
        Schema::create('asuransis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('data_units')->onDelete('cascade');
            $table->string('up')->nullable();
            $table->text('lokasi')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('nama_pic')->nullable();
            $table->date('tanggal_pengajuan')->nullable();
            $table->date('tanggal_kejadian')->nullable();
            $table->string('nama')->nullable();
            $table->string('jenis')->nullable();
            $table->string('periode_mulai')->nullable();
            $table->string('periode_selesai')->nullable();
            $table->decimal('nominal', 15, 2)->nullable();
            $table->string('kategori')->nullable();
            $table->text('status')->nullable();
            $table->text('tujuan_pengajuan')->nullable();
            $table->string('foto_ktp')->nullable();
            $table->string('foto_sim')->nullable();
            $table->string('foto_sntk')->nullable();
            $table->string('foto_bpkb')->nullable();
            $table->string('foto_polis_asuransi')->nullable();
            $table->string('foto_ba')->nullable();
            $table->string('foto_keterangan_bengkel')->nullable();
            $table->string('foto_npwp_pt')->nullable();
            $table->json('foto_unit')->nullable();
            $table->json('foto_nota')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asuransis');
    }
};
