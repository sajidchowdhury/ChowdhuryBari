<?php

namespace Database\Seeders\Central;

use App\Models\Central\SuperAdmin;
use Illuminate\Database\Seeder;

/**
 * Seeds the default super admin account on the CENTRAL database.
 *
 * Run with: php artisan db:seed --class=Database\\Seeders\\Central\\SuperAdminSeeder
 *
 * Credentials (change after first login!):
 *   Email:    superadmin@chowdhurybari.test
 *   Password: SuperAdmin@123456
 *
 * NOTE: We pass the PLAIN password here — the SuperAdmin model has a
 * 'hashed' cast that auto-hashes it on save. Using Hash::make() here
 * would double-hash it and login would fail.
 */
class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        SuperAdmin::firstOrCreate(
            ['email' => 'superadmin@chowdhurybari.test'],
            [
                'name' => 'Platform Owner',
                'password' => 'SuperAdmin@123456',  // plain — model cast will hash it
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Super admin seeded. Login: superadmin@chowdhurybari.test / SuperAdmin@123456');
    }
}
