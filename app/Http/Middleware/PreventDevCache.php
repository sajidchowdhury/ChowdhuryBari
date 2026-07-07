<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Prevents the browser from caching responses in local/dev environments.
 *
 * Problem: after `git pull` + server restart, the browser often shows
 * stale cached HTML/CSS/JS. This middleware sets Cache-Control: no-store
 * on all responses when APP_ENV=local, forcing the browser to re-fetch
 * every request.
 *
 * In production (APP_ENV=production), this middleware is a no-op —
 * the browser caches normally for performance.
 */
class PreventDevCache
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (app()->environment('local', 'testing')) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }

        return $response;
    }
}
