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
        Schema::create('reimbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('km_awal');
            $table->string('foto_odometer_awal');
            $table->unsignedBigInteger('km_akhir')->nullable();
            $table->string('foto_odometer_akhir')->nullable();
            $table->string('tujuan_perjalanan')->nullable();
            $table->text('keterangan')->nullable();
            $table->decimal('dana_masuk', 15, 2)->nullable();
            $table->decimal('dana_keluar', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reimbursements');
    }
};
