# ChowdhuryBari — চৌধুরীপাড়াস্থ সমাজ উন্নায়ন সংস্থা

> **Full Project Context Document** — for human documentation AND as an AI context prompt.
> Paste this entire file into any AI assistant to give it complete project understanding.

---

## 📋 Project Overview

**ChowdhuryBari** is a community management web application for the **চৌধুরীপাড়াস্থ সমাজ উন্নায়ন সংস্থা** (Chowdhurypara Social Development Organization) — a residential society in Bangladesh with 90+ buildings across 15+ roads. The platform serves three audiences:

1. **Public Website** (`/`) — society info, notices, gallery, area coverage, leadership team, top-10 cleanest families, contact form, delivery location finder
2. **Admin Panel** (`/admin/*`) — full CRUD for all content + member management + billing + social value rating + family reduction applications
3. **Member Portal** (`/member/*`) — building owners log in to see their building details, toggle flat/family status, upload yard photos for the cleanliness ranking, and view billing

### Tech Stack
- **Framework:** Laravel 11 (PHP 8.2+)
- **Database:** MySQL (central) + per-tenant databases (stancl/tenancy ready, but currently single-tenant)
- **Frontend:** Tailwind CSS (CDN) + Alpine.js + Font Awesome + Blade templates
- **Auth:** Dual-guard system (admin = `web` guard, member = `member` guard) with separate session cookies
- **File storage:** Local disk (`public/uploads/*`)

---

## 🏗️ Architecture

### Two Separate Authentication Systems

| Aspect | Admin | Member |
|---|---|---|
| **Guard** | `web` | `member` |
| **Login URL** | `/admin/login` (email + password) | `/member/login` (phone + OTP `9999`) |
| **Session cookie** | `chowdhurybari_session` | `chowdhurybari_session_member` |
| **Middleware** | `auth` + `is_admin` | `auth:member` |
| **User source** | `users` table (pre-seeded admins) | `buildings.owner_phone` → `User::firstOrCreate()` |

**Key insight:** A person can be logged in as admin in one browser tab AND as a member in another tab simultaneously — completely independent sessions. The `MemberSessionCookie` middleware swaps the cookie name for `member/*` routes before `StartSession` runs.

### Route Structure

```
/                          → Public website (welcome.blade.php)

/admin/*                   → Admin panel
  /login                   → Email + password login
  /dashboard               → Admin home
  /our-area                → Roads + buildings management
  /buildings/{id}          → Building detail (flats, meters, residents)
  /members                 → Committee team members
  /notices                 → Notice board CRUD
  /gallery                 → Photo gallery uploads
  /about                   → About Us content (singleton)
  /contact                 → Get In Touch settings (singleton)
  /settings                → Navigation & Footer settings (singleton)
  /service-charges         → Service charge configuration (per building type)
  /social-value            → Rate member yard photos (anonymous, 1-10 stars)
  /applications            → Family reduction applications review

/member/*                  → Member portal
  /login                   → Phone + OTP (9999) two-step login
  /dashboard               → 5-tab dashboard (Alpine.js)
  /uploads                 → Yard photo upload (4/month)
  /flats/update-statuses   → Toggle flat active/inactive
  /applications            → Legacy family reduction application
```

### Data Flow Summary

```
Admin sets up:
  Road → Building (with category + per_family_amount + billing_family_count)
           → Flats (auto-generated from floor_count × families_per_floor)
              → Meters (per flat)

Building owner logs in via phone → sees their building
  → toggles flat active/inactive → billing auto-updates
  → uploads yard photos (4/month) → admin rates 1-10 stars
  → social value = avg(stars) × 10 → leaderboard rank
  → top 10 shown on public website
```

---

## 📊 Database Schema

### Core Tables

