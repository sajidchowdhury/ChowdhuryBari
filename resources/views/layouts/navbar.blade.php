<!-- ==================== PREMIUM NAVBAR ==================== -->
<nav class="bg-white/95 backdrop-blur-xl border-b border-slate-200 sticky top-0 z-[100]">
    <div class="max-w-7xl mx-auto">
        <div class="px-5 lg:px-8 py-4 flex items-center justify-between">
            
            <!-- Logo - Now Much More Prominent & Clean -->
            <a href="#home" class="flex items-center gap-3.5 group">
                <div class="w-14 h-14 lg:w-[58px] lg:h-[58px] rounded-2xl overflow-hidden 
                            bg-white shadow-lg ring-1 ring-emerald-900/10 
                            group-hover:ring-emerald-700/30 group-active:scale-95 
                            transition-all duration-300 flex items-center justify-center p-1.5">
                    <img src="{{ asset('img/logo.png') }}" 
                         alt="চৌধুরীপাড়াস্থ সমাজ উন্নায়ন সংস্থা" 
                         class="w-full h-full object-contain">
                </div>
                <div class="leading-none">
                    <div class="font-bold text-[21px] lg:text-[23px] tracking-tighter heading-serif 
                                text-emerald-900 group-hover:text-emerald-800 transition">
                        চৌধুরীপাড়াস্থ
                    </div>
                    <div class="text-[9.5px] text-emerald-700 -mt-0.5 font-semibold tracking-[1.6px]">
                        সমাজ উন্নায়ন সংস্থা
                    </div>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center gap-x-8 text-[14.5px]">
                <a href="#services" class="nav-link font-medium text-slate-600 hover:text-emerald-800 transition">সেবাসমূহ</a>
                <a href="#coverage" class="nav-link font-medium text-slate-600 hover:text-emerald-800 transition">এলাকা</a>
                <a href="#leadership" class="nav-link font-medium text-slate-600 hover:text-emerald-800 transition">নেতৃত্ব</a>
                <a href="#notices" class="nav-link font-medium text-slate-600 hover:text-emerald-800 transition">নোটিশ</a>
                <a href="#gallery" class="nav-link font-medium text-slate-600 hover:text-emerald-800 transition">গ্যালারি</a>
                <a href="#classifieds" class="nav-link font-medium text-slate-600 hover:text-emerald-800 transition">বিজ্ঞাপন</a>
                
                <!-- Special Delivery Link with Gold Accent -->
                <a href="#" onclick="openDeliveryFinder(); return false;" 
                   class="nav-link font-semibold text-emerald-700 hover:text-emerald-900 flex items-center gap-1.5 transition">
                    <i class="fas fa-motorcycle text-sm"></i>
                    <span>ডেলিভারি লোকেশন</span>
                </a>
            </div>

            <!-- Desktop CTA -->
            <div class="hidden lg:flex items-center">
                <button onclick="openMemberLoginModal()"
                   class="px-6 py-2.5 text-sm font-semibold rounded-2xl border border-emerald-700/70 
                          hover:bg-emerald-50 hover:border-emerald-700 text-emerald-800 transition-all 
                          flex items-center gap-2 active:scale-[0.985]">
                    <i class="fas fa-user-check"></i>
                    <span>সদস্য লগইন</span>
                </button>
            </div>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-btn"
                    class="lg:hidden w-11 h-11 flex items-center justify-center text-2xl text-emerald-800 
                           hover:bg-emerald-50 rounded-2xl transition-colors">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</nav>

