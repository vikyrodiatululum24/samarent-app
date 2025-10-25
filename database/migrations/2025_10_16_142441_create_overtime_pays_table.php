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
        Schema::create('overtime_pays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_attendence_id')->constrained()->onDelete('cascade');
            $table->date('tanggal');
            $table->string('hari', 15);
            $table->string('shift', 10)->nullable();
            $table->time('from_time')->nullable();
            $table->time('to_time')->nullable();
            $table->time('ot_hours_time')->nullable();
            // $table->decimal('ot_hours_numeric', 5, 2)->default(0);
            $table->decimal('ot_1x')->nullable();
            $table->decimal('ot_2x')->nullable();
            $table->decimal('ot_3x')->nullable();
            $table->decimal('ot_4x')->nullable();
            $table->decimal('calculated_ot_hours', 5, 2)->default(0);
            $table->decimal('amount_per_hour', 10, 2)->default(0);
            $table->decimal('ot_amount', 10, 2)->default(0);
            $table->decimal('out_of_town', 10, 2)->default(0);
            $table->decimal('overnight', 10, 2)->default(0);
            $table->decimal('transport', 10, 2)->default(0);
            $table->decimal('monthly_allowance', 10, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overtime_pays');
    }
};
