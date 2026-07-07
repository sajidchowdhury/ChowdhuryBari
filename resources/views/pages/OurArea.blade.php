@php
    $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    $toBn = function($n) use ($bn) { return str_replace(range(0,9), $bn, (string) $n); };
    $allBuildings = $roads->flatMap(fn($r) => $r->buildings->map(fn($b) => ['building' => $b, 'road' => $r]));
    $totalBuildingsOnAllRoads = $allBuildings->count();
@endphp

<section id="coverage" class="max-w-7xl mx-auto px-6 pt-20 pb-24">
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-y-3 mb-10">
        <div>
            <div class="uppercase text-xs tracking-[2px] text-emerald-700 font-semibold mb-1">OUR AREA</div>
            <h2 class="section-header text-5xl tracking-tighter font-bold heading-serif">আওতাধীন এলাকা</h2>
        </div>
        <p class="text-lg text-slate-600 max-w-sm">{{ $toBn($roads->count()) }}টি রাস্তায় {{ $toBn($totalBuildingsOnAllRoads) }}+ ভবন — সবকিছু এক নজরে</p>
    </div>

    {{-- Road cards grid --}}
    @if($roads->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            @foreach($roads as $road)
                <div class="road-card group bg-white border border-slate-100 rounded-3xl overflow-hidden cursor-pointer premium-card"
                     onclick="showRoadDetails({{ $road->id }})">
                    <div class="relative h-40">
                        @if($road->image_path)
                            <img src="{{ $road->image_url }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="{{ $road->name }}">
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-700 to-emerald-900 flex items-center justify-center">
                                <i class="fas fa-road text-4xl text-white/40"></i>
                            </div>
                        @endif
                        <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-black/75 to-transparent"></div>
                        <div class="absolute top-4 right-4 bg-white/95 px-4 py-1 text-xs font-semibold rounded-2xl shadow flex items-center gap-1">
                            <span class="text-emerald-800">{{ $toBn($road->buildings->count()) }} ভবন</span>
                        </div>
                        <div class="absolute bottom-4 left-4 text-white">
                            <div class="font-bold text-2xl tracking-tight">{{ $road->name }}</div>
                            @if($road->description)
                                <div class="text-xs text-white/80">{{ $road->description }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="px-5 py-4 flex items-center justify-between text-sm">
                        <div>
                            @if($road->tag_list)
                                @foreach($road->tag_list as $tag)
                                    <span class="inline-block px-3 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold mr-1">{{ $tag }}</span>
                                @endforeach
                            @else
                                <span class="text-slate-400 text-xs">কোনো ট্যাগ নেই</span>
                            @endif
                        </div>
                        <div class="text-emerald-700 font-medium flex items-center gap-1 text-sm hover:underline">
                            বিস্তারিত <i class="fas fa-arrow-right text-xs"></i>
                        </div>
                    </div>
                </div>

                {{-- Hidden JSON data for this road + its buildings --}}
                <script type="application/json" id="road-data-{{ $road->id }}" x-ignore>
                {
                    "id": {{ $road->id }},
                    "name": {{ json_encode($road->name) }},
                    "buildings_count": {{ $road->buildings->count() }},
                    "tags": {{ json_encode($road->tag_list) }},
                    "buildings": [
                        @foreach($road->buildings as $b)
                        {
                            "id": {{ $b->id }},
                            "name": {{ json_encode($b->name) }},
                            "owner_name": {{ json_encode($b->owner_name) }},
                            "owner_phone": {{ json_encode($b->owner_phone) }},
                            "caretaker_name": {{ json_encode($b->caretaker_name) }},
                            "caretaker_phone": {{ json_encode($b->caretaker_phone) }},
                            "structure_type": {{ json_encode($b->structure_type) }},
                            "usage_type": {{ json_encode($b->usage_type) }},
                            "floor_count": {{ $b->floor_count }},
                            "families_per_floor": {{ $b->families_per_floor }},
                            "total_flats": {{ $b->total_flats }},
                            "active_flats": {{ $b->active_flats }},
                            "has_security": {{ $b->has_security ? 'true' : 'false' }},
                            "has_cleaning": {{ $b->has_cleaning ? 'true' : 'false' }},
                            "google_lt": {{ json_encode($b->google_lt) }},
                            "google_ln": {{ json_encode($b->google_ln) }},
                            "extra_info": {{ json_encode($b->extra_information) }},
                            "image": {{ json_encode($b->image_url) }}
                        }@if(!$loop->last),@endif
                        @endforeach
                    ]
                }
                </script>
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

{{-- ============ ROAD DETAILS MODAL (with searchable building list) ============ --}}
<div id="roadModal" class="hidden fixed inset-0 z-[9999] items-center justify-center p-4 bg-black/60">
    <div class="modal-enter bg-white rounded-3xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="sticky top-0 bg-white border-b border-slate-100 px-6 py-4 flex items-center justify-between z-10">
            <div>
                <h3 id="roadModalTitle" class="text-2xl font-bold heading-serif text-slate-900"></h3>
                <p id="roadModalSubtitle" class="text-slate-500 text-sm mt-0.5"></p>
            </div>
            <button onclick="closeRoadModal()" class="w-10 h-10 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-500 hover:text-slate-900 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        {{-- Search bar --}}
        <div class="px-6 py-4 border-b border-slate-100">
            <div class="relative">
                <input type="text" id="buildingSearch" oninput="filterBuildings()"
                       placeholder="ভবনের নাম, মালিকের নাম দিয়ে খুঁজুন..."
                       class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-10 py-3 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            </div>
        </div>

        {{-- Buildings grid --}}
        <div class="p-6">
            <div id="buildingsGrid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4"></div>
        </div>
    </div>
</div>

{{-- ============ BUILDING DETAIL MODAL (with Google Map) ============ --}}
<div id="buildingModal" class="hidden fixed inset-0 z-[10000] items-center justify-center p-4 bg-black/60">
    <div class="modal-enter bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        {{-- Close button --}}
        <div class="sticky top-0 bg-white border-b border-slate-100 px-6 py-4 flex items-center justify-between z-10">
            <div>
                <h3 id="buildingModalTitle" class="text-2xl font-bold heading-serif text-slate-900"></h3>
                <p id="buildingModalRoad" class="text-slate-500 text-sm mt-0.5"></p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="showRoadFromBuilding()" class="text-sm text-emerald-700 hover:text-emerald-900 font-medium flex items-center gap-1">
                    <i class="fas fa-arrow-left"></i> রাস্তায় ফিরুন
                </button>
                <button onclick="closeBuildingModal()" class="w-10 h-10 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-500 hover:text-slate-900 transition ml-2">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <div class="p-6 space-y-5">
            {{-- Building image --}}
            <img id="buildingModalImage" src="" alt="" class="w-full h-48 object-cover rounded-2xl">

            {{-- Badges --}}
            <div id="buildingModalBadges" class="flex flex-wrap gap-2"></div>

            {{-- Info grid --}}
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-slate-50 p-4">
                    <div class="text-xs text-slate-400 font-semibold uppercase tracking-wide">মালিক</div>
                    <div id="buildingModalOwner" class="font-semibold text-slate-800 mt-1"></div>
                    <a id="buildingModalOwnerPhone" href="#" class="text-sm text-emerald-700 mt-1 flex items-center gap-1">
                        <i class="fas fa-phone"></i> <span></span>
                    </a>
                </div>
                <div id="buildingModalCaretakerWrap" class="rounded-2xl bg-slate-50 p-4" style="display:none;">
                    <div class="text-xs text-slate-400 font-semibold uppercase tracking-wide">কেয়ারটেকার</div>
                    <div id="buildingModalCaretaker" class="font-semibold text-slate-800 mt-1"></div>
                    <div id="buildingModalCaretakerPhone" class="text-sm text-emerald-700 mt-1"></div>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                    <div class="text-xs text-slate-400 font-semibold uppercase tracking-wide">ফ্লোর ও পরিবার</div>
                    <div id="buildingModalFloorInfo" class="font-semibold text-slate-800 mt-1"></div>
                    <div id="buildingModalFlatInfo" class="text-sm text-slate-500 mt-0.5"></div>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                    <div class="text-xs text-slate-400 font-semibold uppercase tracking-wide">ধরন</div>
                    <div id="buildingModalType" class="font-semibold text-slate-800 mt-1"></div>
                </div>
            </div>

            {{-- Google Map --}}
            <div id="buildingModalMapWrap" style="display:none;">
                <div class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-2">গুগল ম্যাপ লোকেশন</div>
                <div class="rounded-2xl overflow-hidden border border-slate-200">
                    <iframe id="buildingModalMap" src="" width="100%" height="250" style="border:0;" loading="lazy" allowfullscreen></iframe>
                </div>
            </div>

            {{-- Extra info --}}
            <div id="buildingModalExtraWrap" style="display:none;">
                <div class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-1">অতিরিক্ত তথ্য</div>
                <p id="buildingModalExtra" class="text-sm text-slate-600 bg-slate-50 rounded-2xl p-3"></p>
            </div>
        </div>
    </div>
</div>

<script>
    const bnDigits = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    const toBn = n => String(n).replace(/[0-9]/g, d => bnDigits[d]);
    const typeMap = {building: 'ভবন', tin_shed: 'টিন শেড', other: 'অন্যান্য'};
    const usageMap = {residential: 'আবাসিক', shop: 'দোকান', mixed: 'মিশ্র'};

    let currentRoad = null;
    let currentBuilding = null;

    function getRoadData(id) {
        const script = document.getElementById('road-data-' + id);
        if (!script) return null;
        return JSON.parse(script.textContent);
    }

    // ============ ROAD MODAL ============
    function showRoadDetails(id) {
        const road = getRoadData(id);
        if (!road) return;
        currentRoad = road;
        document.getElementById('roadModalTitle').textContent = road.name;
        const tags = road.tags && road.tags.length ? road.tags.join(' • ') : '';
        document.getElementById('roadModalSubtitle').textContent = toBn(road.buildings_count) + 'টি ভবন' + (tags ? ' • ' + tags : '');
        renderBuildings(road.buildings);
        document.getElementById('buildingSearch').value = '';
        const modal = document.getElementById('roadModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeRoadModal() {
        const modal = document.getElementById('roadModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    function renderBuildings(buildings) {
        const grid = document.getElementById('buildingsGrid');
        grid.innerHTML = '';
        if (!buildings || buildings.length === 0) {
            grid.innerHTML = '<div class="col-span-full py-9 text-center text-slate-400">এই রাস্তায় বর্তমানে কোনো ভবন তথ্য যোগ করা হয়নি।</div>';
            return;
        }
        buildings.forEach(b => {
            const hasLoc = b.google_lt && b.google_ln;
            const card = document.createElement('div');
            card.className = 'border border-slate-100 rounded-2xl p-4 hover:border-emerald-200 transition-all cursor-pointer group';
            card.innerHTML = `
                <div class="flex justify-between">
                    <div>
                        <div class="font-semibold text-lg flex items-center gap-2">
                            ${b.name}
                            ${hasLoc ? '<span class="inline-flex items-center text-emerald-600"><i class="fas fa-map-marker-alt text-xs ml-1"></i></span>' : ''}
                        </div>
                        <div class="text-sm text-slate-500">${b.owner_name || '—'}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs px-2.5 py-px bg-slate-100 text-slate-500 rounded-xl w-fit ml-auto">${toBn(b.floor_count)} তলা</div>
                        <div class="text-xs text-emerald-700 mt-1 font-medium">${usageMap[b.usage_type] || b.usage_type}</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-4 pt-3 border-t text-xs">
                    <a href="tel:${b.owner_phone}" onclick="event.stopPropagation()" class="flex-1 text-center py-2 bg-emerald-50 hover:bg-emerald-100 rounded-xl text-emerald-700 font-medium">
                        <i class="fas fa-phone mr-1"></i> কল করুন
                    </a>
                    <button onclick="event.stopPropagation(); copyToClipboard('${b.owner_phone}'); this.innerText='কপি হয়েছে'" class="flex-1 text-center py-2 border border-emerald-100 hover:bg-emerald-50 rounded-xl text-emerald-700 font-medium">নম্বর কপি</button>
                    ${hasLoc ? `<button onclick="event.stopPropagation(); showBuildingDetails(${b.id})" class="px-3 py-2 bg-emerald-800 text-white text-xs font-semibold rounded-xl flex items-center gap-1 hover:bg-emerald-900"><i class="fas fa-map"></i><span class="hidden sm:inline">ম্যাপ</span></button>` : ''}
                </div>
            `;
            card.onclick = (e) => {
                if (!e.target.closest('a, button')) showBuildingDetails(b.id);
            };
            grid.appendChild(card);
        });
    }

    function filterBuildings() {
        if (!currentRoad) return;
        const query = document.getElementById('buildingSearch').value.toLowerCase().trim();
        const filtered = currentRoad.buildings.filter(b =>
            (b.name || '').toLowerCase().includes(query) ||
            (b.owner_name || '').toLowerCase().includes(query) ||
            (usageMap[b.usage_type] || '').toLowerCase().includes(query)
        );
        renderBuildings(filtered);
    }

    // ============ BUILDING MODAL ============
    function showBuildingDetails(buildingId) {
        if (!currentRoad) return;
        const b = currentRoad.buildings.find(x => x.id === buildingId);
        if (!b) return;
        currentBuilding = b;

        document.getElementById('buildingModalTitle').textContent = b.name;
        document.getElementById('buildingModalRoad').textContent = currentRoad.name;
        document.getElementById('buildingModalImage').src = b.image;
        document.getElementById('buildingModalImage').alt = b.name;

        // Badges
        const badgesDiv = document.getElementById('buildingModalBadges');
        badgesDiv.innerHTML = '';
        if (b.has_security) badgesDiv.innerHTML += '<span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full"><i class="fas fa-shield-alt"></i> নিরাপত্তা</span>';
        if (b.has_cleaning) badgesDiv.innerHTML += '<span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full"><i class="fas fa-broom"></i> পরিচ্ছন্নতা</span>';

        // Owner
        document.getElementById('buildingModalOwner').textContent = b.owner_name || '—';
        const phoneLink = document.getElementById('buildingModalOwnerPhone');
        if (b.owner_phone) {
            phoneLink.href = 'tel:' + b.owner_phone;
            phoneLink.querySelector('span').textContent = b.owner_phone;
            phoneLink.style.display = '';
        } else {
            phoneLink.style.display = 'none';
        }

        // Caretaker
        const caretakerWrap = document.getElementById('buildingModalCaretakerWrap');
        if (b.caretaker_name) {
            caretakerWrap.style.display = '';
            document.getElementById('buildingModalCaretaker').textContent = b.caretaker_name;
            document.getElementById('buildingModalCaretakerPhone').innerHTML = b.caretaker_phone ? '<i class="fas fa-phone"></i> ' + b.caretaker_phone : '';
        } else {
            caretakerWrap.style.display = 'none';
        }

        // Floor info
        document.getElementById('buildingModalFloorInfo').textContent = toBn(b.floor_count) + ' তলা × ' + toBn(b.families_per_floor) + ' পরিবার/তলা';
        document.getElementById('buildingModalFlatInfo').textContent = 'মোট ' + toBn(b.total_flats) + ' ফ্ল্যাট, ' + toBn(b.active_flats) + ' সক্রিয়';

        // Type
        document.getElementById('buildingModalType').textContent = (typeMap[b.structure_type] || b.structure_type) + ' • ' + (usageMap[b.usage_type] || b.usage_type);

        // Map
        const mapWrap = document.getElementById('buildingModalMapWrap');
        if (b.google_lt && b.google_ln) {
            mapWrap.style.display = '';
            document.getElementById('buildingModalMap').src = 'https://www.google.com/maps?q=' + b.google_lt + ',' + b.google_ln + '&hl=bn&z=18&output=embed';
        } else {
            mapWrap.style.display = 'none';
        }

        // Extra
        const extraWrap = document.getElementById('buildingModalExtraWrap');
        if (b.extra_info) {
            extraWrap.style.display = '';
            document.getElementById('buildingModalExtra').textContent = b.extra_info;
        } else {
            extraWrap.style.display = 'none';
        }

        // Show modal
        closeRoadModal();
        setTimeout(() => {
            const modal = document.getElementById('buildingModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }, 180);
    }

    function closeBuildingModal() {
        const modal = document.getElementById('buildingModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    function showRoadFromBuilding() {
        closeBuildingModal();
        if (currentRoad) {
            setTimeout(() => showRoadDetails(currentRoad.id), 180);
        }
    }

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!document.getElementById('buildingModal').classList.contains('hidden')) closeBuildingModal();
            else if (!document.getElementById('roadModal').classList.contains('hidden')) closeRoadModal();
        }
    });

    // Copy helper
    function copyToClipboard(text) {
        if (navigator.clipboard) navigator.clipboard.writeText(text);
        else { const ta = document.createElement('textarea'); ta.value = text; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); document.body.removeChild(ta); }
    }
</script>
