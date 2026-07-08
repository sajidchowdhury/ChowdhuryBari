<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>সদস্য ড্যাশবোর্ড — চৌধুরীপাড়াস্থ সমাজ উন্নায়ন সংস্থা</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700&family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', 'Noto Sans Bengali', system-ui, sans-serif; }
        .heading-serif { font-family: 'Playfair Display', 'Noto Sans Bengali', Georgia, serif; font-weight: 700; letter-spacing: -0.02em; }
        [x-cloak] { display: none !important; }

        /* Sidebar gradient */
        .sidebar-bg {
            background: linear-gradient(180deg, #064e3b 0%, #065f46 50%, #047857 100%);
        }
        .nav-item {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .nav-item:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateX(4px);
        }
        .nav-item.active {
            background: rgba(255, 255, 255, 0.18);
            border-left: 3px solid #fbbf24;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Gradient stat cards */
        .stat-card {
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.2);
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .tab-content { animation: fadeIn 0.35s ease; }

        /* Mobile bottom nav */
        @media (max-width: 1023px) {
            .mobile-nav-active {
                color: #065f46;
                font-weight: 600;
            }
            .mobile-nav-active .nav-icon {
                background: linear-gradient(135deg, #065f46, #0d9488);
                color: white;
            }
        }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 4px; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen" x-data="{ activeTab: 'dashboard' }">

<div class="flex min-h-screen">

    <!-- ============ DESKTOP SIDEBAR ============ -->
    <aside class="sidebar-bg w-64 fixed inset-y-0 left-0 hidden lg:flex flex-col text-white z-40">
        <!-- Logo header -->
        <div class="px-6 py-6 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center ring-2 ring-white/20">
                    <i class="fas fa-user-shield text-xl"></i>
                </div>
                <div>
                    <div class="font-bold text-lg heading-serif">সদস্য পোর্টাল</div>
                    <div class="text-emerald-200 text-[11px]">চৌধুরীপাড়া</div>
                </div>
            </div>
        </div>

        <!-- User card -->
        <div class="px-4 py-4">
            <div class="bg-white/10 backdrop-blur rounded-2xl p-3 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-amber-600 rounded-xl flex items-center justify-center text-emerald-900 font-bold text-lg shadow-inner">
                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-sm truncate">{{ $user->name }}</div>
                    <div class="text-emerald-200 text-[11px] truncate">{{ $user->phone }}</div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-2 space-y-1 overflow-y-auto">
            <button @click="activeTab='dashboard'" :class="{ 'active': activeTab==='dashboard' }"
                    class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium text-white/90">
                <i class="fas fa-th-large w-5 text-center"></i>
                <span>ড্যাশবোর্ড</span>
            </button>
            <button @click="activeTab='dues'" :class="{ 'active': activeTab==='dues' }"
                    class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium text-white/90">
                <i class="fas fa-wallet w-5 text-center"></i>
                <span>ডিউ ও পেমেন্ট</span>
            </button>
            <button @click="activeTab='gallery'" :class="{ 'active': activeTab==='gallery' }"
                    class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium text-white/90">
                <i class="fas fa-camera w-5 text-center"></i>
                <span>আমার গ্যালারি</span>
            </button>
            <button @click="activeTab='ranking'" :class="{ 'active': activeTab==='ranking' }"
                    class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium text-white/90">
                <i class="fas fa-trophy w-5 text-center"></i>
                <span>র‍্যাঙ্কিং ও স্কোর</span>
            </button>
        </nav>

        <!-- Bottom actions -->
        <div class="p-4 border-t border-white/10 space-y-2">
            <a href="{{ route('home') }}" class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-2xl text-sm text-white/80 hover:text-white">
                <i class="fas fa-home w-5 text-center"></i> <span>ওয়েবসাইট</span>
            </a>
            <form method="POST" action="{{ route('member.logout') }}">
                @csrf
                <button type="submit" class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-sm text-red-200 hover:text-white hover:bg-red-500/20">
                    <i class="fas fa-sign-out-alt w-5 text-center"></i> <span>লগআউট</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ============ MAIN CONTENT ============ -->
    <div class="flex-1 lg:ml-64 flex flex-col min-h-screen">

        <!-- Top header (mobile + desktop) -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-30 px-4 sm:px-6 py-3 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <!-- Mobile logo -->
                <div class="lg:hidden w-10 h-10 sidebar-bg rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <div class="font-bold text-base sm:text-lg text-slate-800 heading-serif">স্বাগতম, {{ $user->name }}</div>
                    <div class="text-xs text-emerald-700 flex items-center gap-1">
                        <i class="fas fa-check-circle"></i> যাচাইকৃত সদস্য
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="hidden sm:flex items-center gap-1.5 text-sm text-slate-600 hover:text-emerald-700 px-3 py-2 rounded-xl hover:bg-slate-50 transition">
                    <i class="fas fa-external-link-alt text-xs"></i> ওয়েবসাইট
                </a>
                <form method="POST" action="{{ route('member.logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-1.5 text-sm font-medium text-slate-600 hover:text-red-600 hover:bg-red-50 px-3 py-2 rounded-xl transition">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="hidden sm:inline">লগআউট</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- ============ DESKTOP: Tab bar (alternative to sidebar) ============ -->
        <!-- Not needed — sidebar handles it. Below is the content area. -->

        <!-- Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 pb-24 lg:pb-8 overflow-y-auto">

            <!-- ==================== TAB: DASHBOARD ==================== -->
            <div x-show="activeTab==='dashboard'" x-cloak class="tab-content space-y-6">

                <!-- Welcome banner -->
                <div class="bg-gradient-to-r from-emerald-700 via-emerald-800 to-teal-800 rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-48 h-48 bg-amber-400/10 rounded-full -mr-20 -mt-20"></div>
                    <div class="absolute bottom-0 left-1/3 w-32 h-32 bg-teal-300/10 rounded-full blur-2xl"></div>
                    <div class="relative">
                        <div class="text-emerald-200 text-sm font-medium">স্বাগতম আপনার সামাজিক অবদান গুরুত্বপূর্ণ</div>
                        <div class="text-2xl sm:text-3xl font-bold heading-serif mt-1">আপনার পরিচ্ছন্নতার স্কোর</div>
                    </div>
                </div>

                <!-- Quick stats (colourful gradient cards) -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="stat-card bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-3xl p-5 text-white">
                        <div class="flex items-center justify-between">
                            <i class="fas fa-star text-white/40 text-xl"></i>
                            <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-full">এই মাস</span>
                        </div>
                        <div class="text-4xl font-bold mt-3">৮৭</div>
                        <div class="text-emerald-100 text-xs mt-1">/ ১০০ স্কোর</div>
                    </div>
                    <div class="stat-card bg-gradient-to-br from-amber-500 to-orange-600 rounded-3xl p-5 text-white">
                        <div class="flex items-center justify-between">
                            <i class="fas fa-medal text-white/40 text-xl"></i>
                            <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-full">র‍্যাঙ্ক</span>
                        </div>
                        <div class="text-4xl font-bold mt-3">৩</div>
                        <div class="text-amber-100 text-xs mt-1">৫২০ জনের মধ্যে</div>
                    </div>
                    <div class="stat-card bg-gradient-to-br from-sky-500 to-blue-600 rounded-3xl p-5 text-white">
                        <div class="flex items-center justify-between">
                            <i class="fas fa-camera text-white/40 text-xl"></i>
                            <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-full">আপলোড</span>
                        </div>
                        <div class="text-4xl font-bold mt-3">৪</div>
                        <div class="text-sky-100 text-xs mt-1">এই মাসের ছবি</div>
                    </div>
                    <div class="stat-card bg-gradient-to-br from-purple-500 to-pink-600 rounded-3xl p-5 text-white">
                        <div class="flex items-center justify-between">
                            <i class="fas fa-fire text-white/40 text-xl"></i>
                            <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-full">সর্বোচ্চ</span>
                        </div>
                        <div class="text-4xl font-bold mt-3">৯<span class="text-2xl">/১০</span></div>
                        <div class="text-purple-100 text-xs mt-1">একটি ছবিতে</div>
                    </div>
                </div>

                <!-- Due card + charge breakdown -->
                <div class="grid lg:grid-cols-5 gap-5">
                    <div class="lg:col-span-3 bg-white border border-amber-200 rounded-3xl p-7 shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="text-amber-600 text-sm font-semibold flex items-center gap-1.5">
                                    <i class="fas fa-exclamation-circle"></i> বর্তমান বকেয়া
                                </div>
                                <div class="text-5xl font-bold text-amber-700 tabular-nums mt-2">৳ ৯০০</div>
                            </div>
                            <div class="text-right">
                                <div class="inline-flex items-center gap-1 bg-red-100 text-red-700 px-3 py-1 rounded-2xl text-xs font-semibold">
                                    <i class="fas fa-clock"></i> মেয়াদোত্তীর্ণ
                                </div>
                                <div class="text-xs text-amber-600 mt-1.5">১৫ জানুয়ারি ২০২৬</div>
                            </div>
                        </div>
                        <button class="mt-6 w-full py-3.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 active:scale-[0.98] transition text-white font-semibold rounded-2xl flex items-center justify-center gap-2 text-sm shadow-lg shadow-amber-500/30">
                            <i class="fas fa-wallet"></i> এখনই পেমেন্ট করুন
                        </button>
                        <div class="text-[10px] text-amber-600 mt-3 text-center flex items-center justify-center gap-1">
                            <i class="fas fa-shield-alt"></i> bKash / Nagad / ব্যাংক ট্রান্সফার সাপোর্টেড
                        </div>
                    </div>

                    <div class="lg:col-span-2 bg-slate-50 border border-slate-200 rounded-3xl p-6 text-sm">
                        <div class="font-semibold mb-3 flex items-center gap-2"><i class="fas fa-file-invoice text-slate-400"></i> সেবা চার্জের বিবরণ</div>
                        <div class="space-y-2 text-slate-600 text-[13px]">
                            <div class="flex justify-between"><span>মাসিক সদস্য ফি</span> <span class="font-medium">৳ ৩০০</span></div>
                            <div class="flex justify-between"><span>নিরাপত্তা চার্জ</span> <span class="font-medium">৳ ৩০০</span></div>
                            <div class="flex justify-between"><span>পরিচ্ছন্নতা চার্জ</span> <span class="font-medium">৳ ৩০০</span></div>
                            <div class="h-px bg-slate-200 my-1"></div>
                            <div class="flex justify-between font-semibold text-slate-800"><span>মোট মাসিক</span> <span>৳ ৯০০</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== TAB: DUES & PAYMENT ==================== -->
            <div x-show="activeTab==='dues'" x-cloak class="tab-content space-y-6">
                <div>
                    <h2 class="text-2xl font-bold heading-serif text-slate-800">ডিউ ও পেমেন্ট ইতিহাস</h2>
                    <p class="text-slate-500 text-sm mt-1">আপনার সকল পেমেন্ট ও বকেয়ার তালিকা</p>
                </div>

                <!-- Summary cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-gradient-to-br from-red-500 to-rose-600 rounded-3xl p-5 text-white">
                        <div class="text-red-100 text-xs font-medium">মোট বকেয়া</div>
                        <div class="text-3xl font-bold mt-1">৳ ৯০০</div>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-500 to-green-600 rounded-3xl p-5 text-white">
                        <div class="text-emerald-100 text-xs font-medium">মোট পরিশোধিত</div>
                        <div class="text-3xl font-bold mt-1">৳ ৫,৪০০</div>
                    </div>
                    <div class="bg-gradient-to-br from-sky-500 to-blue-600 rounded-3xl p-5 text-white">
                        <div class="text-sky-100 text-xs font-medium">এই বছর পরিশোধ</div>
                        <div class="text-3xl font-bold mt-1">৳ ৩,৬০০</div>
                    </div>
                </div>

                <!-- Payment history table -->
                <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
                    <div class="px-6 py-4 border-b flex items-center justify-between">
                        <div class="font-semibold text-slate-800 flex items-center gap-2"><i class="fas fa-history text-emerald-600"></i> পেমেন্ট ইতিহাস</div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-xs text-slate-500 uppercase tracking-wide">
                                    <th class="px-6 py-3 font-semibold">তারিখ</th>
                                    <th class="px-6 py-3 font-semibold">বিবরণ</th>
                                    <th class="px-6 py-3 font-semibold text-right">পরিমাণ</th>
                                    <th class="px-6 py-3 font-semibold">স্ট্যাটাস</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 text-slate-600">১৫ জানু ২০২৬</td>
                                    <td class="px-6 py-4 font-medium text-slate-800">জানুয়ারি মাসিক ফি</td>
                                    <td class="px-6 py-4 text-right font-semibold text-amber-700">৳ ৯০০</td>
                                    <td class="px-6 py-4"><span class="text-xs bg-red-100 text-red-700 px-2.5 py-1 rounded-full font-semibold">বকেয়া</span></td>
                                </tr>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 text-slate-600">২৮ ডিসে ২০২৫</td>
                                    <td class="px-6 py-4 font-medium text-slate-800">ডিসেম্বর মাসিক ফি</td>
                                    <td class="px-6 py-4 text-right font-semibold text-emerald-700">৳ ৯০০</td>
                                    <td class="px-6 py-4"><span class="text-xs bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full font-semibold">পরিশোধিত</span></td>
                                </tr>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 text-slate-600">৩০ নভে ২০২৫</td>
                                    <td class="px-6 py-4 font-medium text-slate-800">নভেম্বর মাসিক ফি</td>
                                    <td class="px-6 py-4 text-right font-semibold text-emerald-700">৳ ৯০০</td>
                                    <td class="px-6 py-4"><span class="text-xs bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full font-semibold">পরিশোধিত</span></td>
                                </tr>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 text-slate-600">৩১ অক্টো ২০২৫</td>
                                    <td class="px-6 py-4 font-medium text-slate-800">অক্টোবর মাসিক ফি</td>
                                    <td class="px-6 py-4 text-right font-semibold text-emerald-700">৳ ৯০০</td>
                                    <td class="px-6 py-4"><span class="text-xs bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full font-semibold">পরিশোধিত</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ==================== TAB: MY GALLERY ==================== -->
            <div x-show="activeTab==='gallery'" x-cloak class="tab-content space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold heading-serif text-slate-800">আমার গ্যালারি</h2>
                        <p class="text-slate-500 text-sm mt-1">সামনের উঠানের ছবি আপলোড করুন — অ্যাডমিন পরিচ্ছন্নতার ভিত্তিতে স্কোর দেবেন</p>
                    </div>
                    <button class="flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white text-sm font-semibold rounded-2xl shadow-lg shadow-emerald-600/30 transition active:scale-95">
                        <i class="fas fa-camera"></i>
                        <span>ছবি আপলোড করুন</span>
                    </button>
                </div>

                <!-- Upload zone (demo) -->
                <div class="border-2 border-dashed border-emerald-200 rounded-3xl p-8 text-center bg-emerald-50/40 hover:bg-emerald-50 transition cursor-pointer">
                    <i class="fas fa-cloud-upload-alt text-4xl text-emerald-600 mb-3"></i>
                    <div class="font-semibold text-slate-700">ছবি টেনে আনুন অথবা ক্লিক করুন</div>
                    <div class="text-xs text-slate-500 mt-1">JPG / PNG • সর্বোচ্চ ৫MB</div>
                </div>

                <!-- My photos grid (demo) -->
                <div>
                    <div class="text-sm font-semibold text-slate-700 mb-3 flex items-center justify-between">
                        <span>আমার আপলোড করা ছবি</span>
                        <span class="text-emerald-700 text-xs font-normal">৪টি ছবি</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                        @for($i = 1; $i <= 4; $i++)
                            <div class="group relative rounded-2xl overflow-hidden border border-slate-200 bg-white shadow-sm">
                                <div class="aspect-square bg-gradient-to-br from-emerald-100 to-teal-200 flex items-center justify-center">
                                    <i class="fas fa-image text-4xl text-emerald-400"></i>
                                </div>
                                <div class="p-3">
                                    <div class="text-xs font-medium text-slate-700">উঠানের ছবি {{ $i }}</div>
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="text-[10px] text-slate-400">{{ $i }} দিন আগে</span>
                                        <span class="text-[10px] bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded-full font-semibold">★ {{ 7 + $i }}/১০</span>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- ==================== TAB: RANKING & SCORE ==================== -->
            <div x-show="activeTab==='ranking'" x-cloak class="tab-content space-y-6">
                <div>
                    <h2 class="text-2xl font-bold heading-serif text-slate-800">র‍্যাঙ্কিং ও স্কোর</h2>
                    <p class="text-slate-500 text-sm mt-1">আপনার সামাজিক অবদান ও পরিচ্ছন্নতার স্কোর</p>
                </div>

                <div class="max-w-2xl space-y-6">
                    <!-- Big score card -->
                    <div class="bg-gradient-to-br from-emerald-700 via-emerald-800 to-teal-900 text-white rounded-3xl p-8 relative overflow-hidden shadow-xl">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-amber-400/10 rounded-full -mr-16 -mt-16"></div>
                        <div class="relative">
                            <div class="uppercase tracking-widest text-xs text-emerald-300 font-semibold">SOCIAL VALUE SCORE</div>
                            <div class="text-7xl font-bold mt-2 heading-serif">৮৭</div>
                            <div class="text-emerald-200 mt-1">এই মাসের সামাজিক অবদান স্কোর</div>

                            <div class="mt-8">
                                <div class="flex justify-between text-xs text-emerald-200 mb-2">
                                    <div>আপনার স্কোর</div>
                                    <div>৮৭ / ১০০</div>
                                </div>
                                <div class="h-3 bg-white/20 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-amber-400 to-amber-500 rounded-full transition-all" style="width: 87%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rank badge -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white border border-slate-200 rounded-3xl p-5 text-center shadow-sm">
                            <div class="w-14 h-14 mx-auto bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl flex items-center justify-center text-white text-2xl shadow-lg">
                                <i class="fas fa-medal"></i>
                            </div>
                            <div class="text-3xl font-bold text-slate-800 mt-3">৩য়</div>
                            <div class="text-xs text-slate-500">বর্তমান র‍্যাঙ্ক</div>
                        </div>
                        <div class="bg-white border border-slate-200 rounded-3xl p-5 text-center shadow-sm">
                            <div class="w-14 h-14 mx-auto bg-gradient-to-br from-purple-400 to-pink-500 rounded-2xl flex items-center justify-center text-white text-2xl shadow-lg">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                            <div class="text-3xl font-bold text-slate-800 mt-3">+৫</div>
                            <div class="text-xs text-slate-500">গত মাস থেকে</div>
                        </div>
                    </div>

                    <!-- How scoring works -->
                    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                        <div class="font-semibold text-emerald-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-info-circle"></i> কীভাবে স্কোর হয়?
                        </div>
                        <ul class="space-y-2.5 text-sm text-slate-600">
                            <li class="flex gap-2"><span class="text-emerald-600 font-bold">•</span> প্রতিটি আপলোড করা ছবি অ্যাডমিন ১-১০ স্কোর দেন (পরিচ্ছন্নতা, গোছানো, সবুজের উপস্থিতি)</li>
                            <li class="flex gap-2"><span class="text-emerald-600 font-bold">•</span> মাস শেষে সব ছবির গড় স্কোর থেকে আপনার সামাজিক মান নির্ধারিত হয়</li>
                            <li class="flex gap-2"><span class="text-emerald-600 font-bold">•</span> সর্বোচ্চ স্কোর পাওয়া সদস্যরা "মাসের সেরা পরিচ্ছন্ন পরিবার" হিসেবে স্বীকৃতি পান</li>
                        </ul>
                    </div>
                </div>
            </div>

        </main>

        <!-- ============ MOBILE BOTTOM NAV ============ -->
        <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 z-40 grid grid-cols-4 shadow-2xl">
            <button @click="activeTab='dashboard'" :class="{ 'mobile-nav-active': activeTab==='dashboard' }"
                    class="flex flex-col items-center gap-1 py-3 text-slate-500 text-[11px] transition">
                <span class="nav-icon w-8 h-8 rounded-xl flex items-center justify-center transition"><i class="fas fa-th-large"></i></span>
                ড্যাশবোর্ড
            </button>
            <button @click="activeTab='dues'" :class="{ 'mobile-nav-active': activeTab==='dues' }"
                    class="flex flex-col items-center gap-1 py-3 text-slate-500 text-[11px] transition">
                <span class="nav-icon w-8 h-8 rounded-xl flex items-center justify-center transition"><i class="fas fa-wallet"></i></span>
                পেমেন্ট
            </button>
            <button @click="activeTab='gallery'" :class="{ 'mobile-nav-active': activeTab==='gallery' }"
                    class="flex flex-col items-center gap-1 py-3 text-slate-500 text-[11px] transition">
                <span class="nav-icon w-8 h-8 rounded-xl flex items-center justify-center transition"><i class="fas fa-camera"></i></span>
                গ্যালারি
            </button>
            <button @click="activeTab='ranking'" :class="{ 'mobile-nav-active': activeTab==='ranking' }"
                    class="flex flex-col items-center gap-1 py-3 text-slate-500 text-[11px] transition">
                <span class="nav-icon w-8 h-8 rounded-xl flex items-center justify-center transition"><i class="fas fa-trophy"></i></span>
                র‍্যাঙ্কিং
            </button>
        </nav>
    </div>
</div>

</body>
</html>
