<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\FamilyReductionApplication;
use App\Models\Flat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FamilyReductionApplicationController extends Controller
{
    // ============ MEMBER SIDE ============

    /**
     * Member: submit a flat-status change request.
     * Creates a PENDING application — does NOT change flats or billing yet.
     * Admin reviews + approves → then changes take effect.
     * Each save creates a separate application record.
     */
    public function updateFlatStatuses(Request $request)
    {
        $user = Auth::guard('member')->user();
        $building = $user->building;

        if (!$building) {
            return back()->with('app_error', 'আপনার কোনো বাড়ি নিবন্ধিত নেই।');
        }

        $validated = $request->validate([
            'flats'               => ['nullable', 'array'],
            'flats.*'              => ['string'],
            'flat_ids'            => ['required', 'array'],
            'flat_ids.*'          => ['exists:flats,id'],
            'meter_numbers'       => ['nullable', 'array'],
            'meter_numbers.*'     => ['nullable', 'string', 'max:100'],
        ]);

        // Verify all flats belong to this member's building
        $buildingFlatIds = $building->flats()->pluck('id')->toArray();
        foreach ($validated['flat_ids'] as $flatId) {
            if (!in_array($flatId, $buildingFlatIds)) {
                return back()->with('app_error', 'অবৈধ ফ্ল্যাট নির্বাচন।');
            }
        }

        // Build the requested flat states (what the member wants)
        $flatsInput = $validated['flats'] ?? [];
        $requestedStates = [];
        $requestedCount = 0;
        $hasChanges = false;

        foreach ($validated['flat_ids'] as $flatId) {
            $flat = Flat::with('meters')->find($flatId);
            $isActive = isset($flatsInput[$flatId]) && $flatsInput[$flatId] === '1';
            $meterNumber = $validated['meter_numbers'][$flatId] ?? $flat->meters->first()?->meter_number ?? '';
            if (empty($meterNumber)) $meterNumber = '';

            $requestedStates[] = [
                'flat_id'      => (int) $flatId,
                'flat_number'  => $flat->flat_number,
                'active'       => $isActive,
                'meter_number' => $meterNumber,
            ];

            if ($isActive) $requestedCount++;

            // Check if this is actually a change from current state
            if ($flat->is_active !== $isActive) {
                $hasChanges = true;
            }
        }

        if (!$hasChanges) {
            return redirect()->route('member.dashboard', ['tab' => 'building'])
                ->with('app_error', 'কোনো পরিবর্তন করা হয়নি। সুইচ পরিবর্তন করে আবার চেষ্টা করুন।');
        }

        $currentCount = $building->effective_billing_family_count;

        // Create application — flats and billing DO NOT change yet
        FamilyReductionApplication::create([
            'user_id'                => $user->id,
            'building_id'            => $building->id,
            'current_family_count'   => $currentCount,
            'requested_family_count' => $requestedCount,
            'vacant_flat_ids'        => array_column(array_filter($requestedStates, fn($s) => !$s['active']), 'flat_id'),
            'requested_flat_states'  => $requestedStates,
            'reason'                 => "সদস্য পরিবার সংখ্যা আপডেটের অনুরোধ করেছেন: {$currentCount} → {$requestedCount} পরিবার।",
            'status'                 => 'pending',
        ]);

        return redirect()->route('member.dashboard', ['tab' => 'building'])
            ->with('app_success', "আপনার আবেদন জমা হয়েছে! অনুরোধ করা পরিবার: {$requestedCount}টি (বর্তমান: {$currentCount}টি)। অ্যাডমিন মিটার নম্বর যাচাই করে অনুমোদন দিলে আপনার বিল আপডেট হবে।");
    }

    /**
     * Member: submit a family-reduction application (legacy — kept for admin review flow).
     */
    public function store(Request $request)
    {
        $user = Auth::guard('member')->user();
        $building = $user->building;

        if (!$building) {
            return back()->with('app_error', 'আপনার কোনো বাড়ি নিবন্ধিত নেই।');
        }

        $validated = $request->validate([
            'requested_family_count' => ['required', 'integer', 'min:0', 'max:' . $building->expected_family_count],
            'vacant_flat_ids'         => ['nullable', 'array'],
            'vacant_flat_ids.*'       => ['exists:flats,id'],
            'reason'                  => ['required', 'string', 'max:1000'],
        ]);

        // Prevent duplicate pending applications for the same building
        $existingPending = FamilyReductionApplication::where('building_id', $building->id)
            ->where('status', 'pending')
            ->exists();
        if ($existingPending) {
            return back()->with('app_error', 'আপনার একটি আবেদন ইতিমধ্যে অপেক্ষমাণ অবস্থায় আছে। অ্যাডমিন রিভিউ করার পর নতুন আবেদন করতে পারবেন।');
        }

        FamilyReductionApplication::create([
            'user_id'                => $user->id,
            'building_id'            => $building->id,
            'current_family_count'   => $building->effective_billing_family_count,
            'requested_family_count' => $validated['requested_family_count'],
            'vacant_flat_ids'        => $validated['vacant_flat_ids'] ?? [],
            'reason'                 => $validated['reason'],
            'status'                 => 'pending',
        ]);

        return back()->with('app_success', 'আপনার আবেদন জমা হয়েছে। অ্যাডমিন রিভিউ করে আপডেট দেবেন।');
    }

    // ============ ADMIN SIDE ============

    /**
     * Admin: list all applications (filterable by status).
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $query = FamilyReductionApplication::with(['building.road', 'user', 'reviewer']);

        if (in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        $applications = $query->latest()->get();

        $counts = [
            'pending'  => FamilyReductionApplication::where('status', 'pending')->count(),
            'approved' => FamilyReductionApplication::where('status', 'approved')->count(),
            'rejected' => FamilyReductionApplication::where('status', 'rejected')->count(),
        ];

        return view('admin.applications.index', compact('applications', 'status', 'counts'));
    }

    /**
     * Admin: show a single application with building details (flats + meters)
     * so admin can check which meters are active before approving.
     */
    public function show(FamilyReductionApplication $application)
    {
        $application->load(['building.road', 'building.flats.meters', 'user', 'reviewer']);
        return view('admin.applications.show', compact('application'));
    }

    /**
     * Admin: approve an application → set building.billing_family_count.
     */
    public function approve(Request $request, FamilyReductionApplication $application)
    {
        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($application->status !== 'pending') {
            return back()->with('error', 'এই আবেদনটি ইতিমধ্যে প্রক্রিয়া করা হয়েছে।');
        }

        $application->update([
            'status'       => 'approved',
            'admin_notes'  => $validated['admin_notes'] ?? null,
            'reviewed_by'  => Auth::guard('web')->id(),
            'reviewed_at'  => now(),
        ]);

        // Apply the requested flat states to the actual flats
        if (!empty($application->requested_flat_states)) {
            foreach ($application->requested_flat_states as $state) {
                Flat::where('id', $state['flat_id'])->update([
                    'is_active' => $state['active'],
                ]);
            }
        }

        // Update billing_family_count to the approved requested count
        $application->building->update([
            'billing_family_count' => $application->requested_family_count,
        ]);

        return redirect()->route('admin.applications.index')
            ->with('status', "আবেদন অনুমোদিত। ফ্ল্যাট স্ট্যাটাস ও বিলিং আপডেট হয়েছে। বিলিং পরিবার: {$application->requested_family_count}");
    }

    /**
     * Admin: reject an application.
     */
    public function reject(Request $request, FamilyReductionApplication $application)
    {
        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($application->status !== 'pending') {
            return back()->with('error', 'এই আবেদনটি ইতিমধ্যে প্রক্রিয়া করা হয়েছে।');
        }

        $application->update([
            'status'       => 'rejected',
            'admin_notes'  => $validated['admin_notes'] ?? null,
            'reviewed_by'  => Auth::guard('web')->id(),
            'reviewed_at'  => now(),
        ]);

        return redirect()->route('admin.applications.index')
            ->with('status', 'আবেদন প্রত্যাখ্যাত।');
    }

    /**
     * Admin: manually override the billing family count for a building.
     * Used when admin discovers the actual family count differs from what
     * the member reported (e.g. member forgot to report new families).
     * This bypasses the application system entirely.
     */
    public function overrideBilling(Request $request, Building $building)
    {
        $validated = $request->validate([
            'billing_family_count' => ['required', 'integer', 'min:0', 'max:' . $building->expected_family_count],
            'override_reason'      => ['nullable', 'string', 'max:500'],
        ]);

        $oldCount = $building->billing_family_count ?? $building->getActiveFamilyCount();

        $building->update([
            'billing_family_count' => $validated['billing_family_count'],
        ]);

        return back()->with('status', "বিলিং পরিবার সংখ্যা ম্যানুয়ালি আপডেট হয়েছে: {$oldCount} → {$validated['billing_family_count']}");
    }
}
