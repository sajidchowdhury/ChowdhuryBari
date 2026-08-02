# Buildings, Flats & Active Family Detection — Design

> **Status:** Design document for the building/flat/meter system
> **Owner goal:** Each road has multiple buildings; each building has multiple families/flats; we need to dynamically detect which families are *actually active* (not vacated) so service charges are billed correctly.

---

## 1. The core problem

A building has 10 flats. 10 families live there in January. In February, 1 family moves out — their flat is now empty. The society charges a monthly service fee per family. If they bill for 10 families in February, they overcharge the building owner by 1 family.

**How do we know if a family is still there?**

The owner's insight: **BPDB prepaid electricity meter recharges**. If a flat's meter was recharged in the last 30 days, someone is living there (paying for electricity = occupying the flat). If the meter hasn't been recharged in 60+ days, the flat is likely vacated.

---

## 2. Data model

```
Road (1) ───< Building (N) ───< Flat (N) ───< Meter (1-2)
                                       └───< MeterReading (N)  [monthly snapshots]
```

### 2.1 Road (existing, updated)
- id, name, image_path, description, tags (JSON), timestamps

### 2.2 Building (updated)
| Field | Type | Notes |
|-------|------|-------|
| id | id | |
| road_id | FK → roads | |
| name | string | e.g. "Block A-1", "House 12" |
| owner_name | string | |
| owner_phone | string | |
| caretaker_name | string, nullable | যত্নকারী/পাহারাদার |
| caretaker_phone | string, nullable | |
| structure_type | enum: `building`, `tin_shed`, `other` | |
| usage_type | enum: `residential`, `shop`, `mixed` | |
| total_floor | int | |
| google_lt | string, nullable | latitude |
| google_ln | string, nullable | longitude |
| extra_information | text, nullable | |
| image_path | string, nullable | |
| timestamps | | |

**Note:** `service_taking` (security/cleaning) is moved to the **Flat** level — each flat pays for services individually, not the whole building. This makes billing accurate.

Wait — re-reading the owner's spec: "service taking [security guard, cleaning]" is listed under building. Let me keep it at the building level for now (the building as a whole receives security/cleaning service). Per-flat service charges are computed from active flat count × per-flat rate.

Actually, to keep it simple and match the owner's spec: **service_taking stays on Building** (does this building get security? cleaning?). The **active family count** determines the bill amount.

### 2.3 Flat (NEW)
Each flat/unit in a building. One family lives in one flat.

| Field | Type | Notes |
|-------|------|-------|
| id | id | |
| building_id | FK → buildings | |
| flat_number | string | e.g. "A-1", "2nd Floor Left", "Flat 3B" |
| floor_number | int, nullable | which floor |
| is_active | bool, default true | manual override — secretary can mark vacated |
| vacated_at | date, nullable | when the family moved out |
| notes | text, nullable | |
| timestamps | | |

### 2.4 Meter (NEW)
Each flat has 1 (sometimes 2) electricity meter. The meter number is the key we use to check BPDB recharges.

| Field | Type | Notes |
|-------|------|-------|
| id | id | |
| flat_id | FK → flats | |
| meter_number | string, unique | e.g. "BPDB-123456789" |
| provider | enum: `bpdb`, `desco`, `other` | |
| is_active | bool, default true | |
| last_checked_at | timestamp, nullable | when we last queried BPDB |
| last_recharge_amount | decimal, nullable | from BPDB API or manual entry |
| last_recharge_at | timestamp, nullable | when the last recharge happened |
| timestamps | | |

### 2.5 MeterReading (NEW) — monthly snapshot
Records each monthly check so we have history.

| Field | Type | Notes |
|-------|------|-------|
| id | id | |
| meter_id | FK → meters | |
| reading_date | date | the month this snapshot is for |
| recharge_amount | decimal, nullable | null = no recharge that month |
| recharged_at | timestamp, nullable | |
| source | enum: `manual`, `bpdb_api` | how we got this data |
| notes | text, nullable | |
| timestamps | | |

---

## 3. Active family detection algorithm

For a given building, count active families:

```php
public function getActiveFamilyCount(): int
{
    return $this->flats()
        ->where('is_active', true)
        ->where(function ($query) {
            // Flat is considered active if:
            // (a) is_active = true (manual override), AND
            // (b) the flat's meter was recharged in the last 45 days
            $query->whereHas('meters', function ($meterQuery) {
                $meterQuery->where('last_recharge_at', '>=', now()->subDays(45));
            })
            ->orWhereDoesntHave('meters'); // flats without meters are assumed active
        })
        ->count();
}
```

**Why 45 days?** BPDB prepaid meters typically need recharging every 30 days. 45 days gives a 15-day grace period (residents might recharge a few days late).

