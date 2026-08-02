{{-- Inline styles matching index.html (masonry + gallery image hover) --}}
<style>
    .masonry-grid {
        column-count: 2;
        column-gap: 12px;
    }
    @media (min-width: 768px) {
        .masonry-grid { column-count: 3; column-gap: 16px; }
    }
    @media (min-width: 1024px) {
        .masonry-grid { column-count: 4; column-gap: 16px; }
    }
    .masonry-item {
        break-inside: avoid;
        margin-bottom: 12px;
    }
    .gallery-img {
        transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s;
    }
    .gallery-img:hover {
        transform: scale(1.04);
    }
</style>

<!-- ==================== GALLERY (PREMIUM + LIGHTBOX) ==================== -->
<section id="gallery" class="bg-slate-900 py-20">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-end justify-between mb-9">
            <div>
                <div class="text-emerald-400 text-xs tracking-[2px] font-semibold">MOMENTS</div>
                <h2 class="text-white text-5xl tracking-tighter font-bold heading-serif">গ্যালারি</h2>
            </div>
            @if($galleryItems->isNotEmpty())
                <button onclick="openGalleryModal()"
                        class="text-emerald-400 hover:text-emerald-300 text-sm flex items-center gap-2 font-medium">
                    সম্পূর্ণ গ্যালারি <i class="fas fa-images"></i>
                </button>
            @endif
        </div>

        @if($galleryItems->isNotEmpty())
            <div class="masonry-grid" id="galleryGrid">
                {{-- Populated by JS --}}
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @for($i = 0; $i < 4; $i++)
                    <div class="aspect-[4/3] rounded-3xl bg-white/5 border border-white/10 flex flex-col items-center justify-center text-white/30">
                        <i class="fas fa-image text-4xl mb-2"></i>
                        <p class="text-xs">শীঘ্রই আসছে</p>
                    </div>
                @endfor
            </div>
        @endif
    </div>
</section>

<!-- ==================== LIGHTBOX MODAL ==================== -->
<div id="lightbox" onclick="closeLightbox()" class="hidden fixed inset-0 z-[220] bg-black/95 flex items-center justify-center p-4">
    <div onclick="event.stopImmediatePropagation()" class="relative max-w-[1100px] w-full" id="lightboxContent">
        <img id="lightboxImage" class="max-h-[88dvh] w-full object-contain rounded-2xl shadow-2xl" alt="">
        <div class="absolute top-4 right-4 flex gap-2">
            <button onclick="closeLightbox()" class="bg-white/90 hover:bg-white text-black px-5 py-2 text-sm rounded-2xl font-medium">বন্ধ করুন</button>
        </div>
        <div id="lightboxCaption" class="text-center text-white/70 text-sm mt-4"></div>
    </div>
</div>

{{-- Hidden JSON data for gallery (last 10 uploads) --}}
<script type="application/json" id="galleryData" x-ignore>
[
    @foreach($galleryItems as $item)
    {
        "id": {{ $item->id }},
        "src": {!! json_encode(asset($item->image_path)) !!},
        "caption": {!! json_encode($item->caption) !!},
        "cat": {!! json_encode($item->category) !!}
    }@if(!$loop->last),@endif
    @endforeach
]
</script>

<script>
    // ==================== GALLERY + LIGHTBOX (follows index.html) ====================
    let galleryImages = [];
    try { galleryImages = JSON.parse(document.getElementById('galleryData').textContent); } catch(e) {}

    function renderGallery() {
        const container = document.getElementById('galleryGrid');
        if (!container) return;
        container.innerHTML = '';

        galleryImages.forEach((img, index) => {
            const item = document.createElement('div');
            item.className = 'masonry-item group relative rounded-3xl overflow-hidden cursor-pointer border border-white/10';
            item.innerHTML = `
                <img src="${img.src}" class="gallery-img w-full object-cover" alt="${img.caption}">
                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/60 p-4">
                    <div class="text-white text-sm font-medium">${img.caption}</div>
                    ${img.cat ? `<div class="text-[10px] text-white/70">${img.cat}</div>` : ''}
                </div>
                <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition bg-white/90 text-black text-[10px] px-2 py-px rounded font-medium">বড় করে দেখুন</div>
            `;
            item.onclick = () => openLightbox(index);
            container.appendChild(item);
        });
    }

    let currentLightboxIndex = 0;

    function openLightbox(index) {
        currentLightboxIndex = index;
        const lb = document.getElementById('lightbox');
        const imgEl = document.getElementById('lightboxImage');
        const caption = document.getElementById('lightboxCaption');

        imgEl.src = galleryImages[index].src;
        caption.innerHTML = `<span class="font-medium text-white">${galleryImages[index].caption}</span>` +
                            (galleryImages[index].cat ? ` — ${galleryImages[index].cat}` : '');
        lb.classList.remove('hidden');
        lb.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        const lb = document.getElementById('lightbox');
        lb.classList.remove('flex');
        lb.classList.add('hidden');
        document.body.style.overflow = '';
        // Restore default lightbox markup in case openGalleryModal replaced it
        restoreLightbox();
    }

    // Restore the lightbox to its default single-image layout (used after openGalleryModal)
    function restoreLightbox() {
        const content = document.getElementById('lightboxContent');
        content.innerHTML = `
            <img id="lightboxImage" class="max-h-[88dvh] w-full object-contain rounded-2xl shadow-2xl" alt="">
            <div class="absolute top-4 right-4 flex gap-2">
                <button onclick="closeLightbox()" class="bg-white/90 hover:bg-white text-black px-5 py-2 text-sm rounded-2xl font-medium">বন্ধ করুন</button>
            </div>
            <div id="lightboxCaption" class="text-center text-white/70 text-sm mt-4"></div>
        `;
    }

    function openGalleryModal() {
        const lb = document.getElementById('lightbox');
        const content = document.getElementById('lightboxContent');

        content.innerHTML = `
            <div class="max-w-[1100px] mx-auto">
                <div class="flex justify-between items-center mb-4 px-1">
                    <div class="text-white text-xl font-semibold">সম্পূর্ণ গ্যালারি</div>
                    <button onclick="closeLightbox()" class="bg-white/90 px-5 py-1.5 text-sm rounded-2xl font-medium">বন্ধ করুন</button>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3" id="fullGalleryGrid"></div>
            </div>
        `;

        const grid = document.getElementById('fullGalleryGrid');
        galleryImages.forEach((img, i) => {
            const el = document.createElement('div');
            el.className = 'overflow-hidden rounded-2xl relative group cursor-pointer';
            el.innerHTML = `
                <img src="${img.src}" class="w-full aspect-[4/3] object-cover group-hover:scale-105 transition" alt="${img.caption}">
                <div class="absolute inset-x-0 bottom-0 p-3 bg-gradient-to-t from-black/80">
                    <div class="text-white text-sm">${img.caption}</div>
                </div>
            `;
            el.onclick = () => {
                closeLightbox();
                setTimeout(() => openLightbox(i), 120);
            };
            grid.appendChild(el);
        });

        lb.classList.remove('hidden');
        lb.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowRight' && !document.getElementById('lightbox').classList.contains('hidden')) {
            const next = (currentLightboxIndex + 1) % galleryImages.length;
            openLightbox(next);
        }
        if (e.key === 'ArrowLeft' && !document.getElementById('lightbox').classList.contains('hidden')) {
            const prev = (currentLightboxIndex - 1 + galleryImages.length) % galleryImages.length;
            openLightbox(prev);
        }
    });

    // Initial render
    renderGallery();
</script>
