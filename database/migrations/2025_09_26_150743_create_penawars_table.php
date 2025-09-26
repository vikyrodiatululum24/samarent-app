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
        Schema::create('penawars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_jual_id')->constrained('unit_juals')->onDelete('cascade');
            $table->string('nama');
            $table->unsignedBigInteger('no_wa');
            $table->decimal('harga_penawaran', 15, 2)->unsigned();
            $table->decimal('down_payment', 15, 2)->unsigned();
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penawars');
    }
};
