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
        Schema::table('unit_juals', function (Blueprint $table) {
            $table->string('odometer')->nullable()->after('foto_odometer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit_juals', function (Blueprint $table) {
            $table->dropColumn('odometer');
        });
    }
};
