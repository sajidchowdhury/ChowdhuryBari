@php
    $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    $toBn = function($n) use ($bn) { return str_replace(range(0,9), $bn, (string) $n); };
    $bnMonths = ['January'=>'জানুয়ারি','February'=>'ফেব্রুয়ারি','March'=>'মার্চ','April'=>'এপ্রিল','May'=>'মে','June'=>'জুন','July'=>'জুলাই','August'=>'আগস্ট','September'=>'সেপ্টেম্বর','October'=>'অক্টোবর','November'=>'নভেম্বর','December'=>'ডিসেম্বর'];
    $bnDate = function($date) use ($bn, $bnMonths) {
        if (!$date) return '';
        $d = $date instanceof \Illuminate\Support\Carbon ? $date : \Illuminate\Support\Carbon::parse($date);
        return $toBn($d->format('d')) . ' ' . ($bnMonths[$d->format('F')] ?? $d->format('F')) . ' ' . $toBn($d->format('Y'));
    };
    $bnTime = function($date) use ($bn) {
        if (!$date) return '';
        $d = $date instanceof \Illuminate\Support\Carbon ? $date : \Illuminate\Support\Carbon::parse($date);
        $h = (int)$d->format('g');
        $m = $d->format('i');
        $a = $d->format('A') === 'AM' ? 'সকাল' : 'বিকেল';
        if ($d->format('A') === 'PM' && (int)$d->format('H') >= 18) $a = 'সন্ধ্যা';
        if ($d->format('A') === 'AM' && (int)$d->format('H') < 6) $a = 'রাত';
        if ($d->format('A') === 'PM' && (int)$d->format('H') >= 12 && (int)$d->format('H') < 15) $a = 'দুপুর';
        return $toBn($h) . ':' . $toBn($m) . ' ' . $a;
    };
