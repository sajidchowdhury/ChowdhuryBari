@extends('admin.layout')

@section('title', 'Building — ' . $building->name)
@section('page-title', $building->name)

@section('content')
<div class="space-y-6">
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('admin.our-area') }}" class="hover:text-emerald-700">Our Area</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <a href="{{ route('admin.our-area') }}" class="hover:text-emerald-700">{{ $building->road->name }}</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-slate-700 font-medium">{{ $building->name }}</span>
    </div>

    @if(session('status'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl bg-red-50 border border-red-200 p-4 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Building info card --}}
    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="grid lg:grid-cols-3 gap-0">
            <div class="lg:col-span-1">
                <img src="{{ $building->image_url }}" alt="{{ $building->name }}" class="w-full h-64 lg:h-full object-cover">
            </div>
            <div class="lg:col-span-2 p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold">{{ $building->name }}</h2>
                        <p class="text-slate-500 text-sm mt-1">
                            {{ ucfirst($building->structure_type) }} • {{ ucfirst($building->usage_type) }} •
                            {{ $building->floor_count }} floor(s) × {{ $building->families_per_floor }} families/floor
                        </p>
                    </div>
                    <div class="flex gap-2 items-center">
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                            {{ $building->total_flats }} flats
                        </span>
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                            {{ $building->active_flats }} active
                        </span>
                        <button type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-building' }))"
                            class="rounded-2xl border border-slate-300 hover:bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700 transition">
                            <i class="fas fa-edit mr-1"></i> Edit Building
                        </button>
                    </div>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2 text-sm">
                    <div><i class="fas fa-user w-4 text-slate-400"></i> <strong>Owner:</strong> {{ $building->owner_name }}</div>
                    <div><i class="fas fa-phone w-4 text-slate-400"></i> <strong>Phone:</strong> {{ $building->owner_phone }}</div>
                    @if($building->caretaker_name)
                        <div><i class="fas fa-user-shield w-4 text-slate-400"></i> <strong>Caretaker:</strong> {{ $building->caretaker_name }}</div>
                        <div><i class="fas fa-phone w-4 text-slate-400"></i> <strong>Caretaker Phone:</strong> {{ $building->caretaker_phone }}</div>
                    @endif
                    @if($building->google_lt && $building->google_ln)
                        <div class="sm:col-span-2"><i class="fas fa-map-marker-alt w-4 text-slate-400"></i> <strong>Location:</strong> {{ $building->google_lt }}, {{ $building->google_ln }}</div>
                    @endif
                </div>

                <div class="mt-4 flex gap-2">
                    @if($building->has_security)
                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full"><i class="fas fa-shield-alt"></i> Security Guard</span>
                    @endif
                    @if($building->has_cleaning)
                        <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full"><i class="fas fa-broom"></i> Cleaning</span>
                    @endif
                </div>

                @if($building->extra_information)
                    <p class="mt-4 text-sm text-slate-600 bg-slate-50 p-3 rounded-2xl">{{ $building->extra_information }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Flats grouped by floor --}}
    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between p-6 border-b border-slate-200">
            <div>
                <h3 class="text-lg font-semibold">Flats by Floor</h3>
                <p class="text-slate-500 text-sm">Auto-generated from {{ $building->floor_count }} floor(s) × {{ $building->families_per_floor }} families/floor. Add meters per floor + edit resident info per flat.</p>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'add-flat' }))"
                        class="rounded-2xl bg-slate-700 hover:bg-slate-800 px-4 py-2 text-white text-sm font-medium">
                    <i class="fas fa-plus mr-1"></i> Add Flat/Family
                </button>
            </div>
        </div>

        <div class="p-6 space-y-6">
            @php($flatsByFloor = $building->flats_by_floor)

            @if(empty($flatsByFloor))
                <div class="text-center py-8">
                    <i class="fas fa-door-open text-4xl text-slate-300 mb-3"></i>
                    <p class="text-slate-500">No flats yet.</p>
                </div>
            @else
                @foreach($flatsByFloor as $floorNumber => $floorFlats)
                    <div class="rounded-2xl border border-slate-200 overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-3 bg-slate-50 border-b border-slate-200">
                            <h4 class="font-semibold text-slate-800">
                                @if($floorNumber == 0)
                                    <i class="fas fa-warehouse text-slate-500 mr-1"></i> Ground / Other
                                @else
                                    <i class="fas fa-layer-group text-emerald-600 mr-1"></i> Floor {{ $floorNumber }}
                                @endif
                                <span class="ml-2 text-xs text-slate-500">({{ count($floorFlats) }} flat(s))</span>
                            </h4>
                            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'add-meters-floor-{{ $floorNumber }}' }))"
                                    class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                <i class="fas fa-bolt"></i> Add Meters for this Floor
                            </button>
                        </div>

                        <div class="divide-y divide-slate-100">
                            @foreach($floorFlats as $flat)
                                <div class="p-4 hover:bg-slate-50/50">
                                    <div class="flex items-start justify-between gap-4 flex-wrap">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-mono font-semibold text-slate-800">{{ $flat->flat_number }}</span>
                                                @php($badge = $flat->status_badge)
                                                @if($badge === 'active')
                                                    <span class="text-[10px] bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">Active</span>
                                                @elseif($badge === 'vacated')
                                                    <span class="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Vacated</span>
                                                @else
                                                    <span class="text-[10px] bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Meter inactive</span>
                                                @endif
                                            </div>

                                            {{-- Resident info (inline editable form) --}}
                                            <form action="{{ route('admin.flats.update', $flat) }}" method="POST" class="mt-2 flex flex-wrap items-center gap-2 text-sm">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="resident_name" value="{{ $flat->resident_name }}" placeholder="Resident name" class="rounded-xl border border-slate-200 px-2 py-1 text-sm w-40">
                                                <input type="text" name="resident_phone" value="{{ $flat->resident_phone }}" placeholder="Phone" class="rounded-xl border border-slate-200 px-2 py-1 text-sm w-32">
                                                <button type="submit" class="text-xs text-teal-600 hover:text-teal-800 font-medium" title="Save resident info">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            </form>

                                            {{-- Meters for this flat --}}
                                            @if($flat->meters->isNotEmpty())
                                                <div class="mt-2 space-y-1">
                                                    @foreach($flat->meters as $meter)
                                                        <div class="flex items-center gap-2 text-xs flex-wrap">
                                                            <i class="fas fa-bolt text-amber-500"></i>
                                                            <span class="font-mono">{{ $meter->meter_number }}</span>
                                                            <span class="text-slate-400">({{ strtoupper($meter->provider) }})</span>
                                                            @if($meter->last_recharge_at)
                                                                <span class="text-slate-500">Last: ৳{{ $meter->last_recharge_amount }} on {{ $meter->last_recharge_at->format('M d, Y') }}</span>
                                                            @else
                                                                <span class="text-red-500">No recharge recorded</span>
                                                            @endif
                                                            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'record-recharge-{{ $meter->id }}' }))"
                                                                    class="ml-2 text-teal-600 hover:text-teal-800 font-medium">
                                                                <i class="fas fa-plus-circle"></i> Recharge
                                                            </button>
                                                            <form action="{{ route('admin.meters.destroy', $meter) }}" method="POST" class="inline" onsubmit="return confirm('Delete meter {{ $meter->meter_number }}?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-red-500 hover:text-red-700">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-xs text-slate-400 mt-2">No meter yet — use "Add Meters for this Floor" above.</p>
                                            @endif
                                        </div>

                                        <div class="flex flex-col gap-1 items-end">
                                            @if($flat->is_active)
                                                <form action="{{ route('admin.flats.update', $flat) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="is_active" value="0">
                                                    <button type="submit" class="text-xs text-red-600 hover:text-red-800">
                                                        <i class="fas fa-times-circle"></i> Mark Vacated
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.flats.update', $flat) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="is_active" value="1">
                                                    <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-800">
                                                        <i class="fas fa-check-circle"></i> Mark Active
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.flats.destroy', $flat) }}" method="POST" onsubmit="return confirm('Delete flat {{ $flat->flat_number }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">
                                                    <i class="fas fa-trash"></i> Delete Flat
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Add Meters for this Floor modal --}}
                    <x-modal name="add-meters-floor-{{ $floorNumber }}" maxWidth="2xl">
                        <div class="bg-white p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold">Add Meters — Floor {{ $floorNumber }}</h3>
                                    <p class="text-xs text-slate-500">{{ $building->name }} • One meter number per flat</p>
                                </div>
                                <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'add-meters-floor-{{ $floorNumber }}' }))" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
                            </div>

                            <div class="rounded-2xl bg-blue-50 border border-blue-200 p-3 mb-4 text-xs text-blue-800">
                                <i class="fas fa-info-circle"></i>
                                Enter the meter number for each flat. Leave blank to skip a flat.
                                Need to look up a meter? <a href="https://web.bpdbprepaid.gov.bd/bn/token-check" target="_blank" rel="noopener" class="underline font-medium">Check BPDB ↗</a>
                            </div>

                            <form action="{{ route('admin.meters.store-floor', $building) }}" method="POST" class="space-y-2">
                                @csrf
                                <input type="hidden" name="floor_number" value="{{ $floorNumber }}">
                                @foreach($floorFlats as $flat)
                                    <div class="grid grid-cols-12 gap-2 items-center">
                                        <label class="col-span-4 text-sm text-slate-700">{{ $flat->flat_number }}</label>
                                        <input type="hidden" name="meters[{{ $loop->index }}][flat_id]" value="{{ $flat->id }}">
                                        <input type="text" name="meters[{{ $loop->index }}][meter_number]" placeholder="Meter number (skip if flat has none)" class="col-span-6 rounded-2xl border border-slate-300 px-3 py-2 text-sm font-mono">
                                        <select name="meters[{{ $loop->index }}][provider]" class="col-span-2 rounded-2xl border border-slate-300 px-2 py-2 text-sm bg-white">
                                            <option value="bpdb">BPDB</option>
                                            <option value="desco">DESCO</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                @endforeach

                                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'add-meters-floor-{{ $floorNumber }}' }))" class="rounded-2xl border border-slate-300 px-4 py-2 text-sm">Cancel</button>
                                    <button type="submit" class="rounded-2xl bg-teal-600 hover:bg-teal-700 px-4 py-2 text-sm text-white font-medium">
                                        <i class="fas fa-save mr-1"></i> Save Meters
                                    </button>
                                </div>
                            </form>
                        </div>
                    </x-modal>
                @endforeach
            @endif
        </div>
    </div>
