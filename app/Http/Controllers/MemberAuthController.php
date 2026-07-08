<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\User;
use App\Models\MemberUpload;
use App\Models\ServiceCharge;
use App\Services\SocialValueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberAuthController extends Controller
{
    /**
     * Demo OTP — always 9999 for now.
     * Real OTP via SMS will be added later.
     */
    const DEMO_OTP = '9999';

    /** How long a phone-verification token stays valid (minutes). */
    const TOKEN_TTL_MINUTES = 10;

    /**
     * Show the member login form (step 1: phone, step 2: OTP).
     */
    public function showLogin(Request $request)
    {
        if (Auth::guard('member')->check() && Auth::guard('member')->user()->role !== 'admin') {
            return redirect()->route('member.dashboard');
        }

        // Step 2 (OTP) requires a valid phone token in the query string.
        // If it's missing/invalid, fall back to step 1.
        $step = $request->query('step', 'phone');
        $phoneToken = $request->query('phone_token', '');

        $phone = '';
        if ($step === 'otp') {
            try {
                $data = Crypt::decrypt($phoneToken);
                if (!is_array($data) || ($data['expires_at'] ?? 0) < now()->timestamp) {
                    return redirect()->route('member.login')
                        ->withErrors(['phone' => 'সময় শেষ হয়ে গেছে, আবার চেষ্টা করুন।']);
                }
                $phone = $data['phone'] ?? '';
            } catch (\Throwable $e) {
                return redirect()->route('member.login')
                    ->withErrors(['phone' => 'অবৈধ অনুরোধ। আবার চেষ্টা করুন।']);
            }
        }

        return view('member.login', [
            'step' => $step,
            'phone' => $phone,
            'phoneToken' => $phoneToken,
        ]);
    }

    /**
     * Step 1: Validate the phone number belongs to a registered building
     * owner (matched against buildings.owner_phone), then redirect to the
     * OTP step with an encrypted token.
     */
    public function sendOtp(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $phone = $this->normalizePhone($validated['phone']);

        // Look up the building by owner's phone number.
        // Every building owner can log in — no separate user registration needed.
        $building = Building::where('owner_phone', $phone)->first();

        if (!$building) {
            return back()
                ->withInput()
                ->withErrors(['phone' => 'এই নম্বরে কোনো নিবন্ধিত বাড়ি পাওয়া যায়নি। অ্যাডমিন আপনার বাড়ি যোগ করলে লগইন করতে পারবেন।']);
        }

        // Build an encrypted, time-limited token carrying the verified phone.
        // This avoids depending on session persistence between the two steps
        // (which is the common cause of 419 CSRF errors during 2-step logins).
        $token = Crypt::encrypt([
            'phone'      => $phone,
            'expires_at' => now()->addMinutes(self::TOKEN_TTL_MINUTES)->timestamp,
        ]);

        return redirect()->route('member.login', ['step' => 'otp', 'phone_token' => $token]);
    }

    /**
     * Step 2: Verify the OTP (demo: 9999) and log the user in.
     */
    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'otp'         => ['required', 'string', 'size:4'],
            'phone_token' => ['required', 'string'],
        ]);

        // Decrypt + validate the phone token
        try {
            $data = Crypt::decrypt($validated['phone_token']);
            if (!is_array($data) || ($data['expires_at'] ?? 0) < now()->timestamp) {
                return redirect()->route('member.login')
                    ->withErrors(['phone' => 'সময় শেষ হয়ে গেছে, আবার চেষ্টা করুন।']);
            }
            $phone = $data['phone'] ?? '';
        } catch (\Throwable $e) {
            return redirect()->route('member.login')
                ->withErrors(['phone' => 'অবৈধ অনুরোধ। আবার চেষ্টা করুন।']);
        }

        if ($validated['otp'] !== self::DEMO_OTP) {
            return back()
                ->withInput()
                ->withErrors(['otp' => 'ভুল OTP। ডেমো OTP হলো ৯৯৯৯।']);
        }

        // Look up the building by owner phone (verified in step 1)
        $building = Building::where('owner_phone', $phone)->first();
        if (!$building) {
            return redirect()->route('member.login')
                ->withErrors(['phone' => 'সদস্য খুঁজে পাওয়া যায়নি।']);
        }

        // Find or create the user record for this building owner.
        // Every building owner can log in — no separate registration needed.
        $user = User::firstOrCreate(
            ['phone' => $phone],
            [
                'name'       => $building->owner_name ?: ('বাড়ি ' . $building->name),
                'email'      => $phone . '@chowdhuripara.local',
                'password'   => Hash::make(Str::random(32)),
                'role'       => 'user',
                'is_active'  => true,
            ]
        );

        if (!$user->is_active) {
            return redirect()->route('member.login')
                ->withErrors(['phone' => 'আপনার অ্যাকাউন্ট নিষ্ক্রিয় আছে। অ্যাডমিনের সাথে যোগাযোগ করুন।']);
        }

        Auth::guard('member')->login($user, true);
        $request->session()->regenerate();

        return redirect()->route('member.dashboard');
    }

    /**
     * Show the member dashboard.
     */
    public function dashboard(SocialValueService $sv)
    {
        $user = Auth::guard('member')->user();

        if (!$user) {
            return redirect()->route('member.login');
        }

        // Admins have their own panel — redirect there.
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $monthKey = MemberUpload::currentMonthKey();
        $prevMonthKey = MemberUpload::previousMonthKey();

        // Member's current-month uploads
        $myUploads = MemberUpload::where('user_id', $user->id)
            ->where('month_key', $monthKey)
            ->latest()
            ->get();

        // Social value + ranking
        $currentSV = $sv->currentSocialValue($user->id);
        $prevSV    = $sv->previousSocialValue($user->id);
        $rank      = $sv->rankFor($user->id);
        $totalRanked = $sv->totalRankedMembers();

        // Best image this month (for the stat card)
        $bestImage = MemberUpload::bestImageFor($user->id, $monthKey);
        $ratedCount = $myUploads->where('star_rating', '!==', null)->count();

        // Service charges — filtered by the member's building category
        $building = $user->building;
        $buildingCategory = $building?->building_category;
        $serviceCharges = $buildingCategory
            ? ServiceCharge::activeForCategory($buildingCategory)
            : collect();
        $totalCharge = $buildingCategory
            ? ServiceCharge::totalForCategory($buildingCategory)
            : 0;

        // Building billing data (for "আমার বাড়ি" tab + due card)
        $buildingFlats = collect();
        $activeFlatCount = 0;
        $expectedFamilyCount = 0;
        $billingFamilyCount = 0;
        $perFamilyAmount = 0;
        $monthlyDue = 0;
        $chargeBreakdown = null;
        $myApplications = collect();
        $hasPendingApplication = false;

        if ($building) {
            $building->load(['road', 'flats.meters']);
            $buildingFlats = $building->flats()->orderBy('floor_number')->orderBy('flat_number')->get();
            $buildingFlats->load('meters');
            $activeFlatCount = $building->getActiveFamilyCount();
            $expectedFamilyCount = $building->expected_family_count;
            $billingFamilyCount = $building->effective_billing_family_count;
            $perFamilyAmount = $building->per_family_amount;
            $monthlyDue = $building->monthlyDue();
            $chargeBreakdown = $building->chargeBreakdown();

            $myApplications = \App\Models\FamilyReductionApplication::where('user_id', $user->id)
                ->where('building_id', $building->id)
                ->latest()
                ->take(10)
                ->get();
            $hasPendingApplication = $myApplications->where('status', 'pending')->isNotEmpty();
        }

        return view('member.dashboard', [
            'user'           => $user,
            'serviceCharges' => $serviceCharges,
            'totalCharge'    => $totalCharge,
            'building'       => $building,
            'buildingCategory' => $buildingCategory,

            // Building billing data
            'buildingFlats'    => $buildingFlats,
            'activeFlatCount'  => $activeFlatCount,
            'expectedFamilyCount' => $expectedFamilyCount,
            'billingFamilyCount' => $billingFamilyCount,
            'perFamilyAmount'  => $perFamilyAmount,
            'monthlyDue'       => $monthlyDue,
            'chargeBreakdown'  => $chargeBreakdown,
            'myApplications'   => $myApplications,
            'hasPendingApplication' => $hasPendingApplication,

            // Gallery / social-value data
            'myUploads'      => $myUploads,
            'uploadLimit'    => SocialValueService::MAX_UPLOADS_PER_MONTH,
            'uploadRemaining'=> SocialValueService::MAX_UPLOADS_PER_MONTH - $myUploads->count(),
            'monthKey'       => $monthKey,

            // Ranking data
            'currentSV'      => $currentSV,
            'prevSV'         => $prevSV,
            'rank'           => $rank,
            'totalRanked'    => $totalRanked,
            'bestImageStars' => $bestImage?->star_rating,
            'ratedCount'     => $ratedCount,
        ]);
    }

    /**
     * Log the member out.
     */
    public function logout(Request $request)
    {
        Auth::guard('member')->logout();
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
