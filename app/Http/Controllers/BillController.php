<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Building;
use App\Models\FamilyCountHistory;
use App\Models\FieldDataCollection;
use App\Models\Road;
use App\Models\ServiceCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{
    /**
     * Show the bill management page — select road, select month, load buildings.
     */
    public function index(Request $request)
    {
        $roads = Road::with('buildings')->orderBy('name')->get();
        $categories = Building::CATEGORIES;

        // Get selected filters
        $selectedRoad = $request->query('road_id');
        $selectedMonth = $request->query('billing_month', Bill::currentMonthKey());

        // Get buildings for selected road
        $buildings = collect();
        if ($selectedRoad) {
            $buildings = Building::where('road_id', $selectedRoad)
                ->with(['road', 'flats'])
                ->orderBy('name')
                ->get();
        }

        // Get existing bills for these buildings for the selected month
        $existingBills = collect();
        if ($buildings->isNotEmpty()) {
            $existingBills = Bill::where('billing_month', $selectedMonth)
                ->whereIn('building_id', $buildings->pluck('id'))
                ->get()
                ->keyBy('building_id');
        }

        // Get field data family counts for buildings
        $fieldDataCounts = [];
        if ($buildings->isNotEmpty()) {
            $fieldData = FieldDataCollection::where('road_id', $selectedRoad)
                ->where('status', 'migrated')
                ->get();

            foreach ($fieldData as $fd) {
                if ($fd->migrated_building_id) {
                    $fieldDataCounts[$fd->migrated_building_id] = $fd->expected_families;
                }
            }
        }

        // Get security guard bill rates per category from service_charges
        $guardRates = [];
        foreach ($categories as $key => $label) {
            $guardCharge = ServiceCharge::where('is_active', true)
                ->where('building_category', $key)
                ->where('name', 'like', '%guard%')
                ->orWhere(function ($q) use ($key) {
                    $q->where('is_active', true)
                      ->where('building_category', $key)
                      ->where('name', 'like', '%security%');
                })
                ->first();

            if ($guardCharge) {
                $guardRates[$key] = $guardCharge->charge_type === 'fixed'
                    ? $guardCharge->amount
                    : $guardCharge->amount; // simplified — use the amount as the guard bill
            }
        }

        // Generate list of months for the dropdown (last 12 months + next 3)
        $months = [];
        for ($i = -12; $i <= 3; $i++) {
            $date = now()->addMonths($i);
            $key = $date->format('Y-m');
            $months[$key] = $date->format('F Y');
        }

        return view('admin.bills.index', compact(
            'roads', 'categories', 'selectedRoad', 'selectedMonth',
            'buildings', 'existingBills', 'fieldDataCounts', 'guardRates', 'months'
        ));
    }

    /**
     * Save bills for all buildings in the selected road and month.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'billing_month'            => ['required', 'string', 'regex:/^\d{4}-\d{2}$/'],
            'road_id'                  => ['required', 'exists:roads,id'],
            'bills'                    => ['required', 'array'],
            'bills.*.building_id'      => ['required', 'exists:buildings,id'],
            'bills.*.actual_family_count' => ['required', 'integer', 'min:0'],
            'bills.*.cleaning_rate_per_family' => ['required', 'integer', 'min:0'],
            'bills.*.security_guard_bill' => ['required', 'integer', 'min:0'],
            'bills.*.field_data_family_count' => ['nullable', 'integer', 'min:0'],
            'bills.*.notes'            => ['nullable', 'string', 'max:500'],
        ]);

        $billingMonth = $validated['billing_month'];
        $userId = Auth::id();
        $savedCount = 0;
        $updatedCount = 0;

        foreach ($validated['bills'] as $billData) {
            $buildingId = $billData['building_id'];
            $actualFamilyCount = $billData['actual_family_count'];
            $cleaningRate = $billData['cleaning_rate_per_family'];
            $guardBill = $billData['security_guard_bill'];
            $cleaningTotal = $cleaningRate * $actualFamilyCount;
            $totalBill = $cleaningTotal + $guardBill;
            $fieldDataCount = $billData['field_data_family_count'] ?? 0;

            // Find existing bill or create new
            $bill = Bill::where('building_id', $buildingId)
                ->where('billing_month', $billingMonth)
                ->first();

            $previousFamilyCount = null;

            if ($bill) {
                // Update existing bill
                $previousFamilyCount = $bill->actual_family_count;

                $bill->update([
                    'field_data_family_count'  => $fieldDataCount,
                    'actual_family_count'      => $actualFamilyCount,
                    'cleaning_rate_per_family' => $cleaningRate,
                    'cleaning_total'           => $cleaningTotal,
                    'security_guard_bill'      => $guardBill,
                    'total_bill'               => $totalBill,
                    'notes'                    => $billData['notes'] ?? null,
                ]);
                $updatedCount++;
            } else {
                // Create new bill
                $bill = Bill::create([
                    'building_id'              => $buildingId,
                    'billing_month'            => $billingMonth,
                    'field_data_family_count'  => $fieldDataCount,
                    'actual_family_count'      => $actualFamilyCount,
                    'cleaning_rate_per_family' => $cleaningRate,
                    'cleaning_total'           => $cleaningTotal,
                    'security_guard_bill'      => $guardBill,
                    'total_bill'               => $totalBill,
                    'status'                   => 'pending',
                    'notes'                    => $billData['notes'] ?? null,
                    'created_by'               => $userId,
                ]);
                $savedCount++;

                // For new bills, the previous count is the building's expected family count
                $building = Building::find($buildingId);
                $previousFamilyCount = $building ? $building->expected_family_count : 0;
            }

            // Track family count change history
            if ($previousFamilyCount !== null && $previousFamilyCount != $actualFamilyCount) {
                FamilyCountHistory::create([
                    'building_id'     => $buildingId,
                    'bill_id'         => $bill->id,
                    'previous_count'  => $previousFamilyCount,
                    'new_count'       => $actualFamilyCount,
                    'changed_by'      => $userId,
                    'reason'          => $billData['notes'] ?? 'বিল তৈরি/আপডেটের সময় পরিবার সংখ্যা পরিবর্তন',
                ]);
            }

            // Also update the building's billing_family_count
            $building = Building::find($buildingId);
            if ($building && $building->billing_family_count != $actualFamilyCount) {
                $building->update(['billing_family_count' => $actualFamilyCount]);
            }
        }

        $message = '';
        if ($savedCount > 0 && $updatedCount > 0) {
            $message = "{$savedCount}টি নতুন বিল তৈরি এবং {$updatedCount}টি বিল আপডেট হয়েছে।";
        } elseif ($savedCount > 0) {
            $message = "{$savedCount}টি নতুন বিল তৈরি হয়েছে।";
        } elseif ($updatedCount > 0) {
            $message = "{$updatedCount}টি বিল আপডেট হয়েছে।";
        } else {
            $message = 'কোনো বিল পরিবর্তন হয়নি।';
        }

        return redirect()->route('admin.bills.index', [
            'road_id'       => $validated['road_id'],
            'billing_month' => $billingMonth,
        ])->with('status', $message);
    }

    /**
     * Update a single bill's status (e.g. mark as paid).
     */
    public function updateStatus(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,paid,partial'],
        ]);

        $bill->update($validated);

        return back()->with('status', "বিলের স্ট্যাটাস আপডেট হয়েছে: {$bill->status_label}");
    }

    /**
     * Show family count change history for a building.
     */
    public function history(Building $building)
    {
        $histories = FamilyCountHistory::where('building_id', $building->id)
            ->with(['changer', 'bill'])
            ->latest()
            ->paginate(20);

        $building->load('road');

        return view('admin.bills.history', compact('building', 'histories'));
    }
}
