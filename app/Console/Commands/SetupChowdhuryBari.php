<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Command: php artisan setup:chowdhurybari
 *
 * Runs every step needed to set up the project from a fresh clone:
 *   1. Copies .env.example → .env (if .env doesn't exist)
 *   2. Generates APP_KEY
 *   3. Creates the central MySQL database (if it doesn't exist)
 *   4. Runs migrations (central DB)
 *   5. Seeds the super admin
 *   6. Publishes Filament assets
 *   7. Symlinks storage/public → public/storage
 *   8. (Optional) runs npm install + npm run build — skipped, run manually
 *
 * This command is IDEMPOTENT — safe to run multiple times.
 *
 * Usage:
 *   php artisan setup:chowdhurybari
 */
class SetupChowdhuryBari extends Command
{
    protected $signature = 'setup:chowdhurybari
                            {--skip-db : Skip database creation + migrations}
                            {--force : Force overwrite of existing data}';

    protected $description = 'One-shot setup: env, key, DB, migrations, seeders, Filament assets. Run after `composer install` on a fresh clone.';

    public function handle(): int
    {
        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║   ChowdhuryBari SaaS — One-shot Setup                         ║');
        $this->info('╚══════════════════════════════════════════════════════════════╝');
        $this->newLine();

        // 1. .env file
        $this->step('Step 1/6 — Ensuring .env file exists');
        if (!File::exists(base_path('.env'))) {
            File::copy(base_path('.env.example'), base_path('.env'));
            $this->line('   ✓ Created .env from .env.example');
        } else {
            $this->line('   ✓ .env already exists');
        }

        // 2. APP_KEY
        $this->step('Step 2/6 — Generating APP_KEY');
        if (empty(config('app.key')) || $this->option('force')) {
            Artisan::call('key:generate', ['--force' => true]);
            $this->line('   ✓ ' . Artisan::output());
        } else {
            $this->line('   ✓ APP_KEY already set');
        }

        if ($this->option('skip-db')) {
            $this->warn('   ⚠ --skip-db flag detected, skipping DB steps');
            $this->newLine();
            $this->info('✅ Setup complete (DB skipped).');
            $this->info('   Run `php artisan serve` to start the server.');
            return self::SUCCESS;
        }

        // 3. Create central database (if MySQL)
        $this->step('Step 3/6 — Ensuring central database exists');
        $this->ensureDatabaseExists();

        // 4. Migrate (central)
        $this->step('Step 4/6 — Running central DB migrations');
        Artisan::call('migrate', ['--force' => true]);
        $this->line(Artisan::output());

        // 5. Seed users (old admin panel uses User model, not SuperAdmin)
        $this->step('Step 5/6 — Seeding users');
        Artisan::call('db:seed', ['--force' => true]);
        $this->line(Artisan::output());

        // NOTE: Filament super admin panel is DISABLED for now.
        // Will re-enable + publish assets after stabilizing the public site.

        // 6. Storage symlink
        $this->step('Step 6/6 — Linking storage/public → public/storage');
        if (File::exists(public_path('storage'))) {
            $this->line('   ✓ storage symlink already exists');
        } else {
            Artisan::call('storage:link');
            $this->line('   ✓ ' . trim(Artisan::output()));
        }

        $this->newLine();
        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║   ✅  SETUP COMPLETE                                            ║');
        $this->info('╠══════════════════════════════════════════════════════════════╣');
        $this->info('║                                                                ║');
        $this->info('║   Next steps:                                                  ║');
        $this->info('║   1. Start the dev server:                                     ║');
        $this->info('║      php artisan serve --host=127.0.0.1 --port=8000            ║');
        $this->info('║                                                                ║');
        $this->info('║   2. Open the public website:                                  ║');
        $this->info('║      http://127.0.0.1:8000/                                    ║');
        $this->info('║                                                                ║');
        $this->info('║   3. Open the admin panel (old Blade version):                 ║');
        $this->info('║      http://127.0.0.1:8000/admin/login                         ║');
        $this->info('║                                                                ║');
        $this->info('║   4. Admin login (from UserSeeder):                            ║');
        $this->info('║      Email:    sajid@gmail.com                                 ║');
        $this->info('║      Password: password123                                     ║');
        $this->info('║                                                                ║');
        $this->info('║   Optional: build frontend assets (for the public site)        ║');
        $this->info('║      npm install && npm run build                              ║');
        $this->info('║                                                                ║');
        $this->info('╚══════════════════════════════════════════════════════════════╝');

        return self::SUCCESS;
    }

    private function step(string $label): void
    {
        $this->newLine();
        $this->info($label);
    }

    /**
     * Create the central MySQL database if it doesn't exist yet.
     * Connects without specifying a DB name, then runs CREATE DATABASE IF NOT EXISTS.
     */
    private function ensureDatabaseExists(): void
    {
        $driver = config('database.default');

        if ($driver !== 'mysql') {
            $this->line("   ⚠ DB driver is '{$driver}' — skipping DB creation (only auto-creates for MySQL)");
            $this->line("   If using SQLite, the DB file will be auto-created on migrate.");
            return;
        }

        $config = config("database.connections.{$driver}");
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? '3306';
        $username = $config['username'] ?? 'root';
        $password = $config['password'] ?? '';
        $database = $config['database'] ?? 'chowdhurybari_central';

        try {
            $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);

            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->line("   ✓ Database '{$database}' exists on {$host}:{$port}");
        } catch (\PDOException $e) {
            $this->error("   ✗ Could not connect to MySQL at {$host}:{$port} as '{$username}'");
            $this->error("   Error: " . $e->getMessage());
            $this->newLine();
            $this->warn('   Fix this manually:');
            $this->line('   1. Open .env and check DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD');
            $this->line('   2. Make sure MySQL is running (start XAMPP Apache + MySQL)');
            $this->line('   3. Create the database manually in phpMyAdmin:');
            $this->line("      CREATE DATABASE {$database} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            $this->line('   4. Re-run: php artisan setup:chowdhurybari');
            exit(1);
        }
    }
}
