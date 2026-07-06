<?php

namespace App\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

/**
 * Tenant — represents ONE society (e.g. ChowdhuryBari, AnotherSociety, ...).
 *
 * Lives in the CENTRAL database's `tenants` table (created by stancl/tenancy).
 * Each tenant row has a UUID `id` and is linked to one or more `domains` rows
 * (e.g. chowdhurypara.app.com → this tenant).
 *
 * The HasDatabase trait gives this tenant its own separate database
 * (configured via TENANCY_DB_* in .env). The HasDomains trait lets us
 * attach subdomains to this tenant.
 *
 * Usage:
 *   $tenant = Tenant::create(['id' => 'chowdhurypara']);
 *   $tenant->domains()->create(['domain' => 'chowdhurypara.chowdhurybari.test']);
 *   $tenant->database->create();  // creates the tenant DB + runs tenant migrations
 */
class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase;
    use HasDomains;

    /**
     * Custom attributes that get persisted to the `data` JSON column
     * on the tenants table. Use this for tenant-specific config like
     * display name, plan, branding, etc.
     */
    protected $data = [
        // 'name'        => 'ChowdhuryBari Society',
        // 'plan'        => 'starter',
        // 'locale'      => 'bn',
        // 'brand_color' => '#065F46',
    ];
}
