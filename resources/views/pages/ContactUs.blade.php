 <!-- ==================== CONTACT ==================== -->
    <section id="contact" class="max-w-7xl mx-auto px-6 py-20">
        <div class="grid lg:grid-cols-2 gap-x-16 gap-y-12">
            <div>
                <div class="uppercase text-xs tracking-[2px] text-emerald-700 font-semibold mb-2">GET IN TOUCH</div>
                <h2 class="text-6xl tracking-[-1.6px] leading-none font-bold heading-serif mb-8">যোগাযোগ করুন</h2>
                
                <div class="space-y-6 text-[15px]">
                    <div class="flex gap-4">
                        <i class="fas fa-map-marker-alt text-emerald-700 mt-1 w-5"></i>
                        <div>
                            <div class="font-semibold">অফিস</div>
                            <div class="text-slate-600">চৌধুরীপাড়া</div>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <i class="fas fa-phone text-emerald-700 mt-1 w-5"></i>
                        <div>
                            <div class="font-semibold">হটলাইন</div>
                            <a href="tel:017XXXXXXXX" class="text-emerald-700 hover:underline">০১৭১১-২২৩৩৪৪</a>
                            <div class="text-xs text-slate-500">সকাল ৮টা — রাত ১০টা</div>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <i class="fas fa-envelope text-emerald-700 mt-1 w-5"></i>
                        <div>
                            <div class="font-semibold">ইমেইল</div>
                            <a href="mailto:info@chowdhuripara.org" class="text-emerald-700 hover:underline">info@chowdhuripara.org</a>
                        </div>
                    </div>
                </div>

                <div class="mt-9 flex gap-3">
                    <a href="https://wa.me/8801711223344" target="_blank" 
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl border border-emerald-200 hover:bg-emerald-50 text-emerald-700 text-sm font-medium">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                    <a href="#" onclick="copyToClipboard('01711223344')" 
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl border border-emerald-200 hover:bg-emerald-50 text-emerald-700 text-sm font-medium">
                        <i class="fas fa-copy"></i> নম্বর কপি করুন
                    </a>
                </div>
            </div>

            <!-- Contact Form (UI only) -->
            <div class="bg-white rounded-3xl p-8 border border-slate-100 modern-shadow">
                <form id="contactForm" onsubmit="handleContactSubmit(event)">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1.5">আপনার নাম</label>
                            <input type="text" required class="w-full border border-slate-200 focus:border-emerald-700 transition rounded-2xl px-4 py-3 text-sm outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1.5">মোবাইল নম্বর</label>
                            <input type="tel" required class="w-full border border-slate-200 focus:border-emerald-700 transition rounded-2xl px-4 py-3 text-sm outline-none">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5">বিষয়</label>
                        <select class="w-full border border-slate-200 focus:border-emerald-700 transition rounded-2xl px-4 py-3 text-sm outline-none">
                            <option>সাধারণ অনুসন্ধান</option>
                            <option>নিরাপত্তা সংক্রান্ত</option>
                            <option>রাস্তা/ড্রেনেজ সমস্যা</option>
                            <option>সদস্যপদ / ফি</option>
                            <option>ভাড়া সংক্রান্ত</option>
                            <option>অন্যান্য</option>
                        </select>
                    </div>
                    <div class="mt-4">
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5">আপনার বার্তা</label>
                        <textarea rows="4" required class="w-full border border-slate-200 focus:border-emerald-700 transition rounded-3xl px-4 py-3 text-sm outline-none resize-y"></textarea>
                    </div>
                    <button type="submit" 
                            class="mt-5 w-full py-[15px] bg-emerald-800 hover:bg-emerald-900 active:bg-black transition-all text-white font-semibold rounded-2xl text-sm tracking-wide">
                        বার্তা পাঠান
                    </button>
                    <div class="text-center text-[11px] text-slate-400 mt-3">আমরা সাধারণত ২৪ ঘণ্টার মধ্যে উত্তর দেই</div>
                </form>
            </div>
        </div>
    </section>