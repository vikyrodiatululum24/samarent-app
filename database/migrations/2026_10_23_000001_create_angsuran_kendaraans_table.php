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
        Schema::create('angsuran_kendaraans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('data_units')->cascadeOnDelete();
            $table->date('tgl_serah_terima');
            $table->date('tgl_jatuh_tempo_akhir');
            $table->unsignedInteger('jangka_waktu_tahun');
            $table->decimal('dp', 14, 2)->nullable();
            $table->date('tgl_dp')->nullable();
            $table->unsignedInteger('frekuensi_per_bulan')->default(28);
            $table->decimal('angsuran_per_hari', 14, 2);
            $table->decimal('normal_angsuran', 14, 2);
            $table->decimal('nilai_kontrak', 14, 2);
            $table->decimal('deposit', 14, 2)->nullable();
            $table->date('tgl_deposit')->nullable();
            $table->string('status', 30)->default('aktif');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('angsuran_kendaraans');
    }
};
