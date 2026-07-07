@extends('admin.layout')

@section('title', 'Our Area')
@section('page-title', 'Our Area')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row justify-between gap-4 items-start">
        <div>
            <h1 class="text-3xl font-semibold">Our Area</h1>
            <p class="text-slate-600 mt-2">Manage roads, buildings, flats, and electricity meters.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="https://web.bpdbprepaid.gov.bd/bn/token-check" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 rounded-2xl border border-blue-300 bg-blue-50 px-4 py-3 text-blue-700 text-sm font-medium hover:bg-blue-100 transition"
               title="Open BPDB token-check page in a new tab">
                <i class="fas fa-bolt"></i> Check BPDB ↗
            </a>
            <button type="button"
                onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'create-road' }))"
                class="rounded-2xl bg-slate-900 px-5 py-3 text-white font-medium hover:bg-slate-700 transition">
                <i class="fas fa-plus mr-2"></i> Create Road
            </button>
        </div>
    </div>

    @if(session('status'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    {{-- Road list --}}
    @forelse($roads as $road)
        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-emerald-100">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3">
                            @if($road->image_path)
                                <img src="{{ $road->image_url }}" alt="{{ $road->name }}" class="h-14 w-14 rounded-2xl object-cover">
                            @else
                                <div class="h-14 w-14 rounded-2xl bg-emerald-600 text-white flex items-center justify-center">
                                    <i class="fas fa-road text-xl"></i>
                                </div>
                            @endif
                            <div>
                                <h2 class="text-xl font-semibold text-slate-900">{{ $road->name }}</h2>
                                @if($road->description)
                                    <p class="text-slate-600 text-sm mt-0.5">{{ $road->description }}</p>
                                @endif
                                @if($road->tag_list)
                                    <div class="mt-2 flex flex-wrap gap-1">
                                        @foreach($road->tag_list as $tag)
                                            <span class="inline-flex items-center rounded-full bg-white border border-emerald-200 px-2 py-0.5 text-xs font-medium text-emerald-700">
                                                {{ $tag }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="rounded-full bg-white border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">
                            {{ $road->buildings->count() }} buildings
                        </span>
                        <button type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'add-building-{{ $road->id }}' }))"
                            class="rounded-2xl bg-teal-600 hover:bg-teal-700 px-4 py-2 text-white text-sm font-medium transition">
                            <i class="fas fa-plus mr-1"></i> Add Building
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if($road->buildings->isNotEmpty())
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($road->buildings as $building)
                            <a href="{{ route('admin.buildings.show', $building) }}"
                               class="block rounded-2xl border border-slate-200 hover:border-emerald-300 hover:shadow-md transition p-4 group">
                                <div class="flex items-start gap-3">
                                    <img src="{{ $building->image_url }}" alt="{{ $building->name }}" class="h-16 w-16 rounded-2xl object-cover">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-slate-900 group-hover:text-emerald-700">{{ $building->name }}</h3>
                                        <p class="text-xs text-slate-500 mt-0.5">{{ ucfirst($building->structure_type) }} • {{ ucfirst($building->usage_type) }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $building->floor_count }} floor(s) × {{ $building->families_per_floor }} families = {{ $building->total_flats }} flats</p>
                                        <p class="text-xs text-slate-500 mt-0.5">Owner: {{ $building->owner_name }}</p>
                                        <div class="mt-2 flex flex-wrap gap-1">
                                            <span class="text-[10px] bg-slate-100 px-1.5 py-0.5 rounded">{{ $building->active_flats }}/{{ $building->total_flats }} active</span>
                                            @if($building->has_security)
                                                <span class="text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded">Security</span>
                                            @endif
                                            @if($building->has_cleaning)
                                                <span class="text-[10px] bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded">Cleaning</span>
                                            @endif
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-slate-300 group-hover:text-emerald-600 mt-2"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-slate-500 text-sm text-center py-8">
                        No buildings yet. Click <strong>"Add Building"</strong> to create the first one.
                    </p>
                @endif
            </div>
        </div>

        {{-- Add Building modal for this road --}}
        <x-modal name="add-building-{{ $road->id }}" maxWidth="3xl">
            <div class="bg-white p-6">
                <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-4 mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold">Add Building to {{ $road->name }}</h2>
                        <p class="text-slate-500 mt-1 text-sm">Flats will be auto-generated based on floors × families per floor.</p>
                    </div>
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'add-building-{{ $road->id }}' }))" class="text-slate-500 hover:text-slate-900">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form action="{{ route('admin.buildings.store', $road) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Building Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Block A-1, House 12" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Building Image</label>
                            <input type="file" name="image" accept="image/*" class="mt-1.5 w-full text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Owner Name <span class="text-red-500">*</span></label>
                            <input type="text" name="owner_name" value="{{ old('owner_name') }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Owner Phone <span class="text-red-500">*</span></label>
                            <input type="text" name="owner_phone" value="{{ old('owner_phone') }}" placeholder="01XXXXXXXXX" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Caretaker Name</label>
                            <input type="text" name="caretaker_name" value="{{ old('caretaker_name') }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Caretaker Phone</label>
                            <input type="text" name="caretaker_phone" value="{{ old('caretaker_phone') }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Structure Type <span class="text-red-500">*</span></label>
                            <select name="structure_type" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm bg-white" required>
                                <option value="building" @selected(old('structure_type') === 'building')>Building</option>
                                <option value="tin_shed" @selected(old('structure_type') === 'tin_shed')>Tin Shed</option>
                                <option value="other" @selected(old('structure_type') === 'other')>Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Usage Type <span class="text-red-500">*</span></label>
                            <select name="usage_type" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm bg-white" required>
                                <option value="residential" @selected(old('usage_type') === 'residential')>Residential</option>
                                <option value="shop" @selected(old('usage_type') === 'shop')>Shop</option>
                                <option value="mixed" @selected(old('usage_type') === 'mixed')>Mixed</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Number of Floors <span class="text-red-500">*</span></label>
                            <input type="number" name="floor_count" min="1" max="50" value="{{ old('floor_count', 1) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Families per Floor <span class="text-red-500">*</span></label>
                            <input type="number" name="families_per_floor" min="1" max="20" value="{{ old('families_per_floor', 1) }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                        </div>
                        <div class="sm:col-span-2">
                            <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-3 text-xs text-emerald-800">
                                <i class="fas fa-info-circle"></i>
                                <strong>Auto-generation preview:</strong>
                                <span id="auto-flat-preview-{{ $road->id }}">1 floor × 1 family = 1 flat</span>.
                                Flats will be named "Floor 1 - Flat A", "Floor 1 - Flat B", etc.
                                You can add more flats later (garage, rooftop, etc.).
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700">Services</label>
                            <div class="mt-2 flex gap-3">
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="has_security" value="1" @checked(old('has_security')) class="rounded"> Security Guard
                                </label>
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="has_cleaning" value="1" @checked(old('has_cleaning')) class="rounded"> Cleaning
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Google Latitude</label>
                            <input type="text" name="google_lt" value="{{ old('google_lt') }}" placeholder="23.8103" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Google Longitude</label>
                            <input type="text" name="google_ln" value="{{ old('google_ln') }}" placeholder="90.4125" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700">Extra Information</label>
                            <textarea name="extra_information" rows="2" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">{{ old('extra_information') }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'add-building-{{ $road->id }}' }))" class="rounded-2xl border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="rounded-2xl bg-teal-600 hover:bg-teal-700 px-6 py-2.5 text-sm font-medium text-white">Save Building</button>
                    </div>
                </form>

                {{-- Auto-update preview as user types --}}
                <script>
                    (function() {
                        var modal = document.querySelector('[x-data*="add-building-{{ $road->id }}"]');
                        if (!modal) return;
                        var fcInput = modal.querySelector('[name="floor_count"]');
                        var fpfInput = modal.querySelector('[name="families_per_floor"]');
                        var preview = document.getElementById('auto-flat-preview-{{ $road->id }}');
                        function update() {
                            var fc = parseInt(fcInput.value) || 0;
                            var fpf = parseInt(fpfInput.value) || 0;
                            preview.textContent = fc + ' floor(s) × ' + fpf + ' family/floor = ' + (fc * fpf) + ' flat(s)';
                        }
                        fcInput.addEventListener('input', update);
                        fpfInput.addEventListener('input', update);
                        update();
                    })();
                </script>
            </div>
        </x-modal>
    @empty
        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-12 text-center">
            <i class="fas fa-road text-5xl text-slate-300 mb-4"></i>
            <h3 class="text-lg font-semibold text-slate-700">No roads yet</h3>
            <p class="text-slate-500 mt-1">Click "Create Road" to add your first road.</p>
        </div>
    @endforelse

    {{-- Create Road modal (simplified) --}}
    <x-modal name="create-road" maxWidth="2xl">
        <div class="bg-white p-6">
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-4 mb-6">
                <div>
                    <h2 class="text-2xl font-semibold">Create Road</h2>
                    <p class="text-slate-500 mt-1 text-sm">Add a new road. You can add buildings to it afterwards.</p>
                </div>
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'create-road' }))" class="text-slate-500 hover:text-slate-900">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="{{ route('admin.our-area.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Road Name <span class="text-red-500">*</span></label>
                        <input type="text" name="road_name" value="{{ old('road_name') }}" placeholder="e.g. Main Road" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Road Image</label>
                        <input type="file" name="road_image" accept="image/*" class="mt-1.5 w-full text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Short Description</label>
                    <textarea name="road_description" rows="2" maxlength="500" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">{{ old('road_description') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Tags</label>
                    <input type="text" name="road_tags" value="{{ old('road_tags') }}" placeholder="Comma-separated, e.g. Main Road, CCTV, Cleanest 2024" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                    <p class="text-xs text-slate-500 mt-1">Separate tags with commas.</p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'create-road' }))" class="rounded-2xl border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="rounded-2xl bg-teal-600 hover:bg-teal-700 px-6 py-2.5 text-sm font-medium text-white">Save Road</button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
@endsection
