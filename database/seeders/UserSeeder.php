<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@kmsystem.local',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Editor User',
            'email' => 'editor@kmsystem.local',
            'password' => Hash::make('password'),
            'role' => 'editor',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Contributor User',
            'email' => 'contributor@kmsystem.local',
            'password' => Hash::make('password'),
            'role' => 'contributor',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Viewer User',
            'email' => 'viewer@kmsystem.local',
            'password' => Hash::make('password'),
            'role' => 'viewer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::factory()->count(10)->create();
    }
}
