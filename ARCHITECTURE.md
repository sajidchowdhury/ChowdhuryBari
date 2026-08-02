# ChowdhuryBari — Multi-Tenant SaaS Architecture

> **Status:** Phase 1 (foundation) — in progress
> **Last updated:** 2026-07-06
> **Decisions confirmed by owner:**
> 1. Stay with Laravel (not Node.js)
> 2. Database-per-tenant (each society gets its own DB)
> 3. Subdomain-based tenancy (`chowdhurypara.app.com`, `anothersociety.app.com`)
> 4. Payments: **bKash**, **Nagad**, **SSL Commerz**

---

## 1. The 4 panels

| # | Panel | URL pattern | Who logs in | DB | Status |
|---|-------|-------------|-------------|-----|--------|
| 1 | **Public website** | `{tenant}.app.com` | Anonymous + members | Tenant | ✅ Exists (Blade) |
| 2 | **Society admin** | `{tenant}.app.com/admin` | Society admin/moderator | Tenant | 🚧 Convert to Filament |
| 3 | **Society member** | `{tenant}.app.com/member` | Residents | Tenant | ⏳ New build |
| 4 | **Super admin** | `app.com/super-admin` | You (platform owner) | Central | ⏳ New build |

The **central domain** (`app.com`) hosts the super admin panel + a landing/marketing site for the SaaS itself. The **tenant subdomains** (`{society}.app.com`) host each society's website + admin + member panels.

A user account in panel #2 or #3 belongs to ONE society's database — they cannot log in to another society. A super admin in panel #4 belongs to the central DB and can impersonate any society admin.

---

## 2. Database layout

### 2.1 Central DB (the "system" DB)

Stores platform-wide data: tenant registry, super admins, product catalog, orders, payments, billing.

| Table | Purpose |
|-------|---------|
| `tenants` | One row per society — id (UUID), name, slug, plan, status, created_at. **Created by `stancl/tenancy`.** |
| `domains` | Maps subdomain → tenant. `chowdhurypara.app.com` → tenant UUID. **Created by `stancl/tenancy`.** |
| `super_admins` | Platform owner accounts (separate auth guard from tenant users). |
| `products` | Catalog of products you sell across all societies (e.g. security cameras, cleaning supplies, branded merchandise). |
| `orders` | Orders placed by tenants/members for products. Links to tenant via `tenant_id`. |
| `payments` | Payment records — one payment can be for dues, an order, or a subscription. Stores gateway (bkash/nagad/ssl), txn_id, amount, status. |
| `subscriptions` | Per-tenant monthly SaaS subscription to the platform itself (separate from member dues). |

### 2.2 Tenant DB (one per society)

Each tenant DB is fully isolated. The current ChowdhuryBari tables become tenant tables.

| Table | Purpose |
|-------|---------|
| `users` | Residents + society admins of THAT society. Has `role` (admin/moderator/member). |
| `roads` | Roads in this society (already exists). |
| `buildings` | Buildings, linked to roads (already exists). |
| `notices` | Notice board posts (new). |
| `events` | Upcoming events (new). |
| `classifieds` | Rent/sale/service ads posted by members (new). |
| `gallery_images` | Community photos (new). |
| `clean_yard_ratings` | Monthly clean-yard scores per building (new). |
| `testimonials` | Member testimonials (new). |
| `dues_payments` | Per-family monthly dues tracking (new). |
| `expenses` | Society expenses (security guard salary, cleaner, electricity, etc.) — for transparent accounting (new). |

---

## 3. Tech stack (confirmed)

| Layer | Choice | Why |
|-------|--------|-----|
| Framework | Laravel 12 + PHP 8.2 | Already in use |
| Multi-tenancy | `stancl/tenancy` v3.8 | The de-facto Laravel multi-tenancy package. Supports DB-per-tenant + subdomain routing out of the box. |
| Admin panels | **Filament v3** | Purpose-built for multi-panel admin dashboards. We'll create 3 panels (admin/member/super-admin). Each gets its own login, sidebar, middleware, scoped data. |
| Roles & permissions | `spatie/laravel-permission` + `bezhansalleh/filament-shield` | Fine-grained RBAC inside each panel. Shield auto-generates Filament permissions. |
| Payments (BD) | Custom integration for **bKash**, **Nagad**, **SSL Commerz** | No single well-maintained Laravel package covers all 3. We'll build a `PaymentGateway` interface and 3 adapters. SSL Commerz can act as a fallback aggregator (handles bKash/Nagad/cards via one integration). |
| Frontend (website) | Blade + Tailwind 4 (existing) | The public site is already Blade. No need to migrate to Inertia/React. |
| Asset bundling | Vite 7 (existing) | Already configured. |
| Testing | Pest v3 (existing) | Already configured. |

