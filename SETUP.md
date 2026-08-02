# ChowdhuryBari SaaS — Setup Guide

> **Current state:** Public website + old Blade admin panel. Filament super admin panel is **disabled** for now (will re-enable after we stabilize the basics).

---

## TL;DR — One-shot install (5 commands)

After cloning the repo to your machine:

```bash
# 1. cd to the project folder (where artisan lives)
cd path/to/ChowdhuryBari

# 2. Install PHP dependencies (use update if install complains about lock file)
composer install
# — OR —
composer update

# 3. Run the one-shot setup command
php artisan setup:chowdhurybari

# 4. Start the dev server (leave this window open)
php artisan serve --host=127.0.0.1 --port=8000

# 5. Open in your browser
#    Public website: http://127.0.0.1:8000/
#    Admin panel:    http://127.0.0.1:8000/admin/login
```

**Admin login credentials** (from UserSeeder):
- Email: `sajid@gmail.com`
- Password: `password123`

---

## Prerequisites (one-time setup)

| Tool | Version | How to check |
|------|---------|--------------|
| **PHP** | 8.2+ | `php -v` |
| **Composer** | 2.6+ | `composer -V` |
| **MySQL** | 5.7+ or MariaDB 10.4+ | `mysql --version` |
| **Node.js** (optional) | 18+ | `node -v` |

### Required PHP extensions

Run `php -m` and make sure these are listed:
```
intl  gd  mbstring  pdo  pdo_mysql  xml  curl  zip  fileinfo
```

If any are missing (common on XAMPP), edit `C:\xampp\php\php.ini` and uncomment the `extension=...` line (remove the leading `;`).

---

## Step-by-step (detailed)

### Step 1 — Get the code

```bash
git clone https://github.com/sajidchowdhury/ChowdhuryBari.git
cd ChowdhuryBari
```

### Step 2 — Install PHP dependencies

```bash
composer install
```

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
4. Runs all migrations
5. Seeds the users (admin + moderator + regular user)
6. Creates the `public/storage` symlink

### Step 4 — Start the dev server

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

### Step 5 — Open in your browser

| URL | What it is |
|-----|-----------|
| `http://127.0.0.1:8000/` | Public website (Bengali landing page) |
| `http://127.0.0.1:8000/admin/login` | Old Blade admin panel |

Log in to the admin panel with:
- Email: `sajid@gmail.com`
- Password: `password123`

---

## Optional — Build frontend assets

The public website uses Tailwind CSS + Alpine.js. In dev, it loads them from CDN automatically. For production:

```bash
npm install
npm run build
```

---

## What's working now

✅ Public website at `/` (Bengali ChowdhuryBari landing page)
✅ Admin panel at `/admin/login` (old Blade version)
✅ User management (CRUD)
✅ Our Area management (roads + buildings)
✅ Central DB with all platform tables (for future use)

## What's DISABLED for now

⏸️ Filament super admin panel (was causing redirect loops — will re-enable later)
⏸️ Multi-tenancy (Tenant model exists but not wired into routing yet)
⏸️ Payments (bKash/Nagad/SSL — interface exists, not implemented)

---

## Troubleshooting

### "Class Filament\PanelProvider not found"

You didn't run `composer install` / `composer update`. Run:
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

Create it manually in phpMyAdmin:
```sql
CREATE DATABASE chowdhurybari_central CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
Then re-run `php artisan setup:chowdhurybari`.

### "127.0.0.1 refused to connect"

`artisan serve` isn't running. Start it:
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

### Admin login says "These credentials do not match"

Re-seed the users:
```bash
php artisan migrate:fresh --seed
```

### Public website shows "Vite manifest not found"

Run `npm install && npm run build`. Or just ignore it — the site auto-falls back to CDN Tailwind/Alpine.

### `php artisan` commands say "Could not open input file: artisan"

You're in the wrong folder. `artisan` lives in the project root, not in `public/`.

---

## Need help?

Paste back:
1. The exact command you ran
2. The full error output
3. Output of `php -v` and `composer -V`

**And revoke your GitHub PAT** at https://github.com/settings/tokens if you shared one in chat.
