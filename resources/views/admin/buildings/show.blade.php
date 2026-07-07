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
                            {{ ucfirst($building->structure_type) }} • {{ ucfirst($building->usage_type) }} • {{ $building->total_floor }} floor(s)
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                            {{ $building->total_flats }} flats
                        </span>
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                            {{ $building->active_flats }} active
                        </span>
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

    {{-- Flats section --}}
    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between p-6 border-b border-slate-200">
            <div>
                <h3 class="text-lg font-semibold">Flats / Units</h3>
                <p class="text-slate-500 text-sm">Each flat = one family. Add electricity meters to track active families via BPDB recharges.</p>
            </div>
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'add-flat' }))"
                    class="rounded-2xl bg-teal-600 hover:bg-teal-700 px-4 py-2 text-white text-sm font-medium">
                <i class="fas fa-plus mr-1"></i> Add Flat
            </button>
        </div>

        <div class="p-6">
            @if($building->flats->isNotEmpty())
                <div class="space-y-3">
                    @foreach($building->flats as $flat)
                        <div class="rounded-2xl border border-slate-200 p-4">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-semibold">{{ $flat->flat_number }}</h4>
                                        @if($flat->floor_number)
                                            <span class="text-xs text-slate-500">Floor {{ $flat->floor_number }}</span>
                                        @endif
                                        @php($badge = $flat->status_badge)
                                        @if($badge === 'active')
                                            <span class="text-[10px] bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">Active</span>
                                        @elseif($badge === 'vacated')
                                            <span class="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Vacated</span>
                                        @else
                                            <span class="text-[10px] bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Meter inactive</span>
                                        @endif
                                    </div>

                                    @if($flat->meters->isNotEmpty())
                                        <div class="mt-3 space-y-2">
                                            @foreach($flat->meters as $meter)
                                                <div class="rounded-2xl bg-slate-50 p-3">
                                                    <div class="flex items-center gap-3 text-sm flex-wrap">
                                                        <i class="fas fa-bolt text-amber-500"></i>
                                                        <span class="font-mono font-semibold">{{ $meter->meter_number }}</span>
                                                        <span class="text-slate-400">({{ strtoupper($meter->provider) }})</span>
                                                        @if($meter->last_recharge_at)
                                                            <span class="text-xs text-slate-500">
                                                                Last: ৳{{ $meter->last_recharge_amount }} on {{ $meter->last_recharge_at->format('M d, Y') }}
                                                            </span>
                                                        @else
                                                            <span class="text-xs text-red-500">No recharge recorded</span>
                                                        @endif
                                                    </div>
                                                    <div class="mt-2 flex items-center gap-3 flex-wrap">
                                                        @if($meter->provider === 'bpdb')
                                                            <a href="https://web.bpdbprepaid.gov.bd/bn/token-check" target="_blank" rel="noopener"
                                                               class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-medium"
                                                               title="Open BPDB token-check page in a new tab (you'll solve the CAPTCHA there)">
                                                                <i class="fas fa-external-link-alt"></i> Open BPDB ↗
                                                            </a>
                                                            <span class="text-slate-300">|</span>
                                                        @endif
                                                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'quick-entry-{{ $meter->id }}' }))"
                                                                class="inline-flex items-center gap-1 text-xs text-emerald-700 hover:text-emerald-900 font-medium"
                                                                title="Quickly enter the 3 recharge tokens you see on the BPDB site">
                                                            <i class="fas fa-bolt"></i> Quick Entry (3 tokens)
                                                        </button>
                                                        <span class="text-slate-300">|</span>
                                                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'record-recharge-{{ $meter->id }}' }))"
                                                                class="inline-flex items-center gap-1 text-xs text-teal-600 hover:text-teal-800 font-medium">
                                                            <i class="fas fa-plus-circle"></i> Single Recharge
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-xs text-slate-500 mt-2">No meter added. <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'add-meter-{{ $flat->id }}' }))" class="text-teal-600 hover:underline font-medium">Add meter →</button></p>
                                    @endif
                                </div>

                                <div class="flex flex-col gap-1">
                                    @if($flat->is_active)
                                        <form action="{{ route('admin.flats.update', $flat) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="is_active" value="0">
                                            <button type="submit" class="text-xs text-red-600 hover:text-red-800" title="Mark as vacated">
                                                <i class="fas fa-times-circle"></i> Mark Vacated
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.flats.update', $flat) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="is_active" value="1">
                                            <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-800" title="Mark as active">
                                                <i class="fas fa-check-circle"></i> Mark Active
                                            </button>
                                        </form>
                                    @endif
                                    @if($flat->meters->isEmpty())
                                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'add-meter-{{ $flat->id }}' }))"
                                                class="text-xs text-teal-600 hover:text-teal-800">
                                            <i class="fas fa-bolt"></i> Add Meter
                                        </button>
                                    @endif
                                    <form action="{{ route('admin.flats.destroy', $flat) }}" method="POST" class="inline" onsubmit="return confirm('Delete flat {{ $flat->flat_number }}? This will also delete its meters.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Add Meter modal for this flat --}}
                        <x-modal name="add-meter-{{ $flat->id }}" maxWidth="md">
                            <div class="bg-white p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold">Add Meter to Flat {{ $flat->flat_number }}</h3>
                                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'add-meter-{{ $flat->id }}' }))" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
                                </div>
                                <form action="{{ route('admin.meters.store', $flat) }}" method="POST" class="space-y-3">
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700">Meter Number <span class="text-red-500">*</span></label>
                                        <input type="text" name="meter_number" required class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700">Provider</label>
                                        <select name="provider" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm bg-white">
                                            <option value="bpdb">BPDB</option>
                                            <option value="desco">DESCO</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700">Last Recharge Amount (optional)</label>
                                        <input type="number" name="last_recharge_amount" step="0.01" min="0" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700">Last Recharge Date (optional)</label>
                                        <input type="date" name="last_recharge_at" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
                                    </div>
                                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'add-meter-{{ $flat->id }}' }))" class="rounded-2xl border border-slate-300 px-4 py-2 text-sm">Cancel</button>
                                        <button type="submit" class="rounded-2xl bg-teal-600 hover:bg-teal-700 px-4 py-2 text-sm text-white">Add Meter</button>
                                    </div>
                                </form>
                            </div>
                        </x-modal>

                        {{-- Record Recharge modal for each meter --}}
                        @foreach($flat->meters as $meter)
                            <x-modal name="record-recharge-{{ $meter->id }}" maxWidth="md">
                                <div class="bg-white p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h3 class="text-lg font-semibold">Record Single Recharge</h3>
                                            <p class="text-xs text-slate-500">Meter: {{ $meter->meter_number }} (Flat {{ $flat->flat_number }})</p>
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
                                            <label class="block text-sm font-medium text-slate-700">Notes (optional)</label>
                                            <textarea name="notes" rows="2" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm"></textarea>
                                        </div>
                                        <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                                            <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'record-recharge-{{ $meter->id }}' }))" class="rounded-2xl border border-slate-300 px-4 py-2 text-sm">Cancel</button>
                                            <button type="submit" class="rounded-2xl bg-teal-600 hover:bg-teal-700 px-4 py-2 text-sm text-white">Record</button>
                                        </div>
                                    </form>
                                </div>
                            </x-modal>

                            {{-- Quick Entry modal — enter 3 tokens at once (from BPDB site) --}}
                            <x-modal name="quick-entry-{{ $meter->id }}" maxWidth="2xl">
                                <div class="bg-white p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h3 class="text-lg font-semibold">Quick Recharge Entry — 3 Tokens</h3>
                                            <p class="text-xs text-slate-500">Meter: <span class="font-mono font-semibold">{{ $meter->meter_number }}</span> (Flat {{ $flat->flat_number }})</p>
                                        </div>
                                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'quick-entry-{{ $meter->id }}' }))" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
                                    </div>

                                    <div class="rounded-2xl bg-blue-50 border border-blue-200 p-3 mb-4 text-sm text-blue-800">
                                        <div class="flex items-start gap-2">
                                            <i class="fas fa-info-circle mt-0.5"></i>
                                            <div>
                                                <p class="font-medium">How to use this:</p>
                                                <ol class="list-decimal ml-4 mt-1 text-xs space-y-0.5">
                                                    <li>Click <a href="https://web.bpdbprepaid.gov.bd/bn/token-check" target="_blank" rel="noopener" class="text-blue-700 underline font-medium">this link to open BPDB's token-check page</a> (opens in new tab)</li>
                                                    <li>Solve the CAPTCHA + enter meter number <span class="font-mono">{{ $meter->meter_number }}</span></li>
                                                    <li>BPDB shows the last 3 recharge tokens</li>
                                                    <li>Copy the amounts + dates into the form below</li>
                                                    <li>Click "Save 3 Tokens" — done!</li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>

                                    <form action="{{ route('admin.meters.bulk-readings', $meter) }}" method="POST" class="space-y-3">
                                        @csrf
                                        <div class="space-y-2">
                                            @for($i = 0; $i < 3; $i++)
                                                <div class="grid grid-cols-12 gap-2 items-end">
                                                    <div class="col-span-1">
                                                        <label class="block text-xs font-medium text-slate-500">#{{ $i + 1 }}</label>
                                                    </div>
                                                    <div class="col-span-5">
                                                        <label class="block text-xs font-medium text-slate-700">Token Number</label>
                                                        <input type="text" name="readings[{{ $i }}][token_number]" class="mt-1 w-full rounded-2xl border border-slate-300 px-3 py-2 text-sm font-mono" placeholder="e.g. 5123406789012345">
                                                    </div>
                                                    <div class="col-span-3">
                                                        <label class="block text-xs font-medium text-slate-700">Amount (৳)</label>
                                                        <input type="number" name="readings[{{ $i }}][recharge_amount]" step="0.01" min="0" class="mt-1 w-full rounded-2xl border border-slate-300 px-3 py-2 text-sm" placeholder="500">
                                                    </div>
                                                    <div class="col-span-3">
                                                        <label class="block text-xs font-medium text-slate-700">Date</label>
                                                        <input type="date" name="readings[{{ $i }}][recharged_at]" class="mt-1 w-full rounded-2xl border border-slate-300 px-3 py-2 text-sm">
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>

                                        <p class="text-xs text-slate-500">Leave a row empty to skip it (if BPDB shows fewer than 3 tokens).</p>

                                        <div class="flex justify-between items-center pt-3 border-t border-slate-200">
                                            <a href="https://web.bpdbprepaid.gov.bd/bn/token-check" target="_blank" rel="noopener"
                                               class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 font-medium">
                                                <i class="fas fa-external-link-alt"></i> Open BPDB ↗
                                            </a>
                                            <div class="flex gap-2">
                                                <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'quick-entry-{{ $meter->id }}' }))" class="rounded-2xl border border-slate-300 px-4 py-2 text-sm">Cancel</button>
                                                <button type="submit" class="rounded-2xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2 text-sm text-white font-medium">
                                                    <i class="fas fa-bolt mr-1"></i> Save Tokens
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </x-modal>
                        @endforeach
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-door-open text-4xl text-slate-300 mb-3"></i>
                    <p class="text-slate-500">No flats added yet.</p>
                    <p class="text-slate-400 text-sm mt-1">Click "Add Flat" to create the first one.</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Add Flat modal --}}
<x-modal name="add-flat" maxWidth="md">
    <div class="bg-white p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Add Flat to {{ $building->name }}</h3>
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'add-flat' }))" class="text-slate-400 hover:text-slate-700"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.flats.store', $building) }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700">Flat Number <span class="text-red-500">*</span></label>
                <input type="text" name="flat_number" required placeholder="e.g. A-1, 2nd Floor Left" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Floor Number</label>
                <input type="number" name="floor_number" min="0" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Notes (optional)</label>
                <textarea name="notes" rows="2" class="mt-1.5 w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'add-flat' }))" class="rounded-2xl border border-slate-300 px-4 py-2 text-sm">Cancel</button>
                <button type="submit" class="rounded-2xl bg-teal-600 hover:bg-teal-700 px-4 py-2 text-sm text-white">Add Flat</button>
            </div>
        </form>
    </div>
</x-modal>
@endsection
