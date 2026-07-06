<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Central DB seeder.
 *
 * For Phase 1, only the UserSeeder is called (creates demo users on the
 * central `users` table — these will be MOVED to the tenant DB in Phase 2).
 *
 * The Road/Building/Admin seeders were removed in this commit because they
 * referenced columns that don't exist in the actual migrations. They will
 * be rewritten in Phase 2 when we move users/roads/buildings to the tenant DB.
 *
 * To seed the super admin, run separately:
 *   php artisan db:seed --class="Database\Seeders\Central\SuperAdminSeeder"
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);
    }
}
