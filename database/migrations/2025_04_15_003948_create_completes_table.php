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
        Schema::create('completes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('pengajuan_id')->constrained()->onDelete('cascade');
            $table->string('bengkel_estimasi');
            $table->string('no_telp_bengkel');
            $table->integer('nominal_estimasi');
            $table->string('kode');
            $table->date('tanggal_masuk_finance');
            $table->date('tanggal_tf_finance')->nullable();
            $table->integer('nominal_tf_finance')->nullable();
            $table->string('payment_2')->nullable();
            $table->string('bank_2')->nullable();
            $table->string('norek_2')->nullable();
            $table->integer('nominal_tf_bengkel')->nullable();
            $table->integer('selisih_tf')->nullable();
            $table->date('tanggal_tf_bengkel')->nullable();
            $table->date('tanggal_pengerjaan')->nullable();
            $table->enum('status_finance', ['paid', 'unpaid'])->default('unpaid');
            $table->json('foto_nota')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('completes');
    }
};
