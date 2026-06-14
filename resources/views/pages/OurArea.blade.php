    <section id="coverage" class="max-w-7xl mx-auto px-6 pt-20 pb-24">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-y-3 mb-10">
            <div>
                <div class="uppercase text-xs tracking-[2px] text-emerald-700 font-semibold mb-1">OUR AREA</div>
                <h2 class="section-header text-5xl tracking-tighter font-bold heading-serif">আওতাধীন এলাকা</h2>
            </div>
            <p class="text-lg text-slate-600 max-w-sm">১৫টি রাস্তায় ৯০+ ভবন — সবকিছু এক নজরে</p>
        </div>

        <!-- Interactive Road Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4" id="roadGrid">
            <!-- Populated by JS -->
        </div>

        <div class="mt-6 text-center">
            <div onclick="showAllRoadsModal()" class="inline-flex cursor-pointer items-center gap-2 text-sm font-medium text-emerald-700 hover:text-emerald-800">
                <span>সম্পূর্ণ তালিকা ও বিস্তারিত দেখুন</span> <i class="fas fa-expand-arrows-alt"></i>
            </div>
        </div>

        <!-- Delivery Location Quick Tool -->
        <div class="mt-8 max-w-md mx-auto">
            <button onclick="openDeliveryFinder()" 
                    class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-sky-50 hover:bg-sky-100 border border-sky-200 text-sky-800 font-semibold rounded-3xl transition-all active:scale-[0.985]">
                <i class="fas fa-motorcycle text-xl"></i>
                <span>ডেলিভারি ম্যানকে লোকেশন পাঠাতে চান?</span>
            </button>
            <div class="text-center text-[11px] text-sky-600 mt-2">ফোন নম্বর বা বাড়ির নম্বর দিয়ে সহজে খুঁজুন → কপি/শেয়ার করুন</div>
        </div>
    </section>