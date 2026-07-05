<?php

namespace Database\Seeders\Central;

use App\Models\Central\SuperAdmin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds the default super admin account on the CENTRAL database.
 *
 * Run with: php artisan db:seed --class=Database\\Seeders\\Central\\SuperAdminSeeder
 *
 * Credentials (change after first login!):
 *   Email:    superadmin@chowdhurybari.test
 *   Password: SuperAdmin@123456
 */
class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        SuperAdmin::firstOrCreate(
            ['email' => 'superadmin@chowdhurybari.test'],
            [
                'name' => 'Platform Owner',
                'password' => Hash::make('SuperAdmin@123456'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Super admin seeded. Login: superadmin@chowdhurybari.test / SuperAdmin@123456');
    }
}
