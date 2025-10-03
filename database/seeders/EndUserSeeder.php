<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EndUser;
use Illuminate\Support\Str;

class EndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            EndUser::create([
                'project_id' => $i,
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@example.com',
                'no_wa' => '0812345678' . $i,
            ]);
        }
    }
}
