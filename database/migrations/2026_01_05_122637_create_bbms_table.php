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
        Schema::create('bbms', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('unit_id')->nullable()->constrained('data_units')->onDelete('set null');
            $table->string('barcode_bbm')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bbms');
    }
};
