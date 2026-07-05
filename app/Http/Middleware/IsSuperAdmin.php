<?php

namespace App\Http\Middleware;

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
 * If the user is not authenticated as a super admin, they are redirected
 * to the super admin login page.
 */
class IsSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('super_admin')->check()) {
            return redirect()->route('filament.super-admin.auth.login');
        }

        $superAdmin = Auth::guard('super_admin')->user();
        if (!$superAdmin->is_active) {
            Auth::guard('super_admin')->logout();
            return redirect()->route('filament.super-admin.auth.login')
                ->withErrors(['email' => 'Your super admin account has been deactivated.']);
        }

        return $next($request);
    }
}
