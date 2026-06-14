<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function dashboard()
    {
        Gate::authorize('view-admin');

        return view('admin.dashboard', [
            'totalUsers' => User::count(),
            'activeToday' => User::whereDate('updated_at', today())->count(),
            'totalPosts' => 0, // Add your posts model count
            'pendingItems' => 0, // Add pending tasks count
            'recentUsers' => User::latest()->limit(5)->get(),
        ]);
    }
}
