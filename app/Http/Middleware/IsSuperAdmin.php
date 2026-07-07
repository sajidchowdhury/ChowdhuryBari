<?php

namespace App\Http\Middleware;

use App\Models\Central\SuperAdmin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: IsSuperAdmin
 *
 * Protects routes on the CENTRAL domain that should only be accessible
 * to authenticated super admins (the platform owner).
 *
 * Used by panel #4 (Super Admin Filament panel) and any custom
 * super-admin-only routes outside Filament.
 *
 * NOTE: We now use the default 'web' guard (not a custom 'super_admin' guard)
 * because Filament v3 has known issues with custom guards. The 'web' guard's
 * provider is configured to use App\Models\Central\SuperAdmin, so Auth::user()
 * returns a SuperAdmin instance.
 *
 * If the user is not authenticated as a super admin, they are redirected
 * to the super admin login page.
 */
class IsSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('web')->check()) {
            return redirect()->route('filament.super-admin.auth.login');
        }

        $superAdmin = Auth::guard('web')->user();
        if (!$superAdmin instanceof SuperAdmin || !$superAdmin->is_active) {
            Auth::guard('web')->logout();
            return redirect()->route('filament.super-admin.auth.login')
                ->withErrors(['email' => 'Your super admin account has been deactivated.']);
        }

        return $next($request);
    }
}
