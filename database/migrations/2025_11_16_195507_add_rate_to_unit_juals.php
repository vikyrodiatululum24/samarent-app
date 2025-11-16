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
            $table->integer('rateBody')->after('harga_jual')->default(0);
            $table->integer('rateInterior')->after('rateBody')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit_juals', function (Blueprint $table) {
            $table->dropColumn('rateBody');
            $table->dropColumn('rateInterior');
        });
    }
};
