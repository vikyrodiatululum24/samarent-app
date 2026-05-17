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
        Schema::table('set_salaries', function (Blueprint $table) {
            $table->json('workdays')->nullable()->after('project_id'); // Menambahkan kolom hari kerja setelah project_id
            $table->integer('workhours')->nullable()->after('workdays'); // Menambahkan kolom jam kerja setelah workdays
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('set_salaries', function (Blueprint $table) {
            $table->dropColumn('workdays');
            $table->dropColumn('workhours');
        });
    }
};
