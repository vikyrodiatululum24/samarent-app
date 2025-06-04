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
        Schema::create('pengajuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('no_pengajuan')->unique();
            $table->string('nama');
            $table->string('no_wa');
            $table->string('project');
            $table->string('up');
            $table->string('up_lainnya')->nullable();
            $table->string('provinsi');
            $table->string('kota');
            $table->enum('keterangan', ['REIMBURSE', 'CASH ADVANCE', 'INVOICE', 'FREE']);
            $table->string('payment_1')->nullable();
            $table->string('bank_1')->nullable();
            $table->string('norek_1')->nullable();
            $table->string('keterangan_proses')->default('CS');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};
