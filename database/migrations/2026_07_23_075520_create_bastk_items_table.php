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
        Schema::create('bastk_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bastk_id')->constrained('bastk')->cascadeOnDelete();
            $table->string('kelengkapan')->nullable();
            $table->boolean('baik')->default(false);
            $table->boolean('rusak')->default(false);
            $table->boolean('tidak_ada')->default(false);
            $table->string('keterangan')->nullable();
            $table->string('jenis_bbm')->nullable();
            $table->integer('bbm')->nullable();
            $table->integer('km')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bastk_items');
    }
};
