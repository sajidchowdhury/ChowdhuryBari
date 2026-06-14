  <!-- ==================== FLOATING ACTIONS ==================== -->
    <div class="fixed bottom-5 right-5 flex flex-col gap-3 z-[90]">
        <!-- WhatsApp -->
        <a href="https://wa.me/8801711223344" target="_blank"
           class="w-12 h-12 flex items-center justify-center bg-[#25D366] text-white rounded-2xl shadow-xl hover:scale-105 transition floating-action"
           title="WhatsApp করুন">
            <i class="fab fa-whatsapp text-3xl"></i>
        </a>

        <!-- Quotation Button -->
        <div class="relative group">
            <button onclick="openQuotationPage()"
                    class="w-12 h-12 flex items-center justify-center bg-gradient-to-br from-amber-600 to-amber-700 text-white rounded-2xl shadow-xl hover:scale-105 transition floating-action"
                    title="প্রজেক্ট কোটেশন দেখুন">
                <i class="fas fa-file-invoice-dollar text-2xl"></i>
            </button>
            <div class="hidden lg:block absolute right-14 top-1/2 -translate-y-1/2 bg-zinc-800 text-white text-xs px-3 py-1.5 rounded-2xl opacity-0 group-hover:opacity-100 transition whitespace-nowrap pointer-events-none">
                কোটেশন দেখুন
            </div>
        </div>

        <!-- Quick Report -->
        <button onclick="showReportModal()"
                class="w-12 h-12 flex items-center justify-center bg-white border border-slate-300 shadow-xl text-emerald-800 rounded-2xl hover:scale-105 transition floating-action"
                title="সমস্যা রিপোর্ট করুন">
            <i class="fas fa-exclamation-triangle"></i>
        </button>

        <!-- Delivery Location Finder (very useful for members) -->
        <button onclick="openDeliveryFinder()"
                class="w-12 h-12 flex items-center justify-center bg-sky-600 hover:bg-sky-700 text-white rounded-2xl shadow-xl hover:scale-105 transition floating-action ring-4 ring-sky-200/50"
                title="ডেলিভারির জন্য লোকেশন খুঁজুন ও শেয়ার করুন">
            <i class="fas fa-motorcycle text-2xl"></i>
        </button>
    </div>