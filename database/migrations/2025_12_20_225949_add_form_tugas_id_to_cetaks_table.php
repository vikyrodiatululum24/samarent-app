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
            $table->foreignId('form_tugas_id')->nullable()->constrained('form_tugas')->onDelete('set null')->after('periode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cetaks', function (Blueprint $table) {
            $table->dropForeign(['form_tugas_id']);
            $table->dropColumn('form_tugas_id');
        });
    }
};
