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
        Schema::create('bastk_dokumentasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bastk_id')->constrained('bastk')->cascadeOnDelete();
            $table->string('unit_depan')->nullable();
            $table->string('unit_belakang')->nullable();
            $table->string('unit_samping_kanan')->nullable();
            $table->string('unit_samping_kiri')->nullable();
            $table->string('kabin_depan')->nullable();
            $table->string('kabin_tengah')->nullable();
            $table->string('kabin_belakang')->nullable();
            $table->string('dashboard')->nullable();
            $table->string('odometer')->nullable();
            $table->json('kerusakan')->nullable();
            $table->json('tools')->nullable();
            $table->string('buku_service')->nullable();
            $table->string('manual_book')->nullable();
            $table->string('ban_serep')->nullable();
            $table->string('stnk_depan')->nullable();
            $table->string('stnk_belakang')->nullable();
            $table->string('bastk')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bastk_dokumentasis');
    }
};
