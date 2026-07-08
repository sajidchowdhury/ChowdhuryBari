<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberAuthController extends Controller
{
    /**
     * Demo OTP — always 9999 for now.
     * Real OTP via SMS will be added later.
     */
    const DEMO_OTP = '9999';

    /**
     * Show the member login form (step 1: phone, step 2: OTP).
     */
    public function showLogin(Request $request)
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return redirect()->route('member.dashboard');
        }

        return view('member.login', [
            'step' => $request->query('step', 'phone'),
            'phone' => old('phone', $request->query('phone', session('member_login_phone', ''))),
        ]);
    }

    /**
     * Step 1: Validate the phone number exists in the users table,
     * then move to the OTP step.
     */
    public function sendOtp(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $phone = $this->normalizePhone($validated['phone']);

        $user = User::where('phone', $phone)
            ->where('role', '!=', 'admin')
            ->where('is_active', true)
            ->first();

        if (!$user) {
            return back()
                ->withInput()
                ->withErrors(['phone' => 'এই নম্বরে কোনো নিবন্ধিত সদস্য পাওয়া যায়নি।']);
        }

        // Store phone for the OTP step
        session(['member_login_phone' => $phone]);

        return redirect()->route('member.login', ['step' => 'otp']);
    }

    /**
     * Step 2: Verify the OTP (demo: 9999) and log the user in.
     */
    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'otp' => ['required', 'string', 'size:4'],
        ]);

        $phone = session('member_login_phone');
        if (!$phone) {
            return redirect()->route('member.login')
                ->withErrors(['phone' => 'দয়া করে আবার আপনার নম্বর দিন।']);
        }

        if ($validated['otp'] !== self::DEMO_OTP) {
            return back()
                ->withInput()
                ->withErrors(['otp' => 'ভুল OTP। ডেমো OTP হলো ৯৯৯৯।']);
        }

        $user = User::where('phone', $phone)
            ->where('role', '!=', 'admin')
            ->where('is_active', true)
            ->first();

        if (!$user) {
            return redirect()->route('member.login')
                ->withErrors(['phone' => 'সদস্য খুঁজে পাওয়া যায়নি।']);
        }

        Auth::login($user, true);
        session()->forget('member_login_phone');
        $request->session()->regenerate();

        return redirect()->route('member.dashboard');
    }

    /**
     * Show the member dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('member.login');
        }

        // Admins have their own panel — redirect there.
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('member.dashboard', [
            'user' => $user,
        ]);
    }

    /**
     * Log the member out.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * Normalize a Bangladeshi phone number: strip spaces/dashes,
     * convert Bengali digits to English.
     */
    private function normalizePhone(string $phone): string
    {
        $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
        $phone = str_replace($bn, range(0, 9), $phone);
        $phone = preg_replace('/[\s\-()]/', '', $phone);
        return $phone;
    }
}