### Payment gateway strategy

SSL Commerz is a **payment aggregator** — it accepts bKash, Nagad, cards, and internet banking through a single integration. So the smart play is:

1. **Phase 4a — SSL Commerz first** (1 integration, covers everything). Lower fee (~2.5%) but covers all payment methods.
2. **Phase 4b — Direct bKash** (for lower fees on bKash transactions, ~1.5%). Only worth adding once transaction volume justifies it.
3. **Phase 4c — Direct Nagad** (same logic as bKash).

We'll build a `PaymentGateway` interface so all three adapters have the same API:

```php
interface PaymentGateway {
    public function initiatePayment(Order $order): string;  // returns redirect URL
    public function verifyPayment(string $paymentId): Payment;
    public function handleWebhook(Request $request): Payment;
}
```

---

## 4. Directory structure (target)

```
app/
├── Models/
│   ├── Central/                       ← Central DB models
│   │   ├── Tenant.php                 (created by stancl)
│   │   ├── Domain.php                 (created by stancl)
│   │   ├── SuperAdmin.php             (NEW — this commit)
│   │   ├── Product.php                (NEW — this commit)
│   │   ├── Order.php                  (NEW — this commit)
│   │   ├── Payment.php                (NEW — this commit)
│   │   └── Subscription.php
│   ├── Tenant/                        ← Tenant DB models
│   │   ├── User.php                   (moved from app/Models)
│   │   ├── Road.php                   (moved)
│   │   ├── Building.php               (moved)
│   │   ├── Notice.php
│   │   ├── Event.php
│   │   ├── Classified.php
│   │   ├── GalleryImage.php
│   │   ├── CleanYardRating.php
│   │   ├── Testimonial.php
│   │   ├── DuesPayment.php
│   │   └── Expense.php
│   └── (legacy aliases for backward compat during migration)
├── Providers/
│   ├── TenancyServiceProvider.php     (created by stancl)
│   └── AppServiceProvider.php
├── Filament/
│   ├── SuperAdmin/                    ← Panel #4
│   │   ├── Pages/Dashboard.php
│   │   ├── Resources/
│   │   │   ├── TenantResource.php
│   │   │   ├── ProductResource.php
│   │   │   ├── OrderResource.php
│   │   │   └── PaymentResource.php
│   │   └── SuperAdminPanelProvider.php
│   ├── Admin/                         ← Panel #2 (society admin)
│   │   ├── Pages/Dashboard.php
│   │   ├── Resources/
│   │   │   ├── UserResource.php
│   │   │   ├── RoadResource.php
│   │   │   ├── BuildingResource.php
│   │   │   ├── NoticeResource.php
│   │   │   └── EventResource.php
│   │   └── AdminPanelProvider.php
│   └── Member/                        ← Panel #3 (society member)
│       ├── Pages/Dashboard.php
│       ├── Resources/
│       │   ├── ClassifiedResource.php
│       │   ├── DuesPaymentResource.php
│       │   └── NoticeResource.php     (view-only)
│       └── MemberPanelProvider.php
├── Http/Controllers/
│   ├── Payment/                       ← Payment gateway adapters
│   │   ├── PaymentGateway.php         (interface)
│   │   ├── SslCommerzGateway.php
│   │   ├── BkashGateway.php
│   │   └── NagadGateway.php
│   └── ...
config/
└── tenancy.php                        (created by stancl)
database/
├── migrations/                        ← Central DB (default folder)
│   ├── 0001_01_01_000000_create_users_table.php  ← repurpose for super_admins OR delete
│   ├── stancl_tenants_table.php      (created by tenancy:install)
│   ├── stancl_domains_table.php      (created by tenancy:install)
│   ├── 2026_07_06_000001_create_super_admins_table.php    (NEW — this commit)
│   ├── 2026_07_06_000002_create_products_table.php        (NEW — this commit)
│   ├── 2026_07_06_000003_create_orders_table.php          (NEW — this commit)
│   ├── 2026_07_06_000004_create_payments_table.php        (NEW — this commit)
│   └── 2026_07_06_000005_create_subscriptions_table.php
└── migrations/tenant/                 ← Tenant DB (run for each society)
    ├── 0001_01_01_000000_create_users_table.php   (moved from central)
    ├── 2026_06_15_043924_create_roads_table.php   (moved)
    ├── 2026_06_15_043924_create_buildings_table.php (moved)
    ├── 2026_07_06_000001_create_notices_table.php
    ├── 2026_07_06_000002_create_events_table.php
    ├── 2026_07_06_000003_create_classifieds_table.php
    ├── 2026_07_06_000004_create_gallery_images_table.php
    ├── 2026_07_06_000005_create_clean_yard_ratings_table.php
    ├── 2026_07_06_000006_create_testimonials_table.php
    ├── 2026_07_06_000007_create_dues_payments_table.php
    └── 2026_07_06_000008_create_expenses_table.php
```

