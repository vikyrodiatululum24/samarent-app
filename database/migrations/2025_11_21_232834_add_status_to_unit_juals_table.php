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
        Schema::table('unit_juals', function (Blueprint $table) {
            $table->string('status')->default('available')->after('harga_netto');
            $table->decimal('harga_terjual', 15, 2)->unsigned()->nullable()->after('status');
            $table->json('bukti_pembayaran')->nullable()->after('harga_terjual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit_juals', function (Blueprint $table) {
            $table->dropColumn(['status', 'harga_terjual', 'bukti_pembayaran']);
        });
    }
};
