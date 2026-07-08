{{-- ==================== DELIVERY LOCATION FINDER ====================
     Modal + JS for searching buildings by phone / owner / building number
     and sharing the location with a delivery man (copy / WhatsApp / Maps).
     Data is read from the road-data-{id} JSON scripts emitted by pages/OurArea.
     Included globally via layouts/app.blade.php. --}}

<!-- ==================== DELIVERY LOCATION FINDER MODAL ==================== -->
<div id="deliveryLocationModal" onclick="if (event.target.id === 'deliveryLocationModal') closeDeliveryModal()" class="hidden fixed inset-0 z-[230] flex items-end lg:items-center justify-center bg-black/70 backdrop-blur-sm p-0 lg:p-4">
    <div onclick="event.stopImmediatePropagation()" class="modal bg-white w-full lg:w-[620px] lg:rounded-3xl lg:m-6 rounded-t-3xl max-h-[92dvh] flex flex-col overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-5 border-b flex items-center justify-between bg-emerald-900 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-motorcycle text-xl"></i>
                </div>
                <div>
                    <div class="font-bold text-xl">ডেলিভারি লোকেশন খুঁজুন</div>
                    <div class="text-emerald-200 text-xs">ফুডপান্ডা, পাঠাও, উবার ইত্যাদির জন্য সঠিক লোকেশন শেয়ার করুন</div>
                </div>
            </div>
            <button onclick="closeDeliveryModal()" class="text-3xl text-white/70 hover:text-white leading-none w-9 h-9 flex items-center justify-center">×</button>
        </div>

        <div class="p-6 flex-1 overflow-auto">
            <!-- Search -->
            <div class="mb-4">
                <label class="text-xs font-semibold text-emerald-700 tracking-wider">ফোন নম্বর বা বাড়ির নাম লিখুন</label>
                <div class="relative mt-1.5">
                    <i class="fas fa-search absolute left-5 top-4 text-emerald-600"></i>
                    <input id="deliverySearchInput"
                           onkeyup="searchDeliveryLocations()"
                           type="text"
                           class="w-full pl-12 pr-4 py-3.5 border border-emerald-200 focus:border-emerald-700 rounded-2xl text-lg outline-none"
                           placeholder="01711-445566 অথবা মালিকের নাম / বাড়ির নাম">
                </div>
                <div class="text-[11px] text-slate-500 mt-1.5 px-1">ফোনের শেষ ৪-৫ ডিজিট দিলেও খুঁজে পাবেন</div>
            </div>

            <!-- Results -->
            <div id="deliveryResults" class="space-y-2 min-h-[120px]">
                <!-- Populated by JS -->
                <div class="text-center py-8 text-slate-400 text-sm" id="deliveryResultsPlaceholder">
                    ফোন নম্বর বা বাড়ির নাম লিখে সার্চ করুন
                </div>
            </div>

            <!-- Share Panel (shown after selecting a building) -->
            <div id="deliverySharePanel" class="hidden mt-4 border border-emerald-100 rounded-3xl p-5 bg-emerald-50/40">
                <div class="text-xs uppercase tracking-[1px] text-emerald-700 font-semibold mb-2">শেয়ার করার জন্য প্রস্তুত</div>

                <div id="shareBuildingInfo" class="mb-4 text-sm"></div>

                <div class="bg-white border border-emerald-200 rounded-2xl p-4 mb-4 font-mono text-xs leading-relaxed text-slate-700 whitespace-pre-line" id="shareLocationText">
                    <!-- JS injected formatted text -->
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <button onclick="copyFullDeliveryInfo()"
                            class="flex items-center justify-center gap-2 py-3 bg-emerald-800 hover:bg-emerald-900 text-white font-semibold rounded-2xl text-sm">
                        <i class="fas fa-copy"></i>
                        <span>পুরো তথ্য কপি করুন</span>
                    </button>

                    <button onclick="copyMapsLinkOnly()"
                            class="flex items-center justify-center gap-2 py-3 border border-emerald-700 text-emerald-800 hover:bg-emerald-100 font-semibold rounded-2xl text-sm">
                        <i class="fas fa-link"></i>
                        <span>শুধু ম্যাপ লিংক কপি</span>
                    </button>

                    <button onclick="shareViaWhatsApp()"
                            class="flex items-center justify-center gap-2 py-3 bg-[#25D366] hover:bg-[#1da851] text-white font-semibold rounded-2xl text-sm col-span-1 sm:col-span-2">
                        <i class="fab fa-whatsapp"></i>
                        <span>WhatsApp-এ পাঠান (ডেলিভারি ম্যানকে)</span>
                    </button>

                    <a id="deliveryOpenMapsBtn" target="_blank" rel="noopener"
                       class="flex items-center justify-center gap-2 py-3 border border-slate-300 hover:bg-white rounded-2xl text-sm font-medium text-center">
                        <i class="fas fa-external-link-alt"></i>
                        <span>Google Maps-এ খুলুন</span>
                    </a>

                    <button onclick="closeDeliveryModalAndShowBuilding()"
                            class="flex items-center justify-center gap-2 py-3 border border-slate-300 hover:bg-white rounded-2xl text-sm font-medium">
                        <i class="fas fa-info-circle"></i>
                        <span>বিস্তারিত দেখুন</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="p-4 border-t bg-slate-50 text-center text-[11px] text-slate-500">
            এই লোকেশন সরাসরি ডেলিভারি ম্যানকে পাঠালে তিনি সহজে আপনার বাড়ি খুঁজে পাবেন
        </div>
    </div>
