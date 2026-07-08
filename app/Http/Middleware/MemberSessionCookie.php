<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Switches the session cookie name for member/* routes so that member
 * sessions are completely independent from admin sessions.
 *
 * This allows a user to be logged in as admin in one browser tab AND as
 * a member in another tab simultaneously — the two sessions use different
 * cookies and never interfere.
 *
 * Must run BEFORE StartSession (registered as a global middleware in
 * bootstrap/app.php) so that StartSession picks up the swapped cookie name.
 */
class MemberSessionCookie
{
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->is('member/*')) {
            $baseCookie = config('session.cookie', 'chowdhurybari_session');
            if (!str_ends_with($baseCookie, '_member')) {
                config(['session.cookie' => $baseCookie . '_member']);
            }
        }

        return $next($request);
    }
}