| Table | Purpose | Key Fields |
|---|---|---|
| `users` | Admin + auto-created member accounts | `name, email, phone, role (admin/user/moderator), is_active` |
| `roads` | Society roads/lanes | `name, description, tags (json), image_path` |
| `buildings` | Buildings on each road | `road_id, name, owner_name, owner_phone, structure_type, usage_type, building_category, per_family_amount, billing_family_count, floor_count, families_per_floor, google_lt, google_ln` |
| `flats` | Individual flats/families per building | `building_id, flat_number, floor_number, resident_name, resident_phone, is_active` |
| `meters` | Electricity meters per flat | `flat_id, meter_number, provider, last_recharge_at` |
| `meter_readings` | Monthly recharge history | `meter_id, recharge_amount, recharged_at, source` |

### Content Tables

| Table | Purpose | Key Fields |
|---|---|---|
| `members` | Committee leadership team (public "আমাদের নেতৃত্ব") | `name, designation, started_from, phone, image_path, bio, sort_order, is_active` |
| `notices` | Public notice board | `type, headline, description, published_at, active_till_date, is_active, sort_order` |
| `gallery_items` | Public website gallery (admin uploads) | `image_path, caption, category, is_active` |
| `about_infos` | About Us section (singleton, id=1) | `headline, description, image_path` |
| `contact_infos` | Contact section + form recipient (singleton) | `address, phone, email, whatsapp, office_hours, recipient_email, form_active` |
| `site_settings` | Logo + navbar color + social links (singleton) | `logo_path, nav_color, whatsapp_link, facebook_link, youtube_link, footer_address` |

### Billing Tables

| Table | Purpose | Key Fields |
|---|---|---|
| `service_charges` | Monthly charges per building type | `name, building_category, amount, charge_type (per_family/per_floor/fixed), is_active` |
| `family_reduction_applications` | Member requests to reduce billing | `user_id, building_id, current_family_count, requested_family_count, vacant_flat_ids, reason, status (pending/approved/rejected), admin_notes` |

### Social Value Tables

| Table | Purpose | Key Fields |
|---|---|---|
| `member_uploads` | Yard photos submitted by members | `user_id, image_path, caption, month_key (YYYY-MM), star_rating (1-10, null=unrated), rated_at, rated_by` |

### Building Categories (4 types)
```
tin_shed              → টিন শেড
below_or_equal_4_floor → ৪তলা বা নিচে
above_4_floor          → ৪তলার উপরে
shop                   → দোকান
```

### Charge Types (3 types)
```
per_family → amount × billing_family_count  (e.g. ময়লা বিল)
per_floor  → amount × floor_count           (e.g. লিফট বিল)
fixed      → amount (once per building)     (e.g. গার্ড বিল)
```

---

## 💰 Billing System

### Formula
```
Monthly Due = (building.per_family_amount × billing_family_count)
            + Σ(per_family charges × billing_family_count)
            + Σ(per_floor charges × floor_count)
            + Σ(fixed charges)
```

### How billing_family_count works
1. **Default (null):** Auto-calculated from active flats (meters with recharge in last 45 days)
2. **Admin-set / member-toggle:** When a member toggles flats on/off and saves, `billing_family_count` = number of active flats
3. **Admin override:** Admin can manually set any number via building edit form
4. **Persists:** The set count stays until changed again (not monthly reset)

### Example
- Building: 5 floors × 4 families = 20 expected
- `per_family_amount` = ৳300
- Member toggles 2 flats off → 18 active
- Service charges: ময়লা বিল ৳50/family (per_family), গার্ড বিল ৳2000 (fixed)
- **Total** = (300 × 18) + (50 × 18) + 2000 = 5400 + 900 + 2000 = **৳8,300**

---

## ⭐ Social Value System (Cleanliness Ranking)