**Flats without meters:** assumed active (we can't prove they're vacated). The secretary should add meters to all flats eventually.

---

## 4. BPDB API integration — pragmatic approach

### The reality
- **No official public API** from BPDB/DESCO for prepaid meter recharge history
- The `prepaid.desco.org.bd` site requires login (per-meter credentials)
- Scraping is fragile (CAPTCHA, IP bans, ToS issues)

### What we build instead

#### Phase A (now) — Manual entry + smart UX
- Secretary collects monthly BPDB bill from each flat (or building owner)
- Enters `recharge_amount` + `recharged_at` for each meter via admin panel
- System auto-updates `is_active` on the flat based on the 45-day rule
- **This works today, no API needed**

#### Phase B (later, optional) — BPDB sync attempt
- A `php artisan meters:sync-bpdb` command that tries to fetch recharge data
- Uses a pluggable `MeterDataProvider` interface
- Default implementation: `ManualProvider` (does nothing, just returns last manual entry)
- Future implementation: `BpdbScrapingProvider` (attempts to scrape, gracefully fails)
- If sync fails for a meter → falls back to last manual entry, logs a warning

```php
interface MeterDataProvider
{
    public function getLastRecharge(string $meterNumber): ?array;
    // returns ['amount' => 500, 'recharged_at' => Carbon, 'source' => 'bpdb_api'] or null
}
```

#### Phase C (future) — Resident self-report
- Each family can log in to a member portal
- They upload a photo of their BPDB bill or enter the recharge amount
- System updates the meter reading automatically

### Why this is better than betting on a scraper

1. **Works from day 1** — no dependency on a fragile API
2. **Graceful degradation** — if BPDB API is down/blocked, system keeps working with manual data
3. **Audit trail** — every meter reading has a `source` (manual/api) and timestamp
4. **Future-proof** — when BPDB eventually offers an official API (they will), we just add a new `MeterDataProvider` implementation
5. **Legal safety** — no scraping gray area

---

## 5. Admin UI flow

### 5.1 Our Area page (`/admin/our-area`)
- List of roads (cards with image, description, tags, building count)
- Each road card has:
  - **"Add Building"** button
  - List of buildings under this road (clickable → building detail page)

### 5.2 Building detail page (`/admin/buildings/{id}`)
- Building info (editable)
- **Flats list** with active/vacated status
- **"Add Flat"** button
- Each flat shows:
  - Flat number, floor
  - Meter number(s) + last recharge date
  - Active/Vacated badge
  - **"Add Meter"** / **"Record Recharge"** buttons
- Summary: "X of Y flats active" (auto-calculated)

### 5.3 Flat detail / meter management
- Add/edit meter
- Record monthly recharge (amount + date)
- View recharge history (line chart)

---

## 6. Implementation phases

### Phase 6A (this commit) — Foundation
- Migrations: update buildings, create flats, meters, meter_readings
- Models with relationships
- Updated admin Our Area UI (create road → add buildings)
- Building detail page with flats list
- Add flat / add meter / record recharge forms

### Phase 6B (next) — Active family calculation
- `Building::getActiveFamilyCount()` method
- Display active count on building cards
- Monthly snapshot command (`php artisan meters:snapshot`)

### Phase 6C (future) — BPDB sync
- `MeterDataProvider` interface
- `ManualProvider` (default)
- `BpdbScrapingProvider` (optional, off by default)
- `php artisan meters:sync-bpdb` command

### Phase 6D (future) — Member portal
- Residents log in, see their flat's meter readings
- Self-report recharges
- View their service charge calculations

---

## 7. Open questions for owner

1. **Per-flat service charge rate** — is it fixed (e.g. 300৳/flat/month) or variable per building?
2. **Vacated flat grace period** — 45 days OK, or do you want a different threshold?
3. **Who enters meter readings** — society secretary only, or can building owners do it?
4. **BPDB scraping** — do you want me to attempt the scraper anyway (with graceful fallback), or stick with manual entry for now?
5. **Member portal** — should residents be able to log in and see their own flat's data?

---

## 8. What I'm building in this commit

To keep the change manageable, this commit delivers:

✅ Updated `buildings` migration with new fields (structure_type, usage_type, caretaker, etc.)
✅ New `flats` table
✅ New `meters` table
✅ New `meter_readings` table
✅ Updated models with relationships
✅ Updated admin Our Area UI:
   - Create road (existing) — now without forcing a building
   - Add building to existing road (new)
   - Building detail page with flats list (new)
   - Add flat to building (new)
   - Add meter to flat (new)
   - Record meter recharge (new)

⚠️ NOT in this commit (coming next):
   - Active family count calculation
   - Monthly snapshot command
   - BPDB API integration
   - Member portal
