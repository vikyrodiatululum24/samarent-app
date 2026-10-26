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
        Schema::create('angsuran_pengeluarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('angsuran_kendaraan_id')->constrained('angsuran_kendaraans')->cascadeOnDelete();
            $table->date('tgl_pengeluaran');
            $table->string('jenis_pengeluaran');
            $table->decimal('nominal', 14, 2);
            $table->string('bukti_nota')->nullable();
            $table->string('status', 30)->default('diajukan'); // diajukan, disetujui, sudah_dikompensasi, ditolak
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('angsuran_pengeluarans');
    }
};
