<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'User One', 'email' => 'user1@example.com', 'password' => Hash::make('password1'), 'role' => 'admin'],
            ['name' => 'User Two', 'email' => 'user2@example.com', 'password' => Hash::make('password2'), 'role' => 'admin'],
            ['name' => 'User Three', 'email' => 'user3@example.com', 'password' => Hash::make('password3'), 'role' => 'user'],
            ['name' => 'User Four', 'email' => 'user4@example.com', 'password' => Hash::make('password4'), 'role' => 'user'],
            ['name' => 'User Five', 'email' => 'user5@example.com', 'password' => Hash::make('password5'), 'role' => 'user'],
        ];

        DB::table('users')->insert($users);
    }
}
