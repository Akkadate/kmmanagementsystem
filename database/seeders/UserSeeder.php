<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        User::updateOrCreate(
            ['email' => 'admin@northbkk.ac.th'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Editor User
        User::updateOrCreate(
            ['email' => 'editor@northbkk.ac.th'],
            [
                'name' => 'Editor User',
                'password' => Hash::make('password'),
                'role' => 'editor',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Contributor User
        User::updateOrCreate(
            ['email' => 'contributor@northbkk.ac.th'],
            [
                'name' => 'Contributor User',
                'password' => Hash::make('password'),
                'role' => 'contributor',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Viewer User
        User::updateOrCreate(
            ['email' => 'viewer@northbkk.ac.th'],
            [
                'name' => 'Viewer User',
                'password' => Hash::make('password'),
                'role' => 'viewer',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Sample users (without Faker for production)
        $sampleUsers = [
            ['name' => 'John Doe', 'email' => 'john@northbkk.ac.th', 'role' => 'contributor'],
            ['name' => 'Jane Smith', 'email' => 'jane@northbkk.ac.th', 'role' => 'contributor'],
            ['name' => 'Bob Wilson', 'email' => 'bob@northbkk.ac.th', 'role' => 'viewer'],
            ['name' => 'Alice Brown', 'email' => 'alice@northbkk.ac.th', 'role' => 'viewer'],
            ['name' => 'Charlie Davis', 'email' => 'charlie@northbkk.ac.th', 'role' => 'contributor'],
        ];

        foreach ($sampleUsers as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'role' => $userData['role'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
