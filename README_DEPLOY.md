# ChowdhuryBari — Deployment Guide

Complete guide for deploying the ChowdhuryBari application on a cPanel/WHM server using Git.

---

## Table of Contents

1. [Prerequisites](#1-prerequisites)
2. [Server Setup (One-Time)](#2-server-setup-one-time)
3. [Initial Deployment](#3-initial-deployment)
4. [One-Command Deployment](#4-one-command-deployment)
5. [Routine Updates](#5-routine-updates)
6. [Rollback](#6-rollback)
7. [Post-Deployment Verification](#7-post-deployment-verification)
8. [Cron Jobs & Queue Workers](#8-cron-jobs--queue-workers)
9. [SSL & Domain Configuration](#9-ssl--domain-configuration)
10. [Troubleshooting](#10-troubleshooting)
11. [Environment Variables Reference](#11-environment-variables-reference)
12. [Architecture Overview](#12-architecture-overview)

---

## 1. Prerequisites

| Requirement | Minimum Version | How to Check |
|-------------|----------------|--------------|
| PHP | 8.2+ | `php -v` |
| Composer | 2.x | `composer -V` |
| Node.js | 18.x+ | `node -v` |
| NPM | 9.x+ | `npm -v` |
| Git | 2.x | `git --version` |
| MySQL | 5.7+ / 8.0+ | `mysql --version` |
| cPanel/WHM | Latest | WHM dashboard |

### PHP Extensions Required

```
php -m | grep -E "bcmath|ctype|curl|dom|fileinfo|filter|gd|hash|iconv|intl|json|mbstring|mysql|openssl|pcre|pdo|session|tokenizer|xml|zip"
```

All of these must be present. Enable missing extensions via WHM → EasyApache 4 or cPanel → Select PHP Version.

---

## 2. Server Setup (One-Time)

### 2.1 SSH Access

Ensure SSH access is enabled for your cPanel account:

1. Log into cPanel → **Security** → **SSH Access**
2. Enable **Password Authentication** or set up **SSH Keys**
3. Connect via terminal: `ssh youruser@yourdomain.com`

### 2.2 Create the Database

1. cPanel → **MySQL Databases**
2. Create database: `chowdhurybari_central`
3. Create user: `chowdhury_user` with a strong password
4. **Add User to Database** with **ALL PRIVILEGES**

### 2.3 Set PHP Version

1. cPanel → **Software** → **MultiPHP Manager**
2. Select your domain → Set PHP to **8.2** or higher
3. Verify: `php -v` in SSH

### 2.4 Configure Node.js (if needed)

On some cPanel servers, Node.js must be registered:

1. cPanel → **Software** → **Setup Node.js App**
2. Create a new application (even if temporary, just to get node/npm in PATH)
3. Or add to your `~/.bashrc`:
   ```bash
   export PATH="/opt/cpanel/ea-nodejs16/root/bin:$PATH"
   ```

### 2.5 Set Up Git Repository on Server

```bash
# SSH into your server
ssh youruser@yourdomain.com

# Navigate to your web root (cPanel default)
cd ~/public_html
# Or if using a subdomain/addon domain:
# cd ~/chowdhurybari.com

# If public_html already has files, back them up
cp -r ~/public_html ~/public_html.backup.$(date +%Y%m%d)

# Remove default cPanel files (optional)
rm -rf ~/public_html/{.cpanel,cache,tmp,error_log,default.html}

# Clone the repository
git clone https://github.com/sajidchowdhury/ChowdhuryBari.git .

# Make deploy scripts executable
chmod +x deploy/deploy.sh deploy/update.sh deploy/rollback.sh
```

### 2.6 Configure the Document Root

**Critical for security**: The web server's document root must point to the `public/` directory, NOT the project root. Laravel is designed so that only `public/` is web-accessible.

#### Option A: cPanel Document Root (Recommended)

1. WHM → **Account Functions** → **Modify Account**
2. Change document root from `/home/user/public_html` to `/home/user/public_html/public`
3. Or use cPanel → **Domains** → **Manage Document Root**

#### Option B: Symlink Approach (Alternative)

If you cannot change the document root:

```bash
# Move the project to a non-web-accessible location
mv ~/public_html ~/chowdhurybari_app

# Create public_html as a symlink to the public directory
ln -s ~/chowdhurybari_app/public ~/public_html
```

#### Option C: .htaccess Redirect (Last Resort)

If neither option works, add this to `~/public_html/.htaccess`:

```apache
RewriteEngine On
RewriteRule ^(.*)$ public/$1 [L]
```

⚠️ This is less secure — prefer Option A or B.

---

## 3. Initial Deployment

### 3.1 Configure Environment

```bash
cd ~/public_html  # or your project directory

# Copy the environment template
cp .env.example .env

# Generate application key
php artisan key:generate

# Edit .env with your production values
nano .env
```

**Critical .env settings for production:**

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_DATABASE=chowdhurybari_central
DB_USERNAME=your_db_user
DB_PASSWORD=your_strong_password

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=465
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_mail_password
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
```

### 3.2 Run the Deployment Script

```bash
./deploy/deploy.sh
```

This single command will:
1. ✅ Enable maintenance mode
2. ✅ Pull latest code from Git
3. ✅ Install Composer dependencies (no-dev)
4. ✅ Install Node dependencies & build Vite assets
5. ✅ Run database migrations
6. ✅ Optimize all Laravel caches
7. ✅ Create the storage symlink
8. ✅ Set correct file permissions
9. ✅ Verify queue/scheduler setup
10. ✅ Bring the application back online

### 3.3 Seed the Database (First Time Only)

```bash
php artisan db:seed
# Or use the project's custom setup command:
php artisan setup:chowdhurybari
```

---

## 4. One-Command Deployment

After initial setup, every subsequent deployment is just:

```bash
./deploy/deploy.sh
```

The script is **idempotent** — running it multiple times produces the same result. It's safe to run on a already-deployed server.

### Options

| Flag | Description |
|------|-------------|
| `--branch=staging` | Deploy from a specific Git branch |
| `--skip-npm` | Skip npm install & Vite build (if assets are pre-built) |
| `--skip-migrate` | Skip database migrations (if none pending) |
| `--dry-run` | Run pre-flight checks without making changes |

### Example: Deploy Staging Branch

```bash
./deploy/deploy.sh --branch=staging
```

### Example: Quick Deploy (Skip npm)

If you've already built assets locally and committed `public/build/`:

```bash
./deploy/deploy.sh --skip-npm
```

---

## 5. Routine Updates

For small, incremental updates where full deployment is overkill:

```bash
./deploy/update.sh
```

This is a lighter version that:
- Only enters maintenance mode briefly
- Pulls the latest code
- Reinstalls Composer dependencies
- Rebuilds Vite assets
- Runs pending migrations
- Re-optimizes caches
- Brings the app back up

It **skips** the full permission sweep and symlink verification (assumes they're already correct from the initial `deploy.sh` run).

| Flag | Description |
|------|-------------|
| `--skip-npm` | Skip frontend rebuild |
| `--skip-migrate` | Skip migrations |

---

## 6. Rollback

If a deployment goes wrong, roll back immediately:

```bash
# Roll back 1 commit
./deploy/rollback.sh

# Roll back 3 commits
./deploy/rollback.sh --steps=3

# Roll back to a specific known-good commit
./deploy/rollback.sh --to=a1b2c3d
```

The rollback script will:
1. Enable maintenance mode
2. Reset Git to the target commit
3. Reinstall Composer dependencies for that version
4. Rebuild Vite assets for that version
5. Re-optimize caches
6. Bring the app back up

⚠️ **Migrations are NOT automatically rolled back** (to prevent data loss). If a bad migration caused the issue, roll it back manually:

```bash
php artisan migrate:rollback --step=1
```

---

## 7. Post-Deployment Verification

After deployment, verify everything works:

```bash
# Check Laravel is healthy
php artisan about

# Check the health endpoint
curl -s https://yourdomain.com/up | head -1
# Expected: HTTP/1.1 200 OK

# Verify caches are built
ls -la bootstrap/cache/config.php     # Config cache
ls -la bootstrap/cache/routes-v7.php  # Route cache

# Verify storage symlink
ls -la public/storage
# Expected: public/storage -> ../storage/app/public

# Verify Vite assets
ls -la public/build/assets/
# Expected: CSS and JS files with content hashes

# Check maintenance mode is off
php artisan up  # No-op if already up

# Check the deployment log
cat deploy/last_release.txt
```

### Quick Smoke Test

Visit these URLs in your browser:

| URL | Expected |
|-----|----------|
| `https://yourdomain.com/` | Homepage loads |
| `https://yourdomain.com/admin/login` | Admin login page |
| `https://yourdomain.com/member/login` | Member login page |
| `https://yourdomain.com/up` | Health check returns 200 |

---

## 8. Cron Jobs & Queue Workers

### 8.1 Laravel Scheduler

Add this cron entry via cPanel → **Cron Jobs** or SSH:

```bash
* * * * * /usr/local/bin/php /home/youruser/public_html/artisan schedule:run >> /dev/null 2>&1
```

### 8.2 Queue Worker (Supervisor)

For the `database` queue driver, set up a supervisor process:

1. SSH into server
2. Create `/etc/supervisor/conf.d/chowdhurybari-worker.conf` (requires root):

```ini
[program:chowdhurybari-worker]
process_name=%(program_name)s_%(process_num)02d
command=/usr/local/bin/php /home/youruser/public_html/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=youruser
numprocs=2
redirect_stderr=true
stdout_logfile=/home/youruser/public_html/storage/logs/worker.log
stopwaitsecs=3600
```

3. Start the worker:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start chowdhurybari-worker:*
```

**Alternative (no root access):** Use cPanel → **Cron Jobs** to run the queue worker every minute:

```bash
* * * * * /usr/local/bin/php /home/youruser/public_html/artisan queue:work database --once --tries=3 >> /dev/null 2>&1
```

---

## 9. SSL & Domain Configuration

### 9.1 Enable SSL

1. cPanel → **Security** → **SSL/TLS Status** — verify domain is covered
2. cPanel → **Security** → **Let's Encrypt™ SSL** — issue free certificate
3. Enable force HTTPS redirect (already handled by Laravel if `APP_URL` starts with `https://`)

### 9.2 Update APP_URL

After SSL is active, update `.env`:

```env
APP_URL=https://yourdomain.com
```

Then re-cache config:

```bash
php artisan config:cache
```

---

## 10. Troubleshooting

### 500 Internal Server Error

```bash
# Check Laravel logs
tail -50 storage/logs/laravel.log

# Check PHP error log
tail -50 /var/log/php/error.log
# Or on cPanel:
tail -50 ~/error_log

# Common causes:
# - Missing .env file
# - Wrong file permissions on storage/ or bootstrap/cache/
# - Missing PHP extension
# - APP_KEY not generated
```

### 419 Page Expired (CSRF)

```bash
# Check SESSION_DOMAIN matches your domain
grep SESSION_DOMAIN .env

# For domain: chowdhurybari.com → SESSION_DOMAIN=.chowdhurybari.com
# For local: 127.0.0.1 → SESSION_DOMAIN=null
```

### Blank Page / White Screen

```bash
# Usually a fatal PHP error
# Enable debug temporarily:
# In .env: APP_DEBUG=true
php artisan config:cache
# Visit the page, read the error, then set APP_DEBUG=false
```

### Storage Symlink Not Working

```bash
# Remove and recreate
rm -f public/storage
php artisan storage:link

# On some cPanel servers, symlinks may be restricted
# Alternative: mount storage as a directory
# ln -s ../storage/app/public public/storage
```

### Vite Assets Not Loading (404)

```bash
# Verify build output
ls -la public/build/manifest.json
ls -la public/build/assets/

# If missing, rebuild:
npm install
npm run build

# Check Blade templates use @vite() directive
# The manifest must exist for @vite() to resolve assets
```

### Permission Denied Errors

```bash
# Fix writable directories
chmod -R 775 storage bootstrap/cache
chown -R youruser:youruser storage bootstrap/cache

# On cPanel, youruser is typically the cPanel account username
```

### Database Connection Refused

```bash
# Verify MySQL is running
mysql -u your_db_user -p -e "SELECT 1"

# Check .env DB_ values
grep DB_ .env

# Verify the database exists
mysql -u root -p -e "SHOW DATABASES LIKE 'chowdhurybari%'"
```

### Deployment Log

```bash
# View all deployments
cat deploy/last_release.txt

# Example output:
# 2026-08-01 14:30:00 | main | a1b2c3d
# 2026-08-01 15:00:00 | main | e4f5g6h | update
# 2026-08-01 15:30:00 | main | a1b2c3d | rollback-from-e4f5g6h
```

---

## 11. Environment Variables Reference

| Variable | Production Value | Description |
|----------|-----------------|-------------|
| `APP_ENV` | `production` | Never use `local` in production |
| `APP_DEBUG` | `false` | **CRITICAL**: Never `true` in production |
| `APP_KEY` | Auto-generated | Run `php artisan key:generate` |
| `APP_URL` | `https://yourdomain.com` | Must match actual URL |
| `APP_LOCALE` | `bn` | Bengali (primary) |
| `DB_CONNECTION` | `mysql` | Database driver |
| `DB_HOST` | `127.0.0.1` | Usually localhost on cPanel |
| `DB_DATABASE` | `chowdhurybari_central` | Central database name |
| `SESSION_DRIVER` | `database` | Most reliable for production |
| `CACHE_STORE` | `database` | Persistent cache |
| `QUEUE_CONNECTION` | `database` | For async jobs |
| `MAIL_MAILER` | `smtp` | Or `sendmail` on cPanel |
| `LOG_CHANNEL` | `stack` | Or `daily` for rotation |

### Deploy Script Variables (Optional)

| Variable | Default | Description |
|----------|---------|-------------|
| `DEPLOY_BRANCH` | Current branch | Git branch to deploy |
| `DEPLOY_PHP_PATH` | `/usr/local/bin/php` | Path to PHP binary |
| `DEPLOY_COMPOSER_PATH` | `/usr/local/bin/composer` | Path to Composer |
| `DEPLOY_NODE_PATH` | `/usr/local/bin/node` | Path to Node.js |
| `DEPLOY_NPM_PATH` | `/usr/local/bin/npm` | Path to NPM |

---

## 12. Architecture Overview

```
Internet
   │
   ▼
┌─────────────────┐
│  Apache/Nginx   │  (cPanel managed)
│  Document Root  │──→ ~/public_html/public/
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   public/       │  ← Web-accessible entry point
│   index.php     │  ← Laravel front controller
│   build/        │  ← Vite compiled assets (CSS/JS)
│   storage/ →    │  ← Symlink to ../storage/app/public
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Laravel App   │
│   routes/       │  ← Public, Admin, Member routes
│   app/          │  ← Controllers, Models, Middleware
│   resources/    │  ← Blade views, source CSS/JS
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   MySQL         │  ← chowdhurybari_central database
│   (cPanel)      │  ← Users, Buildings, Flats, Meters, etc.
└─────────────────┘
```

### Route Structure

| Panel | URL Prefix | Auth Guard | Middleware |
|-------|-----------|------------|-----------|
| Public Website | `/` | None | None |
| Admin Panel | `/admin/*` | `web` | `auth` + `is_admin` |
| Member Portal | `/member/*` | `member` | `auth:member` |

---

## Quick Reference Card

```bash
# First-time setup
cp .env.example .env && php artisan key:generate
# Edit .env, then:
./deploy/deploy.sh

# Daily update
./deploy/update.sh

# Something broke?
./deploy/rollback.sh

# Check status
php artisan about
cat deploy/last_release.txt

# Clear everything and start fresh
php artisan optimize:clear
./deploy/deploy.sh
```
