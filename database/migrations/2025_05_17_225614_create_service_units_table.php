<?php

use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        Schema::create('service_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('data_units')->onDelete('cascade');
            $table->string('odometer');
            $table->string('service');
            $table->string('foto_unit');
            $table->string('foto_odometer');
            $table->json('foto_kondisi');
            $table->string('foto_pengerjaan_bengkel')->nullable();
            $table->json('foto_tambahan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_units');
    }
};
