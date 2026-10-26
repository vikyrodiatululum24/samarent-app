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
        Schema::create('angsuran_setorans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('angsuran_kendaraan_id')->constrained('angsuran_kendaraans')->cascadeOnDelete();
            $table->date('tgl_setor');
            $table->unsignedTinyInteger('periode_bulan'); // 1 - 12
            $table->unsignedSmallInteger('periode_tahun');
            $table->decimal('nominal', 14, 2);
            $table->string('metode_setor', 30)->default('transfer'); // transfer, tunai, kompensasi_biaya, lainnya
            $table->foreignId('angsuran_pengeluaran_id')->nullable()->constrained('angsuran_pengeluarans')->nullOnDelete();
            $table->string('bukti_setor')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('angsuran_setorans');
    }
};
