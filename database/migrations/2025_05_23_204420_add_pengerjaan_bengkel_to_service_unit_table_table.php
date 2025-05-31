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
        Schema::table('service_units', function (Blueprint $table) {
            $table->string('foto_pengerjaan_bengkel')->nullable()->after('foto_kondisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_units', function (Blueprint $table) {
            $table->dropColumn('foto_pengerjaan_bengkel');
        });
    }
};
