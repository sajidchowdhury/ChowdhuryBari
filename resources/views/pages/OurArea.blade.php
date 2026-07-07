@php
    $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    $toBn = function($n) use ($bn) { return str_replace(range(0,9), $bn, (string) $n); };
    $totalBuildingsOnAllRoads = $roads->sum(fn($r) => $r->buildings->count());
@endphp

<section id="coverage" class="max-w-7xl mx-auto px-6 pt-20 pb-24" x-data="ourAreaSearch()">
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-y-3 mb-10">
        <div>
            <div class="uppercase text-xs tracking-[2px] text-emerald-700 font-semibold mb-1">OUR AREA</div>
            <h2 class="section-header text-5xl tracking-tighter font-bold heading-serif">আওতাধীন এলাকা</h2>
        </div>
        <p class="text-lg text-slate-600 max-w-sm">{{ $toBn($roads->count()) }}টি রাস্তায় {{ $toBn($totalBuildingsOnAllRoads) }}+ ভবন — সবকিছু এক নজরে</p>
    </div>

    {{-- Search bar --}}
    @if($roads->isNotEmpty() && $totalBuildingsOnAllRoads > 0)
    <div class="mb-8 max-w-2xl mx-auto">
        <div class="relative">
            <input type="text"
                   x-model="searchQuery"
                   placeholder="ভবনের নাম, মালিকের নাম বা রাস্তার নাম দিয়ে খুঁজুন..."
                   class="w-full rounded-3xl border border-slate-200 bg-white px-12 py-4 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition">
            <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <button x-show="searchQuery" x-cloak @click="searchQuery = ''" class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                <i class="fas fa-times-circle"></i>
            </button>
        </div>
        <p class="text-center text-xs text-slate-400 mt-2" x-show="searchQuery" x-cloak>
            খুঁজে পাওয়া ভবন: <span x-text="filteredCount" class="font-semibold text-emerald-700"></span>টি
        </p>
    </div>
    @endif

    {{-- Road sections with buildings --}}
    @if($roads->isNotEmpty())
        <div class="space-y-8">
            @foreach($roads as $road)
                <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    {{-- Road header --}}
                    <div class="p-5 bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-emerald-100">
                        <div class="flex items-center gap-4">
                            @if($road->image_path)
                                <img src="{{ $road->image_url }}" alt="{{ $road->name }}" class="h-14 w-14 rounded-2xl object-cover">
                            @else
                                <div class="h-14 w-14 rounded-2xl bg-emerald-600 text-white flex items-center justify-center">
                                    <i class="fas fa-road text-xl"></i>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-slate-900">{{ $road->name }}</h3>
                                @if($road->description)
                                    <p class="text-slate-600 text-sm mt-0.5">{{ $road->description }}</p>
                                @endif
                                @if($road->tag_list)
                                    <div class="mt-2 flex flex-wrap gap-1">
                                        @foreach($road->tag_list as $tag)
                                            <span class="inline-flex items-center rounded-full bg-white border border-emerald-200 px-2 py-0.5 text-[10px] font-medium text-emerald-700">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <span class="rounded-full bg-white border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 whitespace-nowrap">
                                {{ $toBn($road->buildings->count()) }} ভবন
                            </span>
                        </div>
                    </div>

                    {{-- Buildings grid --}}
                    @if($road->buildings->isNotEmpty())
                        <div class="p-5">
                            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                                @foreach($road->buildings as $building)
                                    <div x-show="!searchQuery || matchesSearch('{{ strtolower($building->name . ' ' . $building->owner_name . ' ' . $road->name) }}', searchQuery)"
                                         class="group cursor-pointer rounded-2xl border border-slate-200 hover:border-emerald-300 hover:shadow-lg transition-all overflow-hidden"
                                         onclick="openBuildingModal({{ $building->id }})">
                                        <img src="{{ $building->image_url }}" alt="{{ $building->name }}" class="h-32 w-full object-cover group-hover:scale-105 transition-transform">
                                        <div class="p-3">
                                            <h4 class="font-semibold text-slate-900 group-hover:text-emerald-700 text-sm">{{ $building->name }}</h4>
                                            <p class="text-[11px] text-slate-500 mt-0.5">{{ ucfirst($building->structure_type) }} • {{ ucfirst($building->usage_type) }}</p>
                                            <p class="text-[11px] text-slate-500 mt-0.5">
                                                <i class="fas fa-user text-slate-400"></i> {{ $building->owner_name }}
                                            </p>
                                            <div class="mt-2 flex flex-wrap gap-1">
                                                <span class="text-[10px] bg-slate-100 px-1.5 py-0.5 rounded">{{ $toBn($building->total_flats) }} ফ্ল্যাট</span>
                                                @if($building->has_security)
                                                    <span class="text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded">নিরাপত্তা</span>
                                                @endif
                                                @if($building->has_cleaning)
                                                    <span class="text-[10px] bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded">পরিচ্ছন্নতা</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Hidden data for this building (used by modal) --}}
                                    <script type="application/json" id="building-data-{{ $building->id }}" x-ignore>
                                    {
                                        "id": {{ $building->id }},
                                        "name": {{ json_encode($building->name) }},
                                        "image": {{ json_encode($building->image_url) }},
                                        "road": {{ json_encode($road->name) }},
                                        "structure_type": {{ json_encode($building->structure_type) }},
                                        "usage_type": {{ json_encode($building->usage_type) }},
                                        "owner_name": {{ json_encode($building->owner_name) }},
                                        "owner_phone": {{ json_encode($building->owner_phone) }},
                                        "caretaker_name": {{ json_encode($building->caretaker_name) }},
                                        "caretaker_phone": {{ json_encode($building->caretaker_phone) }},
                                        "floor_count": {{ $building->floor_count }},
                                        "families_per_floor": {{ $building->families_per_floor }},
                                        "total_flats": {{ $building->total_flats }},
                                        "active_flats": {{ $building->active_flats }},
                                        "has_security": {{ $building->has_security ? 'true' : 'false' }},
                                        "has_cleaning": {{ $building->has_cleaning ? 'true' : 'false' }},
                                        "google_lt": {{ json_encode($building->google_lt) }},
                                        "google_ln": {{ json_encode($building->google_ln) }},
                                        "extra_info": {{ json_encode($building->extra_information) }}
                                    }
                                    </script>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="p-8 text-center text-slate-500 text-sm">এই রাস্তায় এখনো কোনো ভবন যোগ করা হয়নি।</div>
                    @endif
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

