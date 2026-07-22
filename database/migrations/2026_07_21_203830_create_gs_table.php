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
        Schema::create('gs', function (Blueprint $table) {
            $table->id();

            // Data Driver Ori
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->string('no_hp', 15)->nullable();
            $table->text('alasan')->nullable();

            // Data User
            $table->string('project')->nullable();
            $table->string('user')->nullable();
            $table->string('no_hp_user', 15)->nullable();

            // Detail
            $table->text('lokasi');
            $table->foreignId('unit_id')->constrained('data_units')->cascadeOnDelete();
            $table->time('jam_standby_mulai');
            $table->time('jam_standby_selesai')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('kunci_unit')->nullable();
            $table->text('keterangan')->nullable();

            //driver pengganti
            $table->string('driver_pengganti')->nullable();
            $table->string('no_hp_pengganti', 15)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gs');
    }
};