---

## 5. Phased build plan

### Phase 1 — Foundation (THIS commit + your local install)

**What I'm committing:**
- `composer.json` — adds Filament v3, stancl/tenancy v3.8, spatie/permission, filament-shield
- `ARCHITECTURE.md` — this document
- `database/migrations/2026_07_06_*` — central DB migrations (super_admins, products, orders, payments)
- `app/Models/Central/` — central models
- `app/Filament/SuperAdmin/SuperAdminPanelProvider.php` — basic panel scaffolding

**What you need to run locally after `git pull`:**
```bash
composer install
php artisan tenancy:install           # creates config/tenancy.php, TenancyServiceProvider, tenants + domains migrations, Tenant model
php artisan vendor:publish --tag="filament-config"
php artisan filament:install --panels  # creates panel providers
php artisan migrate                    # runs central migrations (incl. stancl's tenants + domains)
php artisan migrate --path=database/migrations/tenant  # runs tenant migrations on central DB (for testing — real tenants run via tenancy:migrate)
```

### Phase 2 — Move existing tables to tenant DB

- Move `users`, `roads`, `buildings` migrations from `database/migrations/` to `database/migrations/tenant/`
- Move `User`, `Road`, `Building` models from `app/Models/` to `app/Models/Tenant/`
- Update `bootstrap/providers.php` to register `TenancyServiceProvider` + `AuthServiceProvider`
- Create first real tenant (ChowdhuryBari itself) via `php artisan tinker`
- Run `php artisan tenants:migrate` to create the tenant DB + run tenant migrations
- Verify existing ChowdhuryBari site loads under `chowdhurypara.localhost`

### Phase 3 — Filament admin panel (#2) for society admin

- Replace current `AdminController` + Blade admin views with Filament `AdminPanelProvider`
- Resources: User, Road, Building, Notice, Event, Classified, GalleryImage, Testimonial, Expense, DuesPayment
- Auto-generate RBAC via `filament-shield`
- Login at `{tenant}.app.com/admin`

### Phase 4 — Filament member panel (#3)

- New `MemberPanelProvider` with member-specific dashboard
- Members can: view notices, view events, post classifieds, view dues, pay dues online, upload clean-yard photos
- Login at `{tenant}.app.com/member`

### Phase 5 — Filament super admin panel (#4)

- `SuperAdminPanelProvider` on central domain
- Resources: Tenant (create new societies), Product, Order, Payment, Subscription
- Impersonation: super admin can "log in as" any society admin for support
- Revenue dashboard: aggregate payments across all tenants

### Phase 6 — Payments

- Build `PaymentGateway` interface
- Implement `SslCommerzGateway` first (covers bKash + Nagad + cards)
- Add webhooks + IPN handlers
- Wire up dues payment flow in member panel
- Wire up product purchase flow in super admin panel

### Phase 7 — Public website per-tenant

- Make `welcome.blade.php` tenant-aware (load society name, logo, notices, events from current tenant DB)
- Add a SaaS marketing landing page on the central domain (`app.com`) to recruit new societies

### Phase 8 — Production hardening

