<nav class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}">
                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                </a>
            </div>

            @if(request()->is('admin*') && auth()->check())
                <!-- Admin Navigation -->
                <div class="hidden sm:flex items-center space-x-8">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-gray-900 font-medium">ড্যাশবোর্ড</a>
                    <a href="{{ route('admin.website') }}" class="text-gray-700 hover:text-gray-900 font-medium">ওয়েবসাইট দেখুন</a>
                    <!-- Add more admin links here if needed -->
                </div>

                <div class="flex items-center gap-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-medium">
                            লগআউট
                        </button>
                    </form>
                </div>
            @else
                <!-- Public Navigation -->
                <div class="hidden sm:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-gray-900 font-medium">হোম</a>
                    <!-- আপনার অন্যান্য মেনু এখানে যোগ করুন -->
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.login') }}" 
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium">
                        অ্যাডমিন লগইন
                    </a>
                </div>
            @endif
        </div>
    </div>
</nav>