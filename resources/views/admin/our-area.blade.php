@extends('admin.layout')

@section('title', 'Our Area')
@section('page-title', 'Our Area')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row justify-between gap-4 items-start">
        <div>
            <h1 class="text-3xl font-semibold">Our Area</h1>
            <p class="text-slate-600 mt-2">Filter roads and buildings with quick search and category controls.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <form action="{{ route('admin.our-area') }}" method="GET" class="flex items-center gap-2">
                <label for="filter" class="sr-only">Filter</label>
                <select id="filter" name="filter" class="rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm">
                    <option value="road" @if($filter === 'road') selected @endif>By Road</option>
                    <option value="building" @if($filter === 'building') selected @endif>By Building</option>
                </select>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search road, building or owner" class="rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm w-64">
                <button type="submit" class="rounded-3xl bg-teal-600 px-5 py-3 text-white font-medium hover:bg-teal-700 transition">Search</button>
            </form>

            <button type="button"
    onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'create-road' }))"
    class="rounded-3xl bg-slate-900 px-5 py-3 text-white font-medium hover:bg-slate-700 transition">
    Create Road
</button>
        </div>
    </div>

    @if(session('status'))
        <div class="rounded-3xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        @if($filter === 'road')
            @forelse($roads as $road)
                <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <img src="{{ $road->image_url }}" alt="{{ $road->name }}" class="h-56 w-full object-cover">
                    <div class="p-6">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <h2 class="text-xl font-semibold">{{ $road->name }}</h2>
                                @if($road->description)
                                    <p class="text-slate-500 mt-1 text-sm leading-relaxed">{{ $road->description }}</p>
                                @endif
                                @if($road->tag_list)
                                    <div class="mt-3 flex flex-wrap gap-1.5">
                                        @foreach($road->tag_list as $tag)
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-medium text-emerald-700">
                                                {{ $tag }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <span class="rounded-full bg-slate-100 text-slate-700 px-3 py-1 text-xs font-semibold whitespace-nowrap">{{ $road->buildings->count() }} buildings</span>
                        </div>
                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            @foreach($road->buildings as $building)
                                <div class="rounded-3xl bg-slate-50 p-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $building->image_url }}" alt="{{ $building->name }}" class="h-14 w-14 rounded-3xl object-cover">
                                        <div>
                                            <h3 class="font-semibold">{{ $building->name }}</h3>
                                            <p class="text-slate-500 text-sm">{{ $building->building_type }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 text-sm text-slate-600 space-y-2">
                                        <p><strong>Owner:</strong> {{ $building->owner }}</p>
                                        <p><strong>Families:</strong> {{ $building->total_family }}</p>
                                        <p><strong>Services:</strong> {{ implode(', ', $building->service_taking ?? []) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-8 text-slate-600">No roads found. Click "Create Road" to add your first road.</div>
            @endforelse
        @else
            @forelse($buildings as $building)
                <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-6 flex flex-col lg:flex-row gap-6">
                        <img src="{{ $building->image_url }}" alt="{{ $building->name }}" class="h-48 w-full rounded-3xl object-cover lg:w-72">
                        <div class="flex-1">
                            <h2 class="text-xl font-semibold">{{ $building->name }}</h2>
                            <p class="text-slate-500 mt-1">{{ $building->road->name ?? '' }}</p>
                            <div class="mt-5 grid gap-3 sm:grid-cols-2 text-sm text-slate-600">
                                <div><strong>Owner:</strong> {{ $building->owner }}</div>
                                <div><strong>Owner Number:</strong> {{ $building->owner_number }}</div>
                                <div><strong>Floors:</strong> {{ $building->total_floor }}</div>
                                <div><strong>Families:</strong> {{ $building->total_family }}</div>
                                <div><strong>Google LN:</strong> {{ $building->google_ln }}</div>
                                <div><strong>Google LT:</strong> {{ $building->google_lt }}</div>
                                <div class="sm:col-span-2"><strong>Type:</strong> {{ $building->building_type }}</div>
                                <div class="sm:col-span-2"><strong>Extra:</strong> {{ $building->extra_information }}</div>
                            </div>
                            <div class="mt-4 text-sm text-slate-500"><strong>Services:</strong> {{ implode(', ', $building->service_taking ?? []) }}</div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-8 text-slate-600">No buildings found.</div>
            @endforelse
        @endif
    </div>
</div>

<x-modal name="create-road" maxWidth="2xl">
    <div class="bg-white p-6">
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-4">
            <div>
                <h2 class="text-2xl font-semibold">Create Road</h2>
                <p class="text-slate-500 mt-1">Add a road with its details. Buildings are optional.</p>
            </div>
            <button type="button"
                onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'create-road' }))"
                class="text-slate-500 hover:text-slate-900">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('admin.our-area.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 mt-6">
            @csrf

            {{-- ============ ROAD DETAILS ============ --}}
            <div class="rounded-3xl bg-emerald-50/40 border border-emerald-100 p-6 space-y-4">
                <h3 class="text-sm font-semibold text-emerald-800 uppercase tracking-wide flex items-center gap-2">
                    <i class="fas fa-road"></i> Road Information
                </h3>

                <div class="grid gap-4 lg:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Road Name <span class="text-red-500">*</span></label>
                        <input type="text" name="road_name" value="{{ old('road_name') }}" placeholder="e.g. Main Road, East Para Road" class="mt-1.5 w-full rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm">
                        @error('road_name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Road Image</label>
                        <input type="file" name="road_image" accept="image/*" class="mt-1.5 w-full text-sm text-slate-700 file:mr-3 file:rounded-full file:border-0 file:bg-emerald-100 file:px-3 file:py-1.5 file:text-emerald-700 hover:file:bg-emerald-200">
                        @error('road_image')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Short Description</label>
                    <textarea name="road_description" rows="2" maxlength="500" placeholder="One-line description of this road (max 500 chars)" class="mt-1.5 w-full rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm">{{ old('road_description') }}</textarea>
                    @error('road_description')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Tags</label>
                    <input type="text" name="road_tags" value="{{ old('road_tags') }}" placeholder="Comma-separated, e.g. Main Road, CCTV Covered, Cleanest 2024" class="mt-1.5 w-full rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm">
                    <p class="text-xs text-slate-500 mt-1">Separate tags with commas. They'll appear as colored chips on the road card.</p>
                    @error('road_tags')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- ============ BUILDING (OPTIONAL) ============ --}}
            <div class="rounded-3xl bg-slate-50 border border-slate-200 p-6">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div>
                        <h3 class="text-lg font-semibold">Building Information <span class="text-xs font-normal text-slate-500">(optional)</span></h3>
                        <p class="text-slate-500 text-sm">Add a building now, or skip and add buildings later.</p>
                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Building Name</label>
                        <input type="text" name="buildings[0][building_name]" value="{{ old('buildings.0.building_name') }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm">
                        @error('buildings.0.building_name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Owner Name</label>
                        <input type="text" name="buildings[0][owner_name]" value="{{ old('buildings.0.owner_name') }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm">
                        @error('buildings.0.owner_name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Total Floor</label>
                        <input type="number" min="1" name="buildings[0][total_floor]" value="{{ old('buildings.0.total_floor') }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm">
                        @error('buildings.0.total_floor')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Total Family</label>
                        <input type="number" min="0" name="buildings[0][total_family]" value="{{ old('buildings.0.total_family') }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm">
                        @error('buildings.0.total_family')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Building Type</label>
                        <input type="text" name="buildings[0][building_type]" value="{{ old('buildings.0.building_type') }}" placeholder="Residential, Mixed, Commercial" class="mt-1.5 w-full rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm">
                        @error('buildings.0.building_type')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Owner Number</label>
                        <input type="text" name="buildings[0][owner_number]" value="{{ old('buildings.0.owner_number') }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm">
                        @error('buildings.0.owner_number')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Google Ln</label>
                        <input type="text" name="buildings[0][google_ln]" value="{{ old('buildings.0.google_ln') }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm">
                        @error('buildings.0.google_ln')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Google Lt</label>
                        <input type="text" name="buildings[0][google_lt]" value="{{ old('buildings.0.google_lt') }}" class="mt-1.5 w-full rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm">
                        @error('buildings.0.google_lt')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-slate-700">Extra Information</label>
                        <textarea name="buildings[0][extra_information]" rows="2" class="mt-1.5 w-full rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm">{{ old('buildings.0.extra_information') }}</textarea>
                        @error('buildings.0.extra_information')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Service Taking</label>
                        <div class="mt-1.5 flex flex-wrap gap-2">
                            <label class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2 text-sm">
                                <input type="checkbox" name="buildings[0][service_taking][]" value="cleaning" @checked(is_array(old('buildings.0.service_taking')) && in_array('cleaning', old('buildings.0.service_taking')))>Cleaning</label>
                            <label class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2 text-sm">
                                <input type="checkbox" name="buildings[0][service_taking][]" value="security" @checked(is_array(old('buildings.0.service_taking')) && in_array('security', old('buildings.0.service_taking')))>Security</label>
                        </div>
                        @error('buildings.0.service_taking')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Building Image</label>
                        <input type="file" name="buildings[0][building_image]" accept="image/*" class="mt-1.5 w-full text-sm text-slate-700 file:mr-3 file:rounded-full file:border-0 file:bg-slate-200 file:px-3 file:py-1.5 file:text-slate-700 hover:file:bg-slate-300">
                        @error('buildings.0.building_image')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <button type="button"
                    onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'create-road' }))"
                    class="rounded-2xl border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                    Cancel
                </button>
                <button type="submit" class="rounded-2xl bg-teal-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-teal-700 transition">
                    Save Road
                </button>
            </div>
        </form>
    </div>
</x-modal>
@endsection
