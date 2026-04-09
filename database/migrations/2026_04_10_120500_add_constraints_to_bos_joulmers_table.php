<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::table('bos_joulmers', function (Blueprint $table) {
            $table->unique('pengajuan_id');
            $table->index(['is_approved', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bos_joulmers', function (Blueprint $table) {
            $table->dropUnique(['pengajuan_id']);
            $table->dropIndex(['is_approved', 'created_at']);
        });
    }
};
