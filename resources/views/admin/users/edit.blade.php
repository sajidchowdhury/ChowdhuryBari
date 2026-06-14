@extends('admin.layout')

@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('page-subtitle', 'Update user information and permissions')

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="card-premium p-8">
        @csrf
        @method('PUT')

        <!-- Personal Information Section -->
        <h3 class="heading-serif text-xl text-gray-900 mb-6 pb-4 border-b border-gray-200">
            <i class="fas fa-user-circle text-green-600 mr-2"></i> Personal Information
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Full Name -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name *</label>
                <input type="text" name="name" class="input-field" placeholder="Enter full name"
                       value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address *</label>
                <input type="email" name="email" class="input-field" placeholder="user@example.com"
                       value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                <input type="tel" name="phone" class="input-field" placeholder="+880 1234567890"
                       value="{{ old('phone', $user->phone ?? '') }}">
            </div>

            <!-- Address -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                <input type="text" name="address" class="input-field" placeholder="Enter address"
                       value="{{ old('address', $user->address ?? '') }}">
            </div>
        </div>

        <!-- Account Settings Section -->
        <h3 class="heading-serif text-xl text-gray-900 mb-6 pb-4 border-b border-gray-200 mt-8">
            <i class="fas fa-lock text-green-600 mr-2"></i> Account Settings
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Password -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                <input type="password" name="password" class="input-field" placeholder="Leave empty to keep current password">
                @error('password')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Only if you want to change it</p>
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" class="input-field" placeholder="Re-enter password">
            </div>

            <!-- Role -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">User Role *</label>
                <select name="role" class="input-field" required>
                    <option value="user" @if($user->role == 'user') selected @endif>Member (User)</option>
                    <option value="moderator" @if($user->role == 'moderator') selected @endif>Moderator</option>
                    <option value="admin" @if($user->role == 'admin') selected @endif>Administrator</option>
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Account Status</label>
                <select name="is_active" class="input-field">
                    <option value="1" @if($user->is_active) selected @endif>Active</option>
                    <option value="0" @if(!$user->is_active) selected @endif>Inactive</option>
                </select>
            </div>
        </div>

        <!-- Activity Section -->
        <h3 class="heading-serif text-xl text-gray-900 mb-6 pb-4 border-b border-gray-200 mt-8">
            <i class="fas fa-history text-green-600 mr-2"></i> Activity
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 bg-gray-50 p-4 rounded-lg">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Account Created</p>
                <p class="text-gray-900">{{ $user->created_at->format('M d, Y h:i A') }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Last Updated</p>
                <p class="text-gray-900">{{ $user->updated_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>

        <!-- Permissions Section -->
        <h3 class="heading-serif text-xl text-gray-900 mb-6 pb-4 border-b border-gray-200 mt-8">
            <i class="fas fa-key text-green-600 mr-2"></i> Permissions
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:bg-green-50 cursor-pointer transition">
                <input type="checkbox" name="permissions[]" value="edit_content" class="w-4 h-4 text-green-600 rounded"
                       @if(in_array('edit_content', $userPermissions ?? [])) checked @endif>
                <span class="font-semibold text-gray-700">Edit Content</span>
            </label>
            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:bg-green-50 cursor-pointer transition">
                <input type="checkbox" name="permissions[]" value="manage_users" class="w-4 h-4 text-green-600 rounded"
                       @if(in_array('manage_users', $userPermissions ?? [])) checked @endif>
                <span class="font-semibold text-gray-700">Manage Users</span>
            </label>
            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:bg-green-50 cursor-pointer transition">
                <input type="checkbox" name="permissions[]" value="view_reports" class="w-4 h-4 text-green-600 rounded"
                       @if(in_array('view_reports', $userPermissions ?? [])) checked @endif>
                <span class="font-semibold text-gray-700">View Reports</span>
            </label>
            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:bg-green-50 cursor-pointer transition">
                <input type="checkbox" name="permissions[]" value="system_settings" class="w-4 h-4 text-green-600 rounded"
                       @if(in_array('system_settings', $userPermissions ?? [])) checked @endif>
                <span class="font-semibold text-gray-700">System Settings</span>
            </label>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4 justify-end">
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-check"></i> Save Changes
            </button>
        </div>
    </form>

    <!-- Danger Zone -->
    <div class="mt-8 border-2 border-red-200 rounded-lg p-6 bg-red-50">
        <h4 class="font-bold text-red-900 mb-3">
            <i class="fas fa-exclamation-triangle"></i> Danger Zone
        </h4>
        <p class="text-sm text-red-800 mb-4">Deleting a user is permanent and cannot be undone.</p>
        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline"
              onsubmit="return confirm('Are you absolutely sure? This action cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                <i class="fas fa-trash"></i> Delete User Permanently
            </button>
        </form>
    </div>
</div>
@endsection
