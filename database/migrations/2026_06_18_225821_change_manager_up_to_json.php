<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Manager;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('managers', function (Blueprint $table) {
            $table->json('new_up')->nullable()->after('up');
        });

        $manager = Manager::all();
        foreach ($manager as $key => $value) {
            $value->new_up = json_decode($value->up);
            $value->save();
        }


        Schema::table('managers', function (Blueprint $table) {
            $table->dropColumn('up');
            $table->renameColumn('new_up', 'up')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('managers', function (Blueprint $table) {
            $table->json('new_up')->nullable()->after('up');
        });


        $manager = Manager::all();
        foreach ($manager as $key => $value) {
            $value->new_up = json_decode($value->up);
            $value->save();
        }


        Schema::table('managers', function (Blueprint $table) {
            $table->dropColumn('up');
            $table->renameColumn('new_up', 'up')->change();
        }); 
    }
};
