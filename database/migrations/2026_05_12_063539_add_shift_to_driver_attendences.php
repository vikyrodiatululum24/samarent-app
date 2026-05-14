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
            $table->string('shift')->nullable()->after('is_complete');
            $table->text('note_admin')->nullable()->after('shift');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_attendences', function (Blueprint $table) {
            $table->dropColumn(['shift', 'note_admin']);
        });
    }
};
