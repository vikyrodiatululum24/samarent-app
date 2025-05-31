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
        Schema::create('data_units', function (Blueprint $table) {
            $table->id();
            $table->string('no_rks')->nullable();
            $table->string('penyerahan_unit')->nullable();
            $table->string('jenis');
            $table->string('merk');
            $table->string('type');
            $table->string('nopol')->unique();
            $table->string('no_rangka')->nullable();
            $table->string('no_mesin')->nullable();
            $table->string('tgl_pajak')->nullable();
            $table->string('regional')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_units');
    }
};
