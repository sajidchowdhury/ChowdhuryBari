 <!-- ==================== GALLERY (PREMIUM + LIGHTBOX) ==================== -->
    <section id="gallery" class="bg-slate-900 py-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-end justify-between mb-9">
                <div>
                    <div class="text-emerald-400 text-xs tracking-[2px] font-semibold">MOMENTS</div>
                    <h2 class="text-white text-5xl tracking-tighter font-bold heading-serif">গ্যালারি</h2>
                </div>
                <button onclick="openGalleryModal()" 
                        class="text-emerald-400 hover:text-emerald-300 text-sm flex items-center gap-2 font-medium">
                    সম্পূর্ণ গ্যালারি <i class="fas fa-images"></i>
                </button>
            </div>

            <div class="masonry-grid" id="galleryGrid">
                <!-- Populated by JS -->
            </div>
        </div>
    </section>