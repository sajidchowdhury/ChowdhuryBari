@php
    $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    $toBn = function($n) use ($bn) { return str_replace(range(0,9), $bn, (string) $n); };
    $rankBadge = ['১' => 'bg-amber-400 text-white', '২' => 'bg-slate-300 text-slate-800', '৩' => 'bg-orange-400 text-white'];
@endphp

@if($topRanked->isNotEmpty())
<!-- ==================== TOP 10 — SOCIAL VALUE LEADERBOARD ==================== -->
<section id="leaderboard" class="max-w-7xl mx-auto px-6 pt-20 pb-16">
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-y-3 mb-9">
        <div>
            <div class="uppercase text-xs tracking-[2px] text-emerald-700 font-semibold mb-1">CLEANEST YARDS</div>
            <h2 class="section-header text-5xl tracking-tighter font-bold heading-serif">মাসের সেরা পরিচ্ছন্ন পরিবার</h2>
            <p class="text-slate-500 text-sm mt-2 max-w-lg">সদস্যদের আপলোড করা উঠানের ছবি অ্যাডমিন বেনামে ১-১০ স্টার দেন — গড় স্টার × ১০ = Social Value (১-১০০)। সর্বোচ্চ স্কোরের শীর্ষ ১০ পরিবার।</p>
        </div>
        <div class="text-xs text-slate-400">
            <i class="fas fa-calendar-alt mr-1"></i> {{ $toBn(now()->format('F')) }} {{ $toBn(now()->format('Y')) }}
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        @foreach($topRanked as $row)
            @php
                $rankBn = $toBn($row->rank);
                $badgeClass = $rankBadge[$rankBn] ?? 'bg-emerald-100 text-emerald-700';
            @endphp
            <div class="premium-card bg-white border border-slate-100 rounded-3xl overflow-hidden group">
                {{-- Best image --}}
                <div class="relative aspect-[4/3] bg-slate-100 overflow-hidden">
                    @if($row->best_image_url)
                        <img src="{{ $row->best_image_url }}" alt="Top {{ $row->rank }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fas fa-image text-4xl"></i></div>
                    @endif
                    {{-- Rank badge --}}
                    <div class="absolute top-3 left-3 w-9 h-9 rounded-full {{ $badgeClass }} font-bold text-sm flex items-center justify-center shadow-lg">
                        {{ $rankBn }}
                    </div>
                    {{-- Star rating --}}
                    <div class="absolute top-3 right-3 bg-amber-400 text-white text-xs px-2 py-0.5 rounded-full font-bold flex items-center gap-0.5 shadow">
                        <i class="fas fa-star text-[9px]"></i> {{ $toBn($row->best_image_stars) }}/১০
                    </div>
                </div>

                {{-- Info --}}
                <div class="p-4">
                    <div class="font-semibold text-slate-800 text-sm leading-tight truncate">{{ $row->building_name }}</div>
                    <div class="text-xs text-slate-500 mt-0.5 truncate">{{ $row->owner_name }}</div>
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100">
                        <div>
                            <div class="text-[10px] text-slate-400 uppercase tracking-wide">Social Value</div>
                            <div class="text-lg font-bold text-emerald-700 tabular-nums">{{ $toBn($row->social_value) }}<span class="text-xs text-slate-400">/১০০</span></div>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] text-slate-400 uppercase tracking-wide">রাস্তা</div>
                            <div class="text-xs font-medium text-slate-600">{{ $row->road_name }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-8 text-center">
        <div class="inline-flex items-center gap-2 text-xs text-slate-400 bg-slate-50 px-4 py-2 rounded-2xl">
            <i class="fas fa-info-circle"></i>
            সমান স্কোর হলে গত মাসের স্কোরের ভিত্তিতে র‍্যাঙ্ক নির্ধারিত হয়
        </div>
    </div>
</section>
@endif
