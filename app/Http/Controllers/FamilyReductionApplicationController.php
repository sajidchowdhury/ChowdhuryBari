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
     * Member: update which flats/families are active.
     * Simple toggle approach — member marks each flat as active/inactive.
     * When flats are turned OFF, a FamilyReductionApplication is created
     * with the meter numbers so admin can verify on BPDB.
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

        $previousActiveCount = $building->flats()->where('is_active', true)->count();
        $turnedOffFlats = [];
        $activeCount = 0;
        $flatsInput = $validated['flats'] ?? [];

        foreach ($validated['flat_ids'] as $flatId) {
            $flat = \App\Models\Flat::with('meters')->find($flatId);
            $wasActive = $flat->is_active;
            $isActive = isset($flatsInput[$flatId]) && $flatsInput[$flatId] === '1';

            if ($isActive) $activeCount++;

            // Track flats being turned OFF (for application record)
            if ($wasActive && !$isActive) {
                $meterNumber = $validated['meter_numbers'][$flatId] ?? $flat->meters->first()?->meter_number ?? '—';
                if (empty($meterNumber)) $meterNumber = '—';
                $turnedOffFlats[] = [
                    'flat_id'      => $flatId,
                    'flat_number'  => $flat->flat_number,
                    'meter_number' => $meterNumber,
                ];
            }

            $flat->update(['is_active' => $isActive]);
        }

        // Auto-update billing_family_count to match active flats
        $building->update(['billing_family_count' => $activeCount]);

        // Create an application record for admin verification if any flats were turned off
        if (!empty($turnedOffFlats)) {
            FamilyReductionApplication::create([
                'user_id'                => $user->id,
                'building_id'            => $building->id,
                'current_family_count'   => $previousActiveCount,
                'requested_family_count' => $activeCount,
                'vacant_flat_ids'        => array_column($turnedOffFlats, 'flat_id'),
                'reason'                 => 'সদস্য টগল করে ' . count($turnedOffFlats) . 'টি ফ্ল্যাট খালি করেছেন। মিটার নম্বর: ' . implode(', ', array_column($turnedOffFlats, 'meter_number')),
                'status'                 => 'pending',
            ]);
        }

        $msg = "আপডেট সফল! বর্তমানে সক্রিয় পরিবার: {$activeCount}টি।";
        if (!empty($turnedOffFlats)) {
            $msg .= ' অ্যাডমিন মিটার নম্বর যাচাই করে নিশ্চিত করবেন।';
        }

        return redirect()->route('member.dashboard', ['tab' => 'building'])
            ->with('app_success', $msg);
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

        // Set the building's billing family count to the approved amount
        $application->building->update([
            'billing_family_count' => $application->requested_family_count,
        ]);

        return redirect()->route('admin.applications.index')
            ->with('status', "আবেদন অনুমোদিত। বিলিং পরিবার সংখ্যা আপডেট হয়েছে: {$application->requested_family_count}");
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
}
