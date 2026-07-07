<section id="coverage" class="max-w-7xl mx-auto px-6 pt-20 pb-24">
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-y-3 mb-10">
        <div>
            <div class="uppercase text-xs tracking-[2px] text-emerald-700 font-semibold mb-1">OUR AREA</div>
            <h2 class="section-header text-5xl tracking-tighter font-bold heading-serif">আওতাধীন এলাকা</h2>
        </div>
        <p class="text-lg text-slate-600 max-w-sm">{{ $roads->count() }}টি রাস্তায় {{ $roads->sum(fn($r) => $r->buildings->count()) }}+ ভবন — সবকিছু এক নজরে</p>
    </div>

    {{-- Road cards rendered from DB --}}
    @if($roads->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            @foreach($roads as $road)
                <div class="group cursor-pointer rounded-3xl p-5 text-white bg-gradient-to-br from-emerald-700 to-emerald-900 shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all">
                    <div class="flex items-start justify-between">
                        <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center text-lg">
                            <i class="fas fa-road"></i>
                        </div>
                        <span class="text-[11px] font-semibold bg-white/20 px-2 py-1 rounded-full">{{ $road->buildings->count() }} ভবন</span>
                    </div>
                    <div class="mt-5 font-semibold text-[17px] tracking-tight">{{ $road->name }}</div>
                    @if($road->description)
                        <div class="text-white/70 text-xs mt-1 leading-relaxed line-clamp-2">{{ $road->description }}</div>
                    @endif
                    @if($road->tag_list)
                        <div class="mt-3 flex flex-wrap gap-1">
                            @foreach($road->tag_list as $tag)
                                <span class="text-[10px] font-medium bg-white/15 border border-white/20 px-2 py-0.5 rounded-full">{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif
                    <div class="text-white/70 text-xs mt-3 flex items-center gap-1">
                        বিস্তারিত <i class="fas fa-arrow-right text-[10px] group-hover:translate-x-0.5 transition"></i>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            @for($i = 0; $i < 5; $i++)
                <div class="rounded-3xl p-5 text-white bg-gradient-to-br from-slate-700 to-slate-900 shadow opacity-50">
                    <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-lg">
                        <i class="fas fa-road"></i>
                    </div>
                    <div class="mt-5 font-semibold text-[17px]">শীঘ্রই আসছে</div>
                    <div class="text-white/60 text-xs mt-1">রাস্তার তথ্য যোগ করা হবে</div>
                </div>
            @endfor
        </div>
    @endif

    <div class="mt-6 text-center">
        <div onclick="showAllRoadsModal()" class="inline-flex cursor-pointer items-center gap-2 text-sm font-medium text-emerald-700 hover:text-emerald-800">
            <span>সম্পূর্ণ তালিকা ও বিস্তারিত দেখুন</span> <i class="fas fa-expand-arrows-alt"></i>
        </div>
    </div>

    {{-- Delivery Location Quick Tool --}}
    <div class="mt-8 max-w-md mx-auto">
        <button onclick="openDeliveryFinder()"
                class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-sky-50 hover:bg-sky-100 border border-sky-200 text-sky-800 font-semibold rounded-3xl transition-all active:scale-[0.985]">
            <i class="fas fa-motorcycle text-xl"></i>
            <span>ডেলিভারি ম্যানকে লোকেশন পাঠাতে চান?</span>
        </button>
        <div class="text-center text-[11px] text-sky-600 mt-2">ফোন নম্বর বা বাড়ির নম্বর দিয়ে সহজে খুঁজুন → কপি/শেয়ার করুন</div>
    </div>
</section>
