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
        Schema::table('confirmations', function (Blueprint $table) {
            $table->unsignedBigInteger('end_user_id')->nullable()->after('confirmable_id');
            $table->index('end_user_id');
            $table->foreign('end_user_id')->references('id')->on('end_users')->onDelete('set null');
            $table->string('approval_type')->nullable()->after('end_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('confirmations', function (Blueprint $table) {
            $table->dropForeign(['end_user_id']);
            $table->dropColumn('end_user_id');
            $table->dropColumn('approval_type');
        });
    }
};