- Queue workers for payment webhooks + email notifications
- Backup strategy (per-tenant DB backups via `spatie/laravel-backup`)
- SSL via Let's Encrypt for `*.app.com` wildcard
- Monitoring via `telescope` + `pulse`
- Rate limiting on auth endpoints
- Audit log (who did what, when) — important for multi-tenant SaaS

---

## 6. Environment setup

### 6.1 Local development — wildcard subdomain

Add to `/etc/hosts` (or use `valet park` / `herd park`):

```
127.0.0.1   chowdhurypara.test
127.0.0.1   anothersociety.test
127.0.0.1   test                  # central domain (super admin)
```

### 6.2 `.env` (central DB + tenant DB connection)

```env
APP_URL=http://test
TENANCY_CENTRAL_DOMAINS=test

# Central DB (system DB)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chowdhurybari_central
DB_USERNAME=root
DB_PASSWORD=

# Tenant DB connection (used by stancl to create/connect tenant DBs)
TENANCY_DB_CONNECTION=mysql
TENANCY_DB_HOST=127.0.0.1
TENANCY_DB_PORT=3306
TENANCY_DB_USERNAME=root
TENANCY_DB_PASSWORD=
TENANCY_DB_PREFIX=tenant_

# Payment gateway credentials (add when Phase 6 starts)
SSLCOMMERZ_STORE_ID=
SSLCOMMERZ_STORE_PASSWD=
SSLCOMMERZ_MODE=sandbox

BKASH_APP_KEY=
BKASH_APP_SECRET=
BKASH_USERNAME=
BKASH_PASSWORD=
BKASH_MODE=sandbox

NAGAD_MERCHANT_ID=
NAGAD_PUBLIC_KEY=
NAGAD_PRIVATE_KEY=
NAGAD_MODE=sandbox
```

> **Note on DB choice:** SQLite is fine for central DB in dev, but **MySQL/Postgres is strongly recommended** for production with DB-per-tenant. SQLite has OS-level file locking issues at scale, and managing hundreds of SQLite files is painful. MySQL makes creating new tenant DBs trivial (`CREATE DATABASE tenant_xxx`).

---

## 7. Why these specific choices

### Why Filament v3 (not v4)?
- v3 is stable, widely documented, and has the largest ecosystem of plugins.
- v4 just shipped — wait 6 months for it to mature before considering upgrade.
- All Filament plugins we need (Shield, translatable) target v3.

### Why `stancl/tenancy` (not `tenancy/multitenancy` or DIY)?
- Most popular Laravel multi-tenancy package (~5k stars).
- Built-in support for: DB-per-tenant, subdomain routing, tenant identification middleware, global scopes, tenant-aware Artisan commands.
- Active maintenance, comprehensive docs at `tenancyforlaravel.com`.

### Why DB-per-tenant (not single-DB with `tenant_id`)?
You confirmed this. Trade-offs:

| | DB-per-tenant | Single-DB + tenant_id |
|---|---|---|
| Data isolation | ✅ Strong — each society's data in its own DB | ❌ Logical only — bug in code = cross-tenant leak |
| Backup/restore | ✅ Per-society — can restore one tenant without affecting others | ❌ All-or-nothing |
| Performance at scale | ✅ Smaller indexes per DB | ❌ One huge `users` table |
| Migration complexity | ❌ Run migrations N times (one per tenant) | ✅ One migration |
| Cross-tenant queries (super admin) | ❌ Harder — need to connect to each DB | ✅ Trivial — just `WHERE tenant_id` |
| Compliance / contracts | ✅ Some societies may require data isolation by contract | ❌ Not enforceable |

For your use case (Bangladeshi residential societies, each with member PII like phone numbers + addresses), **DB-per-tenant is the right call** — it makes data isolation contracts possible and lets you offer "your data is in a separate database" as a selling point.

### Why subdomain routing (not path-based)?
- Cleaner URLs: `chowdhurypara.app.com` vs `app.com/society/chowdhurypara`
- SSL wildcard cert (`*.app.com`) covers all tenants automatically
- Each society can customize their subdomain (`greenresidency.app.com`)
- Cookie isolation — tenant sessions are scoped to the subdomain
- Easier for societies to share their URL (looks more "official")

