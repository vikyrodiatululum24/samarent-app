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
        Schema::table('completes', function (Blueprint $table) {
            $table->string('rek_bengkel')->nullable();
            $table->string('nama_rek_bengkel')->nullable();
            $table->string('bank_bengkel')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('completes', function (Blueprint $table) {
            $table->dropColumn('rek_bengkel');
            $table->dropColumn('nama_rek_bengkel');
            $table->dropColumn('bank_bengkel');
        });
    }
};