@endphp

    <!-- ==================== NOTICES ==================== -->
    <section id="notices" class="max-w-7xl mx-auto px-6 pt-20 pb-16">
        <div class="flex items-center justify-between mb-9">
            <div>
                <h2 class="section-header text-5xl tracking-tighter font-bold heading-serif">নোটিশ ও ঘোষণা</h2>
            </div>
            @if($notices->count() > 3)
                <button onclick="nextNotices()" class="text-sm flex items-center gap-2 font-semibold px-5 py-2.5 text-emerald-700 hover:bg-emerald-50 rounded-2xl transition">
                    <span>পরবর্তী নোটিশ</span> <i class="fas fa-arrow-right text-xs"></i>
                </button>
            @endif
        </div>

        @if($notices->isNotEmpty())
            <div id="noticesGrid" class="grid md:grid-cols-3 gap-5">
                {{-- Cards rendered by JS --}}
            </div>
        @else
            <div class="grid md:grid-cols-3 gap-5">
                @for($i = 0; $i < 3; $i++)
                    <div class="bg-white border border-slate-100 rounded-3xl p-6 text-center text-slate-400">
                        <i class="fas fa-bullhorn text-4xl mb-3"></i>
                        <p>শীঘ্রই আসছে</p>
                    </div>
                @endfor
            </div>
        @endif
    </section>

    {{-- Old Newspaper Notice Modal --}}
    <div id="noticeModal" class="hidden fixed inset-0 z-[9999] items-center justify-center p-4 bg-black/70">
        <div class="modal-enter max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            {{-- Old newspaper styled paper --}}
            <div class="relative bg-[#f4ecd8] rounded-lg shadow-2xl" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22200%22 viewBox=%220 0 200 200%22%3E%3Cfilter id=%22n%22%3E%3CfeTurbulence baseFrequency=%220.9%22 numOctaves=%223%22/%3E%3CfeColorMatrix values=%220 0 0 0 0.55 0 0 0 0 0.45 0 0 0 0 0.3 0 0 0 0.08 0%22/%3E%3C/filter%3E%3Crect width=%22200%22 height=%22200%22 filter=%22url(%23n)%22/%3E%3C/svg%3E');">

                {{-- Close button --}}
                <button onclick="closeNoticeModal()" class="absolute top-3 right-3 w-9 h-9 rounded-full bg-[#3a2e1f]/10 hover:bg-[#3a2e1f]/20 flex items-center justify-center text-[#3a2e1f] transition z-10">
                    <i class="fas fa-times"></i>
                </button>

                {{-- Newspaper masthead --}}
                <div class="border-b-2 border-double border-[#3a2e1f] px-8 pt-7 pb-3 text-center">
                    <div class="text-[10px] uppercase tracking-[4px] text-[#3a2e1f]/60 mb-1">চৌধুরীপাড়াস্থ সমাজ উন্নায়ন সংস্থা</div>
                    <div class="font-serif text-3xl font-bold tracking-tight text-[#3a2e1f]" style="font-family: 'Playfair Display', Georgia, serif;">
                        দৈনিক বার্তা
                    </div>
                    <div class="flex justify-between items-center text-[10px] text-[#3a2e1f]/70 mt-2 px-2">
                        <span id="noticeModalDate"></span>
                        <span>প্রকাশনা</span>
                        <span id="noticeModalTime"></span>
                    </div>
                </div>

                {{-- Notice body --}}
                <div class="px-8 py-6 text-[#2a2218]">
                    <div class="text-center mb-4">
                        <span id="noticeModalType" class="inline-block text-[10px] uppercase tracking-widest font-bold border border-[#3a2e1f]/40 px-3 py-0.5 rounded-full"></span>
                    </div>

                    <h3 id="noticeModalHeadline" class="font-serif text-2xl font-bold text-center leading-tight mb-4 text-[#1a1410]" style="font-family: 'Playfair Display', Georgia, serif;"></h3>

                    <div class="border-t border-[#3a2e1f]/20 pt-4">
                        <p id="noticeModalDescription" class="text-sm leading-[1.9] whitespace-pre-line text-justify" style="font-family: 'Inter', Georgia, serif;"></p>
                    </div>

                    <div class="border-t border-[#3a2e1f]/20 mt-6 pt-3 text-center text-[10px] text-[#3a2e1f]/50 uppercase tracking-widest">
                        — চৌধুরীপাড়া সমাজ উন্নায়ন সংস্থা —
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden JSON data for all notices --}}
    <script type="application/json" id="allNoticesData" x-ignore>
    [
        @foreach($notices as $n)
        {
            "id": {{ $n->id }},
            "type": {!! json_encode($n->type) !!},
            "headline": {!! json_encode($n->headline) !!},
            "description": {!! json_encode($n->description) !!},
            "date": {!! json_encode($bnDate($n->published_at)) !!},
            "time": {!! json_encode($bnTime($n->published_at)) !!}
        }@if(!$loop->last),@endif
        @endforeach
    ]
    </script>

    <script>
        const bnMonths = {1:'জানুয়ারি',2:'ফেব্রুয়ারি',3:'মার্চ',4:'এপ্রিল',5:'মে',6:'জুন',7:'জুলাই',8:'আগস্ট',9:'সেপ্টেম্বর',10:'অক্টোবর',11:'নভেম্বর',12:'ডিসেম্বর'};
        const bnDigits = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
        const toBn = n => String(n).replace(/[0-9]/g, d => bnDigits[d]);

        let allNotices = [];
        try { allNotices = JSON.parse(document.getElementById('allNoticesData').textContent); } catch(e) {}
        let noticeOffset = 0;

        function renderNotices() {
            const grid = document.getElementById('noticesGrid');
            if (!grid || allNotices.length === 0) return;

            grid.innerHTML = '';
            const visible = allNotices.slice(noticeOffset, noticeOffset + 3);

            visible.forEach(n => {
                const card = document.createElement('div');
                card.className = 'notice-card bg-white border border-slate-100 hover:border-emerald-200 rounded-3xl p-6 flex flex-col cursor-pointer transition-all';
                card.onclick = () => showNoticeDetail(n.id);
                card.innerHTML = `
                    <div class="flex items-center justify-between">
                        <span class="inline-block px-3 py-px text-xs font-semibold rounded-xl bg-emerald-100 text-emerald-700">${n.type}</span>
                        <span class="text-xs text-slate-400">${n.date}</span>
                    </div>
                    <div class="mt-4 font-semibold text-xl leading-tight tracking-tight">${n.headline}</div>
                    <div class="text-sm text-slate-600 mt-2 flex-1 line-clamp-3">${n.description}</div>
                    <div class="mt-4 text-xs text-emerald-700 font-medium"><i class="fas fa-clock mr-1"></i> ${n.time}</div>
                `;
                grid.appendChild(card);
            });
        }

        function nextNotices() {
            const next = noticeOffset + 3;
            if (next >= allNotices.length) {
                noticeOffset = 0;
            } else {
                noticeOffset = next;
            }
            renderNotices();
        }

        function showNoticeDetail(id) {
            const n = allNotices.find(x => x.id === id);
            if (!n) return;

            document.getElementById('noticeModalType').textContent = n.type;
            document.getElementById('noticeModalHeadline').textContent = n.headline;
            document.getElementById('noticeModalDescription').textContent = n.description;
            document.getElementById('noticeModalDate').textContent = n.date;
            document.getElementById('noticeModalTime').textContent = n.time;

            const modal = document.getElementById('noticeModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeNoticeModal() {
            const modal = document.getElementById('noticeModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeNoticeModal();
        });

        // Initial render
        renderNotices();
    </script>
