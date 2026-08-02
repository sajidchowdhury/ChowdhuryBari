<!-- ==================== LEADERSHIP ==================== -->
<section id="leadership" class="bg-white border-y py-20">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-end justify-between mb-9">
            <div>
                <div class="text-xs tracking-[2px] uppercase text-emerald-700 font-semibold mb-1">THE TEAM</div>
                <h2 class="section-header text-5xl tracking-tighter font-bold heading-serif">আমাদের নেতৃত্ব</h2>
            </div>
            <div class="text-sm text-slate-500 hidden md:block">স্বেচ্ছাসেবী কমিটি</div>
        </div>

        @if($members->isNotEmpty())
            {{-- Carousel container: horizontally scrollable on overflow --}}
            <div class="relative">
                <div id="leadershipCarousel" class="flex gap-5 overflow-x-auto snap-x snap-mandatory pb-4 -mx-6 px-6"
                     style="scrollbar-width: thin; scrollbar-color: #065F46 #f1f5f9;">

                    @foreach($members as $member)
                        <div class="flex-shrink-0 w-64 sm:w-72 snap-start">
                            <div class="premium-card bg-white border border-slate-100 rounded-3xl overflow-hidden cursor-pointer h-full"
                                 onclick="showMemberDetail({{ $member->id }})">
                                <div class="h-44 overflow-hidden relative">
                                    <img src="{{ $member->image_url }}" class="w-full h-full object-cover" alt="{{ $member->name }}">
                                    <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/60"></div>
                                </div>
                                <div class="p-5">
                                    <div class="font-semibold text-lg tracking-tight">{{ $member->name }}</div>
                                    <div class="text-emerald-700 text-sm font-medium">{{ $member->designation }}</div>
                                    <div class="flex items-center justify-between text-xs text-slate-500 mt-3 pt-3 border-t">
                                        <div>দায়িত্বে: {{ $member->started_from }}</div>
                                        @if($member->phone)
                                            <a href="tel:{{ $member->phone }}" onclick="event.stopPropagation()" class="text-emerald-700 hover:underline font-medium">যোগাযোগ</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Hidden JSON data for modal --}}
                        <script type="application/json" id="member-detail-{{ $member->id }}" x-ignore>
                        {
                            "name": {!! json_encode($member->name) !!},
                            "designation": {!! json_encode($member->designation) !!},
                            "started_from": {!! json_encode($member->started_from) !!},
                            "phone": {!! json_encode($member->phone) !!},
                            "image": {!! json_encode($member->image_url) !!},
                            "bio": {!! json_encode($member->bio) !!}
                        }
                        </script>
                    @endforeach
                </div>

                {{-- Carousel navigation arrows --}}
                <button onclick="scrollCarousel(-1)" class="absolute left-0 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white shadow-lg border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50 hover:text-emerald-700 transition z-10 hidden md:flex">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button onclick="scrollCarousel(1)" class="absolute right-0 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white shadow-lg border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50 hover:text-emerald-700 transition z-10 hidden md:flex">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">
                @for($i = 0; $i < 4; $i++)
                    <div class="rounded-3xl border border-slate-100 p-8 text-center text-slate-400">
                        <i class="fas fa-user-tie text-4xl mb-3"></i>
                        <p>শীঘ্রই আসছে</p>
                    </div>
                @endfor
            </div>
        @endif
    </div>
</section>

{{-- Member Detail Modal --}}
<div id="memberModal" class="hidden fixed inset-0 z-[9999] items-center justify-center p-4 bg-black/60">
    <div class="modal-enter bg-white rounded-3xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-slate-100 px-6 py-3 flex justify-end z-10">
            <button onclick="closeMemberModal()" class="w-10 h-10 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-500 hover:text-slate-900 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <div class="grid md:grid-cols-5 gap-0">
            {{-- Left: image --}}
            <div class="md:col-span-2">
                <img id="memberModalImage" src="" alt="" class="w-full h-64 md:h-full object-cover">
            </div>

            {{-- Right: bio + info --}}
            <div class="md:col-span-3 p-6">
                <h3 id="memberModalName" class="text-2xl font-bold heading-serif text-slate-900"></h3>
                <p id="memberModalDesignation" class="text-emerald-700 font-medium mt-1"></p>

                <div class="mt-4 flex flex-wrap gap-4 text-sm text-slate-500">
                    <div><i class="fas fa-calendar-alt text-slate-400"></i> <span id="memberModalSince"></span></div>
                    <div id="memberModalPhoneWrap">
                        <i class="fas fa-phone text-slate-400"></i> <a id="memberModalPhone" href="#" class="text-emerald-700 hover:underline"></a>
                    </div>
                </div>

                <div class="mt-5 pt-5 border-t border-slate-100">
                    <div class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-2">সম্পর্কে</div>
                    <p id="memberModalBio" class="text-slate-600 text-sm leading-relaxed whitespace-pre-line"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function scrollCarousel(direction) {
        const carousel = document.getElementById('leadershipCarousel');
        const cardWidth = 288; // w-72 = 18rem = 288px + gap
        carousel.scrollBy({ left: direction * cardWidth, behavior: 'smooth' });
    }

    function showMemberDetail(id) {
        const script = document.getElementById('member-detail-' + id);
        if (!script) return;
        const data = JSON.parse(script.textContent);

        document.getElementById('memberModalImage').src = data.image;
        document.getElementById('memberModalImage').alt = data.name;
        document.getElementById('memberModalName').textContent = data.name;
        document.getElementById('memberModalDesignation').textContent = data.designation;
        document.getElementById('memberModalSince').textContent = 'দায়িত্বে: ' + data.started_from;

        const phoneWrap = document.getElementById('memberModalPhoneWrap');
        if (data.phone) {
            phoneWrap.style.display = '';
            const phoneLink = document.getElementById('memberModalPhone');
            phoneLink.href = 'tel:' + data.phone;
            phoneLink.textContent = data.phone;
        } else {
            phoneWrap.style.display = 'none';
        }

        const bioEl = document.getElementById('memberModalBio');
        if (data.bio) {
            bioEl.textContent = data.bio;
            bioEl.style.display = '';
        } else {
            bioEl.style.display = 'none';
        }

        const modal = document.getElementById('memberModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeMemberModal() {
        const modal = document.getElementById('memberModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeMemberModal();
    });
</script>
