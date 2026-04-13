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
        // Create ONLY ONE Super Admin account
        $superAdmin = User::create([
            'email' => 'superadmin@lehsff.org',
            'username' => 'super_admin',
            'phone' => '+266 5000 0001',
            'bio' => 'System Super Administrator',
            'password' => Hash::make('SuperAdmin@2024'),
            'is_active' => true,
            'timezone' => 'Africa/Maseru',
            'language' => 'en',
            'email_verified_at' => now(),
        ]);

        // Assign super-admin role to the super admin user
        $superAdmin->assignRole('Super Administrator');

        $this->command->info('===================================');
        $this->command->info('Super Admin Account Created Successfully!');
        $this->command->info('-----------------------------------');
        $this->command->info('Email: superadmin@lehsff.org');
        $this->command->info('Password: SuperAdmin@2024');
        $this->command->info('Role: Super Administrator');
        $this->command->info('===================================');
    }
}