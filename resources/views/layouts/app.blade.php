<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'চৌধুরীপাড়াস্থ সমাজ উন্নায়ন সংস্থা')</title>
        <meta name="description" content="@yield('description', 'চৌধুরীপাড়ার ভবন ও রাস্তার নিরাপত্তা, পরিচ্ছন্নতা ও উন্নয়নের জন্য নিবেদিত সমাজ-চালিত সংস্থা।')">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

        <!-- Premium Fonts: Inter + Playfair Display + Noto Sans Bengali -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700&family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Tailwind + Alpine via Vite (if built) or CDN fallback -->
        @if(file_exists(public_path('build/manifest.json')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        @endif

        <style>
            :root {
                --green: #065F46;
                --green-light: #059669;
                --gold: #C9A227;
                --gold-light: #E8D48B;
            }
            body { font-family: 'Inter', 'Noto Sans Bengali', system-ui, sans-serif; }
            .heading-serif { font-family: 'Playfair Display', 'Noto Sans Bengali', Georgia, serif; font-weight: 700; letter-spacing: -0.02em; }
            .premium-card { transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease; box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 8px 24px -12px rgba(15,23,42,.08); }
            .premium-card:hover { transform: translateY(-3px); box-shadow: 0 4px 8px rgba(15,23,42,.06), 0 16px 40px -16px rgba(15,23,42,.18); }
            .road-card { transition: transform 0.4s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.3s ease; }
            .road-card:hover { transform: translateY(-8px) scale(1.01); }
            .section-header { position: relative; display: inline-block; }
            .section-header::after { content: ""; position: absolute; left: 0; bottom: -10px; width: 56px; height: 3px; border-radius: 9999px; background: linear-gradient(90deg, #047857 0%, #f59e0b 100%); }
            .stat-number { font-variant-numeric: tabular-nums; letter-spacing: -0.02em; }
            .masonry-grid { column-count: 2; column-gap: 1rem; }
            @media (min-width: 768px) { .masonry-grid { column-count: 3; } }
            @media (min-width: 1024px) { .masonry-grid { column-count: 4; } }
            .masonry-grid > * { break-inside: avoid; margin-bottom: 1rem; }
            .modal-enter { animation: modalEnter 0.2s cubic-bezier(0.32,0.72,0,1); }
            @keyframes modalEnter { from { opacity: 0; transform: translateY(40px) scale(0.96); } to { opacity: 1; transform: translateY(0) scale(1); } }
            [x-cloak] { display: none !important; }
            html { scroll-behavior: smooth; }
        </style>
    </head>
    <body class="font-sans antialiased">

        @if(request()->is('admin*') && session('admin_mode') && auth()->check())
            <div class="bg-amber-600 text-white py-3 text-center text-sm fixed top-0 left-0 right-0 z-50 flex items-center justify-center gap-6 shadow-md">
                <span class="flex items-center gap-2">👷 আপনি ওয়েবসাইট দেখছেন <strong>ADMIN MODE</strong>-এ</span>
                <a href="{{ route('admin.dashboard') }}" class="underline hover:no-underline font-medium">← অ্যাডমিন প্যানেলে ফিরুন</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="underline hover:no-underline font-medium text-white">লগআউট</button>
                </form>
            </div>
            <style>body { padding-top: 60px !important; }</style>
        @endif

        <div class="min-h-screen bg-gray-100">
            @include('layouts.navbar')
            <main>@yield('content')</main>
        </div>

        @include('layouts.FloatingButton')
        @include('layouts.footer')
    </body>
</html>
