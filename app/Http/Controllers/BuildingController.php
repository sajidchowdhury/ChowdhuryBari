<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Flat;
use App\Models\Meter;
use App\Models\Road;
use App\Services\BpdbTokenCheckService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Handles building, flat, and meter management.
 *
 * Routes:
 *   GET    /admin/buildings/{building}          → building detail page (flats + meters)
 *   POST   /admin/roads/{road}/buildings        → add building to a road
 *   GET    /admin/buildings/{building}/edit     → edit building
 *   PUT    /admin/buildings/{building}          → update building
 *   DELETE /admin/buildings/{building}          → delete building
 *
 *   POST   /admin/buildings/{building}/flats    → add flat
 *   PUT    /admin/flats/{flat}                  → update flat (toggle active, etc.)
 *   DELETE /admin/flats/{flat}                  → delete flat
 *
 *   POST   /admin/flats/{flat}/meters           → add meter to flat
 *   DELETE /admin/meters/{meter}                → delete meter
 *
 *   POST   /admin/meters/{meter}/readings       → record meter recharge
 */
class BuildingController extends Controller
{
    /**
     * Building detail page — shows flats + meters.
     */
    public function show(Building $building)
    {
        $building->load(['road', 'flats.meters.readings']);

        return view('admin.buildings.show', compact('building'));
    }

