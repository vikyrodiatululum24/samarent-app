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
            $table->date('tanggal_input_bank')->nullable()->after('norek_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('completes', function (Blueprint $table) {
            $table->dropColumn('tanggal_input_bank');
        });
    }
};
