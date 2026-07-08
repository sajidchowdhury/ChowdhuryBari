<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Flat;
use App\Models\Meter;
use App\Models\Road;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Handles building, flat, and meter management.
 *
 * FLOW (per owner spec):
 *   1. Admin creates a road
 *   2. Admin adds buildings to a road (with floor_count + families_per_floor)
 *   3. Flats are AUTO-GENERATED when a building is created (Floor N - Flat X)
 *   4. Per-floor meter list: each floor shows its flats with empty meter slots
 *   5. Single "Add Meters for Floor X" button → bulk-enter meter numbers
 *   6. Per-flat resident info (name + phone) editable inline
 *   7. Single "Check BPDB ↗" link in sidebar (external, no forms)
 *   8. Ad-hoc "Add Flat/Family" button for garage/rooftop units
 */
class BuildingController extends Controller
{
    /**
     * Building detail page — shows flats grouped by floor + meter management.
     */
    public function show(Building $building)
    {
        $building->load(['road', 'flats.meters']);

        return view('admin.buildings.show', compact('building'));
    }

    /**
     * Store a new building + AUTO-GENERATE flats based on floor_count × families_per_floor.
     */
    public function store(Request $request, Road $road)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'owner_name'        => ['required', 'string', 'max:255'],
            'owner_phone'       => ['required', 'string', 'max:255'],
            'caretaker_name'    => ['nullable', 'string', 'max:255'],
            'caretaker_phone'   => ['nullable', 'string', 'max:255'],
            'structure_type'    => ['required', 'in:building,tin_shed,other'],
            'usage_type'        => ['required', 'in:residential,shop,mixed'],
            'building_category' => ['required', 'in:tin_shed,below_or_equal_4_floor,above_4_floor,shop'],
            'floor_count'       => ['required', 'integer', 'min:1', 'max:50'],
            'families_per_floor'=> ['required', 'integer', 'min:1', 'max:20'],
            'has_security'      => ['nullable', 'boolean'],
            'has_cleaning'      => ['nullable', 'boolean'],
            'google_lt'         => ['nullable', 'string', 'max:255'],
            'google_ln'         => ['nullable', 'string', 'max:255'],
            'extra_information' => ['nullable', 'string'],
            'image'             => ['nullable', 'file', 'image', 'max:5120'],
        ]);

        $validated['road_id'] = $road->id;
        $validated['has_security'] = $request->boolean('has_security');
        $validated['has_cleaning'] = $request->boolean('has_cleaning');

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('buildings', 'public');
        }

        unset($validated['image']);

        $building = Building::create($validated);

        // AUTO-GENERATE FLATS — "Floor N - Flat X" naming
        $created = $building->generateFlats();

        return redirect()->route('admin.buildings.show', $building)
            ->with('status', "Building '{$building->name}' created with {$created} flats ({$building->floor_count} floors × {$building->families_per_floor} families/floor).");
    }

    /**
     * Update building info + regenerate flats if floor_count/families_per_floor changed.
     */
    public function update(Request $request, Building $building)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'owner_name'        => ['required', 'string', 'max:255'],
            'owner_phone'       => ['required', 'string', 'max:255'],
            'caretaker_name'    => ['nullable', 'string', 'max:255'],
            'caretaker_phone'   => ['nullable', 'string', 'max:255'],
            'structure_type'    => ['required', 'in:building,tin_shed,other'],
            'usage_type'        => ['required', 'in:residential,shop,mixed'],
            'building_category' => ['required', 'in:tin_shed,below_or_equal_4_floor,above_4_floor,shop'],
            'floor_count'       => ['required', 'integer', 'min:1', 'max:50'],
            'families_per_floor'=> ['required', 'integer', 'min:1', 'max:20'],
            'has_security'      => ['nullable', 'boolean'],
            'has_cleaning'      => ['nullable', 'boolean'],
            'google_lt'         => ['nullable', 'string', 'max:255'],
            'google_ln'         => ['nullable', 'string', 'max:255'],
            'extra_information' => ['nullable', 'string'],
            'image'             => ['nullable', 'file', 'image', 'max:5120'],
        ]);

        $validated['has_security'] = $request->boolean('has_security');
        $validated['has_cleaning'] = $request->boolean('has_cleaning');

        $floorCountChanged = (int)$validated['floor_count'] !== $building->floor_count
            || (int)$validated['families_per_floor'] !== $building->families_per_floor;

        if ($request->hasFile('image')) {
            if ($building->image_path) {
                Storage::disk('public')->delete($building->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('buildings', 'public');
        }

        unset($validated['image']);
        $building->update($validated);

        // If floor_count or families_per_floor changed, auto-generate any new flats
        // (existing flats are NOT deleted — they keep their data)
        if ($floorCountChanged) {
            $newFlats = $building->generateFlats();
            if ($newFlats > 0) {
                session()->flash('status', "Building updated. {$newFlats} new flat(s) auto-generated.");
            } else {
                session()->flash('status', 'Building updated.');
            }
        } else {
            session()->flash('status', 'Building updated.');
        }

        return redirect()->route('admin.buildings.show', $building);
    }

    public function destroy(Building $building)
    {
        $name = $building->name;
        $building->delete();

        return redirect()->route('admin.our-area')
            ->with('status', "Building '{$name}' deleted.");
    }

    /**
     * Add an ad-hoc flat/family (for garage, rooftop, etc.).
     */
    public function storeFlat(Request $request, Building $building)
    {
        $validated = $request->validate([
            'flat_number'    => ['required', 'string', 'max:255'],
            'floor_number'   => ['nullable', 'integer', 'min:0'],
            'resident_name'  => ['nullable', 'string', 'max:255'],
            'resident_phone' => ['nullable', 'string', 'max:255'],
            'notes'          => ['nullable', 'string'],
        ]);

        $validated['building_id'] = $building->id;
        $validated['is_active'] = true;

        Flat::create($validated);

        return redirect()->route('admin.buildings.show', $building)
            ->with('status', "Flat/Family '{$validated['flat_number']}' added.");
    }

    /**
     * Update a flat — resident info, active status, etc.
     */
    public function updateFlat(Request $request, Flat $flat)
    {
        $validated = $request->validate([
            'flat_number'    => ['sometimes', 'string', 'max:255'],
            'floor_number'   => ['sometimes', 'nullable', 'integer', 'min:0'],
            'resident_name'  => ['sometimes', 'nullable', 'string', 'max:255'],
            'resident_phone' => ['sometimes', 'nullable', 'string', 'max:255'],
            'is_active'      => ['sometimes', 'boolean'],
            'vacated_at'     => ['sometimes', 'nullable', 'date'],
            'notes'          => ['sometimes', 'nullable', 'string'],
        ]);

        if (isset($validated['is_active'])) {
            $validated['vacated_at'] = $validated['is_active'] ? null : now();
        }

        $flat->update($validated);

        return redirect()->route('admin.buildings.show', $flat->building_id)
            ->with('status', 'Flat updated.');
    }

    public function destroyFlat(Flat $flat)
    {
        $buildingId = $flat->building_id;
        $name = $flat->flat_number;
        $flat->delete();

        return redirect()->route('admin.buildings.show', $buildingId)
            ->with('status', "Flat '{$name}' deleted.");
    }

    /**
     * Bulk-add meters for all flats on a floor at once.
     * Receives: floor_number + array of [flat_id => meter_number]
     */
    public function storeFloorMeters(Request $request, Building $building)
    {
        $validated = $request->validate([
            'floor_number'           => ['required', 'integer', 'min:0'],
            'meters'                 => ['required', 'array'],
            'meters.*.flat_id'       => ['required', 'exists:flats,id'],
            'meters.*.meter_number'  => ['nullable', 'string', 'max:255', 'unique:meters,meter_number'],
            'meters.*.provider'      => ['nullable', 'in:bpdb,desco,other'],
        ]);

        $created = 0;
        $skipped = 0;
        foreach ($validated['meters'] as $entry) {
            if (empty($entry['meter_number'])) {
                $skipped++;
                continue;
            }

            // Skip if the flat already has a meter with this number
            $exists = Meter::where('flat_id', $entry['flat_id'])
                ->where('meter_number', $entry['meter_number'])
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            Meter::create([
                'flat_id'      => $entry['flat_id'],
                'meter_number' => $entry['meter_number'],
                'provider'     => $entry['provider'] ?? 'bpdb',
                'is_active'    => true,
            ]);
            $created++;
        }

        return redirect()->route('admin.buildings.show', $building)
            ->with('status', "Floor {$validated['floor_number']}: {$created} meter(s) added, {$skipped} skipped.");
    }

    /**
     * Add a single meter to a flat (used for ad-hoc flats).
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

    public function destroyMeter(Meter $meter)
    {
        $buildingId = $meter->flat->building_id;
        $number = $meter->meter_number;
        $meter->delete();

        return redirect()->route('admin.buildings.show', $buildingId)
            ->with('status', "Meter '{$number}' deleted.");
    }

    /**
     * Record a single meter recharge (manual entry).
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
}
