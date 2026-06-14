    <!-- ==================== NOTICES ==================== -->
    <section id="notices" class="max-w-7xl mx-auto px-6 pt-20 pb-16">
        <div class="flex items-center justify-between mb-9">
            <div>
                <h2 class="section-header text-5xl tracking-tighter font-bold heading-serif">নোটিশ ও ঘোষণা</h2>
            </div>
            <button onclick="showAllNotices()" 
                    class="text-sm hidden md:flex items-center gap-2 font-semibold px-5 py-2.5 text-emerald-700 hover:bg-emerald-50 rounded-2xl">
                সব দেখুন <i class="fas fa-arrow-right text-xs"></i>
            </button>
        </div>

        <div class="grid md:grid-cols-3 gap-5" id="noticesGrid">
            <!-- Populated by JS -->
        </div>
    </section>