{{-- Building Detail Modal --}}
<div id="building-modal" class="fixed inset-0 z-[9999] overflow-y-auto" style="display: none;" x-cloak>
    <div class="fixed inset-0 bg-black/60" onclick="closeBuildingModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto" id="building-modal-content">
            <button onclick="closeBuildingModal()" class="absolute top-4 right-4 z-10 w-10 h-10 rounded-full bg-white/80 backdrop-blur shadow flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-white transition">
                <i class="fas fa-times text-lg"></i>
            </button>

            {{-- Building image --}}
            <img id="modal-image" src="" alt="" class="w-full h-56 object-cover rounded-t-3xl">

            <div class="p-6">
                {{-- Title + badges --}}
                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div>
                        <h3 id="modal-name" class="text-2xl font-bold text-slate-900"></h3>
                        <p id="modal-road" class="text-slate-500 text-sm mt-1"></p>
                    </div>
                    <div class="flex gap-2 flex-wrap" id="modal-badges"></div>
                </div>

                {{-- Info grid --}}
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="text-xs text-slate-400 font-semibold uppercase tracking-wide">মালিক</div>
                        <div id="modal-owner-name" class="font-semibold text-slate-800 mt-1"></div>
                        <div id="modal-owner-phone" class="text-sm text-emerald-700 mt-1"></div>
                    </div>
                    <div id="modal-caretaker-section" class="rounded-2xl bg-slate-50 p-4" style="display:none;">
                        <div class="text-xs text-slate-400 font-semibold uppercase tracking-wide">পাহারাদার / কেয়ারটেকার</div>
                        <div id="modal-caretaker-name" class="font-semibold text-slate-800 mt-1"></div>
                        <div id="modal-caretaker-phone" class="text-sm text-emerald-700 mt-1"></div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="text-xs text-slate-400 font-semibold uppercase tracking-wide">ফ্লোর ও পরিবার</div>
                        <div id="modal-floor-info" class="font-semibold text-slate-800 mt-1"></div>
                        <div id="modal-flat-info" class="text-sm text-slate-500 mt-0.5"></div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="text-xs text-slate-400 font-semibold uppercase tracking-wide">ধরন</div>
                        <div id="modal-type-info" class="font-semibold text-slate-800 mt-1"></div>
                    </div>
                </div>

                {{-- Google Map --}}
                <div id="modal-map-section" class="mt-4" style="display:none;">
                    <div class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-2">গুগল ম্যাপ লোকেশন</div>
                    <div class="rounded-2xl overflow-hidden border border-slate-200">
                        <iframe id="modal-map" src="" width="100%" height="250" style="border:0;" loading="lazy" allowfullscreen></iframe>
                    </div>
                </div>

                {{-- Extra info --}}
                <div id="modal-extra-section" class="mt-4" style="display:none;">
                    <div class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-1">অতিরিক্ত তথ্য</div>
                    <p id="modal-extra" class="text-sm text-slate-600 bg-slate-50 rounded-2xl p-3"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Alpine.js component for search functionality
    function ourAreaSearch() {
        return {
            searchQuery: '',
            filteredCount: 0,
            init() {
                this.updateCount();
                this.$watch('searchQuery', () => this.updateCount());
            },
            updateCount() {
                const query = this.searchQuery.toLowerCase().trim();
                if (!query) {
                    this.filteredCount = document.querySelectorAll('[onclick^="openBuildingModal"]').length;
                    return;
                }
                // Count visible building cards
                const cards = document.querySelectorAll('[onclick^="openBuildingModal"]');
                let count = 0;
                cards.forEach(card => {
                    const parent = card.closest('[x-show]');
                    if (parent) {
                        const style = window.getComputedStyle(parent);
                        if (style.display !== 'none') count++;
                    } else {
                        count++;
                    }
                });
                this.filteredCount = count;
            },
            matchesSearch(text, query) {
                if (!query) return true;
                return text.includes(query.toLowerCase().trim());
            }
        };
    }

    function openBuildingModal(id) {
        const script = document.getElementById('building-data-' + id);
        if (!script) return;
        const data = JSON.parse(script.textContent);

        document.getElementById('modal-image').src = data.image;
        document.getElementById('modal-image').alt = data.name;
        document.getElementById('modal-name').textContent = data.name;
        document.getElementById('modal-road').textContent = data.road;

        // Badges
        const badgesDiv = document.getElementById('modal-badges');
        const bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
        const toBn = n => String(n).replace(/[0-9]/g, d => bn[d]);
        badgesDiv.innerHTML = '';
        if (data.has_security) {
            badgesDiv.innerHTML += '<span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full"><i class="fas fa-shield-alt"></i> নিরাপত্তা</span>';
        }
        if (data.has_cleaning) {
            badgesDiv.innerHTML += '<span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full"><i class="fas fa-broom"></i> পরিচ্ছন্নতা</span>';
        }

        // Owner info
        document.getElementById('modal-owner-name').textContent = data.owner_name;
        const phoneEl = document.getElementById('modal-owner-phone');
        if (data.owner_phone) {
            phoneEl.innerHTML = '<i class="fas fa-phone"></i> ' + data.owner_phone;
            phoneEl.style.display = '';
        } else {
            phoneEl.style.display = 'none';
        }

        // Caretaker info
        const caretakerSection = document.getElementById('modal-caretaker-section');
        if (data.caretaker_name) {
            caretakerSection.style.display = '';
            document.getElementById('modal-caretaker-name').textContent = data.caretaker_name;
            const cPhone = document.getElementById('modal-caretaker-phone');
            if (data.caretaker_phone) {
                cPhone.innerHTML = '<i class="fas fa-phone"></i> ' + data.caretaker_phone;
                cPhone.style.display = '';
            } else {
                cPhone.style.display = 'none';
            }
        } else {
            caretakerSection.style.display = 'none';
        }

        // Floor info
        document.getElementById('modal-floor-info').textContent = toBn(data.floor_count) + ' তলা × ' + toBn(data.families_per_floor) + ' পরিবার/তলা';
        document.getElementById('modal-flat-info').textContent = 'মোট ' + toBn(data.total_flats) + ' ফ্ল্যাট, ' + toBn(data.active_flats) + ' সক্রিয়';

        // Type info
        const typeMap = {building: 'ভবন', tin_shed: 'টিন শেড', other: 'অন্যান্য'};
        const usageMap = {residential: 'আবাসিক', shop: 'দোকান', mixed: 'মিশ্র'};
        document.getElementById('modal-type-info').textContent = (typeMap[data.structure_type] || data.structure_type) + ' • ' + (usageMap[data.usage_type] || data.usage_type);

        // Google Map
        const mapSection = document.getElementById('modal-map-section');
        if (data.google_lt && data.google_ln) {
            mapSection.style.display = '';
            document.getElementById('modal-map').src = 'https://maps.google.com/maps?q=' + data.google_lt + ',' + data.google_ln + '&z=16&output=embed';
        } else {
            mapSection.style.display = 'none';
        }

        // Extra info
        const extraSection = document.getElementById('modal-extra-section');
        if (data.extra_info) {
            extraSection.style.display = '';
            document.getElementById('modal-extra').textContent = data.extra_info;
        } else {
            extraSection.style.display = 'none';
        }

        document.getElementById('building-modal').style.display = '';
        document.body.style.overflow = 'hidden';
    }

    function closeBuildingModal() {
        document.getElementById('building-modal').style.display = 'none';
        document.body.style.overflow = '';
    }

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeBuildingModal();
    });
</script>