</div>

{{-- Add Flat/Family modal --}}
<x-modal name="add-flat" maxWidth="md">
    <div class="bg-white p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-semibold">Add Flat / Family</h3>
                <p class="text-xs text-slate-500">For garage, rooftop, or any extra unit not in the auto-generated floors</p>
            </div>
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'add-flat' }))" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.flats.store', $building) }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700">Flat / Unit Name <span class="text-red-500">*</span></label>
                <input type="text" name="flat_number" required placeholder="e.g. Garage, Rooftop Room, Shop 1" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Floor Number</label>
                <input type="number" name="floor_number" min="0" value="0" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                <p class="text-xs text-slate-500 mt-1">Use 0 for ground-level / garage / rooftop.</p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Resident Name</label>
                    <input type="text" name="resident_name" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Resident Phone</label>
                    <input type="text" name="resident_phone" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Notes</label>
                <textarea name="notes" rows="2" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'add-flat' }))" class="rounded-2xl border border-slate-300 px-4 py-2 text-sm">Cancel</button>
                <button type="submit" class="rounded-2xl bg-teal-600 hover:bg-teal-700 px-4 py-2 text-sm text-white">Add Flat</button>
            </div>
        </form>
    </div>
</x-modal>

{{-- Record Recharge modal for each meter --}}
@foreach($building->flats as $flat)
    @foreach($flat->meters as $meter)
        <x-modal name="record-recharge-{{ $meter->id }}" maxWidth="md">
            <div class="bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold">Record Recharge</h3>
                        <p class="text-xs text-slate-500">Meter: <span class="font-mono">{{ $meter->meter_number }}</span> ({{ $flat->flat_number }})</p>
                    </div>
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'record-recharge-{{ $meter->id }}' }))" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
                </div>
                <form action="{{ route('admin.readings.store', $meter) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Recharge Amount (৳) <span class="text-red-500">*</span></label>
                        <input type="number" name="recharge_amount" step="0.01" min="0" required class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Recharge Date <span class="text-red-500">*</span></label>
                        <input type="date" name="recharged_at" required value="{{ date('Y-m-d') }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Notes</label>
                        <textarea name="notes" rows="2" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'record-recharge-{{ $meter->id }}' }))" class="rounded-2xl border border-slate-300 px-4 py-2 text-sm">Cancel</button>
                        <button type="submit" class="rounded-2xl bg-teal-600 hover:bg-teal-700 px-4 py-2 text-sm text-white">Record</button>
                    </div>
                </form>
            </div>
        </x-modal>
    @endforeach
