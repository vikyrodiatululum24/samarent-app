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
            $table->unsignedBigInteger('asuransi_id')->nullable()->after('pengajuan_id');
            $table->unsignedBigInteger('pengajuan_id')->nullable()->change();
            $table->foreign('asuransi_id')->references('id')->on('asuransis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cetaks', function (Blueprint $table) {
            $table->dropColumn('asuransi_id');
        });
    }
};
