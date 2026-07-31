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
        Schema::table('bastk', function (Blueprint $table) {
            if (!Schema::hasColumn('bastk', 'jenis_bastk')) {
                $table->string('jenis_bastk')->nullable()->after('type_bastk');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bastk', function (Blueprint $table) {
            $table->dropColumn('jenis_bastk');
        });
    }
};
