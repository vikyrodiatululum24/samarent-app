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
        Schema::create('unit_juals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('data_units')->onDelete('cascade');
            $table->decimal('harga_jual', 15, 2)->unsigned();
            $table->decimal('harga_netto', 15, 2)->unsigned();
            $table->string('keterangan')->nullable();
            $table->string('foto_depan')->nullable();
            $table->string('foto_belakang')->nullable();
            $table->string('foto_kiri')->nullable();
            $table->string('foto_kanan')->nullable();
            $table->string('foto_interior')->nullable();
            $table->string('foto_odometer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_juals');
    }
};
