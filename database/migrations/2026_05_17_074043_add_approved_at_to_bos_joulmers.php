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
        Schema::table('bos_joulmers', function (Blueprint $table) {
            $table->timestamp('approved_at')->nullable()->after('note'); // Menambahkan kolom approved_at setelah kolom note
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bos_joulmers', function (Blueprint $table) {
            $table->dropColumn('approved_at');
        });
    }
};
