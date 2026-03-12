<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 10 random users using the factory
        User::factory(10)->create();

        // Create specific test users
        User::factory()->create([
            'email' => 'test@example.com',
            'username' => 'testuser',
            'phone' => '+266 1234 5678',
            'bio' => 'This is a test user account',
            'password' => Hash::make('password'),
            'is_active' => true,
            'timezone' => 'Africa/Maseru',
            'language' => 'en',
        ]);

        // Create another test user
        User::factory()->create([
            'email' => 'john@example.com',
            'username' => 'john_doe',
            'phone' => '+266 8765 4321',
            'bio' => 'John Doe test account',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'timezone' => 'Africa/Maseru',
            'language' => 'en',
        ]);

        // Create an inactive user
        User::factory()->create([
            'email' => 'inactive@example.com',
            'username' => 'inactive_user',
            'is_active' => false,
            'bio' => 'This account is inactive',
        ]);

        // Create a user with metadata
        User::factory()->create([
            'email' => 'metadata@example.com',
            'username' => 'metadata_user',
            'metadata' => json_encode([
                'registration_source' => 'web',
                'newsletter_subscribed' => true,
                'preferences' => [
                    'theme' => 'dark',
                    'notifications' => true
                ]
            ]),
        ]);

        $this->command->info('Users created successfully!');
        $this->command->info('-----------------------------------');
        $this->command->info('Test Accounts:');
        $this->command->info('test@example.com / password');
        $this->command->info('john@example.com / password123');
        $this->command->info('inactive@example.com / password');
        $this->command->info('metadata@example.com / password');
        $this->command->info('-----------------------------------');
    }
}