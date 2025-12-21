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
        Schema::create('tujuan_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_tugas_id')->constrained('form_tugas')->onDelete('cascade');
            $table->date('tanggal');
            $table->string('tempat');
            $table->string('location')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tujuan_tugas');
    }
};
