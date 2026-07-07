<?php

namespace App\Http\Controllers;

use App\Models\Road;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function loginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been deactivated. Please contact the administrator.']);
            }

            if ($user->role !== 'admin') {
                Auth::logout();
                return back()->withErrors(['email' => 'You do not have admin privileges. Please contact the administrator.']);
            }

            $request->session()->regenerate();
            session(['admin_mode' => true]);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function ourArea(Request $request)
    {
        $search = trim($request->query('search', ''));

        $roadsQuery = Road::with('buildings')->orderBy('name');

        if ($search !== '') {
            $roadsQuery->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhereHas('buildings', fn ($query) => $query->where('name', 'like', "%{$search}%")
                    ->orWhere('owner_name', 'like', "%{$search}%")
                );
        }

        $roads = $roadsQuery->get();

        return view('admin.our-area', [
            'search' => $search,
            'roads' => $roads,
        ]);
    }

    public function storeOurArea(Request $request)
    {
        $validated = $request->validate([
            'road_name'        => ['required', 'string', 'max:255'],
            'road_description' => ['nullable', 'string', 'max:500'],
            'road_image'       => ['nullable', 'file', 'image', 'max:5120'],
            'road_tags'        => ['nullable', 'string', 'max:500'],
        ]);

        $tags = collect(explode(',', $validated['road_tags'] ?? ''))
            ->map(fn ($tag) => trim($tag))
            ->filter()
            ->values()
            ->all();

        $roadData = [
            'name'        => $validated['road_name'],
            'description' => $validated['road_description'] ?? null,
            'tags'        => $tags ?: null,
        ];

        if ($request->hasFile('road_image')) {
            $roadData['image_path'] = $request->file('road_image')->store('roads', 'public');
        }

        Road::create($roadData);

        session()->flash('status', 'Road created successfully.');

        return redirect()->route('admin.our-area');
    }

    public function viewWebsite()
    {
        session(['admin_mode' => true]);
        return redirect('/');
    }
}
