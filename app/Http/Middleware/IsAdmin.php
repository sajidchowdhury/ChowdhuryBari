<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Not logged in at all → go to admin login
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();

        // Logged in but not an admin → log them out + redirect to admin login
        // (this handles the case where a member/user-type session leaks into
        //  the web guard — e.g. after the member/web session split)
        if ($user->role !== 'admin') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'অ্যাডমিন প্যানেলে প্রবেশের অনুমতি নেই। অ্যাডমিন হিসেবে লগইন করুন।']);
        }

        return $next($request);
    }
}