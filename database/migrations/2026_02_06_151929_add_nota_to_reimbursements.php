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
        Schema::table('reimbursements', function (Blueprint $table) {
            $table->string('nota')->nullable()->after('keterangan');
            $table->string('type')->nullable()->after('nota');
            $table->unsignedBigInteger('km_awal')->nullable()->change();
            $table->string('foto_odometer_awal')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reimbursements', function (Blueprint $table) {
            $table->dropColumn('nota');
            $table->dropColumn('type');
            $table->unsignedBigInteger('km_awal')->nullable(false)->change();
            $table->string('foto_odometer_awal')->nullable(false)->change();
        });
    }
};
