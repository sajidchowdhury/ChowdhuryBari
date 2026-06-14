@extends('admin.layout')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back! Here\'s your site overview.')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Users Card -->
    <div class="card-premium">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-600 text-sm font-semibold">Total Users</h3>
                <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
            <p class="heading-serif text-3xl text-gray-900 mb-2">{{ $totalUsers ?? 0 }}</p>
            <p class="text-green-600 text-sm">
                <i class="fas fa-arrow-up"></i>
                <span>Active members</span>
            </p>
        </div>
    </div>

    <!-- Active Users Today -->
    <div class="card-premium">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-600 text-sm font-semibold">Active Today</h3>
                <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
                    <i class="fas fa-user-check text-green-600 text-xl"></i>
                </div>
            </div>
            <p class="heading-serif text-3xl text-gray-900 mb-2">{{ $activeToday ?? 0 }}</p>
            <p class="text-green-600 text-sm">
                <i class="fas fa-arrow-up"></i>
                <span>Last 24 hours</span>
            </p>
        </div>
    </div>

    <!-- Total Posts -->
    <div class="card-premium">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-600 text-sm font-semibold">Total Posts</h3>
                <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-file-alt text-purple-600 text-xl"></i>
                </div>
            </div>
            <p class="heading-serif text-3xl text-gray-900 mb-2">{{ $totalPosts ?? 0 }}</p>
            <p class="text-green-600 text-sm">
                <i class="fas fa-arrow-up"></i>
                <span>Published content</span>
            </p>
        </div>
    </div>

    <!-- Pending Tasks -->
    <div class="card-premium">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-600 text-sm font-semibold">Pending</h3>
                <div class="w-12 h-12 rounded-lg bg-yellow-100 flex items-center justify-center">
                    <i class="fas fa-hourglass-end text-yellow-600 text-xl"></i>
                </div>
            </div>
            <p class="heading-serif text-3xl text-gray-900 mb-2">{{ $pendingItems ?? 0 }}</p>
            <p class="text-yellow-600 text-sm">
                <i class="fas fa-bell"></i>
                <span>Awaiting action</span>
            </p>
        </div>
    </div>
</div>

<!-- Charts & Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- User Growth Chart -->
    <div class="lg:col-span-2 card-premium">
        <div class="p-6 border-b border-gray-100">
            <h3 class="heading-serif text-lg text-gray-900 flex items-center gap-2">
                <i class="fas fa-chart-line text-green-600"></i>
                User Activity (30 Days)
            </h3>
        </div>
        <div class="p-6">
            <div class="h-64 flex items-end gap-2">
                @for($i = 1; $i <= 30; $i++)
                    <div class="flex-1 bg-gradient-to-t from-green-600 to-green-400 rounded-t opacity-60 hover:opacity-100 transition"
                         style="height: {{ rand(30, 95) }}%; min-height: 2px;"
                         title="Day {{ $i }}"></div>
                @endfor
            </div>
            <p class="text-gray-500 text-sm text-center mt-4">Displaying user registration trends</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card-premium">
        <div class="p-6 border-b border-gray-100">
            <h3 class="heading-serif text-lg text-gray-900 flex items-center gap-2">
                <i class="fas fa-lightning-bolt text-yellow-600"></i>
                Quick Actions
            </h3>
        </div>
        <div class="p-6 space-y-3">
            <a href="{{ route('admin.users.create') }}" class="block p-3 rounded-lg bg-blue-50 hover:bg-blue-100 transition text-blue-700 font-semibold">
                <i class="fas fa-plus-circle"></i> Add New User
            </a>
            <a href="{{ route('admin.users.index') }}" class="block p-3 rounded-lg bg-purple-50 hover:bg-purple-100 transition text-purple-700 font-semibold">
                <i class="fas fa-list"></i> Manage Users
            </a>
            <a href="#" class="block p-3 rounded-lg bg-green-50 hover:bg-green-100 transition text-green-700 font-semibold">
                <i class="fas fa-cog"></i> Settings
            </a>
        </div>
    </div>
</div>

<!-- Recent Users Table -->
<div class="mt-8 card-premium">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <h3 class="heading-serif text-lg text-gray-900 flex items-center gap-2">
            <i class="fas fa-users text-green-600"></i>
            Recent Users
        </h3>
        <a href="{{ route('admin.users.index') }}" class="text-green-600 hover:text-green-700 text-sm font-semibold">View All →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Joined</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recentUsers ?? [] as $user)
                    <tr class="table-row-hover">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                {{ $user->name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded bg-gray-100 text-gray-700 text-xs font-semibold">
                                {{ $user->role ?? 'User' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="@if($user->is_active) badge-active @else badge-inactive @endif">
                                @if($user->is_active) Active @else Inactive @endif
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-2 text-gray-300"></i>
                            <p>No users yet. <a href="{{ route('admin.users.create') }}" class="text-green-600 hover:text-green-700 font-semibold">Create one</a></p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
