<!DOCTYPE html>
<html lang="bn">
@include('layouts.head')
<body class="bg-slate-50 text-slate-800 antialiased">
    @include('layouts.navbar')
    
    @yield('content')

    <script>
        // Disable Boost Browser Logger
        window.__BOOST__ = window.__BOOST__ || {};
        window.__BOOST__.disableLogger = true;
    </script>
</body>
</html>
