<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('Admin@123456'),
                'role' => 'admin',
                'is_active' => true,
                'phone' => '+880 1700000001',
                'address' => 'Chowdhury Para, Dhaka',
                'permissions' => json_encode(['edit_content', 'manage_users', 'view_reports', 'system_settings']),
            ]
        );

        // Create moderator user
        User::firstOrCreate(
            ['email' => 'moderator@example.com'],
            [
                'name' => 'Moderator User',
                'password' => Hash::make('Mod@123456'),
                'role' => 'moderator',
                'is_active' => true,
                'phone' => '+880 1700000002',
                'address' => 'Chowdhury Para, Dhaka',
                'permissions' => json_encode(['edit_content', 'view_reports']),
            ]
        );

        // Create regular user
        User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('User@123456'),
                'role' => 'user',
                'is_active' => true,
                'phone' => '+880 1700000003',
                'address' => 'Chowdhury Para, Dhaka',
                'permissions' => json_encode([]),
            ]
        );

        $this->command->info('Admin seeder completed successfully!');
    }
}
