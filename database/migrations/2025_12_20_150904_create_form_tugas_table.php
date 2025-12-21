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
        Schema::create('form_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('no_form')->unique();
            $table->string('nama_atasan');
            $table->json('penerima_tugas');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('deskripsi')->nullable();
            $table->foreignId('unit_id')->nullable()->constrained('data_units')->onDelete('set null');
            $table->string('lainnya')->nullable();
            $table->string('sopir')->nullable();
            $table->decimal('bbm', 10, 2)->default(0);
            $table->decimal('toll', 10, 2)->default(0);
            $table->decimal('penginapan', 10, 2)->default(0);
            $table->decimal('uang_dinas', 10, 2)->default(0);
            $table->decimal('entertaint_customer', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->string('pemohon');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_tugas');
    }
};
