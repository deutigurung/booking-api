<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'superadmin@gmail.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'role_id' => 1, // Administrator
        ]);

        $owner = User::create([
            'name' => 'Property Owner',
            'email' => 'owner@gmail.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'role_id' => 2, // Property owner
        ]);
    }
}
