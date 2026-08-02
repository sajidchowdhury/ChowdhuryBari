<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        Gate::authorize('manage-users');

        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }

        $users = $query->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        Gate::authorize('manage-users');
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-users');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'in:user,moderator,admin'],
            'is_active' => ['required', 'boolean'],
            'permissions' => ['nullable', 'array'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'role' => $validated['role'],
            'is_active' => $validated['is_active'],
        ]);

        // Store permissions if available
        if (!empty($validated['permissions'])) {
            $user->permissions = json_encode($validated['permissions']);
            $user->save();
        }

        return redirect()->route('admin.users.index')
                       ->with('success', "User '{$user->name}' created successfully!");
    }

    public function show(User $user)
    {
        Gate::authorize('manage-users');

        $userPermissions = json_decode($user->permissions, true) ?? [];

        return view('admin.users.show', compact('user', 'userPermissions'));
    }

    public function edit(User $user)
    {
        Gate::authorize('manage-users');

        $userPermissions = json_decode($user->permissions, true) ?? [];

        return view('admin.users.edit', compact('user', 'userPermissions'));
    }

    public function update(Request $request, User $user)
    {
        Gate::authorize('manage-users');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', "unique:users,email,{$user->id}"],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'in:user,moderator,admin'],
            'is_active' => ['required', 'boolean'],
            'permissions' => ['nullable', 'array'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'role' => $validated['role'],
            'is_active' => $validated['is_active'],
        ]);

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
            $user->save();
        }

        if (!empty($validated['permissions'])) {
            $user->permissions = json_encode($validated['permissions']);
            $user->save();
        }

        return redirect()->route('admin.users.index')
                       ->with('success', "User '{$user->name}' updated successfully!");
    }

    public function destroy(User $user)
    {
        Gate::authorize('manage-users');

        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
                       ->with('success', "User '{$name}' deleted successfully!");
    }
}
