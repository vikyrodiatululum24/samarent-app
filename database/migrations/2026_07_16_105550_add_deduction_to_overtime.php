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
        Schema::table('overtime_pays', function (Blueprint $table) {
            $table->decimal('own_risk', 15, 2)->after('monthly_allowance')->default(0);
            $table->decimal('deduction_value', 15, 2)->after('own_risk')->default(0);
            $table->text('deduction_desc')->nullable()->after('deduction_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('overtime_pays', function (Blueprint $table) {
            $table->dropColumn('deduction_desc');
            $table->dropColumn('deduction_value');
            $table->dropColumn('own_risk');
        });
    }
};
