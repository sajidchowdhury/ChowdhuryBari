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

        /* Subtle sidebar — solid dark emerald, no gradient */
        .sidebar-bg { background: #0f2e26; }

        .nav-item { transition: all 0.2s ease; }
        .nav-item:hover { background: rgba(255, 255, 255, 0.08); }
        .nav-item.active {
            background: rgba(255, 255, 255, 0.10);
            border-left: 3px solid #d4a437;
            color: #fff;
        }

        /* Cards — clean white with soft shadow, no gradients */
        .card {
            background: #fff;
            border: 1px solid #eef0f2;
            border-radius: 1.25rem;
            box-shadow: 0 1px 2px rgba(15,23,42,.03);
        }
        .card-hover { transition: box-shadow .25s ease, transform .25s ease; }
        .card-hover:hover { box-shadow: 0 8px 24px -10px rgba(15,23,42,.10); transform: translateY(-2px); }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .tab-content { animation: fadeIn 0.3s ease; }

        /* Mobile bottom nav active */
        @media (max-width: 1023px) {
            .mobile-nav-active { color: #0f766e; font-weight: 600; }
            .mobile-nav-active .nav-icon { background: #0f766e; color: #fff; }
        }

        /* Toast */
        .toast { animation: fadeIn .25s ease; }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,.12); border-radius: 4px; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen" x-data="{ activeTab: 'dashboard', showToast: false, toastMsg: '' }">

<div class="flex min-h-screen">

    <!-- ============ DESKTOP SIDEBAR ============ -->
    <aside class="sidebar-bg w-64 fixed inset-y-0 left-0 hidden lg:flex flex-col text-white/80 z-40">
        <!-- Logo header -->
        <div class="px-6 py-6 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-shield text-lg text-amber-300"></i>
                </div>
                <div>
                    <div class="font-semibold text-white text-[15px] heading-serif">সদস্য পোর্টাল</div>
                    <div class="text-white/40 text-[11px]">চৌধুরীপাড়া</div>
                </div>
            </div>
        </div>

        <!-- User card -->
        <div class="px-4 py-4">
            <div class="bg-white/5 rounded-2xl p-3 flex items-center gap-3 border border-white/5">
                <div class="w-9 h-9 bg-amber-400/90 rounded-lg flex items-center justify-center text-emerald-950 font-bold text-sm">
                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-sm text-white truncate">{{ $user->name }}</div>
                    <div class="text-white/40 text-[11px] truncate">{{ $user->phone }}</div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-2 space-y-0.5 overflow-y-auto">
            <button @click="activeTab='dashboard'" :class="{ 'active': activeTab==='dashboard' }"
                    class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-white/70">
                <i class="fas fa-th-large w-5 text-center text-[13px]"></i>
                <span>ড্যাশবোর্ড</span>
            </button>
            <button @click="activeTab='building'" :class="{ 'active': activeTab==='building' }"
                    class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-white/70">
                <i class="fas fa-home w-5 text-center text-[13px]"></i>
                <span>আমার বাড়ি</span>
            </button>
            <button @click="activeTab='dues'" :class="{ 'active': activeTab==='dues' }"
                    class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-white/70">
                <i class="fas fa-wallet w-5 text-center text-[13px]"></i>
                <span>ডিউ ও পেমেন্ট</span>
            </button>
            <button @click="activeTab='gallery'" :class="{ 'active': activeTab==='gallery' }"
                    class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-white/70">
                <i class="fas fa-camera w-5 text-center text-[13px]"></i>
                <span>আমার গ্যালারি</span>
            </button>
            <button @click="activeTab='ranking'" :class="{ 'active': activeTab==='ranking' }"
                    class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-white/70">
                <i class="fas fa-trophy w-5 text-center text-[13px]"></i>
                <span>র‍্যাঙ্কিং ও স্কোর</span>
            </button>
        </nav>

        <!-- Bottom actions -->
        <div class="p-4 border-t border-white/10 space-y-1">
            <a href="{{ route('home') }}" class="nav-item flex items-center gap-3 px-4 py-2 rounded-xl text-[13px] text-white/60 hover:text-white">
                <i class="fas fa-home w-5 text-center text-[13px]"></i> <span>ওয়েবসাইট</span>
            </a>
            <form method="POST" action="{{ route('member.logout') }}">
                @csrf
                <button type="submit" class="nav-item w-full flex items-center gap-3 px-4 py-2 rounded-xl text-[13px] text-red-300/70 hover:text-red-200 hover:bg-red-500/10">
                    <i class="fas fa-sign-out-alt w-5 text-center text-[13px]"></i> <span>লগআউট</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ============ MAIN CONTENT ============ -->
    <div class="flex-1 lg:ml-64 flex flex-col min-h-screen">

        <!-- Top header -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-30 px-4 sm:px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="lg:hidden w-9 h-9 sidebar-bg rounded-lg flex items-center justify-center text-amber-300">
                    <i class="fas fa-user-shield text-sm"></i>
                </div>
                <div>
                    <div class="font-semibold text-[15px] sm:text-base text-slate-800">স্বাগতম, {{ $user->name }}</div>
                    <div class="text-[11px] text-emerald-700 flex items-center gap-1">
                        <i class="fas fa-check-circle"></i> যাচাইকৃত সদস্য
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <a href="{{ route('home') }}" class="hidden sm:flex items-center gap-1.5 text-sm text-slate-500 hover:text-emerald-700 px-3 py-2 rounded-lg hover:bg-slate-50 transition">
                    <i class="fas fa-external-link-alt text-xs"></i> ওয়েবসাইট
                </a>
                <form method="POST" action="{{ route('member.logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-red-600 hover:bg-red-50 px-3 py-2 rounded-lg transition">
                        <i class="fas fa-sign-out-alt text-xs"></i>
                        <span class="hidden sm:inline">লগআউট</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 pb-24 lg:pb-8 overflow-y-auto">

            <!-- ==================== TAB: DASHBOARD ==================== -->
            <div x-show="activeTab==='dashboard'" x-cloak class="tab-content space-y-6">

                <!-- Welcome (subtle, single accent) -->
                <div>
                    <div class="text-emerald-700 text-xs font-semibold uppercase tracking-wider">স্বাগতম</div>
                    <div class="text-2xl sm:text-3xl font-bold heading-serif text-slate-800 mt-1">আপনার পরিচ্ছন্নতার স্কোর</div>
                    <p class="text-slate-500 text-sm mt-1">আপনার সামাজিক অবদান গুরুত্বপূর্ণ — এই মাসের সারসংক্ষেপ নিচে।</p>
                </div>

                <!-- Quick stats — clean white cards with subtle icon chips -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="card card-hover p-5">
                        <div class="flex items-center justify-between">
                            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center"><i class="fas fa-star text-sm"></i></div>
                            <span class="text-[10px] text-slate-400 font-medium">এই মাস</span>
                        </div>
                        <div class="text-3xl font-bold text-slate-800 mt-3 tabular-nums">{{ $currentSV ?? '--' }}</div>
                        <div class="text-slate-400 text-xs mt-0.5">@if($currentSV)/ ১০০ স্কোর @else রেট করা হয়নি @endif</div>
                    </div>
                    <div class="card card-hover p-5">
                        <div class="flex items-center justify-between">
                            <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center"><i class="fas fa-medal text-sm"></i></div>
                            <span class="text-[10px] text-slate-400 font-medium">র‍্যাঙ্ক</span>
                        </div>
                        <div class="text-3xl font-bold text-slate-800 mt-3 tabular-nums">{{ $rank ?? '--' }}</div>
                        <div class="text-slate-400 text-xs mt-0.5">@if($rank){{ $totalRanked }} জনের মধ্যে @else ছবি আপলোড করুন @endif</div>
                    </div>
                    <div class="card card-hover p-5">
                        <div class="flex items-center justify-between">
                            <div class="w-9 h-9 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center"><i class="fas fa-camera text-sm"></i></div>
                            <span class="text-[10px] text-slate-400 font-medium">আপলোড</span>
                        </div>
                        <div class="text-3xl font-bold text-slate-800 mt-3 tabular-nums">{{ $myUploads->count() }}<span class="text-lg text-slate-400">/{{ $uploadLimit }}</span></div>
                        <div class="text-slate-400 text-xs mt-0.5">এই মাসের ছবি</div>
                    </div>
                    <div class="card card-hover p-5">
                        <div class="flex items-center justify-between">
                            <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center"><i class="fas fa-fire text-sm"></i></div>
                            <span class="text-[10px] text-slate-400 font-medium">সর্বোচ্চ</span>
                        </div>
                        <div class="text-3xl font-bold text-slate-800 mt-3 tabular-nums">{{ $bestImageStars ?? '--' }}<span class="text-lg text-slate-400">@if($bestImageStars)/১০ @endif</span></div>
                        <div class="text-slate-400 text-xs mt-0.5">একটি ছবিতে</div>
                    </div>
                </div>

                <!-- Due card + charge breakdown -->
                <div class="grid lg:grid-cols-5 gap-5">

                    <!-- Due card -->
                    <div class="lg:col-span-3 card p-6 sm:p-7">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="text-amber-600 text-xs font-semibold flex items-center gap-1.5 uppercase tracking-wide">
                                    <i class="fas fa-exclamation-circle"></i> এই মাসের বকেয়া
                                </div>
                                <div class="text-4xl sm:text-5xl font-bold text-slate-800 tabular-nums mt-2">৳ {{ number_format($monthlyDue) }}</div>
                                @if($chargeBreakdown)
                                    <div class="text-[11px] text-slate-400 mt-1">
                                        {{ $chargeBreakdown['family_count'] }} পরিবার ও অতিরিক্ত চার্জ সহ
                                    </div>
                                @endif
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center gap-1 bg-red-50 text-red-600 px-2.5 py-1 rounded-lg text-[11px] font-semibold">
                                    <i class="fas fa-clock text-[10px]"></i> মেয়াদোত্তীর্ণ
                                </span>
                                <div class="text-[11px] text-slate-400 mt-1.5">এই মাসের</div>
                            </div>
                        </div>
                        <button onclick="showComingSoon()"
                                class="mt-6 w-full py-3.5 bg-slate-900 hover:bg-slate-800 active:scale-[0.99] transition text-white font-medium rounded-xl flex items-center justify-center gap-2 text-sm">
                            <i class="fas fa-wallet"></i> এখনই পেমেন্ট করুন
                        </button>
                        <div class="text-[10px] text-slate-400 mt-3 text-center">bKash / Nagad / ব্যাংক ট্রান্সফার সাপোর্টেড</div>
                    </div>

                    <!-- Charge breakdown — based on member's building type + charge types -->
                    <div class="lg:col-span-2 card p-6">
                        <div class="font-semibold text-slate-800 mb-1 flex items-center gap-2 text-sm">
                            <i class="fas fa-file-invoice text-slate-400"></i> সেবা চার্জের বিবরণ
                        </div>
                        @if($chargeBreakdown)
                            <div class="text-[11px] text-sky-700 bg-sky-50 inline-block px-2 py-0.5 rounded-full mb-3">
                                বিলিং পরিবার: {{ $chargeBreakdown['family_count'] }}
                            </div>
                            <div class="space-y-2 text-[13px]">
                                {{-- Base per-family charge --}}
                                @if($chargeBreakdown['base_per_family_amount'] > 0)
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">
                                            মাসিক ফি <span class="text-[10px] text-slate-400">({{ $chargeBreakdown['base_per_family_amount'] }} × {{ $chargeBreakdown['family_count'] }})</span>
                                        </span>
                                        <span class="font-medium text-slate-800 tabular-nums">৳ {{ number_format($chargeBreakdown['base_family_charge']) }}</span>
                                    </div>
                                @endif
                                {{-- Service charges with type --}}
                                @foreach($chargeBreakdown['charges'] as $charge)
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">
                                            {{ $charge->name }}
                                            <span class="text-[10px] text-slate-400 ml-0.5">
                                                @if($charge->charge_type === 'per_family')({{ $charge->amount }} × {{ $charge->multiplier }})@elseif($charge->charge_type === 'per_floor')({{ $charge->amount }} × {{ $charge->multiplier }})@endif
                                            </span>
                                        </span>
                                        <span class="font-medium text-slate-800 tabular-nums">৳ {{ number_format($charge->line_total) }}</span>
                                    </div>
                                @endforeach
                                @if($chargeBreakdown['base_family_charge'] > 0 || $chargeBreakdown['charges_total'] > 0)
                                    <div class="h-px bg-slate-200 my-2"></div>
                                    <div class="flex justify-between font-semibold text-slate-900">
                                        <span>মোট মাসিক</span>
                                        <span class="tabular-nums">৳ {{ number_format($chargeBreakdown['total']) }}</span>
                                    </div>
                                @endif
                            </div>
                        @elseif($buildingCategory)
                            <div class="text-center py-6 text-slate-400 text-xs">
                                <i class="fas fa-info-circle text-2xl mb-2 block"></i>
                                আপনার বাড়ির ধরনের জন্য এখনো কোনো সেবা চার্জ যোগ করা হয়নি।
                            </div>
                        @else
                            <div class="text-center py-6 text-slate-400 text-xs">
                                <i class="fas fa-info-circle text-2xl mb-2 block"></i>
                                আপনার বাড়ির ধরন এখনো নির্ধারিত হয়নি।<br>অ্যাডমিন আপনার বাড়ির তথ্য যোগ করলে চার্জ দেখা যাবে।
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ==================== TAB: আমার বাড়ি (BUILDING DETAILS) ==================== -->
            <div x-show="activeTab==='building'" x-cloak class="tab-content space-y-6">
                <div>
                    <h2 class="text-xl font-bold heading-serif text-slate-800">আমার বাড়ি</h2>
                    <p class="text-slate-500 text-sm mt-1">অ্যাডমিন কর্তৃক সেট করা আপনার বাড়ির তথ্য (শুধু পড়ার জন্য)</p>
                </div>

                @if($building)
                    {{-- Building summary --}}
                    <div class="card p-6">
                        <div class="grid sm:grid-cols-4 gap-4">
                            <div>
                                <div class="text-xs text-slate-400 uppercase tracking-wide">বাড়ির নাম</div>
                                <div class="font-semibold text-slate-800 mt-1">{{ $building->name }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400 uppercase tracking-wide">রাস্তা</div>
                                <div class="font-semibold text-slate-800 mt-1">{{ $building->road?->name ?? '—' }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400 uppercase tracking-wide">ধরন</div>
                                <div class="font-semibold text-slate-800 mt-1">{{ $building->category_label }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400 uppercase tracking-wide">ফ্লোর × পরিবার</div>
                                <div class="font-semibold text-slate-800 mt-1 tabular-nums">{{ $building->floor_count }} × {{ $building->families_per_floor }} = {{ $expectedFamilyCount }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Billing summary --}}
                    <div class="grid sm:grid-cols-3 gap-4">
                        <div class="card p-5">
                            <div class="text-xs text-slate-400 uppercase tracking-wide">প্রত্যাশিত পরিবার</div>
                            <div class="text-2xl font-bold text-slate-800 mt-1 tabular-nums">{{ $expectedFamilyCount }}</div>
                            <div class="text-[11px] text-slate-400">ফ্লোর × পরিবার/ফ্লোর</div>
                        </div>
                        <div class="card p-5">
                            <div class="text-xs text-emerald-600 uppercase tracking-wide">বিলিং পরিবার</div>
                            <div class="text-2xl font-bold text-emerald-700 mt-1 tabular-nums">{{ $billingFamilyCount }}</div>
                            <div class="text-[11px] text-slate-400">@if($building->billing_family_count !== null)অ্যাডমিন নির্ধারিত@else স্বয়ংক্রিয় (সক্রিয় মিটার)@endif</div>
                        </div>
                        <div class="card p-5">
                            <div class="text-xs text-slate-400 uppercase tracking-wide">সক্রিয় মিটার</div>
                            <div class="text-2xl font-bold text-slate-800 mt-1 tabular-nums">{{ $activeFlatCount }}</div>
                            <div class="text-[11px] text-slate-400">৪৫ দিনে রিচার্জ হয়েছে</div>
                        </div>
                    </div>

                    {{-- Flats + meters table (read-only) --}}
                    <div class="card overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-800 text-sm flex items-center gap-2">
                            <i class="fas fa-th-large text-emerald-600"></i> ফ্ল্যাট ও মিটার তালিকা
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-50">
                                    <tr class="text-left text-xs text-slate-500 uppercase tracking-wide">
                                        <th class="px-4 py-3 font-semibold">ফ্লোর</th>
                                        <th class="px-4 py-3 font-semibold">ফ্ল্যাট</th>
                                        <th class="px-4 py-3 font-semibold">বাসিন্দা</th>
                                        <th class="px-4 py-3 font-semibold">মিটার নম্বর</th>
                                        <th class="px-4 py-3 font-semibold text-center">স্ট্যাটাস</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($buildingFlats as $flat)
                                        @php $meter = $flat->meters->first(); @endphp
                                        <tr>
                                            <td class="px-4 py-3 text-slate-500">{{ $flat->floor_number ?? '—' }}</td>
                                            <td class="px-4 py-3 font-medium text-slate-800">{{ $flat->flat_number }}</td>
                                            <td class="px-4 py-3 text-slate-600">
                                                {{ $flat->resident_name ?: '—' }}
                                                @if($flat->resident_phone)<div class="text-xs text-slate-400">{{ $flat->resident_phone }}</div>@endif
                                            </td>
                                            <td class="px-4 py-3 font-mono text-slate-700">
                                                @if($meter) {{ $meter->meter_number }} @else <span class="text-slate-300">—</span> @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if(!$flat->is_active)
                                                    <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full font-semibold">খালি</span>
                                                @elseif($flat->isFamilyActive())
                                                    <span class="text-[10px] bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-semibold">সক্রিয়</span>
                                                @else
                                                    <span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-semibold">মিটার নিষ্ক্রিয়</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Family reduction application --}}
                    @if(session('app_success'))
                        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-700 text-sm flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> {{ session('app_success') }}
                        </div>
                    @endif
                    @if(session('app_error'))
                        <div class="rounded-2xl bg-red-50 border border-red-200 p-4 text-red-700 text-sm flex items-center gap-2">
                            <i class="fas fa-exclamation-circle"></i> {{ session('app_error') }}
                        </div>
                    @endif

                    @if($hasPendingApplication)
                        <div class="card border-amber-200 bg-amber-50/50 p-6 text-center">
                            <i class="fas fa-hourglass-half text-2xl text-amber-500 mb-2"></i>
                            <div class="font-medium text-slate-700 text-sm">আপনার একটি আবেদন অপেক্ষমাণ অবস্থায় আছে</div>
                            <div class="text-xs text-slate-400 mt-1">অ্যাডমিন রিভিউ করার পর নতুন আবেদন করতে পারবেন।</div>
                        </div>
                    @else
                        <div class="card p-6">
                            <div class="font-semibold text-slate-800 mb-1 text-sm flex items-center gap-2">
                                <i class="fas fa-paper-plane text-emerald-600"></i> পরিবার কমানোর আবেদন
                            </div>
                            <p class="text-xs text-slate-500 mb-4">আপনার বাড়ির কিছু পরিবার চলে গেলে এবং মিটার নিষ্ক্রিয় থাকলে বিলিং কমানোর জন্য আবেদন করুন। অ্যাডমিন মিটার নম্বর BPDB-তে যাচাই করে অনুমোদন দেবেন।</p>
                            <form action="{{ route('member.applications.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="grid sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 mb-1">বর্তমান বিলিং পরিবার</label>
                                        <input type="text" value="{{ $billingFamilyCount }}" disabled
                                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 mb-1">অনুরোধ করা পরিবার সংখ্যা <span class="text-red-500">*</span></label>
                                        <input type="number" name="requested_family_count" min="0" max="{{ $billingFamilyCount }}" required
                                               placeholder="যেমন: {{ max(0, $billingFamilyCount - 2) }}"
                                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">খালি ফ্ল্যাটগুলো নির্বাচন করুন (ঐচ্ছিক)</label>
                                    <div class="flex flex-wrap gap-2 mt-1">
                                        @foreach($buildingFlats as $flat)
                                            @if(!$flat->is_active || !$flat->isFamilyActive())
                                                <label class="inline-flex items-center gap-1.5 text-xs border border-slate-200 rounded-lg px-2.5 py-1.5 cursor-pointer hover:bg-slate-50">
                                                    <input type="checkbox" name="vacant_flat_ids[]" value="{{ $flat->id }}" class="rounded">
                                                    {{ $flat->flat_number }}
                                                </label>
                                            @endif
                                        @endforeach
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-1">শুধু নিষ্ক্রিয়/খালি ফ্ল্যাটগুলো দেখানো হয়েছে।</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">কারণ <span class="text-red-500">*</span></label>
                                    <textarea name="reason" rows="2" required placeholder="যেমন: ২টি পরিবার চলে গেছে, মিটার নিষ্ক্রিয়"
                                              class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm"></textarea>
                                </div>
                                <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-medium rounded-xl transition active:scale-95">
                                    <i class="fas fa-paper-plane mr-1"></i> আবেদন জমা দিন
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- Application history --}}
                    @if($myApplications->isNotEmpty())
                        <div class="card overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-800 text-sm flex items-center gap-2">
                                <i class="fas fa-history text-slate-400"></i> আবেদনের ইতিহাস
                            </div>
                            <div class="divide-y divide-slate-100">
                                @foreach($myApplications as $app)
                                    <div class="px-6 py-3 flex items-center justify-between">
                                        <div>
                                            <div class="text-sm text-slate-700">
                                                {{ $app->current_family_count }} → <strong>{{ $app->requested_family_count }}</strong> পরিবার
                                            </div>
                                            <div class="text-xs text-slate-400">{{ $app->created_at->format('M d, Y') }}</div>
                                        </div>
                                        <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                                            @if($app->status === 'pending') bg-amber-100 text-amber-700
                                            @elseif($app->status === 'approved') bg-emerald-100 text-emerald-700
                                            @else bg-red-100 text-red-700 @endif">
                                            {{ $app->status_label }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <div class="card p-12 text-center">
                        <i class="fas fa-home text-5xl text-slate-300 mb-4"></i>
                        <h3 class="text-lg font-semibold text-slate-700">আপনার কোনো বাড়ি নিবন্ধিত নেই</h3>
                        <p class="text-slate-500 mt-1 text-sm">অ্যাডমিন আপনার ফোন নম্বর দিয়ে বাড়ি যুক্ত করলে এখানে দেখা যাবে।</p>
                    </div>
                @endif
            </div>

            <!-- ==================== TAB: DUES & PAYMENT ==================== -->
            <div x-show="activeTab==='dues'" x-cloak class="tab-content space-y-6">
                <div>
                    <h2 class="text-xl font-bold heading-serif text-slate-800">ডিউ ও পেমেন্ট ইতিহাস</h2>
                    <p class="text-slate-500 text-sm mt-1">আপনার সকল পেমেন্ট ও বকেয়ার তালিকা</p>
                </div>

                {{-- DEMO DATA BANNER --}}
                <div class="rounded-2xl bg-amber-50 border border-amber-200 p-4 flex items-start gap-3">
                    <div class="w-9 h-9 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-flask"></i>
                    </div>
                    <div class="text-sm text-amber-800">
                        <div class="font-semibold">এটি ডেমো ডেটা</div>
                        <p class="text-amber-700 text-xs mt-0.5 leading-relaxed">নিচের পেমেন্ট ইতিহাস ও বকেয়ার পরিমাণ শুধু প্রদর্শনের জন্য। পেমেন্ট গেটওয়ে (bKash / Nagad / SSL Commerz) যুক্ত হওয়ার পর এখানে আপনার আসল লেনদেনের তথ্য দেখা যাবে — ইনশাআল্লাহ শীঘ্রই।</p>
                    </div>
                </div>

                <!-- Summary cards (subtle) -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="card p-5">
                        <div class="text-rose-600 text-[11px] font-medium uppercase tracking-wide">মোট বকেয়া</div>
                        <div class="text-2xl font-bold text-slate-800 mt-1 tabular-nums">৳ {{ number_format($monthlyDue) }}</div>
                    </div>
                    <div class="card p-5">
                        <div class="text-emerald-600 text-[11px] font-medium uppercase tracking-wide">মোট পরিশোধিত</div>
                        <div class="text-2xl font-bold text-slate-800 mt-1 tabular-nums">৳ ৫,৪০০</div>
                    </div>
                    <div class="card p-5">
                        <div class="text-sky-600 text-[11px] font-medium uppercase tracking-wide">এই বছর পরিশোধ</div>
                        <div class="text-2xl font-bold text-slate-800 mt-1 tabular-nums">৳ ৩,৬০০</div>
                    </div>
                </div>

                <!-- Payment history table -->
                <div class="card overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                        <i class="fas fa-history text-emerald-700 text-sm"></i>
                        <span class="font-semibold text-slate-800 text-sm">পেমেন্ট ইতিহাস</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-[11px] text-slate-500 uppercase tracking-wide">
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
                                    <td class="px-6 py-4 text-right font-semibold text-amber-700 tabular-nums">৳ {{ number_format($monthlyDue) }}</td>
                                    <td class="px-6 py-4"><span class="text-[11px] bg-rose-50 text-rose-600 px-2 py-0.5 rounded-full font-semibold">বকেয়া</span></td>
                                </tr>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 text-slate-600">২৮ ডিসে ২০২৫</td>
                                    <td class="px-6 py-4 font-medium text-slate-800">ডিসেম্বর মাসিক ফি</td>
                                    <td class="px-6 py-4 text-right font-semibold text-emerald-700 tabular-nums">৳ {{ number_format($monthlyDue) }}</td>
                                    <td class="px-6 py-4"><span class="text-[11px] bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full font-semibold">পরিশোধিত</span></td>
                                </tr>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 text-slate-600">৩০ নভে ২০২৫</td>
                                    <td class="px-6 py-4 font-medium text-slate-800">নভেম্বর মাসিক ফি</td>
                                    <td class="px-6 py-4 text-right font-semibold text-emerald-700 tabular-nums">৳ {{ number_format($monthlyDue) }}</td>
                                    <td class="px-6 py-4"><span class="text-[11px] bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full font-semibold">পরিশোধিত</span></td>
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
                        <h2 class="text-xl font-bold heading-serif text-slate-800">আমার গ্যালারি</h2>
                        <p class="text-slate-500 text-sm mt-1">সামনের উঠানের ছবি আপলোড করুন — অ্যাডমিন বেনামে স্কোর দেবেন (১-১০ স্টার)</p>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-slate-400">এই মাসে আপলোড</div>
                        <div class="text-lg font-bold text-slate-800 tabular-nums">{{ $myUploads->count() }}<span class="text-sm text-slate-400">/{{ $uploadLimit }}</span></div>
                    </div>
                </div>

                {{-- Upload feedback messages --}}
                @if(session('upload_success'))
                    <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-700 text-sm flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> {{ session('upload_success') }}
                    </div>
                @endif
                @if(session('upload_error'))
                    <div class="rounded-2xl bg-red-50 border border-red-200 p-4 text-red-700 text-sm flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i> {{ session('upload_error') }}
                    </div>
                @endif

                {{-- Upload form — only if under the monthly limit --}}
                @if($uploadRemaining > 0)
                    <form action="{{ route('member.uploads.store') }}" method="POST" enctype="multipart/form-data" class="card border-dashed border-2 border-slate-200 p-8 text-center hover:border-emerald-300 hover:bg-emerald-50/30 transition">
                        @csrf
                        <label class="cursor-pointer block">
                            <input type="file" name="image" accept="image/*" class="sr-only">
                            <i class="fas fa-cloud-upload-alt text-3xl text-slate-400 mb-3"></i>
                            <div class="font-medium text-slate-700 text-sm">ছবি নির্বাচন করুন (ক্লিক করুন)</div>
                            <div class="text-xs text-slate-400 mt-1" id="upload-fname">JPG / PNG / WEBP • সর্বোচ্চ ৫MB</div>
                        </label>
                        <div class="mt-4">
                            <input type="text" name="caption" placeholder="ছবির ক্যাপশন (ঐচ্ছিক)" maxlength="200"
                                   class="w-full max-w-sm mx-auto border border-slate-200 rounded-lg px-3 py-2 text-sm text-center">
                        </div>
                        <button type="submit"
                                class="mt-4 inline-flex items-center gap-2 px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-medium rounded-xl transition active:scale-95">
                            <i class="fas fa-upload"></i> আপলোড করুন
                        </button>
                    </form>
                    <div class="text-[11px] text-slate-400 -mt-3 text-center">প্রতি মাসে সর্বোচ্চ {{ $uploadLimit }}টি ছবি। নতুন মাসে গণনা রিসেট হবে।</div>
                @else
                    <div class="card border border-amber-200 bg-amber-50/50 p-8 text-center">
                        <i class="fas fa-check-circle text-3xl text-amber-500 mb-2"></i>
                        <div class="font-medium text-slate-700 text-sm">এই মাসের জন্য আপনার {{ $uploadLimit }}টি ছবি আপলোড সম্পন্ন</div>
                        <div class="text-xs text-slate-400 mt-1">আগামী মাসে আবার {{ $uploadLimit }}টি ছবি আপলোড করতে পারবেন।</div>
                    </div>
                @endif

                {{-- My photos grid (dynamic) --}}
                <div>
                    <div class="text-sm font-medium text-slate-700 mb-3 flex items-center justify-between">
                        <span>এই মাসের ছবি ও স্কোর</span>
                        <span class="text-slate-400 text-xs font-normal">{{ $myUploads->count() }}টি ছবি</span>
                    </div>
                    @if($myUploads->isNotEmpty())
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($myUploads as $upload)
                                <div class="card overflow-hidden">
                                    <div class="aspect-square bg-slate-100 relative">
                                        <img src="{{ $upload->image_url }}" alt="{{ $upload->caption ?? 'My upload' }}" class="w-full h-full object-cover">
                                        @if($upload->is_rated)
                                            <span class="absolute top-2 right-2 bg-amber-400 text-white text-xs px-2 py-0.5 rounded-full font-bold flex items-center gap-0.5">
                                                <i class="fas fa-star text-[10px]"></i> {{ $upload->star_rating }}/১০
                                            </span>
                                        @else
                                            <span class="absolute top-2 right-2 bg-slate-800/80 text-white text-[10px] px-2 py-0.5 rounded-full font-medium">রিভিউ চলছে</span>
                                        @endif
                                    </div>
                                    <div class="p-3">
                                        <div class="text-xs font-medium text-slate-700 truncate">{{ $upload->caption ?? 'উঠানের ছবি' }}</div>
                                        <div class="flex items-center justify-between mt-1">
                                            <span class="text-[10px] text-slate-400">{{ $upload->created_at->format('M d') }}</span>
                                            @if($upload->is_rated)
                                                <span class="text-[10px] text-emerald-600 font-medium"><i class="fas fa-check text-[8px]"></i> রেট করা হয়েছে</span>
                                            @else
                                                <form method="POST" action="{{ route('member.uploads.destroy', $upload) }}" onsubmit="return confirm('এই ছবি মুছবেন?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-[10px] text-red-500 hover:text-red-700 font-medium"><i class="fas fa-trash text-[8px]"></i> মুছুন</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="card p-10 text-center">
                            <i class="fas fa-camera text-4xl text-slate-300 mb-3"></i>
                            <div class="text-slate-500 text-sm">এই মাসে এখনো কোনো ছবি আপলোড করেননি</div>
                            <div class="text-slate-400 text-xs mt-1">স্কোর ও র‍্যাঙ্কিং পেতে উঠানের ছবি আপলোড করুন।</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ==================== TAB: RANKING & SCORE ==================== -->
            <div x-show="activeTab==='ranking'" x-cloak class="tab-content space-y-6">
                <div>
                    <h2 class="text-xl font-bold heading-serif text-slate-800">র‍্যাঙ্কিং ও স্কোর</h2>
                    <p class="text-slate-500 text-sm mt-1">আপনার সামাজিক অবদান ও পরিচ্ছন্নতার স্কোর</p>
                </div>

                <div class="max-w-2xl space-y-5">
                    {{-- Big score card — dynamic --}}
                    @if($currentSV !== null)
                        <div class="card p-8 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-40 h-40 bg-emerald-50 rounded-full -mr-16 -mt-16"></div>
                            <div class="relative">
                                <div class="uppercase tracking-widest text-[11px] text-emerald-700 font-semibold">SOCIAL VALUE SCORE</div>
                                <div class="text-6xl font-bold mt-2 heading-serif text-slate-800 tabular-nums">{{ $currentSV }}</div>
                                <div class="text-slate-500 mt-1 text-sm">এই মাসের সামাজিক অবদান স্কোর ({{ $ratedCount }}টি রেট করা ছবির গড় × ১০)</div>

                                <div class="mt-7">
                                    <div class="flex justify-between text-xs text-slate-500 mb-2">
                                        <div>আপনার স্কোর</div>
                                        <div class="tabular-nums">{{ $currentSV }} / ১০০</div>
                                    </div>
                                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-emerald-600 rounded-full transition-all" style="width: {{ $currentSV }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- No uploads state --}}
                        <div class="card p-8 text-center border-dashed border-2 border-slate-200">
                            <i class="fas fa-camera text-4xl text-slate-300 mb-3"></i>
                            <div class="text-2xl font-bold text-slate-400 tabular-nums">--</div>
                            <div class="text-slate-600 text-sm mt-2 font-medium">এই মাসে আপনার কোনো রেট করা ছবি নেই</div>
                            <div class="text-slate-400 text-xs mt-1">র‍্যাঙ্কিং পেতে "আমার গ্যালারি" তে গিয়ে উঠানের ছবি আপলোড করুন।</div>
                        </div>
                    @endif

                    {{-- Rank + last-month comparison badges --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="card p-5 text-center">
                            <div class="w-12 h-12 mx-auto bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl">
                                <i class="fas fa-medal"></i>
                            </div>
                            <div class="text-2xl font-bold text-slate-800 mt-3 tabular-nums">
                                @if($rank){{ $rank }}@else--@endif
                            </div>
                            <div class="text-xs text-slate-400">বর্তমান র‍্যাঙ্ক @if($rank)({{ $totalRanked }} জনের মধ্যে)@endif</div>
                        </div>
                        <div class="card p-5 text-center">
                            @php
                                $trendIcon = 'minus';
                                $trendText = 'নতুন';
                                $trendColor = 'slate';
                                if ($currentSV !== null && $prevSV !== null) {
                                    if ($currentSV > $prevSV) { $trendIcon = 'arrow-up'; $trendText = '+' . ($currentSV - $prevSV); $trendColor = 'emerald'; }
                                    elseif ($currentSV < $prevSV) { $trendIcon = 'arrow-down'; $trendText = ($currentSV - $prevSV); $trendColor = 'rose'; }
                                    else { $trendText = 'অপরিবর্তিত'; }
                                } elseif ($prevSV !== null) { $trendIcon = 'arrow-down'; $trendText = 'এই মাস নেই'; $trendColor = 'slate'; }
                            @endphp
                            <div class="w-12 h-12 mx-auto bg-{{ $trendColor }}-50 text-{{ $trendColor }}-600 rounded-xl flex items-center justify-center text-xl">
                                <i class="fas fa-{{ $trendIcon }}"></i>
                            </div>
                            <div class="text-2xl font-bold text-slate-800 mt-3 tabular-nums">{{ $trendText }}</div>
                            <div class="text-xs text-slate-400">গত মাসের স্কোর: {{ $prevSV ?? '--' }}</div>
                        </div>
                    </div>

                    {{-- How scoring works (clear formula) --}}
                    <div class="card p-6">
                        <div class="font-semibold text-slate-800 mb-3 flex items-center gap-2 text-sm">
                            <i class="fas fa-info-circle text-emerald-700"></i> স্কোর কীভাবে কাজ করে?
                        </div>
                        <div class="space-y-2.5 text-sm text-slate-600">
                            <div class="flex gap-2"><span class="text-emerald-600 font-bold">১.</span> আপনি উঠানের ছবি আপলোড করেন (মাসে সর্বোচ্চ {{ $uploadLimit }}টি)</div>
                            <div class="flex gap-2"><span class="text-emerald-600 font-bold">২.</span> অ্যাডমিন <strong>বেনামে</strong> প্রতিটি ছবিকে ১-১০ স্টার দেন (পরিচ্ছন্নতা, গোছানো, সবুজের উপস্থিতি দেখে)</div>
                            <div class="flex gap-2"><span class="text-emerald-600 font-bold">৩.</span> আপনার <strong>Social Value</strong> = রেট করা ছবিগুলোর গড় স্টার × ১০ (উদাহরণ: গড় ৮.৫ → স্কোর ৮৫/১০০)</div>
                            <div class="flex gap-2"><span class="text-emerald-600 font-bold">৪.</span> র‍্যাঙ্কিং এই স্কোরের ভিত্তিতে — সমান স্কোর হলে গত মাসের স্কোর বিবেচিত হয়</div>
                            <div class="flex gap-2"><span class="text-emerald-600 font-bold">৫.</span> সর্বোচ্চ স্কোরের সদস্যরা "মাসের সেরা পরিচ্ছন্ন পরিবার" হিসেবে ওয়েবসাইটে প্রদর্শিত হন</div>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <!-- ============ MOBILE BOTTOM NAV ============ -->
        <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 z-40 grid grid-cols-5 shadow-[0_-4px_12px_rgba(0,0,0,0.04)]">
            <button @click="activeTab='dashboard'" :class="{ 'mobile-nav-active': activeTab==='dashboard' }"
                    class="flex flex-col items-center gap-1 py-2.5 text-slate-400 text-[10px] transition">
                <span class="nav-icon w-8 h-8 rounded-lg flex items-center justify-center transition"><i class="fas fa-th-large text-sm"></i></span>
                হোম
            </button>
            <button @click="activeTab='building'" :class="{ 'mobile-nav-active': activeTab==='building' }"
                    class="flex flex-col items-center gap-1 py-2.5 text-slate-400 text-[10px] transition">
                <span class="nav-icon w-8 h-8 rounded-lg flex items-center justify-center transition"><i class="fas fa-home text-sm"></i></span>
                বাড়ি
            </button>
            <button @click="activeTab='dues'" :class="{ 'mobile-nav-active': activeTab==='dues' }"
                    class="flex flex-col items-center gap-1 py-2.5 text-slate-400 text-[10px] transition">
                <span class="nav-icon w-8 h-8 rounded-lg flex items-center justify-center transition"><i class="fas fa-wallet text-sm"></i></span>
                পেমেন্ট
            </button>
            <button @click="activeTab='gallery'" :class="{ 'mobile-nav-active': activeTab==='gallery' }"
                    class="flex flex-col items-center gap-1 py-2.5 text-slate-400 text-[10px] transition">
                <span class="nav-icon w-8 h-8 rounded-lg flex items-center justify-center transition"><i class="fas fa-camera text-sm"></i></span>
                গ্যালারি
            </button>
            <button @click="activeTab='ranking'" :class="{ 'mobile-nav-active': activeTab==='ranking' }"
                    class="flex flex-col items-center gap-1 py-2.5 text-slate-400 text-[10px] transition">
                <span class="nav-icon w-8 h-8 rounded-lg flex items-center justify-center transition"><i class="fas fa-trophy text-sm"></i></span>
                র‍্যাঙ্কিং
            </button>
        </nav>
    </div>
</div>

<!-- ============ COMING SOON TOAST ============ -->
<div x-show="showToast" x-cloak x-transition
     class="toast fixed bottom-20 lg:bottom-6 left-1/2 -translate-x-1/2 z-[100] bg-slate-900 text-white text-sm px-5 py-3.5 rounded-xl shadow-2xl flex items-center gap-3 max-w-[90vw]">
    <i class="fas fa-clock text-amber-400"></i>
    <span x-text="toastMsg"></span>
</div>

<script>
    function showComingSoon() {
        const app = document.querySelector('[x-data]').__x.$data;
        app.toastMsg = 'কামিং সুন — ইনশাআল্লাহ শীঘ্রই আসছে 🕌';
        app.showToast = true;
        setTimeout(() => { app.showToast = false; }, 3000);
    }
</script>

</body>
</html>
