<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\FieldDataCollection;
use App\Models\Flat;
use App\Models\Meter;
use App\Models\Road;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FieldDataController extends Controller
{
    /**
     * Dashboard — summary + list of DRAFT field data only.
     *
     * Records whose status is 'migrated' are intentionally EXCLUDED from the
     * list — once a field-data record has been pushed into the real
     * buildings/flats/meters tables, it should be edited from the Buildings
     * section, not here. Editing a migrated record here would silently
     * diverge from the migrated Building row.
     *
     * The migrated count is still surfaced in the summary cards so the user
     * can see that their previous migrations succeeded.
     */
    public function index()
    {
        // List only drafts — these are the ones still editable/pending migration.
        $collections = FieldDataCollection::with(['road', 'collector'])
            ->where('status', 'draft')
            ->latest()
            ->get();

        $roads = Road::orderBy('name')->get();

        // Summary stats — computed across ALL records (drafts + migrated)
        // so the dashboard still reflects historical totals.
        $allRecords = FieldDataCollection::all();
        $totalBuildings = $allRecords->count();
        $totalFlats = $allRecords->sum(fn($c) => $c->flat_count);
        $totalMeters = $allRecords->sum(fn($c) => $c->meter_count);
        $draftCount = $allRecords->where('status', 'draft')->count();
        $migratedCount = $allRecords->where('status', 'migrated')->count();
        $roadsCovered = $allRecords->pluck('road_id')->filter()->unique()->count();

        return view('admin.field-data.index', compact(
            'collections', 'roads', 'totalBuildings', 'totalFlats', 'totalMeters',
            'draftCount', 'migratedCount', 'roadsCovered'
        ));
    }

    /**
     * Show the data collection form (mobile-friendly, creative).
     */
    public function create()
    {
        $roads = Road::orderBy('name')->get();
        return view('admin.field-data.create', compact('roads'));
    }

    /**
     * Store a new field data collection.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'road_id'              => ['nullable', 'exists:roads,id'],
            'new_road_name'        => ['nullable', 'string', 'max:255'],
            'building_name'        => ['required', 'string', 'max:255'],
            'owner_name'           => ['required', 'string', 'max:255'],
            'owner_phone'          => ['required', 'string', 'max:255'],
            'caretaker_name'       => ['nullable', 'string', 'max:255'],
            'caretaker_phone'      => ['nullable', 'string', 'max:255'],
            'building_category'    => ['nullable', 'in:tin_shed,below_or_equal_4_floor,above_4_floor,shop'],
            'structure_type'       => ['nullable', 'in:building,tin_shed,other'],
            'usage_type'           => ['nullable', 'in:residential,shop,mixed'],
            'floor_count'          => ['required', 'integer', 'min:1', 'max:50'],
            'families_per_floor'   => ['required', 'integer', 'min:1', 'max:100'],
            'has_security'         => ['nullable', 'boolean'],
            'has_cleaning'         => ['nullable', 'boolean'],
            'google_lt'            => ['nullable', 'string', 'max:255'],
            'google_ln'            => ['nullable', 'string', 'max:255'],
            'extra_information'    => ['nullable', 'string'],
            'image'                => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'flats_data'           => ['nullable', 'string'], // JSON string from the form
        ]);

        // Must have either road_id or new_road_name
        if (empty($validated['road_id']) && empty($validated['new_road_name'])) {
            return back()->withInput()->withErrors(['road_id' => 'রাস্তা নির্বাচন করুন বা নতুন রাস্তার নাম দিন।']);
        }

        // Handle building image upload — use the 'public' disk so the path
        // works with Storage::disk('public')->url() (same as BuildingController).
        // This is critical: when this field-data is migrated to a Building row,
        // the Building model's getImageUrlAttribute() also uses the 'public'
        // disk, so the path MUST be relative to storage/app/public/.
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('field-data', 'public');
        }

        // Decode flats_data JSON
        $flatsData = [];
        if (!empty($validated['flats_data'])) {
            $decoded = json_decode($validated['flats_data'], true);
            if (is_array($decoded)) {
                $flatsData = $decoded;
            }
        }

        FieldDataCollection::create([
            'road_id'              => $validated['road_id'] ?: null,
            'new_road_name'        => $validated['new_road_name'] ?: null,
            'building_name'        => $validated['building_name'],
            'owner_name'           => $validated['owner_name'],
            'owner_phone'          => $validated['owner_phone'],
            'caretaker_name'       => $validated['caretaker_name'] ?? null,
            'caretaker_phone'      => $validated['caretaker_phone'] ?? null,
            'building_category'    => $validated['building_category'] ?? null,
            'structure_type'       => $validated['structure_type'] ?? 'building',
            'usage_type'           => $validated['usage_type'] ?? 'residential',
            'floor_count'          => $validated['floor_count'],
            'families_per_floor'   => $validated['families_per_floor'],
            'has_security'         => $request->boolean('has_security'),
            'has_cleaning'         => $request->boolean('has_cleaning'),
            'google_lt'            => $validated['google_lt'] ?? null,
            'google_ln'            => $validated['google_ln'] ?? null,
            'extra_information'    => $validated['extra_information'] ?? null,
            'image_path'           => $imagePath,
            'flats_data'           => $flatsData,
            'status'               => 'draft',
            'collected_by'         => Auth::guard('web')->id(),
        ]);

        return redirect()->route('admin.field-data.index')
            ->with('status', "বিল্ডিং '{$validated['building_name']}' এর তথ্য সংরক্ষিত হয়েছে!");
    }

    /**
     * Show edit form.
     *
     * Only draft records can be edited. Once a record has been migrated to
     * the real buildings table, it must be edited from the Buildings section
     * — otherwise the field-data row and the migrated Building row would
     * silently diverge.
     */
    public function edit(FieldDataCollection $fieldData)
    {
        if ($fieldData->status === 'migrated') {
            return redirect()
                ->route('admin.field-data.index')
                ->with('error', "এই ডাটা ইতিমধ্যে মাইগ্রেট হয়েছে — এডিট করা যাবে না। বিল্ডিং সেকশন থেকে এডিট করুন।");
        }

        $roads = Road::orderBy('name')->get();
        return view('admin.field-data.edit', compact('fieldData', 'roads'));
    }

    /**
     * Update a field data collection.
     *
     * Only drafts are editable. A migrated record is immutable from this
     * controller — edit the migrated Building row instead.
     */
    public function update(Request $request, FieldDataCollection $fieldData)
    {
        if ($fieldData->status === 'migrated') {
            return redirect()
                ->route('admin.field-data.index')
                ->with('error', "এই ডাটা ইতিমধ্যে মাইগ্রেট হয়েছে — আপডেট করা যাবে না।");
        }

        $validated = $request->validate([
            'road_id'              => ['nullable', 'exists:roads,id'],
            'new_road_name'        => ['nullable', 'string', 'max:255'],
            'building_name'        => ['required', 'string', 'max:255'],
            'owner_name'           => ['required', 'string', 'max:255'],
            'owner_phone'          => ['required', 'string', 'max:255'],
            'caretaker_name'       => ['nullable', 'string', 'max:255'],
            'caretaker_phone'      => ['nullable', 'string', 'max:255'],
            'building_category'    => ['nullable', 'in:tin_shed,below_or_equal_4_floor,above_4_floor,shop'],
            'structure_type'       => ['nullable', 'in:building,tin_shed,other'],
            'usage_type'           => ['nullable', 'in:residential,shop,mixed'],
            'floor_count'          => ['required', 'integer', 'min:1', 'max:50'],
            'families_per_floor'   => ['required', 'integer', 'min:1', 'max:100'],
            'has_security'         => ['nullable', 'boolean'],
            'has_cleaning'         => ['nullable', 'boolean'],
            'google_lt'            => ['nullable', 'string', 'max:255'],
            'google_ln'            => ['nullable', 'string', 'max:255'],
            'extra_information'    => ['nullable', 'string'],
            'image'                => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'flats_data'           => ['nullable', 'string'],
        ]);

        // Handle image — use the 'public' disk for consistency with store()
        // and with BuildingController. The stored path is relative to
        // storage/app/public/ so it works with Storage::disk('public')->url().
        if ($request->hasFile('image')) {
            if ($fieldData->image_path) {
                // Delete the old file from the public disk (ignore failures —
                // the file may have been stored via the legacy File::move path
                // and won't exist on the public disk).
                if (Storage::disk('public')->exists($fieldData->image_path)) {
                    Storage::disk('public')->delete($fieldData->image_path);
                } elseif (file_exists(public_path($fieldData->image_path))) {
                    @unlink(public_path($fieldData->image_path));
                }
            }
            $fieldData->image_path = $request->file('image')->store('field-data', 'public');
        }

        $flatsData = [];
        if (!empty($validated['flats_data'])) {
            $decoded = json_decode($validated['flats_data'], true);
            if (is_array($decoded)) $flatsData = $decoded;
        }

        $fieldData->update([
            'road_id'              => $validated['road_id'] ?: null,
            'new_road_name'        => $validated['new_road_name'] ?: null,
            'building_name'        => $validated['building_name'],
            'owner_name'           => $validated['owner_name'],
            'owner_phone'          => $validated['owner_phone'],
            'caretaker_name'       => $validated['caretaker_name'] ?? null,
            'caretaker_phone'      => $validated['caretaker_phone'] ?? null,
            'building_category'    => $validated['building_category'] ?? null,
            'structure_type'       => $validated['structure_type'] ?? 'building',
            'usage_type'           => $validated['usage_type'] ?? 'residential',
            'floor_count'          => $validated['floor_count'],
            'families_per_floor'   => $validated['families_per_floor'],
            'has_security'         => $request->boolean('has_security'),
            'has_cleaning'         => $request->boolean('has_cleaning'),
            'google_lt'            => $validated['google_lt'] ?? null,
            'google_ln'            => $validated['google_ln'] ?? null,
            'extra_information'    => $validated['extra_information'] ?? null,
            'flats_data'           => $flatsData,
        ]);

        return redirect()->route('admin.field-data.index')
            ->with('status', "বিল্ডিং '{$fieldData->building_name}' আপডেট হয়েছে!");
    }

    /**
     * Delete a field data collection (only if not migrated).
     */
    public function destroy(FieldDataCollection $fieldData)
    {
        if ($fieldData->status === 'migrated') {
            return back()->with('error', 'মাইগ্রেট করা ডাটা মুছা যায় না।');
        }

        if ($fieldData->image_path) {
            if (Storage::disk('public')->exists($fieldData->image_path)) {
                Storage::disk('public')->delete($fieldData->image_path);
            } elseif (file_exists(public_path($fieldData->image_path))) {
                @unlink(public_path($fieldData->image_path));
            }
        }
        $name = $fieldData->building_name;
        $fieldData->delete();

        return back()->with('status', "'{$name}' মুছে ফেলা হয়েছে।");
    }

    /**
     * Migrate ALL draft field data into the real buildings/flats/meters tables.
     */
    public function migrateAll()
    {
        $drafts = FieldDataCollection::where('status', 'draft')->get();

        if ($drafts->isEmpty()) {
            return back()->with('error', 'মাইগ্রেট করার মতো কোনো ড্রাফট ডাটা নেই।');
        }

        $migrated = 0;
        $skipped = 0;

        foreach ($drafts as $draft) {
            // Resolve or create the road
            $roadId = $draft->road_id;
            if (!$roadId && $draft->new_road_name) {
                $road = Road::firstOrCreate(['name' => $draft->new_road_name]);
                $roadId = $road->id;
            }
            if (!$roadId) {
                $skipped++;
                continue;
            }

            // Create the building
            $building = Building::create([
                'road_id'            => $roadId,
                'name'               => $draft->building_name,
                'owner_name'         => $draft->owner_name,
                'owner_phone'        => $draft->owner_phone,
                'caretaker_name'     => $draft->caretaker_name,
                'caretaker_phone'    => $draft->caretaker_phone,
                'structure_type'     => $draft->structure_type,
                'usage_type'         => $draft->usage_type,
                'building_category'  => $draft->building_category,
                'floor_count'        => $draft->floor_count,
                'families_per_floor' => $draft->families_per_floor,
                'has_security'       => $draft->has_security,
                'has_cleaning'       => $draft->has_cleaning,
                'google_lt'          => $draft->google_lt,
                'google_ln'          => $draft->google_ln,
                'extra_information'  => $draft->extra_information,
                'image_path'         => $draft->image_path,
            ]);

            // Create flats + meters from flats_data
            foreach ($draft->flats_data ?? [] as $flatData) {
                $flat = Flat::create([
                    'building_id'    => $building->id,
                    'flat_number'    => $flatData['flat_number'] ?? ('Floor ' . ($flatData['floor'] ?? 1)),
                    'floor_number'   => $flatData['floor'] ?? 1,
                    'resident_name'  => $flatData['resident_name'] ?? null,
                    'resident_phone' => $flatData['resident_phone'] ?? null,
                    'is_active'      => true,
                ]);

                if (!empty($flatData['meter_number'])) {
                    Meter::create([
                        'flat_id'      => $flat->id,
                        'meter_number' => $flatData['meter_number'],
                        'provider'     => $flatData['provider'] ?? 'bpdb',
                        'is_active'    => true,
                    ]);
                }
            }

            // Mark as migrated
            $draft->update([
                'status'               => 'migrated',
                'migrated_at'          => now(),
                'migrated_building_id' => $building->id,
            ]);
            $migrated++;
        }

        return back()->with('status', "{$migrated} টি বিল্ডিং সফলভাবে মাইগ্রেট হয়েছে!" . ($skipped > 0 ? " ({$skipped} টি স্কিপ হয়েছে — রাস্তা নেই)" : ''));
    }

    /**
     * Migrate a single field data record.
     */
    public function migrateOne(FieldDataCollection $fieldData)
    {
        if ($fieldData->status === 'migrated') {
            return back()->with('error', 'এই ডাটা ইতিমধ্যে মাইগ্রেট করা হয়েছে।');
        }

        $roadId = $fieldData->road_id;
        if (!$roadId && $fieldData->new_road_name) {
            $road = Road::firstOrCreate(['name' => $fieldData->new_road_name]);
            $roadId = $road->id;
        }
        if (!$roadId) {
            return back()->with('error', 'রাস্তা নেই — মাইগ্রেট করা যায়নি।');
        }

        $building = Building::create([
            'road_id'            => $roadId,
            'name'               => $fieldData->building_name,
            'owner_name'         => $fieldData->owner_name,
            'owner_phone'        => $fieldData->owner_phone,
            'caretaker_name'     => $fieldData->caretaker_name,
            'caretaker_phone'    => $fieldData->caretaker_phone,
            'structure_type'     => $fieldData->structure_type,
            'usage_type'         => $fieldData->usage_type,
            'building_category'  => $fieldData->building_category,
            'floor_count'        => $fieldData->floor_count,
            'families_per_floor' => $fieldData->families_per_floor,
            'has_security'       => $fieldData->has_security,
            'has_cleaning'       => $fieldData->has_cleaning,
            'google_lt'          => $fieldData->google_lt,
            'google_ln'          => $fieldData->google_ln,
            'extra_information'  => $fieldData->extra_information,
            'image_path'         => $fieldData->image_path,
        ]);

        foreach ($fieldData->flats_data ?? [] as $flatData) {
            $flat = Flat::create([
                'building_id'    => $building->id,
                'flat_number'    => $flatData['flat_number'] ?? ('Floor ' . ($flatData['floor'] ?? 1)),
                'floor_number'   => $flatData['floor'] ?? 1,
                'resident_name'  => $flatData['resident_name'] ?? null,
                'resident_phone' => $flatData['resident_phone'] ?? null,
                'is_active'      => true,
            ]);

            if (!empty($flatData['meter_number'])) {
                Meter::create([
                    'flat_id'      => $flat->id,
                    'meter_number' => $flatData['meter_number'],
                    'provider'     => $flatData['provider'] ?? 'bpdb',
                    'is_active'    => true,
                ]);
            }
        }

        $fieldData->update([
            'status'               => 'migrated',
            'migrated_at'          => now(),
            'migrated_building_id' => $building->id,
        ]);

        return back()->with('status', "'{$fieldData->building_name}' সফলভাবে মাইগ্রেট হয়েছে!");
    }
}
