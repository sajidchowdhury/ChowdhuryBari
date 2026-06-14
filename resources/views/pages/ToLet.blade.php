 <!-- ==================== CLASSIFIEDS / RENT ADS ==================== -->
    <section id="classifieds" class="max-w-7xl mx-auto px-6 pt-20 pb-16">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="section-header text-5xl tracking-tighter font-bold heading-serif">ভাড়া ও বিজ্ঞাপন</h2>
                <p class="text-slate-600 mt-1">এলাকার ভিতরে বাসা ভাড়া ও অন্যান্য সেবা</p>
            </div>
            <button onclick="showPostAdModal()" 
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-emerald-800 hover:bg-emerald-900 text-white text-sm font-semibold shadow-sm transition-all">
                <i class="fas fa-plus"></i> <span class="hidden sm:inline">বিজ্ঞাপন দিন</span>
            </button>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5" id="classifiedsGrid">
            <!-- Populated dynamically by JS -->
        </div>
    </section>
