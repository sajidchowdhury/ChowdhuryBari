<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Tailwind + Alpine via Vite (if built) or CDN fallback (so the site
             renders even before 'npm run build' has been run) -->
        @if(file_exists(public_path('build/manifest.json')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        @endif
    </head>
    <body class="font-sans antialiased">
        
        <!-- ====================== ADMIN MODE TOOLBAR ====================== -->
        @if(request()->is('admin*') && session('admin_mode') && auth()->check())
            <div class="bg-amber-600 text-white py-3 text-center text-sm fixed top-0 left-0 right-0 z-50 flex items-center justify-center gap-6 shadow-md">
                <span class="flex items-center gap-2">
                    👷 আপনি ওয়েবসাইট দেখছেন <strong>ADMIN MODE</strong>-এ
                </span>
                
                <a href="{{ route('admin.dashboard') }}" 
                   class="underline hover:no-underline font-medium">
                    ← অ্যাডমিন প্যানেলে ফিরুন
                </a>
                
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="underline hover:no-underline font-medium text-white">
                        লগআউট
                    </button>
                </form>
            </div>

            <style>
                body { padding-top: 60px !important; } /* Push content down when admin bar is shown */
            </style>
        @endif
        <!-- ====================== END ADMIN MODE TOOLBAR ====================== -->

        <div class="min-h-screen bg-gray-100">
            @include('layouts.navbar')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>

        @include('layouts.FloatingButton')
        @include('layouts.footer')
    </body>
</html>