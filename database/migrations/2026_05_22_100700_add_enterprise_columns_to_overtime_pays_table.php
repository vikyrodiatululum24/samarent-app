<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('overtime_pays', function (Blueprint $table) {
            $table->decimal('worked_hours', 8, 2)->default(0)->after('to_time');
            $table->decimal('normal_hours', 8, 2)->default(0)->after('worked_hours');
            $table->json('calculation_detail')->nullable()->after('remarks');

            $table->dropColumn([
                'ot_1x',
                'ot_2x',
                'ot_3x',
                'ot_4x',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('overtime_pays', function (Blueprint $table) {
            $table->dropConstrainedForeignId('attendance_id');
            $table->dropColumn([
                'worked_hours',
                'normal_hours',
                'overtime_hours',
                'rate',
                'overtime_pay',
                'calculation_detail',
            ]);
        });
    }
};
