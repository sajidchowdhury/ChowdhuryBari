@php
    $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    $toBn = function($n) use ($bn) { return str_replace(range(0,9), $bn, (string) $n); };
    $buildingCount = $totalBuildings ?? 0;
    $roadCount = $totalRoads ?? 0;
    $flatCount = $totalFlats ?? 0;
@endphp
 <!-- ==================== IMPACT STATS ==================== -->
    <section class="border-b bg-white py-8">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                <div class="metric bg-slate-50 border border-slate-100 rounded-3xl px-7 py-6 text-center">
                    <div class="text-5xl font-semibold text-emerald-800 tabular-nums stat-number" data-target="{{ $buildingCount }}">{{ $toBn($buildingCount) }}</div>
                    <div class="text-slate-500 text-sm mt-1 font-medium tracking-wide">আচ্ছাদিত ভবন</div>
                </div>
                <div class="metric bg-slate-50 border border-slate-100 rounded-3xl px-7 py-6 text-center">
                    <div class="text-5xl font-semibold text-emerald-800 tabular-nums stat-number" data-target="{{ $roadCount }}">{{ $toBn($roadCount) }}</div>
                    <div class="text-slate-500 text-sm mt-1 font-medium tracking-wide">রাস্তা ও লেন</div>
                </div>
                <div class="metric bg-slate-50 border border-slate-100 rounded-3xl px-7 py-6 text-center">
                    <div class="text-5xl font-semibold text-emerald-800 tabular-nums stat-number" data-target="{{ $flatCount }}">{{ $toBn($flatCount) }}</div>
                    <div class="text-slate-500 text-sm mt-1 font-medium tracking-wide">সদস্য পরিবার</div>
                </div>
                <div class="metric bg-slate-50 border border-slate-100 rounded-3xl px-7 py-6 text-center">
                    <div class="text-5xl font-semibold text-emerald-800 tabular-nums">২৪<span class="text-3xl align-super">/</span><span class="text-4xl">৭</span></div>
                    <div class="text-slate-500 text-sm mt-1 font-medium tracking-wide">নিরাপত্তা ও সেবা</div>
                </div>
            </div>
        </div>
    </section>