    /**
     * Store a new building under a road.
     */
    public function store(Request $request, Road $road)
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'owner_name'       => ['required', 'string', 'max:255'],
            'owner_phone'      => ['required', 'string', 'max:255'],
            'caretaker_name'   => ['nullable', 'string', 'max:255'],
            'caretaker_phone'  => ['nullable', 'string', 'max:255'],
            'structure_type'   => ['required', 'in:building,tin_shed,other'],
            'usage_type'       => ['required', 'in:residential,shop,mixed'],
            'has_security'     => ['nullable', 'boolean'],
            'has_cleaning'     => ['nullable', 'boolean'],
            'total_floor'      => ['required', 'integer', 'min:0'],
            'google_lt'        => ['nullable', 'string', 'max:255'],
            'google_ln'        => ['nullable', 'string', 'max:255'],
            'extra_information'=> ['nullable', 'string'],
            'image'            => ['nullable', 'file', 'image', 'max:5120'],
        ]);

        $validated['road_id'] = $road->id;
        $validated['has_security'] = $request->boolean('has_security');
        $validated['has_cleaning'] = $request->boolean('has_cleaning');

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('buildings', 'public');
        }

        unset($validated['image']);

        Building::create($validated);

        return redirect()->route('admin.our-area')
            ->with('status', "Building '{$validated['name']}' added to {$road->name}.");
    }

    /**
     * Update an existing building.
     */
    public function update(Request $request, Building $building)
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'owner_name'       => ['required', 'string', 'max:255'],
            'owner_phone'      => ['required', 'string', 'max:255'],
            'caretaker_name'   => ['nullable', 'string', 'max:255'],
            'caretaker_phone'  => ['nullable', 'string', 'max:255'],
            'structure_type'   => ['required', 'in:building,tin_shed,other'],
            'usage_type'       => ['required', 'in:residential,shop,mixed'],
            'has_security'     => ['nullable', 'boolean'],
            'has_cleaning'     => ['nullable', 'boolean'],
            'total_floor'      => ['required', 'integer', 'min:0'],
            'google_lt'        => ['nullable', 'string', 'max:255'],
            'google_ln'        => ['nullable', 'string', 'max:255'],
            'extra_information'=> ['nullable', 'string'],
            'image'            => ['nullable', 'file', 'image', 'max:5120'],
        ]);

        $validated['has_security'] = $request->boolean('has_security');
        $validated['has_cleaning'] = $request->boolean('has_cleaning');

        if ($request->hasFile('image')) {
            if ($building->image_path) {
                Storage::disk('public')->delete($building->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('buildings', 'public');
        }

        unset($validated['image']);
        $building->update($validated);

        return redirect()->route('admin.buildings.show', $building)
            ->with('status', 'Building updated.');
    }

    /**
     * Delete a building (cascades to flats + meters).
     */
    public function destroy(Building $building)
    {
        $name = $building->name;
        $building->delete();

        return redirect()->route('admin.our-area')
            ->with('status', "Building '{$name}' deleted.");
    }

    /**
     * Add a flat to a building.
     */
    public function storeFlat(Request $request, Building $building)
    {
        $validated = $request->validate([
            'flat_number'  => ['required', 'string', 'max:255'],
            'floor_number' => ['nullable', 'integer', 'min:0'],
            'notes'        => ['nullable', 'string'],
        ]);

        $validated['building_id'] = $building->id;

        Flat::create($validated);

        return redirect()->route('admin.buildings.show', $building)
            ->with('status', "Flat '{$validated['flat_number']}' added.");
    }

    /**
     * Toggle flat active status (mark vacated/active).
     */
    public function updateFlat(Request $request, Flat $flat)
    {
        $validated = $request->validate([
            'flat_number'  => ['sometimes', 'string', 'max:255'],
            'floor_number' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'is_active'    => ['sometimes', 'boolean'],
            'vacated_at'   => ['sometimes', 'nullable', 'date'],
            'notes'        => ['sometimes', 'nullable', 'string'],
        ]);

        if (isset($validated['is_active'])) {
            $validated['vacated_at'] = $validated['is_active'] ? null : now();
        }

        $flat->update($validated);

        return redirect()->route('admin.buildings.show', $flat->building_id)
            ->with('status', 'Flat updated.');
    }

    /**
     * Delete a flat.
     */
    public function destroyFlat(Flat $flat)
    {
        $buildingId = $flat->building_id;
        $name = $flat->flat_number;
        $flat->delete();

        return redirect()->route('admin.buildings.show', $buildingId)
            ->with('status', "Flat '{$name}' deleted.");
    }

    /**
     * Add a meter to a flat.
     */
    public function storeMeter(Request $request, Flat $flat)
    {
        $validated = $request->validate([
            'meter_number'      => ['required', 'string', 'max:255', 'unique:meters,meter_number'],
            'provider'          => ['required', 'in:bpdb,desco,other'],
            'last_recharge_amount' => ['nullable', 'numeric', 'min:0'],
            'last_recharge_at'  => ['nullable', 'date'],
        ]);

        $validated['flat_id'] = $flat->id;

        Meter::create($validated);

        return redirect()->route('admin.buildings.show', $flat->building_id)
            ->with('status', "Meter '{$validated['meter_number']}' added.");
    }

    /**
     * Delete a meter.
     */
    public function destroyMeter(Meter $meter)
    {
        $buildingId = $meter->flat->building_id;
        $number = $meter->meter_number;
        $meter->delete();

        return redirect()->route('admin.buildings.show', $buildingId)
            ->with('status', "Meter '{$number}' deleted.");
    }

    /**
     * Record a meter recharge (manual reading entry).
     */
    public function storeReading(Request $request, Meter $meter)
    {
        $validated = $request->validate([
            'recharge_amount' => ['required', 'numeric', 'min:0'],
            'recharged_at'    => ['required', 'date'],
            'notes'           => ['nullable', 'string'],
        ]);

        $meter->recordReading(
            $validated['recharge_amount'],
            $validated['recharged_at'],
            'manual',
            $validated['notes'] ?? null
        );

        return redirect()->route('admin.buildings.show', $meter->flat->building_id)
            ->with('status', "Recharge of ৳{$validated['recharge_amount']} recorded for meter {$meter->meter_number}.");
    }

    /**
     * Record multiple meter recharges at once (Quick Entry from BPDB site).
     *
     * The secretary visits the BPDB token-check page, solves the CAPTCHA,
     * enters the meter number, and sees the last 3 recharge tokens. They
     * paste those 3 amounts + dates into our Quick Entry modal, and this
     * method creates all 3 MeterReading records in one go.
     */
    public function storeBulkReadings(Request $request, Meter $meter)
    {
        $validated = $request->validate([
            'readings'              => ['required', 'array', 'max:3'],
            'readings.*.recharge_amount' => ['required', 'numeric', 'min:0'],
            'readings.*.recharged_at'    => ['required', 'date'],
            'readings.*.token_number'    => ['nullable', 'string', 'max:255'],
        ]);

        $count = 0;
        foreach ($validated['readings'] as $reading) {
            // Skip empty rows
            if (empty($reading['recharge_amount']) && empty($reading['recharged_at'])) {
                continue;
            }

            $rechargedAt = \Carbon\Carbon::parse($reading['recharged_at']);
            $readingDate = $rechargedAt->copy()->startOfMonth();

            $meter->readings()->updateOrCreate(
                ['meter_id' => $meter->id, 'reading_date' => $readingDate->toDateString()],
                [
                    'recharge_amount' => $reading['recharge_amount'],
                    'recharged_at'    => $rechargedAt,
                    'source'          => 'manual',
                    'notes'           => !empty($reading['token_number']) ? "Token: {$reading['token_number']}" : null,
                ]
            );

            $count++;
        }

        // Update the meter's denormalized last_recharge_* fields
        // (use the most recent reading)
        $latestReading = $meter->readings()->latest('recharged_at')->first();
        if ($latestReading) {
            $meter->update([
                'last_recharge_amount' => $latestReading->recharge_amount,
                'last_recharge_at'     => $latestReading->recharged_at,
                'last_checked_at'      => now(),
            ]);
        }

        return redirect()->route('admin.buildings.show', $meter->flat->building_id)
            ->with('status', "{$count} recharge(s) recorded for meter {$meter->meter_number}.");
    }

    /**
     * Sync a single meter from BPDB's token-check portal.
     * NOTE: This will almost always fail due to reCAPTCHA Enterprise on the
     * BPDB site. Kept for the day BPDB offers an official API. Use
     * storeBulkReadings() (Quick Entry) instead.
     */
    public function syncMeter(BpdbTokenCheckService $bpdb, Meter $meter)
    {
        $result = $bpdb->getLastTokens($meter->meter_number);

        if ($result === null) {
            return redirect()->route('admin.buildings.show', $meter->flat->building_id)
                ->with('error', "Could not auto-sync meter {$meter->meter_number} from BPDB (reCAPTCHA blocks automated requests). Please use the 'Quick Entry' button to manually enter the recharge data you see on the BPDB site.");
        }

        // Update the meter's denormalized fields
        $meter->update([
            'last_recharge_amount' => $result['last_recharge_amount'],
            'last_recharge_at'     => $result['last_recharge_at'],
            'last_checked_at'      => now(),
        ]);

        // Create/update readings for each token returned (last 3)
        foreach ($result['tokens'] as $token) {
            if (!$token['recharged_at']) {
                continue;
            }

            $readingDate = $token['recharged_at']->copy()->startOfMonth();

            $meter->readings()->updateOrCreate(
                ['meter_id' => $meter->id, 'reading_date' => $readingDate->toDateString()],
                [
                    'recharge_amount' => $token['amount'],
                    'recharged_at'    => $token['recharged_at'],
                    'source'          => 'bpdb_api',
                    'notes'           => "Token: {$token['token_number']}" . ($token['status'] ? " ({$token['status']})" : ''),
                ]
            );
        }

        $lastAmount = $result['last_recharge_amount'] ? '৳' . $result['last_recharge_amount'] : 'no recharge';
        $lastDate = $result['last_recharge_at'] ? $result['last_recharge_at']->format('M d, Y') : 'unknown';

        return redirect()->route('admin.buildings.show', $meter->flat->building_id)
            ->with('status', "Synced from BPDB! Last recharge: {$lastAmount} on {$lastDate}. " . count($result['tokens']) . " tokens imported.");
    }
}
