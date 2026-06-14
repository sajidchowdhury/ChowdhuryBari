<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        if ($user->role !== 'admin') {
            abort(403, 'আপনার এই প্যানেলে প্রবেশের অনুমতি নেই।');
        }

        return $next($request);
    }
}