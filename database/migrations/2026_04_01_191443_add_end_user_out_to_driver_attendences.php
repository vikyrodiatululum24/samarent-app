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
        Schema::table('driver_attendences', function (Blueprint $table) {
            $table->foreignId('end_user_out')->nullable()->constrained('end_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_attendences', function (Blueprint $table) {
            $table->dropColumn('end_user_out');
        });
    }
};
