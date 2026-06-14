<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') | Chowdhury Para Development</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700&family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --green: #065F46;
            --green-light: #059669;
            --gold: #C9A227;
            --gold-light: #E8D48B;
        }

        body {
            font-family: 'Inter', 'Noto Sans Bengali', system-ui, -apple-system, sans-serif;
        }

        .heading-serif {
            font-family: 'Playfair Display', 'Noto Sans Bengali', Georgia, serif;
        }

        .sidebar-bg {
            background: linear-gradient(135deg, var(--green) 0%, #047857 100%);
        }

        .sidebar-link {
            @apply px-4 py-3 rounded-lg transition-all duration-200 flex items-center gap-3 text-gray-100 hover:bg-white/10 border-l-4 border-transparent;
        }

        .sidebar-link.active {
            @apply bg-white/20 text-white border-l-yellow-400;
        }

        .btn-primary {
            @apply px-4 py-2 rounded-lg bg-gradient-to-r from-green-700 to-green-600 text-white hover:shadow-lg transition-all duration-200;
        }

        .btn-secondary {
            @apply px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition-all duration-200;
        }

        .card-premium {
            @apply bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden;
        }

        .input-field {
            @apply w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100 transition-all duration-200;
        }

        .alert-success {
            @apply bg-green-50 border-l-4 border-green-600 p-4 rounded-r-lg;
        }

        .alert-error {
            @apply bg-red-50 border-l-4 border-red-600 p-4 rounded-r-lg;
        }

        .table-row-hover {
            @apply hover:bg-gray-50 transition-colors duration-200;
        }

        .badge-active {
            @apply px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700;
        }

        .badge-inactive {
            @apply px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700;
        }
    </style>

    @yield('extra-styles')
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="sidebar-bg w-64 text-white shadow-2xl flex flex-col fixed h-screen z-50 md:relative">
            <!-- Logo -->
            <div class="p-6 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                        <i class="fas fa-shield text-lg"></i>
                    </div>
                    <div>
                        <h1 class="heading-serif text-xl font-bold">Admin</h1>
                        <p class="text-xs text-gray-100">Control Panel</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto p-4">
                <div class="space-y-2">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link @if(request()->routeIs('admin.dashboard')) active @endif">
                        <i class="fas fa-chart-line w-5"></i>
                        <span>Dashboard</span>
                    </a>

                    <!-- Users -->
                    <div>
                        <details class="group">
                            <summary class="sidebar-link cursor-pointer @if(request()->routeIs('admin.users.*')) active @endif">
                                <i class="fas fa-users w-5"></i>
                                <span>Users</span>
                                <i class="fas fa-chevron-down ml-auto transition-transform group-open:rotate-180"></i>
                            </summary>
                            <div class="pl-8 mt-2 space-y-1">
                                <a href="{{ route('admin.users.index') }}" class="@if(request()->routeIs('admin.users.index')) text-yellow-300 @else text-gray-200 @endif hover:text-white text-sm transition-colors">
                                    <i class="fas fa-list w-4"></i> All Users
                                </a>
                                <a href="{{ route('admin.users.create') }}" class="@if(request()->routeIs('admin.users.create')) text-yellow-300 @else text-gray-200 @endif hover:text-white text-sm transition-colors">
                                    <i class="fas fa-plus-circle w-4"></i> Create User
                                </a>
                            </div>
                        </details>
                    </div>

                    <!-- Content Management -->
                    <div>
                        <details class="group">
                            <summary class="sidebar-link cursor-pointer">
                                <i class="fas fa-file-alt w-5"></i>
                                <span>Content</span>
                                <i class="fas fa-chevron-down ml-auto transition-transform group-open:rotate-180"></i>
                            </summary>
                            <div class="pl-8 mt-2 space-y-1">
                                <a href="#" class="text-gray-200 hover:text-white text-sm transition-colors">
                                    <i class="fas fa-image w-4"></i> Gallery
                                </a>
                                <a href="#" class="text-gray-200 hover:text-white text-sm transition-colors">
                                    <i class="fas fa-newspaper w-4"></i> Posts
                                </a>
                                <a href="#" class="text-gray-200 hover:text-white text-sm transition-colors">
                                    <i class="fas fa-calendar w-4"></i> Events
                                </a>
                            </div>
                        </details>
                    </div>

                    <!-- Settings -->
                    <div>
                        <details class="group">
                            <summary class="sidebar-link cursor-pointer">
                                <i class="fas fa-cog w-5"></i>
                                <span>Settings</span>
                                <i class="fas fa-chevron-down ml-auto transition-transform group-open:rotate-180"></i>
                            </summary>
                            <div class="pl-8 mt-2 space-y-1">
                                <a href="#" class="text-gray-200 hover:text-white text-sm transition-colors">
                                    <i class="fas fa-sliders-h w-4"></i> General
                                </a>
                                <a href="#" class="text-gray-200 hover:text-white text-sm transition-colors">
                                    <i class="fas fa-lock w-4"></i> Security
                                </a>
                                <a href="#" class="text-gray-200 hover:text-white text-sm transition-colors">
                                    <i class="fas fa-envelope w-4"></i> Email
                                </a>
                            </div>
                        </details>
                    </div>

                    <!-- Reports -->
                    <a href="#" class="sidebar-link">
                        <i class="fas fa-chart-bar w-5"></i>
                        <span>Reports</span>
                    </a>
                </div>
            </nav>

            
            <!-- Admin Profile -->
            <div class="p-4 border-t border-white/10">
                <a href="{{ route('admin.profile') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition">
                    <div class="w-10 h-10 rounded-full bg-yellow-400 flex items-center justify-center text-green-700 font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-100">Admin</p>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 rounded-lg text-sm text-gray-200 hover:bg-red-600/20 transition">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col md:ml-0 ml-64">
            <!-- Top Bar -->
            <header class="bg-white shadow-md p-6 flex items-center justify-between sticky top-0 z-40">
                <div>
                    <h2 class="heading-serif text-2xl text-gray-800">@yield('page-title', 'Dashboard')</h2>
                    <p class="text-gray-500 text-sm">@yield('page-subtitle')</p>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Notifications -->
                    <button class="relative p-2 text-gray-600 hover:text-green-700 transition">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <!-- Back to Website -->
                    <a href="" target="_blank" class="text-gray-600 hover:text-green-700 transition">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </header>

            <!-- Page Content -->
            <section class="flex-1 overflow-y-auto p-6">
                <!-- Alerts -->
                @if ($errors->any())
                    <div class="alert-error mb-6">
                        <h3 class="font-semibold text-red-700 mb-2">Errors:</h3>
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="text-red-600 text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert-success mb-6">
                        <p class="text-green-700 font-semibold">{{ session('success') }}</p>
                    </div>
                @endif

                @yield('content')
            </section>
        </main>
    </div>

    <script>
        // CSRF token setup for AJAX
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Auto-dismiss alerts after 5 seconds
        document.querySelectorAll('[class*="alert-"]').forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.3s ease';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });
    </script>

    @yield('extra-scripts')
</body>
</html>
