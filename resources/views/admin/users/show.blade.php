@extends('admin.layout')

@section('title', 'View User')
@section('page-title', $user->name)
@section('page-subtitle', 'User profile and detailed information')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- User Profile Card -->
    <div class="lg:col-span-1">
        <div class="card-premium p-8 text-center">
            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white text-4xl font-bold mx-auto mb-4">
                {{ substr($user->name, 0, 1) }}
            </div>
            <h2 class="heading-serif text-2xl text-gray-900 mb-1">{{ $user->name }}</h2>
            <p class="text-gray-600 mb-4">{{ $user->email }}</p>

            <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold
                @if($user->role == 'admin') bg-red-100 text-red-700
                @elseif($user->role == 'moderator') bg-blue-100 text-blue-700
                @else bg-gray-100 text-gray-700
                @endif mb-6">
                {{ ucfirst($user->role ?? 'User') }}
            </span>

            <p class="text-sm mb-4">
                @if($user->is_active)
                    <span class="badge-active">Active</span>
                @else
                    <span class="badge-inactive">Inactive</span>
                @endif
            </p>

            <div class="border-t pt-6 mt-6">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-primary w-full mb-2">
                    <i class="fas fa-edit"></i> Edit User
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary w-full">
                    <i class="fas fa-arrow-left"></i> Back to Users
                </a>
            </div>
        </div>
    </div>

    <!-- User Details -->
    <div class="lg:col-span-2">
        <!-- Contact Information -->
        <div class="card-premium mb-6">
            <div class="p-6 border-b border-gray-100">
                <h3 class="heading-serif text-lg text-gray-900 flex items-center gap-2">
                    <i class="fas fa-address-card text-green-600"></i>
                    Contact Information
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Email</p>
                        <p class="text-gray-900"><a href="mailto:{{ $user->email }}" class="text-green-600 hover:text-green-700">{{ $user->email }}</a></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Phone</p>
                        <p class="text-gray-900">
                            @if($user->phone)
                                <a href="tel:{{ $user->phone }}" class="text-green-600 hover:text-green-700">{{ $user->phone }}</a>
                            @else
                                <span class="text-gray-400">Not provided</span>
                            @endif
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Address</p>
                        <p class="text-gray-900">
                            @if($user->address)
                                {{ $user->address }}
                            @else
                                <span class="text-gray-400">Not provided</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Details -->
        <div class="card-premium mb-6">
            <div class="p-6 border-b border-gray-100">
                <h3 class="heading-serif text-lg text-gray-900 flex items-center gap-2">
                    <i class="fas fa-lock text-green-600"></i>
                    Account Details
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">User ID</p>
                        <p class="text-gray-900 font-mono">#{{ $user->id }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Role</p>
                        <p class="text-gray-900">{{ ucfirst($user->role ?? 'User') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Account Status</p>
                        <p>
                            @if($user->is_active)
                                <span class="badge-active">Active</span>
                            @else
                                <span class="badge-inactive">Inactive</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Account Created</p>
                        <p class="text-gray-900">{{ $user->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Last Updated</p>
                        <p class="text-gray-900">{{ $user->updated_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions -->
        <div class="card-premium">
            <div class="p-6 border-b border-gray-100">
                <h3 class="heading-serif text-lg text-gray-900 flex items-center gap-2">
                    <i class="fas fa-key text-green-600"></i>
                    Permissions
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @php
                        $permissions = ['edit_content', 'manage_users', 'view_reports', 'system_settings'];
                        $userPermissions = $userPermissions ?? [];
                    @endphp
                    @foreach($permissions as $permission)
                        <div class="flex items-center gap-3 p-3 rounded-lg @if(in_array($permission, $userPermissions)) bg-green-50 @else bg-gray-50 @endif">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center @if(in_array($permission, $userPermissions)) bg-green-600 @else bg-gray-300 @endif">
                                @if(in_array($permission, $userPermissions))
                                    <i class="fas fa-check text-white text-xs"></i>
                                @endif
                            </div>
                            <span class="font-semibold @if(in_array($permission, $userPermissions)) text-green-700 @else text-gray-600 @endif">
                                {{ ucfirst(str_replace('_', ' ', $permission)) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
