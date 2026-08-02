@extends('admin.layout')

@section('title', 'বিল ব্যবস্থাপনা')
@section('page-title', 'বিল ব্যবস্থাপনা')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">মাসিক বিল তৈরি ও বরাদ্দ</h2>
            <p class="text-slate-500 text-sm mt-1">রাস্তা ও মাস নির্বাচন করুন, তারপর প্রতিটি বাড়ির জন্য বিল সেট করুন</p>
        </div>
    </div>

    {{-- Status message --}}
    @if(session('status'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-2xl flex items-center gap-2 text-sm">
            <i class="fas fa-check-circle"></i> {{ session('status') }}
        </div>
    @endif

    {{-- Filter Section --}}
    <div class="card p-6">
        <form method="GET" action="{{ route('admin.bills.index') }}" id="filterForm" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">রাস্তা নির্বাচন করুন</label>
                <select name="road_id" id="roadSelect" class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition" required>
                    <option value="">— রাস্তা বাছাই করুন —</option>
                    @foreach($roads as $road)
                        <option value="{{ $road->id }}" {{ $selectedRoad == $road->id ? 'selected' : '' }}>
                            {{ $road->name }} ({{ $road->buildings->count() }}টি বাড়ি)
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">বিলিং মাস</label>
                <select name="billing_month" id="monthSelect" class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition">
                    @foreach($months as $key => $label)
                        <option value="{{ $key }}" {{ $selectedMonth == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-6 py-3 bg-teal-700 hover:bg-teal-800 text-white font-medium rounded-xl text-sm transition flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-search"></i> বাড়ি লোড করুন
            </button>
        </form>
    </div>

    {{-- Building Bills Table --}}
    @if($selectedRoad && $buildings->isNotEmpty())
        <form method="POST" action="{{ route('admin.bills.store') }}" id="billForm">
            @csrf
            <input type="hidden" name="billing_month" value="{{ $selectedMonth }}">
            <input type="hidden" name="road_id" value="{{ $selectedRoad }}">

            {{-- Summary bar --}}
            <div class="card p-5 mb-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-4">
                        <div class="text-sm text-slate-600">
                            <i class="fas fa-home text-teal-600 mr-1"></i>
                            মোট বাড়ি: <strong class="text-slate-800">{{ $buildings->count() }}</strong>
                        </div>
                        <div class="text-sm text-slate-600">
                            <i class="fas fa-file-invoice text-amber-600 mr-1"></i>
                            বিল তৈরি: <strong class="text-slate-800">{{ $existingBills->count() }}</strong>
                        </div>
                        <div class="text-sm text-slate-600">
                            <i class="fas fa-calendar text-sky-600 mr-1"></i>
                            মাস: <strong class="text-slate-800">{{ $selectedMonth }}</strong>
                        </div>
                    </div>
                    <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-sm transition flex items-center gap-2 shadow-sm">
                        <i class="fas fa-save"></i> সকল বিল সংরক্ষণ করুন
                    </button>
                </div>
            </div>

            {{-- Bills table --}}
            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr class="text-left text-xs text-slate-500 uppercase tracking-wide">
                                <th class="px-4 py-3 font-semibold">বাড়ির নাম</th>
                                <th class="px-4 py-3 font-semibold">ক্যাটেগরি</th>
                                <th class="px-4 py-3 font-semibold text-center">ফিল্ড ডাটা<br><span class="text-[10px] text-slate-400">পরিবার</span></th>
                                <th class="px-4 py-3 font-semibold text-center">প্রত্যাশিত<br><span class="text-[10px] text-slate-400">পরিবার</span></th>
                                <th class="px-4 py-3 font-semibold text-center">প্রকৃত পরিবার<br><span class="text-[10px] text-slate-400">(বিলিং)</span></th>
                                <th class="px-4 py-3 font-semibold text-center">পরিচ্ছন্নতা<br><span class="text-[10px] text-slate-400">রেট/পরিবার</span></th>
                                <th class="px-4 py-3 font-semibold text-right">পরিচ্ছন্নতা<br><span class="text-[10px] text-slate-400">মোট</span></th>
                                <th class="px-4 py-3 font-semibold text-right">গার্ড<br><span class="text-[10px] text-slate-400">বিল</span></th>
                                <th class="px-4 py-3 font-semibold text-right">মোট বিল</th>
                                <th class="px-4 py-3 font-semibold text-center">স্ট্যাটাস</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($buildings as $building)
                                @php
                                    $existingBill = $existingBills->get($building->id);
                                    $fieldDataCount = $fieldDataCounts[$building->id] ?? 0;
                                    $expectedCount = $building->expected_family_count;
                                    $actualCount = $existingBill ? $existingBill->actual_family_count : $building->effective_billing_family_count;
                                    $cleaningRate = $existingBill ? $existingBill->cleaning_rate_per_family : 100;
                                    $guardBill = $existingBill ? $existingBill->security_guard_bill : ($guardRates[$building->building_category] ?? 0);
                                    $cleaningTotal = $cleaningRate * $actualCount;
                                    $totalBill = $cleaningTotal + $guardBill;
                                    $status = $existingBill ? $existingBill->status : 'pending';
                                @endphp
                                <tr class="hover:bg-slate-50 transition" data-building-row="{{ $building->id }}">
                                    {{-- Hidden fields --}}
                                    <input type="hidden" name="bills[{{ $building->id }}][building_id]" value="{{ $building->id }}">
                                    <input type="hidden" name="bills[{{ $building->id }}][field_data_family_count]" value="{{ $fieldDataCount }}">
                                    <input type="hidden" name="bills[{{ $building->id }}][notes]" value="">

                                    <td class="px-4 py-3">
                                        <div class="font-medium text-slate-800">{{ $building->name }}</div>
                                        <div class="text-xs text-slate-400">{{ $building->owner_name }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded-full font-medium">
                                            {{ $building->category_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-slate-600 font-medium">{{ $fieldDataCount ?: '—' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-slate-400">{{ $expectedCount }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="number" name="bills[{{ $building->id }}][actual_family_count]"
                                               value="{{ $actualCount }}" min="0" max="200"
                                               class="w-20 text-center border border-slate-300 rounded-lg px-2 py-1.5 text-sm font-medium focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none"
                                               data-family-count="{{ $building->id }}"
                                               onchange="recalculateRow({{ $building->id }})">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="number" name="bills[{{ $building->id }}][cleaning_rate_per_family]"
                                               value="{{ $cleaningRate }}" min="0" max="10000"
                                               class="w-20 text-center border border-slate-300 rounded-lg px-2 py-1.5 text-sm font-medium focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none"
                                               data-cleaning-rate="{{ $building->id }}"
                                               onchange="recalculateRow({{ $building->id }})">
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="font-medium text-slate-800 tabular-nums" data-cleaning-total="{{ $building->id }}">
                                            ৳ {{ number_format($cleaningTotal) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <input type="number" name="bills[{{ $building->id }}][security_guard_bill]"
                                               value="{{ $guardBill }}" min="0" max="100000"
                                               class="w-24 text-right border border-slate-300 rounded-lg px-2 py-1.5 text-sm font-medium focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none"
                                               data-guard-bill="{{ $building->id }}"
                                               onchange="recalculateRow({{ $building->id }})">
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="font-bold text-slate-900 tabular-nums text-base" data-total-bill="{{ $building->id }}">
                                            ৳ {{ number_format($totalBill) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($existingBill)
                                            <span class="text-[11px] {{ $existingBill->status_badge }} px-2.5 py-1 rounded-full font-semibold">
                                                {{ $existingBill->status_label }}
                                            </span>
                                        @else
                                            <span class="text-[11px] bg-slate-100 text-slate-500 px-2.5 py-1 rounded-full font-semibold">
                                                নতুন
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 border-t border-slate-200">
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-sm font-semibold text-slate-600 text-right">মোট</td>
                                <td class="px-4 py-3 text-right font-bold text-slate-800 tabular-nums" id="totalCleaning">
                                    ৳ {{ number_format($buildings->sum(fn($b) => ($existingBills->get($b->id)?->cleaning_total ?? 100 * ($existingBills->get($b->id)?->actual_family_count ?? $b->effective_billing_family_count)))) }}
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-slate-800 tabular-nums" id="totalGuard">
                                    ৳ {{ number_format($buildings->sum(fn($b) => $existingBills->get($b->id)?->security_guard_bill ?? ($guardRates[$b->building_category] ?? 0))) }}
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-emerald-700 tabular-nums text-lg" id="grandTotal">
                                    ৳ {{ number_format($buildings->sum(fn($b) => ($existingBills->get($b->id)?->total_bill ?? (100 * ($existingBills->get($b->id)?->actual_family_count ?? $b->effective_billing_family_count) + ($guardRates[$b->building_category] ?? 0))))) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Bottom save button --}}
            <div class="mt-4 flex justify-end">
                <button type="submit" class="px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-sm transition flex items-center gap-2 shadow-lg shadow-emerald-200">
                    <i class="fas fa-save"></i> সকল বিল সংরক্ষণ করুন
                </button>
            </div>
        </form>
    @elseif($selectedRoad && $buildings->isEmpty())
        <div class="card p-12 text-center">
            <i class="fas fa-home text-5xl text-slate-300 mb-4"></i>
            <h3 class="text-lg font-semibold text-slate-700">এই রাস্তায় কোনো বাড়ি নেই</h3>
            <p class="text-slate-500 mt-1 text-sm">প্রথমে "আওতাধীন এলাকা" থেকে বাড়ি যোগ করুন।</p>
        </div>
    @else
        <div class="card p-12 text-center">
            <i class="fas fa-hand-pointer text-5xl text-slate-300 mb-4"></i>
            <h3 class="text-lg font-semibold text-slate-700">রাস্তা নির্বাচন করুন</h3>
            <p class="text-slate-500 mt-1 text-sm">উপরে থেকে একটি রাস্তা বাছাই করুন — বাড়িগুলোর বিল তৈরি করা যাবে।</p>
        </div>
    @endif
</div>
@endsection

@section('extra-scripts')
<script>
    function recalculateRow(buildingId) {
        const familyCount = parseInt(document.querySelector(`[data-family-count="${buildingId}"]`)?.value || 0);
        const cleaningRate = parseInt(document.querySelector(`[data-cleaning-rate="${buildingId}"]`)?.value || 0);
        const guardBill = parseInt(document.querySelector(`[data-guard-bill="${buildingId}"]`)?.value || 0);

        const cleaningTotal = cleaningRate * familyCount;
        const totalBill = cleaningTotal + guardBill;

        // Update display
        const cleaningTotalEl = document.querySelector(`[data-cleaning-total="${buildingId}"]`);
        const totalBillEl = document.querySelector(`[data-total-bill="${buildingId}"]`);

        if (cleaningTotalEl) cleaningTotalEl.textContent = '৳ ' + cleaningTotal.toLocaleString('en-IN');
        if (totalBillEl) totalBillEl.textContent = '৳ ' + totalBill.toLocaleString('en-IN');

        // Recalculate totals
        recalculateTotals();
    }

    function recalculateTotals() {
        let totalCleaning = 0;
        let totalGuard = 0;
        let grandTotal = 0;

        document.querySelectorAll('[data-family-count]').forEach(el => {
            const buildingId = el.dataset.familyCount;
            const familyCount = parseInt(el.value || 0);
            const cleaningRate = parseInt(document.querySelector(`[data-cleaning-rate="${buildingId}"]`)?.value || 0);
            const guardBill = parseInt(document.querySelector(`[data-guard-bill="${buildingId}"]`)?.value || 0);

            totalCleaning += cleaningRate * familyCount;
            totalGuard += guardBill;
            grandTotal += (cleaningRate * familyCount) + guardBill;
        });

        document.getElementById('totalCleaning').textContent = '৳ ' + totalCleaning.toLocaleString('en-IN');
        document.getElementById('totalGuard').textContent = '৳ ' + totalGuard.toLocaleString('en-IN');
        document.getElementById('grandTotal').textContent = '৳ ' + grandTotal.toLocaleString('en-IN');
    }
</script>
@endsection
