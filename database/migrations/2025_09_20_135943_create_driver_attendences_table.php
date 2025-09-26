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
        Schema::create('driver_attendences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('end_user_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('data_units')->onDelete('cascade');
            $table->date('date')->required();
            $table->time('time_in')->required();
            $table->string('start_km')->required();
            $table->text('note')->nullable();
            $table->string('location_in')->nullable();
            $table->string('photo_in')->nullable();
            $table->string('location_check')->nullable();
            $table->string('photo_check')->nullable();
            $table->string('end_km')->nullable();
            $table->time('time_out')->nullable();
            $table->string('location_out')->nullable();
            $table->string('photo_out')->nullable();
            $table->boolean('is_complete')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_attendences');
    }
};