</div>

<!-- ==================== TOAST (used by delivery finder + others) ==================== -->
<div id="toast" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-[300] bg-slate-900 text-white text-sm px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-2">
    <i class="fas fa-check-circle text-emerald-400"></i>
    <span id="toastMessage"></span>
</div>

<script>
    // ==================== DELIVERY LOCATION FINDER ====================
    let selectedDeliveryBuilding = null;
    let selectedDeliveryRoad = null;

    // Cache of all buildings flattened from the road-data-{id} scripts.
    function getAllBuildingsFlat() {
        const all = [];
        document.querySelectorAll('script[type="application/json"][id^="road-data-"]').forEach(script => {
            let road;
            try { road = JSON.parse(script.textContent); } catch(e) { return; }
            if (!road || !road.buildings) return;
            road.buildings.forEach(b => {
                all.push({
                    id: b.id,
                    name: b.name || '',
                    owner: b.owner_name || '',
                    phone: b.owner_phone || '',
                    caretaker: b.caretaker_name || '',
                    caretakerPhone: b.caretaker_phone || '',
                    lat: b.google_lt ? parseFloat(b.google_lt) : null,
                    lng: b.google_ln ? parseFloat(b.google_ln) : null,
                    roadName: road.name,
                    roadId: road.id,
                });
            });
        });
        return all;
    }

    function openDeliveryFinder() {
        const modal = document.getElementById('deliveryLocationModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Reset state
        document.getElementById('deliverySharePanel').classList.add('hidden');
        document.getElementById('deliveryResults').innerHTML = `
            <div class="text-center py-8 text-slate-400 text-sm" id="deliveryResultsPlaceholder">
                ফোন নম্বর বা বাড়ির নাম লিখে সার্চ করুন
            </div>
        `;
        document.getElementById('deliverySearchInput').value = '';
        selectedDeliveryBuilding = null;
        selectedDeliveryRoad = null;

        // Focus search immediately
        setTimeout(() => {
            const input = document.getElementById('deliverySearchInput');
            if (input) input.focus();
        }, 350);
    }

    function closeDeliveryModal() {
        const modal = document.getElementById('deliveryLocationModal');
        if (!modal) return;
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.getElementById('deliverySharePanel').classList.add('hidden');
    }

    function searchDeliveryLocations() {
        const query = document.getElementById('deliverySearchInput').value.trim().toLowerCase();
        const resultsContainer = document.getElementById('deliveryResults');
        const sharePanel = document.getElementById('deliverySharePanel');

        sharePanel.classList.add('hidden');

        if (!query || query.length < 2) {
            resultsContainer.innerHTML = `
                <div class="text-center py-8 text-slate-400 text-sm">
                    ফোন নম্বর বা বাড়ির নাম লিখে সার্চ করুন
                </div>
            `;
            return;
        }

        const allBuildings = getAllBuildingsFlat();
        const digitsOnly = query.replace(/\D/g, '');

        const matches = allBuildings.filter(b => {
            const phoneMatch = b.phone && b.phone.toLowerCase().includes(query);
            const nameMatch = b.name && b.name.toLowerCase().includes(query);
            const ownerMatch = b.owner && b.owner.toLowerCase().includes(query);
            const lastDigitsMatch = b.phone && digitsOnly.length >= 3 && b.phone.replace(/\D/g, '').endsWith(digitsOnly);
            return phoneMatch || nameMatch || ownerMatch || lastDigitsMatch;
        });

        if (matches.length === 0) {
            resultsContainer.innerHTML = `
                <div class="text-center py-6 text-slate-500 text-sm border border-dashed rounded-2xl">
                    কোনো মিল পাওয়া যায়নি।<br>
                    <span class="text-xs">ফোন নম্বরের শেষের অংশ বা বাড়ির নাম সঠিকভাবে লিখুন</span>
                </div>
            `;
            return;
        }

        resultsContainer.innerHTML = '';

        matches.forEach(b => {
            const hasLoc = (b.lat !== null && !isNaN(b.lat) && b.lng !== null && !isNaN(b.lng));
            const div = document.createElement('div');
            div.className = `border border-slate-200 hover:border-emerald-300 bg-white rounded-2xl p-4 cursor-pointer transition-all active:scale-[0.985]`;
            div.innerHTML = `
                <div class="flex justify-between items-start">
                    <div>
                        <div class="font-semibold text-base">${b.name} <span class="text-emerald-700 text-xs font-normal">(${b.roadName})</span></div>
                        <div class="text-sm text-slate-600">${b.owner || '—'}</div>
                    </div>
                    <div class="text-right text-xs">
                        <div class="font-mono text-emerald-700">${b.phone || '—'}</div>
                        ${hasLoc ? '<div class="text-[10px] text-emerald-600 mt-0.5"><i class="fas fa-map-marker-alt"></i> লোকেশন আছে</div>' : '<div class="text-[10px] text-slate-400 mt-0.5">ম্যাপ নেই</div>'}
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-2 text-xs">
                    ${hasLoc
                        ? '<span class="inline-block px-2.5 py-px bg-emerald-100 text-emerald-700 rounded-full font-medium">লোকেশন আছে</span><span class="text-emerald-600 flex items-center gap-1"><i class="fas fa-map-marker-alt"></i> ম্যাপ শেয়ার করুন</span>'
                        : '<span class="inline-block px-2.5 py-px bg-slate-100 text-slate-500 rounded-full font-medium">তথ্য শেয়ার করুন</span><span class="text-slate-500 flex items-center gap-1"><i class="fas fa-share-alt"></i> কপি / WhatsApp</span>'}
                </div>
            `;
            div.onclick = () => selectDeliveryBuilding(b);
            resultsContainer.appendChild(div);
        });
    }

    function selectDeliveryBuilding(buildingFlat) {
        selectedDeliveryBuilding = buildingFlat;
        selectedDeliveryRoad = { id: buildingFlat.roadId, name: buildingFlat.roadName };

        const resultsContainer = document.getElementById('deliveryResults');
        const sharePanel = document.getElementById('deliverySharePanel');

        // Hide results, show share panel
        resultsContainer.innerHTML = '';
        sharePanel.classList.remove('hidden');

        // Fill info header
        document.getElementById('shareBuildingInfo').innerHTML = `
            <div class="font-semibold text-lg">${buildingFlat.name}</div>
            <div class="text-sm text-slate-600">${buildingFlat.roadName} • ${buildingFlat.owner || '—'}</div>
            <div class="text-xs text-emerald-700 mt-0.5">${buildingFlat.phone || ''}</div>
        `;

        const hasCoords = (buildingFlat.lat !== null && !isNaN(buildingFlat.lat) && buildingFlat.lng !== null && !isNaN(buildingFlat.lng));
        const mapsUrl = hasCoords
            ? `https://www.google.com/maps/search/?api=1&query=${buildingFlat.lat},${buildingFlat.lng}`
            : '';

        const fullText = `চৌধুরীপাড়াস্থ সমাজ উন্নায়ন সংস্থা
${buildingFlat.roadName}, ${buildingFlat.name}
মালিক/সদস্য: ${buildingFlat.owner || '—'}
ফোন: ${buildingFlat.phone || '—'}
${hasCoords ? 'Google Maps লোকেশন:\n' + mapsUrl : '(এই বাড়ির জন্য ম্যাপ লোকেশন যোগ করা হয়নি)'}`;

        document.getElementById('shareLocationText').textContent = fullText;

        // Set the open maps link
        const openBtn = document.getElementById('deliveryOpenMapsBtn');
        if (hasCoords) {
            openBtn.href = mapsUrl;
            openBtn.classList.remove('pointer-events-none', 'opacity-50');
        } else {
            openBtn.href = '#';
            openBtn.classList.add('pointer-events-none', 'opacity-50');
        }
    }

    function copyFullDeliveryInfo() {
        const textEl = document.getElementById('shareLocationText');
        if (!textEl) return;
        const text = textEl.textContent || textEl.innerText;

        const done = () => showToast('পুরো লোকেশন তথ্য কপি হয়েছে! ডেলিভারি ম্যানকে পাঠান');

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done).catch(() => fallbackCopy(text, done));
        } else {
            fallbackCopy(text, done);
        }
    }

    function copyMapsLinkOnly() {
        if (!selectedDeliveryBuilding) return;
        const b = selectedDeliveryBuilding;
        if (b.lat === null || isNaN(b.lat) || b.lng === null || isNaN(b.lng)) {
            showToast('এই বাড়ির জন্য ম্যাপ লোকেশন যোগ করা হয়নি', true);
            return;
        }
        const link = `https://www.google.com/maps/search/?api=1&query=${b.lat},${b.lng}`;
        const done = () => showToast('শুধু Google Maps লিংক কপি হয়েছে');

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(link).then(done).catch(() => fallbackCopy(link, done));
        } else {
            fallbackCopy(link, done);
        }
    }

    function shareViaWhatsApp() {
        if (!selectedDeliveryBuilding) return;
        const b = selectedDeliveryBuilding;
        const hasCoords = (b.lat !== null && !isNaN(b.lat) && b.lng !== null && !isNaN(b.lng));
        const mapsUrl = hasCoords
            ? `https://www.google.com/maps/search/?api=1&query=${b.lat},${b.lng}`
            : '';

        const message = encodeURIComponent(
`চৌধুরীপাড়া — আমার বাড়ির সঠিক লোকেশন:
${b.roadName}, ${b.name}
${b.owner || ''}
ফোন: ${b.phone || ''}
${mapsUrl ? 'ম্যাপ: ' + mapsUrl : '(ম্যাপ লোকেশন এখনো যোগ হয়নি)'}

(চৌধুরীপাড়াস্থ সমাজ উন্নায়ন সংস্থা)`
        );

        window.open(`https://wa.me/?text=${message}`, '_blank');
    }

    function closeDeliveryModalAndShowBuilding() {
        closeDeliveryModal();
        // Open the existing building detail modal (from OurArea) if available
        if (selectedDeliveryBuilding && typeof showBuildingDetailsById === 'function') {
            setTimeout(() => showBuildingDetailsById(selectedDeliveryRoad.id, selectedDeliveryBuilding.id), 250);
        }
    }

    // ---------- helpers ----------
    function fallbackCopy(text, cb) {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); } catch(e) {}
        document.body.removeChild(ta);
        if (cb) cb();
    }

    let _toastTimer = null;
    function showToast(message, isError) {
        const toast = document.getElementById('toast');
        const msg = document.getElementById('toastMessage');
        if (!toast || !msg) { alert(message); return; }
        msg.textContent = message;
        toast.classList.remove('hidden');
        toast.querySelector('i').className = isError ? 'fas fa-exclamation-circle text-amber-400' : 'fas fa-check-circle text-emerald-400';
        if (_toastTimer) clearTimeout(_toastTimer);
        _toastTimer = setTimeout(() => toast.classList.add('hidden'), 2600);
    }

    // Close delivery modal on Escape (added safely)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('deliveryLocationModal');
            if (modal && !modal.classList.contains('hidden')) closeDeliveryModal();
        }
    });
</script>
