<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Chowdhury Bari - Admin Panel')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind CDN (যতক্ষণ না Vite সেটআপ করেন) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            content: ["./resources/**/*.blade.php"],
            theme: {
                extend: {
                    fontFamily: {
                        bangla: ['Noto Sans Bengali', 'Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --primary: #0F766E;
            --accent: #F59E0B;
        }

        body {
            font-family: 'Inter', 'Noto Sans Bengali', system-ui, sans-serif;
        }

        /* Hide elements with x-cloak until Alpine.js initializes */
        [x-cloak] { display: none !important; }

        .sidebar {
            background: linear-gradient(180deg, #0F766E 0%, #134E4A 100%);
        }

        .sidebar-link {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-link:hover {
            background-color: rgba(255,255,255,0.15) !important;
            transform: translateX(8px);
        }

        .sidebar-link.active {
            background-color: rgba(245, 158, 11, 0.25) !important;
            border-left: 4px solid #F59E0B;
            color: white !important;
        }

        .card {
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }
    </style>

    @yield('extra-styles')
</head>
<body class="bg-slate-100">

<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar w-72 text-white flex flex-col fixed md:static h-full z-50 transition-transform duration-300 -translate-x-full md:translate-x-0 shadow-2xl">

        <!-- Logo Header -->
        <div class="px-6 py-8 border-b border-white/10">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-3xl shadow-inner">
                    🏠
                </div>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Chowdhury Bari</h1>
                    <p class="text-teal-200 text-sm -mt-1">Admin Dashboard</p>
                </div>
            </div>
        </div>

        <!-- Menu -->
        <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center gap-3 px-5 py-3.5 rounded-2xl text-white font-medium @if(request()->routeIs('admin.dashboard')) active @endif">
                <i class="fas fa-tachometer-alt w-5"></i>
                <span>ড্যাশবোর্ড</span>
            </a>

            <a href="{{ route('admin.about.edit') }}" class="sidebar-link flex items-center gap-3 px-5 py-3.5 rounded-2xl text-white font-medium @if(request()->routeIs('admin.about*')) active @endif">
                <i class="fas fa-info-circle w-5"></i>
                <span>আমাদের সম্পর্কে</span>
            </a>

            <a href="{{ route('admin.our-area') }}" class="sidebar-link flex items-center gap-3 px-5 py-3.5 rounded-2xl text-white font-medium @if(request()->routeIs('admin.our-area*')) active @endif">
                <i class="fas fa-map-marker-alt w-5"></i>
                <span>আওতাধীন এলাকা</span>
            </a>

            <a href="{{ route('admin.members.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-3.5 rounded-2xl text-white font-medium @if(request()->routeIs('admin.members*')) active @endif">
                <i class="fas fa-users w-5"></i>
                <span>আমাদের নেতৃত্ব</span>
            </a>

            <a href="{{ route('admin.notices.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-3.5 rounded-2xl text-white font-medium @if(request()->routeIs('admin.notices*')) active @endif">
                <i class="fas fa-bullhorn w-5"></i>
                <span>নোটিশ ও ঘোষণা</span>
            </a>

            <a href="{{ route('admin.gallery.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-3.5 rounded-2xl text-white font-medium @if(request()->routeIs('admin.gallery*')) active @endif">
                <i class="fas fa-images w-5"></i>
                <span>গ্যালারি</span>
            </a>

            <div class="px-5 text-teal-200 text-xs font-semibold mt-6 mb-2">সেটিংস</div>

            <a href="{{ route('admin.contact.edit') }}" class="sidebar-link flex items-center gap-3 px-5 py-3.5 rounded-2xl text-white font-medium @if(request()->routeIs('admin.contact*')) active @endif">
                <i class="fas fa-envelope w-5"></i>
                <span>Get In Touch</span>
            </a>

            <a href="{{ route('admin.settings.edit') }}" class="sidebar-link flex items-center gap-3 px-5 py-3.5 rounded-2xl text-white font-medium @if(request()->routeIs('admin.settings*')) active @endif">
                <i class="fas fa-sliders-h w-5"></i>
                <span>Navigation &amp; Footer</span>
            </a>
        </nav>

        <!-- Bottom Profile -->
        <div class="p-5 border-t border-white/10 mt-auto">
            <div class="flex items-center gap-4 p-3 rounded-3xl bg-white/10">
                <div class="w-11 h-11 bg-gradient-to-br from-amber-400 to-yellow-500 rounded-2xl flex items-center justify-center text-xl font-bold text-teal-900 shadow-inner">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="flex-1">
                    <p class="font-semibold">{{ auth()->user()->name ?? 'Admin User' }}</p>
                    <p class="text-teal-200 text-sm">Administrator</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-3 text-red-200 hover:bg-red-500/20 hover:text-white rounded-2xl transition-all">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="font-medium">লগআউট</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Area -->
    <div class="flex-1 flex flex-col overflow-hidden">
        
        <!-- Top Navbar -->
        <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="md:hidden p-3 hover:bg-slate-100 rounded-2xl text-slate-700">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
                <h2 class="text-2xl font-semibold text-slate-800">@yield('page-title', 'ড্যাশবোর্ড')</h2>
            </div>

            <div class="flex items-center gap-6">
                <div class="relative hidden md:block w-80">
                    <input type="text" placeholder="কিছু খুঁজুন..." 
                           class="w-full pl-12 pr-5 py-3 bg-slate-100 border border-transparent focus:border-teal-400 rounded-3xl text-sm focus:outline-none">
                    <i class="fas fa-search absolute left-5 top-4 text-slate-400"></i>
                </div>

                <button class="relative text-slate-600 hover:text-teal-600 transition">
                    <i class="fas fa-bell text-2xl"></i>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-full">3</span>
                </button>

                <a href="/" target="_blank" class="text-slate-600 hover:text-teal-600">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
        </header>

        <!-- Content Area -->
        <main class="flex-1 overflow-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('-translate-x-full');
    }
</script>

<script defer src="https://unpkg.com/alpinejs@3.14.2/dist/cdn.min.js"></script>

@yield('extra-scripts')
</body>

</html>