<!-- ==================== MOBILE DRAWER ==================== -->
<div id="mobile-drawer" class="hidden fixed inset-0 z-[200] lg:hidden">
    <!-- Backdrop -->
    <div onclick="closeMobileMenu()" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    
    <!-- Drawer -->
    <div class="nav-drawer absolute right-0 top-0 h-full w-[82%] max-w-[320px] bg-white shadow-2xl flex flex-col">
        <div class="px-6 pt-6 pb-4 border-b flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-800 to-emerald-700 flex items-center justify-center text-white">
                    <i class="fas fa-users"></i>
                </div>
                <span class="font-bold text-xl heading-serif text-emerald-900">চৌধুরীপাড়া</span>
            </div>
            <button onclick="closeMobileMenu()" class="w-10 h-10 flex items-center justify-center text-3xl text-slate-400 hover:text-slate-600">×</button>
        </div>

        <div class="flex-1 px-3 py-6 text-[17px]">
            <a href="#about" onclick="closeMobileMenu()" class="flex items-center gap-4 px-5 py-[15px] hover:bg-emerald-50 rounded-2xl text-slate-700 font-medium"><i class="fas fa-info-circle w-5 text-emerald-700"></i> আমাদের সম্পর্কে</a>
            <a href="#services" onclick="closeMobileMenu()" class="flex items-center gap-4 px-5 py-[15px] hover:bg-emerald-50 rounded-2xl text-slate-700 font-medium"><i class="fas fa-hand-holding-heart w-5 text-emerald-700"></i> সেবাসমূহ</a>
            <a href="#coverage" onclick="closeMobileMenu()" class="flex items-center gap-4 px-5 py-[15px] hover:bg-emerald-50 rounded-2xl text-slate-700 font-medium"><i class="fas fa-map-marked w-5 text-emerald-700"></i> আওতাধীন এলাকা</a>
            <a href="#leadership" onclick="closeMobileMenu()" class="flex items-center gap-4 px-5 py-[15px] hover:bg-emerald-50 rounded-2xl text-slate-700 font-medium"><i class="fas fa-user-tie w-5 text-emerald-700"></i> নেতৃত্ব</a>
            <a href="#notices" onclick="closeMobileMenu()" class="flex items-center gap-4 px-5 py-[15px] hover:bg-emerald-50 rounded-2xl text-slate-700 font-medium"><i class="fas fa-bullhorn w-5 text-emerald-700"></i> নোটিশ বোর্ড</a>
            <a href="#gallery" onclick="closeMobileMenu()" class="flex items-center gap-4 px-5 py-[15px] hover:bg-emerald-50 rounded-2xl text-slate-700 font-medium"><i class="fas fa-images w-5 text-emerald-700"></i> গ্যালারি</a>
            <a href="#classifieds" onclick="closeMobileMenu()" class="flex items-center gap-4 px-5 py-[15px] hover:bg-emerald-50 rounded-2xl text-slate-700 font-medium"><i class="fas fa-home w-5 text-emerald-700"></i> ভাড়া ও বিজ্ঞাপন</a>
            <a href="#" onclick="closeMobileMenu(); openDeliveryFinder(); return false;" class="flex items-center gap-4 px-5 py-[15px] hover:bg-emerald-50 rounded-2xl text-emerald-800 font-semibold">
                <i class="fas fa-motorcycle w-5 text-emerald-700"></i> ডেলিভারি লোকেশন খুঁজুন
            </a>
            <div class="h-px bg-slate-200 my-3 mx-5"></div>
            <a href="#events" onclick="closeMobileMenu()" class="flex items-center gap-4 px-5 py-[15px] hover:bg-emerald-50 rounded-2xl text-slate-700 font-medium"><i class="fas fa-calendar-alt w-5 text-emerald-700"></i> আসন্ন অনুষ্ঠান</a>
        </div>

        <div class="p-6 border-t bg-slate-50 space-y-3">
            <button onclick="closeMobileMenu(); openMemberLoginModal()" 
               class="w-full flex items-center justify-center gap-2 text-center py-3.5 border border-emerald-700/60 rounded-2xl font-semibold text-emerald-800 hover:bg-emerald-50">
                <i class="fas fa-user-check"></i>
                <span>সদস্য লগইন</span>
            </button>
            <a href="#join" onclick="closeMobileMenu()" 
               class="block w-full text-center py-3.5 bg-emerald-800 hover:bg-emerald-900 transition-colors rounded-2xl font-semibold text-white">সদস্য হোন</a>
        </div>
    </div>
</div>
