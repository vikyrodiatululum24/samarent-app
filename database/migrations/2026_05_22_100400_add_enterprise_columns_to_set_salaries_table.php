<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('set_salaries', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('project_id')->constrained()->nullOnDelete();
            $table->foreignId('division_id')->nullable()->after('branch_id')->constrained()->nullOnDelete();
            $table->string('name')->nullable()->after('division_id');
            $table->string('policy_type')->default('flat')->after('name');
            $table->json('rules')->nullable()->after('policy_type');
            $table->boolean('is_active')->default(true)->after('rules');
            $table->date('effective_date')->nullable()->after('is_active');
            $table->date('expired_date')->nullable()->after('effective_date');

            $table->dropColumn([
                'workdays',
                'workhours',
                'amount',
                'overtime1',
                'overtime2',
                'overtime3',
                'overtime4',
                'transport',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('set_salaries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('project_id');
            $table->dropConstrainedForeignId('branch_id');
            $table->dropConstrainedForeignId('division_id');
            $table->dropColumn([
                'name',
                'policy_type',
                'rules',
                'is_active',
                'effective_date',
                'expired_date',
            ]);
            $table->json('workdays')->nullable()->after('id');
            $table->integer('workhours')->nullable()->after('workdays');
            $table->decimal('amount', 15, 2)->nullable()->after('workhours');
            $table->decimal('overtime1', 15, 2)->nullable()->after('amount');
            $table->decimal('overtime2', 15, 2)->nullable()->after('overtime1');
            $table->decimal('overtime3', 15, 2)->nullable()->after('overtime2');
            $table->decimal('overtime4', 15, 2)->nullable()->after('overtime3');
            $table->decimal('transport', 15, 2)->nullable()->after('overtime4');
        });
    }
};
