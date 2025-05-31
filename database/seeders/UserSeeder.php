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
            ['name' => 'Admin One', 'email' => 'admin1@example.com', 'password' => Hash::make('password'), 'role' => 'admin'],
            ['name' => 'Admin Two', 'email' => 'admin2@example.com', 'password' => Hash::make('password'), 'role' => 'admin'],
            ['name' => 'User One', 'email' => 'user1@example.com', 'password' => Hash::make('password'), 'role' => 'user'],
            ['name' => 'User Two', 'email' => 'user2@example.com', 'password' => Hash::make('password'), 'role' => 'user'],
            ['name' => 'User Three', 'email' => 'user3@example.com', 'password' => Hash::make('password'), 'role' => 'user'],
            ['name' => 'Finance', 'email' => 'finance@example.com', 'password' => Hash::make('password'), 'role' => 'finance'],
            ['name' => 'Manager', 'email' => 'manager@example.com', 'password' => Hash::make('password'), 'role' => 'manager'],
        ];

        DB::table('users')->insert($users);
    }
}