### Why custom payment adapters (not a package)?
- bKash, Nagad, SSL Commerz APIs are simple REST APIs (~3 endpoints each: initiate, verify, webhook)
- No single well-maintained Laravel package covers all 3
- Custom code = full control, no abandoned-package risk, can add new gateways easily
- The `PaymentGateway` interface pattern keeps code clean and testable

---

## 8. Security considerations

1. **Tenant isolation is enforced at the DB connection level** — stancl/tenancy switches the default DB connection when a tenant is identified. Even a bug in Eloquent code cannot leak cross-tenant data because the tenant's DB connection literally doesn't have other tenants' tables.

2. **Super admin panel is on a separate domain** — `app.com/super-admin` is not accessible from tenant subdomains. Super admins use a separate auth guard (`super_admin`).

3. **Payment webhooks are IPN-verified** — every bKash/Nagad/SSL webhook validates the gateway's signature before acting on the payload.

4. **PII handling** — member phone numbers, addresses, and dues payment history are sensitive. DB-per-tenant gives us strong isolation. Add a `pii_encrypted` trait later if you want column-level encryption.

5. **Audit log** — Phase 8 will add `spatie/laravel-activitylog` to track every create/update/delete across all panels. Critical for multi-tenant SaaS — when something goes wrong in a tenant, you need to know who did what.

---

## 9. What to do RIGHT NOW

After you `git pull` and run the install commands in §5.1, verify:

1. `php artisan tenancy:install` ran cleanly (check `config/tenancy.php` exists)
2. `php artisan migrate` created the central tables: `tenants`, `domains`, `super_admins`, `products`, `orders`, `payments`
3. Visit `http://test/super-admin/login` — you should see the Filament login screen (even if no resources yet)
4. Create your first super admin: `php artisan tinker` → `App\Models\Central\SuperAdmin::create(['name'=>'Sajid','email'=>'sajid@gmail.com','password'=>bcrypt('password')])`
5. Log in to `http://test/super-admin`

Once that works, tell me and we'll start Phase 2 (move existing tables to tenant DB + create the first real ChowdhuryBari tenant).

---

## 10. Open questions (answer when convenient)

1. **Central domain name** — what's the actual domain? (Currently using `app.com` as placeholder. Replace with your real domain.)
2. **Tenant subdomain format** — `{society-slug}.app.com` (e.g. `chowdhurypara.app.com`) or `{custom}.app.com` (let society choose)?
3. **SaaS subscription pricing** — what do you charge societies monthly for using the platform? (Starter / Pro / Enterprise tiers?)
4. **Member dues** — fixed 300৳/month per family (as shown on the current site), or configurable per-society?
5. **Onboarding flow** — can societies self-signup, or do you (super admin) create each tenant manually?
6. **Custom domains** — do societies need to use their own domain (e.g. `chowdhurybari.com` instead of `chowdhurypara.app.com`)? Adds complexity but is a strong selling point.

---

## 11. Known issues to fix from current codebase

Carried over from the previous security audit (see commit `dccacbb`):

| # | Issue | Status |
|---|-------|--------|
| 1 | Admin routes used only `auth` middleware | ✅ Fixed in `dccacbb` |
| 2 | Broken RoadSeeder + BuildingSeeder (wrong column names) | ⏳ Will fix in Phase 2 |
| 3 | AdminSeeder orphaned (not called by DatabaseSeeder) | ⏳ Will fix in Phase 2 |
| 4 | Three duplicate "add admin fields" migrations | ⏳ Will fix in Phase 2 |
| 5 | README is default Laravel boilerplate | ⏳ Replace with proper README in Phase 7 |
| 6 | Public sections (notice/event/toLet/review) are static Blade, no models | ⏳ Will add models in Phase 2 |
| 7 | AuthServiceProvider not registered in bootstrap/providers.php | ⏳ Will fix in Phase 2 |
| 8 | UserController calls Gate::authorize('manage-users') but gates are dead | ⏳ Will be replaced by Filament Shield in Phase 3 |
| 9 | No feature tests for login/admin flows | ⏳ Will add in Phase 8 |
| 10 | No README setup instructions | ⏳ Will add in Phase 7 |

---

**Questions?** Ping me after running the install commands. If anything breaks, paste the error and I'll fix it.
