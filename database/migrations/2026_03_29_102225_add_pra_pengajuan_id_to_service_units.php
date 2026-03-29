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
        Schema::table('service_units', function (Blueprint $table) {
            $table->foreignId('pra_pengajuan_id')->nullable()->constrained('pengajuans')->onDelete('set null')->after('pengajuan_id');
            // ubah pengajuan_id menjadi nullable dan tambahkan foreign key constraint
            $table->foreignId('pengajuan_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_units', function (Blueprint $table) {

            // 🔥 Drop kolom kalau ada
            if (Schema::hasColumn('service_units', 'pra_pengajuan_id')) {
                $table->dropForeign(['pra_pengajuan_id']);
                $table->dropColumn('pra_pengajuan_id');
            }

            // 🔥 Drop FK pengajuan dulu
            try {
                $table->dropForeign(['pengajuan_id']);
            } catch (\Exception $e) {
                // skip
            }
        });

        Schema::table('service_units', function (Blueprint $table) {
            // ubah jadi NOT NULL
            $table->unsignedBigInteger('pengajuan_id')->nullable(false)->change();
        });

        Schema::table('service_units', function (Blueprint $table) {
            // balikin FK
            $table->foreign('pengajuan_id')
                ->references('id')
                ->on('pengajuans')
                ->cascadeOnDelete();
        });
    }
};
