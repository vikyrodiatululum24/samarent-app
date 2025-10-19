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
        Schema::table('cetaks', function (Blueprint $table) {
            $table->unsignedBigInteger('driver_id')->nullable()->after('asuransi_id');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('set null');
            $table->string('periode')->nullable()->after('driver_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cetaks', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropColumn(['driver_id', 'periode']);
        });
    }
};
