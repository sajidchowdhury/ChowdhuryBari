# ChowdhuryBari

**Multi-tenant SaaS platform for Bangladeshi residential community organizations (সমাজ উন্নয়ন সংস্থা).**

Public website + dual admin panels (society admin + member portal) with bKash, Nagad & SSL Commerz payment gateway integrations.

---

## Quick Start

```bash
# 1. Clone the repository
git clone https://github.com/sajidchowdhury/ChowdhuryBari.git
cd ChowdhuryBari

# 2. Install dependencies
composer install
npm install

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Set up database (edit .env with your DB credentials first)
php artisan migrate
php artisan db:seed

# 5. Build frontend assets
npm run build

# 6. Create storage symlink
php artisan storage:link

# 7. Start the development server
composer dev
```

## Deployment

See [README_DEPLOY.md](./README_DEPLOY.md) for the complete deployment guide including:
- One-command deployment on cPanel/WHM
- Idempotent deploy scripts (`deploy.sh`, `update.sh`, `rollback.sh`)
- SSL, cron, queue worker setup
- Troubleshooting guide

**Quick deploy:**
```bash
./deploy/deploy.sh
```

## Tech Stack

| Component | Technology |
|-----------|-----------|
| Backend | Laravel 12 (PHP 8.2+) |
| Frontend | Blade + Tailwind CSS + Alpine.js |
| Build | Vite 6 |
| Auth | Laravel Breeze (dual guard: web + member) |
| Database | MySQL |
| Testing | Pest 3 |
| Payments | SSL Commerz / bKash / Nagad (planned) |

## Project Structure

| Panel | URL | Description |
|-------|-----|-------------|
| Public Website | `/` | Community info, gallery, notices |
| Admin Panel | `/admin/*` | Buildings, flats, meters, billing, users |
| Member Portal | `/member/*` | Self-service for community members |

## Key Features

- **Building & Flat Management** — Multi-road, multi-building residential inventory
- **Electricity Meter Tracking** — Meter readings, billing calculations, BPDB integration
- **Active Family Detection** — Automatic detection based on meter recharge activity
- **Service Charge Billing** — Per-family, per-floor, and fixed charge types
- **Social Value Ranking** — Community leaderboard with star ratings and tiebreak logic
- **Dual Authentication** — Separate admin and member sessions with isolated cookies
- **Notice Board** — Time-bounded announcements with auto-expiry
- **Gallery & About Pages** — Rich content management for public site
- **Member Upload System** — Yard photos with social value calculations

## Development

```bash
# Run tests
composer test

# Code style fix
./vendor/bin/pint

# Start dev servers (PHP + queue + Vite HMR)
composer dev
```

## License

This project is licensed under the [MIT License](https://opensource.org/licenses/MIT).
