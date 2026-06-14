@extends('admin.layout')

@section('title', 'Manage Users')
@section('page-title', 'Users Management')
@section('page-subtitle', 'Manage all website users and their permissions')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <p class="text-gray-600 mb-2">Total Users: <span class="font-bold text-green-600">{{ $users->total() }}</span></p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn-primary">
        <i class="fas fa-plus-circle"></i> Create New User
    </a>
</div>

<!-- Filter & Search -->
<div class="card-premium mb-6">
    <div class="p-6">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <input type="text" name="search" placeholder="Search by name, email..." class="input-field flex-1"
                   value="{{ request('search') }}">
            <select name="status" class="input-field">
                <option value="">All Status</option>
                <option value="active" @if(request('status') == 'active') selected @endif>Active</option>
                <option value="inactive" @if(request('status') == 'inactive') selected @endif>Inactive</option>
            </select>
            <button type="submit" class="btn-primary">Search</button>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card-premium">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-green-50 to-green-100 border-b border-green-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">User</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Email</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Role</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Joined</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                    <tr class="table-row-hover">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white font-bold">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">ID: {{ $user->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-700">{{ $user->email }}</p>
                            @if($user->phone)
                                <p class="text-xs text-gray-500">{{ $user->phone }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                @if($user->role == 'admin') bg-red-100 text-red-700
                                @elseif($user->role == 'moderator') bg-blue-100 text-blue-700
                                @else bg-gray-100 text-gray-700
                                @endif">
                                {{ ucfirst($user->role ?? 'User') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="@if($user->is_active) badge-active @else badge-inactive @endif">
                                @if($user->is_active) Active @else Inactive @endif
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="text-blue-600 hover:text-blue-700 transition">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="text-green-600 hover:text-green-700 transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-400">
                                <i class="fas fa-inbox text-4xl mb-3"></i>
                                <p class="text-lg font-semibold mb-2">No Users Found</p>
                                <p class="text-sm">Start by <a href="{{ route('admin.users.create') }}" class="text-green-600 hover:text-green-700 font-semibold">creating a new user</a></p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-6 flex justify-center">
    {{ $users->links() }}
</div>
@endsection
