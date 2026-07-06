# ChowdhuryBari SaaS — Setup Guide

> **Read this file completely before running any commands.**
> Phase 1 = the foundation (super admin panel + central DB). Phase 2+ = tenants, members, payments.

---

## TL;DR — One-shot install (5 commands)

After cloning the repo to your machine:

```bash
# 1. cd to the project folder (where artisan lives)
cd path/to/ChowdhuryBari

# 2. Install PHP dependencies
composer install

# 3. Run the one-shot setup command
php artisan setup:chowdhurybari

# 4. Start the dev server (leave this window open)
php artisan serve --host=127.0.0.1 --port=8000

# 5. Open in your browser
#    Super admin login: http://127.0.0.1:8000/super-admin/login
#    Public site:       http://127.0.0.1:8000/
```

**Login credentials** (change after first login):
- Email: `superadmin@chowdhurybari.test`
- Password: `SuperAdmin@123456`

---

## Prerequisites (one-time setup)

You need these installed on your machine:

| Tool | Version | How to check |
|------|---------|--------------|
| **PHP** | 8.2+ | `php -v` |
| **Composer** | 2.6+ | `composer -V` |
| **MySQL** | 5.7+ or MariaDB 10.4+ | `mysql --version` |
| **Node.js** (optional) | 18+ | `node -v` |
| **npm** (optional) | 9+ | `npm -v` |

### Required PHP extensions

Run `php -m` and make sure these are listed:
```
intl  gd  mbstring  pdo  pdo_mysql  xml  curl  zip  fileinfo
```

If any are missing (common on XAMPP), edit `php.ini` and uncomment the `extension=...` line (remove the leading `;`).

---

## Step-by-step (detailed)

### Step 1 — Get the code

If you haven't cloned yet:
```bash
git clone https://github.com/sajidchowdhury/ChowdhuryBari.git
cd ChowdhuryBari
```

If you already have a clone, pull the latest:
```bash
git pull origin main
```

### Step 2 — Install PHP dependencies

```bash
composer install
```

This installs Laravel, Filament, stancl/tenancy, spatie/permission, and all other packages. Takes 1-3 minutes.

If you see "Required package X is not present in the lock file":
```bash
composer update
```

### Step 3 — Run the one-shot setup

```bash
php artisan setup:chowdhurybari
```

