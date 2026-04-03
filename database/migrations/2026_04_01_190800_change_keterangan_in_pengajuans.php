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
        Schema::table('pengajuans', function (Blueprint $table) {
            $table->enum('keterangan', ['REIMBURSE', 'CASH ADVANCE', 'INVOICE', 'FREE', 'PraPengajuan'])->default('REIMBURSE')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
