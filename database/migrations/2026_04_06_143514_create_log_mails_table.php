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
        Schema::create('log_mails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendence_id');
            $table->unsignedBigInteger('end_user_id');
            $table->string('status');
            $table->text('error_message')->nullable();
            $table->foreign('attendence_id')->references('id')->on('driver_attendences')->onDelete('cascade');
            $table->foreign('end_user_id')->references('id')->on('end_users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_mails');
    }
};