### Flow
1. **Member uploads** up to 4 yard photos per month (monthly rotation, `month_key = YYYY-MM`)
2. **Admin rates** each photo 1-10 stars **anonymously** (admin can't see who uploaded)
3. **Social Value** = `round(avg(star_rating of rated photos) × 10)` → scale of 10-100
4. **Ranking** = members sorted by current month's social value (desc)
5. **Tiebreaker** = previous month's social value (desc)
6. **Top 10** displayed on public website with building name + owner name + best photo

### Edge Cases
- No rated photos → social value = `null` → shown as `--` → "upload image for ranking"
- Unrated photos don't count toward the average
- Photos from previous months are kept for history but don't affect current ranking

---

## 🎨 UI/UX Conventions

### Color Palette (decent, premium — not too colorful)
- **Primary:** Emerald (`#065f46`, `#0f766e`)
- **Accent:** Amber/Gold (`#f59e0b`, `#fbbf24`) — for stars, rank badges
- **Neutral:** Slate grays
- **Status:** Emerald (active), Amber (pending), Red (error/expired), Sky (info)
- Admin sidebar: solid dark emerald `#0f2e26` (no gradient)
- Member sidebar: solid dark emerald `#0f2e26`
- Stat cards: white bg + subtle colored icon chips (not full gradients)

### Typography
- Headings: `Playfair Display` (serif) via `.heading-serif` class
- Body: `Inter` + `Noto Sans Bengali`

### Responsive
- Mobile-first, Tailwind responsive prefixes
- Member dashboard: desktop sidebar → mobile bottom nav (5 tabs)
- Admin: fixed sidebar on desktop, hamburger on mobile

### Language
- **All user-facing text is in Bengali (বাংলা)**
- Code comments + variable names in English
- Bengali digit conversion (`০-৯`) for dates/numbers shown to users

---

## 🔑 Key Files Reference

### Controllers
| File | Responsibility |
|---|---|
| `AdminController.php` | Admin login + dashboard + our-area |
| `BuildingController.php` | Building/Flat/Meter CRUD + auto-flat generation |
| `NoticeController.php` | Notice board CRUD |
| `GalleryController.php` | Public gallery image uploads |
| `AboutController.php` | About Us singleton (headline + image + description) |
| `ContactController.php` | Contact info + working form (sends Laravel Mail) |
| `SiteSettingController.php` | Logo + navbar color + social links + footer address |
| `ServiceChargeController.php` | Service charges with building_category + charge_type |
| `MemberAuthController.php` | Member phone+OTP login (via buildings.owner_phone) |
| `MemberUploadController.php` | Yard photo upload + admin anonymous rating |
| `FamilyReductionApplicationController.php` | Flat toggle save + legacy application system |

### Models (with key relationships)
| Model | Relationships | Notable Methods |
|---|---|---|
| `Building` | `hasMany Flats`, `belongsTo Road` | `monthlyDue()`, `chargeBreakdown()`, `getActiveFamilyCount()`, `effective_billing_family_count` |
| `Flat` | `belongsTo Building`, `hasMany Meters` | `isFamilyActive()` (45-day meter threshold) |
| `ServiceCharge` | — | `calculateForBuilding($cat, $familyCount, $floorCount)`, `CHARGE_TYPES`, `CATEGORIES` |
| `MemberUpload` | `belongsTo User` | `socialValueFor($userId, $monthKey)`, `bestImageFor()`, `currentMonthKey()` |
| `User` | `hasMany MemberUploads`, `building` accessor | `getBuildingAttribute()` (matches phone → building) |
| `FamilyReductionApplication` | `belongsTo User, Building, Reviewer` | `status_label` accessor |

### Services
| File | Purpose |
|---|---|
| `SocialValueService.php` | Leaderboard calculation, ranking with tiebreaker, social value computation |

### Middleware
| File | Purpose |
|---|---|
| `IsAdmin.php` | Checks `role === 'admin'`, redirects non-admins to login |
| `MemberSessionCookie.php` | Swaps session cookie name for `member/*` routes (separate sessions) |
| `PreventDevCache.php` | No-cache headers in local env |

---

## 🚀 Setup & Installation

### Prerequisites
- PHP 8.2+
- MySQL 8+
- Composer
- Node.js (for Vite, optional — CDN fallback works)

### Steps
```bash
# 1. Clone + install
git clone https://github.com/sajidchowdhury/ChowdhuryBari.git
cd ChowdhuryBari
composer install

# 2. Environment
cp .env.example .env
php artisan key:generate
# Edit .env: set DB_DATABASE, DB_USERNAME, DB_PASSWORD
# Set SESSION_DRIVER=file (default) or database

# 3. Database
php artisan migrate
php artisan db:seed --class=UserSeeder   # creates admin users

# 4. Run
php artisan serve
# Admin: http://127.0.0.1:8000/admin/login
# Member: http://127.0.0.1:8000/member/login
```

### Default Admin Credentials
- Email: `sajid@gmail.com`
- Password: `password123`
- (Also: `rahim@example.com`, `karim@example.com` — non-admin test users)

### Member Login
- **Username:** Any `owner_phone` from the `buildings` table (set by admin)
- **OTP:** `9999` (demo — real SMS OTP to be added later)
- A `User` record is auto-created on first login via `firstOrCreate`

---

## 📁 Directory Structure

```
ChowdhuryBari/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/              # UserController
│   │   ├── Auth/               # Login, register, password reset
│   │   ├── Payment/            # PaymentGateway (placeholder)
│   │   ├── AdminController.php
│   │   ├── BuildingController.php
│   │   ├── MemberAuthController.php
│   │   ├── MemberUploadController.php
│   │   ├── FamilyReductionApplicationController.php
│   │   ├── ServiceChargeController.php
│   │   └── ... (About, Contact, Gallery, Notice, SiteSetting)
│   ├── Models/                 # 15 models (see table above)
│   ├── Services/
│   │   └── SocialValueService.php
│   ├── Middleware/
│   │   ├── IsAdmin.php
│   │   ├── MemberSessionCookie.php
│   │   └── PreventDevCache.php
│   └── Filament/SuperAdmin/    # Filament panel (unused, legacy)
├── database/
│   ├── migrations/             # 19 migrations (2024–2026)
│   └── seeders/
│       └── UserSeeder.php      # Admin + test users
├── resources/views/
│   ├── admin/                  # 14 admin views
│   │   ├── about/ applications/ buildings/ contact/ gallery/
│   │   ├── members/ notices/ service-charges/ settings/ social-value/
│   │   ├── users/
│   │   ├── dashboard.blade.php
│   │   ├── layout.blade.php    # Sidebar + topbar
│   │   └── login.blade.php
│   ├── member/
│   │   ├── login.blade.php     # Phone → OTP 2-step
│   │   └── dashboard.blade.php # 5-tab Alpine.js dashboard
│   ├── pages/                  # Public website sections
│   │   ├── about.blade.php     # DB-driven (AboutInfo)
│   │   ├── notice.blade.php    # DB-driven (Notice)
│   │   ├── gallery.blade.php   # DB-driven (GalleryItem) + lightbox
│   │   ├── top-ranked.blade.php # Top 10 social value leaderboard
│   │   ├── OurArea.blade.php   # Road/building explorer + delivery finder data
│   │   ├── ContactUs.blade.php # DB-driven + working mail form
│   │   └── ... (team, impact, WhatWeDo, MemberApplication, Review, TopMember)
│   ├── layouts/
│   │   ├── app.blade.php       # Main layout wrapper
│   │   ├── navbar.blade.php    # DB-driven logo + nav color
│   │   ├── footer.blade.php    # DB-driven social links + address
│   │   ├── delivery-finder.blade.php # Modal + JS for location search
│   │   ├── FloatingButton.blade.php  # WhatsApp + Delivery only
│   │   ├── hero.blade.php
│   │   └── head.blade.php
│   ├── components/             # Reusable Blade components (modal, buttons, etc.)
│   └── welcome.blade.php       # Homepage (assembles all page sections)
├── routes/
│   └── web.php                 # All routes (admin + member + public)
├── config/
│   ├── auth.php                # Two guards: web + member
│   ├── session.php             # Default driver: file
│   └── mail.php                # Default: log (configure SMTP in .env)
├── public/
│   ├── uploads/                # Admin/member uploaded images (gitignored)
│   │   ├── gallery/
│   │   ├── about/
│   │   ├── member/             # Yard photos
│   │   └── site/               # Logos
│   └── img/                    # Static images (logo, aboutus, etc.)
└── .env.example                # Template (SESSION_DRIVER=file, APP_URL, DB creds)
```

---

## 🔄 Current Status & Known Limitations

### ✅ Working Features
- Full admin panel CRUD (roads, buildings, flats, meters, notices, gallery, members, about, contact, settings, service charges)
- Member login (phone + OTP 9999) with separate session
- Member dashboard (5 tabs: ড্যাশবোর্ড, আমার বাড়ি, ডিউ ও পেমেন্ট, আমার গ্যালারি, র‍্যাঙ্কিং ও স্কোর)
- Building-type-based billing with per_family/per_floor/fixed charge types
- Flat toggle interface (simpler than application system)
- Social value system (upload → admin rate → leaderboard → top 10 on website)
- Delivery location finder (search by phone/name → WhatsApp share)
- Contact form with Laravel Mail (recipient configurable)
- Dual-guard auth (admin + member can be logged in simultaneously)

### ⏳ Placeholder / Demo Features
- **Payment gateway:** "Coming soon InshaAllah" toast on payment button — no actual bKash/Nagad integration yet
- **Payment history:** Shows demo data with a banner explaining real data will appear after gateway integration
- **OTP:** Hardcoded `9999` — real SMS OTP not yet implemented
- **Filament SuperAdmin panel:** Exists in code but unused (legacy from initial scaffolding)

### 🔧 Technical Notes
- No `.env` in repo (gitignored) — must copy from `.env.example` and run `key:generate`
- `SESSION_DRIVER=file` is the default (config/session.php) to avoid needing the sessions table; `database` driver also works (migration exists)
- Images stored in `public/uploads/*` (no `storage:link` needed) — directories auto-created on first upload
- The `MemberSessionCookie` middleware must run before `StartSession` (registered via `$middleware->prepend()`)
- Admin login uses `Auth::guard('web')` explicitly to avoid member-guard interference

---

## 🤖 AI Context Prompt

> **If you're an AI assistant reading this:** You now have full context of the ChowdhuryBari project. Key things to know:
>
> 1. **This is a Laravel 11 project** (NOT Next.js, NOT Node.js) — use PHP/Blade conventions
> 2. **All user-facing text is in Bengali** — preserve this in any UI changes
> 3. **Two auth guards** — `web` (admin) and `member` (building owners) with separate sessions
> 4. **Member login** is via `buildings.owner_phone` + OTP `9999` (not the users table directly)
> 5. **Billing** = `per_family_amount × billing_family_count` + service charges (per_family/per_floor/fixed)
> 6. **Social value** = `avg(stars) × 10` — admin rates anonymously, top 10 shown on website
> 7. **Design philosophy:** decent/premium, NOT too colorful — emerald + amber accents on white/slate
> 8. **When making changes:** follow existing patterns (singleton models with `cached()`, `@csrf` in forms, Alpine.js for interactivity, Tailwind CDN)
> 9. **Commit style:** descriptive messages explaining what + why
> 10. **Always run `php artisan migrate`** after pulling if there are new migrations

---

## 📝 Recent Work History (latest first)

1. **4 UX improvements** — upload auto-refresh, creative star modal, simplified flat toggles, trimmed floating buttons
2. **Bug fixes** — 403 on admin dashboard (IsAdmin middleware), image-not-focusable (hidden required input)
3. **Building-owner phone login** + per-family/per-floor/fixed charge types
4. **Separate admin/member sessions** (member guard + MemberSessionCookie middleware)
5. **Family reduction application system** + আমার বাড়ি tab (read-only building details)
6. **Social value system** — member uploads, admin anonymous rating, top-10 leaderboard on website
7. **Building-type-based service charges** (4 categories: tin_shed, ≤4floor, >4floor, shop)
8. **Member dashboard redesign** — decent/premium, less colorful + সেবা চার্জ admin module
9. **Member login (phone + OTP 9999)** + premium dashboard with 4 tabs
10. **Delivery location finder** — search by phone/name, WhatsApp share
11. **Admin modules:** About Us, Get In Touch (with mail), Navigation & Footer settings
12. **Removed:** classifieds section, calendar/events section, double footer bug
13. **Public site:** notice board (DB-driven), gallery (DB-driven + lightbox), about (DB-driven)

---

*Last updated: July 2026 · Repository: https://github.com/sajidchowdhury/ChowdhuryBari*
