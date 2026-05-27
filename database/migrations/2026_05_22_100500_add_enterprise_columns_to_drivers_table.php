<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->foreignId('branch_id')->nullable()->after('project_id')->constrained()->nullOnDelete();
            $table->foreignId('division_id')->nullable()->after('branch_id')->constrained()->nullOnDelete();
            $table->decimal('salary', 14, 2)->default(0)->after('pic');
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropConstrainedForeignId('branch_id');
            $table->dropConstrainedForeignId('division_id');
            $table->dropColumn('salary');
        });
    }
};
