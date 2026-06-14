@extends('admin.layout')

@section('title', 'Create New User')
@section('page-title', 'Create New User')
@section('page-subtitle', 'Add a new user to your website')

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ route('admin.users.store') }}" method="POST" class="card-premium p-8">
        @csrf

        <!-- Personal Information Section -->
        <h3 class="heading-serif text-xl text-gray-900 mb-6 pb-4 border-b border-gray-200">
            <i class="fas fa-user-circle text-green-600 mr-2"></i> Personal Information
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Full Name -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name *</label>
                <input type="text" name="name" class="input-field" placeholder="Enter full name"
                       value="{{ old('name') }}" required>
                @error('name')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address *</label>
                <input type="email" name="email" class="input-field" placeholder="user@example.com"
                       value="{{ old('email') }}" required>
                @error('email')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                <input type="tel" name="phone" class="input-field" placeholder="+880 1234567890"
                       value="{{ old('phone') }}">
                @error('phone')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Address -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                <input type="text" name="address" class="input-field" placeholder="Enter address"
                       value="{{ old('address') }}">
            </div>
        </div>

        <!-- Account Settings Section -->
        <h3 class="heading-serif text-xl text-gray-900 mb-6 pb-4 border-b border-gray-200 mt-8">
            <i class="fas fa-lock text-green-600 mr-2"></i> Account Settings
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Password -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password *</label>
                <input type="password" name="password" class="input-field" placeholder="Minimum 8 characters"
                       required>
                @error('password')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Must be at least 8 characters</p>
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password *</label>
                <input type="password" name="password_confirmation" class="input-field" placeholder="Re-enter password"
                       required>
            </div>

            <!-- Role -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">User Role *</label>
                <select name="role" class="input-field" required>
                    <option value="user" selected>Member (User)</option>
                    <option value="moderator">Moderator</option>
                    <option value="admin">Administrator</option>
                </select>
                @error('role')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Account Status</label>
                <select name="is_active" class="input-field">
                    <option value="1" selected>Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>

        <!-- Permissions Section -->
        <h3 class="heading-serif text-xl text-gray-900 mb-6 pb-4 border-b border-gray-200 mt-8">
            <i class="fas fa-key text-green-600 mr-2"></i> Permissions
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:bg-green-50 cursor-pointer transition">
                <input type="checkbox" name="permissions[]" value="edit_content" class="w-4 h-4 text-green-600 rounded">
                <span class="font-semibold text-gray-700">Edit Content</span>
            </label>
            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:bg-green-50 cursor-pointer transition">
                <input type="checkbox" name="permissions[]" value="manage_users" class="w-4 h-4 text-green-600 rounded">
                <span class="font-semibold text-gray-700">Manage Users</span>
            </label>
            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:bg-green-50 cursor-pointer transition">
                <input type="checkbox" name="permissions[]" value="view_reports" class="w-4 h-4 text-green-600 rounded">
                <span class="font-semibold text-gray-700">View Reports</span>
            </label>
            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:bg-green-50 cursor-pointer transition">
                <input type="checkbox" name="permissions[]" value="system_settings" class="w-4 h-4 text-green-600 rounded">
                <span class="font-semibold text-gray-700">System Settings</span>
            </label>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4 justify-end">
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-check"></i> Create User
            </button>
        </div>
    </form>

    <!-- Helpful Information -->
    <div class="mt-8 bg-blue-50 border-l-4 border-blue-600 p-6 rounded-lg">
        <h4 class="font-semibold text-blue-900 mb-2">
            <i class="fas fa-info-circle"></i> User Role Information
        </h4>
        <ul class="text-sm text-blue-800 space-y-1">
            <li><strong>User:</strong> Basic member access, can view content</li>
            <li><strong>Moderator:</strong> Can manage content and moderate discussions</li>
            <li><strong>Admin:</strong> Full system access, can manage users and settings</li>
        </ul>
    </div>
</div>
@endsection