This command does everything for you:
1. Copies `.env.example` → `.env`
2. Generates `APP_KEY`
3. Creates the MySQL database `chowdhurybari_central` (if it doesn't exist)
4. Runs all migrations (creates tables: users, tenants, domains, super_admins, products, orders, payments, etc.)
5. Seeds the super admin account
6. Publishes Filament's CSS/JS assets to `public/`
7. Creates the `public/storage` symlink

You should see green checkmarks (✓) for each step.

### Step 4 — Start the dev server

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

You should see:
```
INFO  Server running on [http://127.0.0.1:8000].
Press Ctrl+C to stop the server
```

Leave this window open. Closing it stops the server.

### Step 5 — Open in your browser

Open any browser (Chrome, Edge, Firefox) and visit:

| URL | What it is |
|-----|-----------|
| `http://127.0.0.1:8000/super-admin/login` | Super admin login page |
| `http://127.0.0.1:8000/` | Public website (Bengali landing page) |

Log in to the super admin panel with:
- Email: `superadmin@chowdhurybari.test`
- Password: `SuperAdmin@123456`

You should land on an empty Filament dashboard with emerald green theme.

---

## Optional — Build frontend assets (for production-grade CSS/JS)

The public website (`/`) uses Tailwind CSS + Alpine.js. In dev, it loads them from CDN automatically (so it works even without `npm install`). For production, build them locally:

```bash
npm install
npm run build
```

This creates `public/build/manifest.json` + compiled CSS/JS. The Blade templates auto-detect this and switch from CDN to local assets.

If you want live-reload during development (CSS/JS changes appear instantly):
```bash
npm run dev
```
(Leave this running in a separate terminal window alongside `artisan serve`.)

---

## Troubleshooting

### "Class Filament\PanelProvider not found"

You didn't run `composer install` (or `composer update`) successfully. Run:
```bash
composer update
```

### "SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost'"

Your MySQL root user has a different password than what `.env` says. Edit `.env`:
```env
DB_USERNAME=root
DB_PASSWORD=your_actual_password
```
Then re-run `php artisan setup:chowdhurybari`.

### "SQLSTATE[HY000] [1049] Unknown database 'chowdhurybari_central'"

The setup command should create it automatically. If it failed, create it manually in phpMyAdmin:
```sql
CREATE DATABASE chowdhurybari_central CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
Then re-run `php artisan setup:chowdhurybari`.

### Login page loads but says "login expired" after submit

This is a CSRF/session issue. Open `.env` and verify:
```env
APP_URL=http://127.0.0.1:8000
SESSION_DRIVER=file
SESSION_DOMAIN=null
```
Then:
```bash
php artisan optimize:clear
```
And restart `artisan serve` (Ctrl+C, then re-run the command).

### Super admin login page has broken CSS (no styling)

Filament assets weren't published. Run:
```bash
php artisan filament:assets
```

### Public website (`/`) shows "Vite manifest not found"

Either:
- Run `npm install && npm run build` to build the assets, OR
- Just visit `/super-admin/login` instead — the super admin panel doesn't need Vite.

The public site auto-falls back to CDN Tailwind/Alpine if the manifest is missing, so this error should not appear after the latest commit. If it does, run `php artisan view:clear`.

### `php artisan` commands say "Could not open input file: artisan"

You're in the wrong folder. `artisan` lives in the project root, not in `public/`. Run:
```bash
cd path/to/ChowdhuryBari
dir artisan
```
The `dir artisan` should list the file. If not, you're in the wrong folder.

### "Duplicate column name 'phone'" during migration

You have old migrations from before the cleanup. Delete them:
```bash
del database\migrations\2026_06_14_105243_add_extra_fields_to_users_table.php
del database\migrations\2026_06_14_105327_add_extra_fields_to_users_table.php
del database\migrations\2026_06_14_135905_add_admin_fields_to_users_table.php
```
Then `git pull origin main` to get the clean state, then re-run `php artisan setup:chowdhurybari`.

---

## File structure (what's where)

```
ChowdhuryBari/
├── app/
│   ├── Console/Commands/
│   │   └── SetupChowdhuryBari.php       ← one-shot setup command
│   ├── Filament/SuperAdmin/
│   │   └── SuperAdminPanelProvider.php  ← panel #4 config
│   ├── Http/Controllers/Payment/
│   │   └── PaymentGateway.php           ← interface for bKash/Nagad/SSL
│   ├── Http/Middleware/
│   │   ├── IsAdmin.php                  ← tenant admin guard
│   │   └── IsSuperAdmin.php             ← super admin guard
│   ├── Models/
│   │   ├── Central/                     ← central DB models
│   │   │   ├── SuperAdmin.php
│   │   │   ├── Product.php
│   │   │   ├── Order.php
│   │   │   └── Payment.php
│   │   ├── Tenant.php                   ← stancl tenant model
│   │   ├── User.php                     ← (will move to Tenant/ in Phase 2)
│   │   ├── Road.php                     ← (will move to Tenant/ in Phase 2)
│   │   └── Building.php                 ← (will move to Tenant/ in Phase 2)
│   └── Providers/
│       ├── AppServiceProvider.php
│       └── TenancyServiceProvider.php   ← stancl tenancy bootstrap
├── bootstrap/
│   ├── app.php                          ← middleware aliases
│   └── providers.php                    ← provider registration
├── config/
│   ├── app.php
│   ├── auth.php                         ← super_admin guard + provider
│   ├── tenancy.php                      ← stancl tenancy config
│   └── ...
├── database/
│   ├── migrations/                      ← central DB migrations
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2019_09_15_000010_create_tenants_table.php
│   │   ├── 2019_09_15_000020_create_domains_table.php
│   │   ├── 2024_06_14_add_admin_fields_to_users.php
│   │   ├── 2026_06_15_043924_create_roads_table.php
│   │   ├── 2026_06_15_043925_create_buildings_table.php
│   │   ├── 2026_07_06_000001_create_super_admins_table.php
│   │   ├── 2026_07_06_000002_create_products_table.php
│   │   ├── 2026_07_06_000003_create_orders_table.php
│   │   ├── 2026_07_06_000004_create_payments_table.php
│   │   └── 2026_07_06_000005_create_super_admin_password_reset_tokens_table.php
│   ├── migrations/tenant/               ← tenant DB migrations (Phase 2)
│   └── seeders/
│       ├── Central/SuperAdminSeeder.php ← default super admin
│       ├── UserSeeder.php
│       └── DatabaseSeeder.php
├── public/
│   ├── css/filament/                    ← Filament's CSS (auto-published)
│   ├── js/filament/                     ← Filament's JS (auto-published)
│   ├── build/                           ← Vite output (after npm run build)
│   └── img/                             ← public images
├── resources/
│   ├── views/
│   │   ├── layouts/                     ← app.blade.php, navbar, hero, etc.
│   │   ├── pages/                       ← 14 page sections (about, services, etc.)
│   │   ├── admin/                       ← old Blade admin (will be replaced by Filament in Phase 3)
│   │   └── welcome.blade.php            ← public site composition
│   ├── css/app.css                      ← Tailwind entry
│   └── js/app.js                        ← Alpine entry
├── routes/
│   ├── web.php                          ← public + admin routes
│   ├── auth.php                         ← Breeze auth routes
│   └── console.php
├── .env.example                         ← env template (copy to .env)
├── ARCHITECTURE.md                      ← full multi-tenant SaaS design
├── SETUP.md                             ← this file
└── composer.json
```

---

## What's working after setup (Phase 1)

✅ Central database with all platform tables
✅ Super admin login at `/super-admin/login`
✅ Empty Filament dashboard (no resources yet — Phase 3 adds them)
✅ Public website at `/` (Bengali landing page)
✅ Old Blade admin at `/admin` (will be migrated to Filament in Phase 3)

## What's NOT working yet (Phase 2+)

⏳ Multiple societies (tenants) — Phase 2
⏳ Tenant DB auto-provisioning — Phase 2
⏳ Subdomain routing (`chowdhurypara.localhost`) — Phase 2
⏳ Society admin Filament panel — Phase 3
⏳ Society member Filament panel — Phase 4
⏳ Super admin Filament resources (manage tenants, products, orders) — Phase 5
⏳ bKash / Nagad / SSL Commerz payments — Phase 6
⏳ Per-tenant public website branding — Phase 7
⏳ Production hardening (backups, monitoring, audit log) — Phase 8

See `ARCHITECTURE.md` for the full roadmap.

---

## Need help?

If something breaks, paste back:
1. The exact command you ran
2. The full error output (don't truncate)
3. Output of `php -v` and `composer -V`

And don't forget to **revoke your GitHub PAT** at https://github.com/settings/tokens if you shared one in chat.
