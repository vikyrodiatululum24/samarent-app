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
            $table->string('is_approved_in')->nullable()->after('photo_out');
            $table->string('is_approved_out')->nullable()->after('is_approved_in');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_attendences', function (Blueprint $table) {
            $table->dropColumn('is_approved_in');
            $table->dropColumn('is_approved_out');
        });
    }
};
