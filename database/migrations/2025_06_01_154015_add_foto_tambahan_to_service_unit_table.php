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
            $table->json('foto_tambahan')->nullable()->after('foto_pengerjaan_bengkel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_units', function (Blueprint $table) {
            $table->dropColumn('foto_tambahan');
        });
    }
};
