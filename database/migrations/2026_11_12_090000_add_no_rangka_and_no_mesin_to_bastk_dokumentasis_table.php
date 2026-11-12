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
        Schema::table('bastk_dokumentasis', function (Blueprint $table) {
            $table->string('no_rangka')->nullable()->after('bastk');
            $table->string('no_mesin')->nullable()->after('no_rangka');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bastk_dokumentasis', function (Blueprint $table) {
            $table->dropColumn(['no_rangka', 'no_mesin']);
        });
    }
};