@endforeach

{{-- Edit Building modal --}}
<x-modal name="edit-building" maxWidth="3xl">
    <div class="bg-white p-6">
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-4 mb-6">
            <div>
                <h2 class="text-2xl font-semibold">Edit Building</h2>
                <p class="text-slate-500 mt-1 text-sm">Update building info. Changing floors/families-per-floor will auto-generate new flats (existing flats are kept).</p>
            </div>
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'edit-building' }))" class="text-slate-500 hover:text-slate-900">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="{{ route('admin.buildings.update', $building) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Current image preview --}}
            @if($building->image_path)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Current Image</label>
                    <img src="{{ $building->image_url }}" alt="{{ $building->name }}" class="h-32 w-48 rounded-2xl object-cover border border-slate-200">
                </div>
            @endif

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Building Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $building->name) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Building Image</label>
                    <input type="file" name="image" accept="image/*" class="mt-1.5 w-full text-sm">
                    <p class="text-xs text-slate-500 mt-1">Leave blank to keep current image.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Owner Name <span class="text-red-500">*</span></label>
                    <input type="text" name="owner_name" value="{{ old('owner_name', $building->owner_name) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Owner Phone <span class="text-red-500">*</span></label>
                    <input type="text" name="owner_phone" value="{{ old('owner_phone', $building->owner_phone) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Caretaker Name</label>
                    <input type="text" name="caretaker_name" value="{{ old('caretaker_name', $building->caretaker_name) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Caretaker Phone</label>
                    <input type="text" name="caretaker_phone" value="{{ old('caretaker_phone', $building->caretaker_phone) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Structure Type <span class="text-red-500">*</span></label>
                    <select name="structure_type" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm bg-white" required>
                        <option value="building" @selected($building->structure_type === 'building')>Building</option>
                        <option value="tin_shed" @selected($building->structure_type === 'tin_shed')>Tin Shed</option>
                        <option value="other" @selected($building->structure_type === 'other')>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Usage Type <span class="text-red-500">*</span></label>
                    <select name="usage_type" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm bg-white" required>
                        <option value="residential" @selected($building->usage_type === 'residential')>Residential</option>
                        <option value="shop" @selected($building->usage_type === 'shop')>Shop</option>
                        <option value="mixed" @selected($building->usage_type === 'mixed')>Mixed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Number of Floors <span class="text-red-500">*</span></label>
                    <input type="number" name="floor_count" min="1" max="50" value="{{ old('floor_count', $building->floor_count) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Families per Floor <span class="text-red-500">*</span></label>
                    <input type="number" name="families_per_floor" min="1" max="20" value="{{ old('families_per_floor', $building->families_per_floor) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Services</label>
                    <div class="mt-2 flex gap-3">
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" name="has_security" value="1" @checked(old('has_security', $building->has_security)) class="rounded"> Security Guard
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" name="has_cleaning" value="1" @checked(old('has_cleaning', $building->has_cleaning)) class="rounded"> Cleaning
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Google Latitude</label>
                    <input type="text" name="google_lt" value="{{ old('google_lt', $building->google_lt) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Google Longitude</label>
                    <input type="text" name="google_ln" value="{{ old('google_ln', $building->google_ln) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Extra Information</label>
                    <textarea name="extra_information" rows="2" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">{{ old('extra_information', $building->extra_information) }}</textarea>
                </div>
            </div>

            <div class="flex justify-between items-center pt-4 border-t border-slate-200">
                <button type="button"
                    onclick="deleteBuilding({{ $building->id }}, '{{ $building->name }}')"
                    class="rounded-2xl border border-red-300 text-red-600 hover:bg-red-50 px-4 py-2.5 text-sm font-medium transition">
                    <i class="fas fa-trash mr-1"></i> Delete Building
                </button>
                <div class="flex gap-3">
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'edit-building' }))" class="rounded-2xl border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="rounded-2xl bg-teal-600 hover:bg-teal-700 px-6 py-2.5 text-sm font-medium text-white">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</x-modal>

<script>
    function deleteBuilding(id, name) {
        if (!confirm('Delete building ' + name + '? This will also delete all its flats and meters.')) return;

        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/buildings/' + id;

        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.innerHTML = '<input type="hidden" name="_token" value="' + csrfToken + '">' +
                         '<input type="hidden" name="_method" value="DELETE">';

        document.body.appendChild(form);
        form.submit();
    }
</script>
@endsection
