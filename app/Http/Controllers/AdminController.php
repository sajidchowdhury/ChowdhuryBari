<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Road;
use Illuminate\Database\Query\Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function loginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login'); // Create this view
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt authentication
        if (Auth::attempt($credentials)) {
            // Check if user is active
            $user = Auth::user();
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been deactivated. Please contact the administrator.']);
            }

            // Reject non-admin users at login time so they get a clear error
            // instead of being authenticated and then 403'd by the is_admin
            // middleware on every protected admin route.
            if ($user->role !== 'admin') {
                Auth::logout();
                return back()->withErrors(['email' => 'You do not have admin privileges. Please contact the administrator.']);
            }

            $request->session()->regenerate();
            session(['admin_mode' => true]); // Important flag
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function dashboard()
    {
        return view('admin.dashboard'); // Your admin panel
    }

    public function ourArea(Request $request)
    {
        $filter = $request->query('filter', 'road');
        $search = trim($request->query('search', ''));

        $roadsQuery = Road::with('buildings');

        if ($search !== '') {
            $roadsQuery->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhereHas('buildings', fn ($query) => $query->where('name', 'like', "%{$search}%")
                    ->orWhere('owner', 'like', "%{$search}%")
                    ->orWhere('building_type', 'like', "%{$search}%")
                );
        }

        $roads = $roadsQuery->get();

        $buildings = Building::with('road')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('owner', 'like', "%{$search}%")
                    ->orWhere('owner_number', 'like', "%{$search}%")
                    ->orWhere('building_type', 'like', "%{$search}%")
                    ->orWhere('google_ln', 'like', "%{$search}%")
                    ->orWhere('google_lt', 'like', "%{$search}%");
            }))
            ->get();

        return view('admin.our-area', [
            'filter' => $filter,
            'search' => $search,
            'roads' => $roads,
            'buildings' => $buildings,
        ]);
    }

    public function storeOurArea(Request $request)
    {
        $validated = $request->validate([
            'road_name' => ['required', 'string', 'max:255'],
            'road_image' => ['nullable', 'file', 'image', 'max:5120'],
            'buildings' => ['required', 'array', 'min:1'],
            'buildings.*.building_name' => ['required', 'string', 'max:255'],
            'buildings.*.owner_name' => ['required', 'string', 'max:255'],
            'buildings.*.total_floor' => ['required', 'integer', 'min:1'],
            'buildings.*.total_family' => ['required', 'integer', 'min:0'],
            'buildings.*.building_type' => ['required', 'string', 'max:255'],
            'buildings.*.owner_number' => ['required', 'string', 'max:255'],
            'buildings.*.google_ln' => ['nullable', 'string', 'max:255'],
            'buildings.*.google_lt' => ['nullable', 'string', 'max:255'],
            'buildings.*.extra_information' => ['nullable', 'string'],
            'buildings.*.service_taking' => ['nullable', 'array'],
            'buildings.*.service_taking.*' => ['string', 'in:cleaning,security'],
            'buildings.*.building_image' => ['nullable', 'file', 'image', 'max:5120'],
        ]);

        $roadData = [
            'name' => $validated['road_name'],
            'description' => null,
        ];

        if ($request->hasFile('road_image')) {
            $roadData['image_path'] = $request->file('road_image')->store('roads', 'public');
        }

        $road = Road::create($roadData);

        foreach ($validated['buildings'] as $buildingData) {
            $building = [
                'road_id' => $road->id,
                'name' => $buildingData['building_name'],
                'owner' => $buildingData['owner_name'],
                'total_floor' => $buildingData['total_floor'],
                'total_family' => $buildingData['total_family'],
                'building_type' => $buildingData['building_type'],
                'owner_number' => $buildingData['owner_number'],
                'google_ln' => $buildingData['google_ln'] ?? null,
                'google_lt' => $buildingData['google_lt'] ?? null,
                'extra_information' => $buildingData['extra_information'] ?? null,
                'service_taking' => $buildingData['service_taking'] ?? [],
            ];

            if (isset($buildingData['building_image']) && $buildingData['building_image'] instanceof UploadedFile) {
                $building['image_path'] = $buildingData['building_image']->store('buildings', 'public');
            }

            Building::create($building);
        }

        session()->flash('status', 'Road created successfully.');

        return redirect()->route('admin.our-area');
    }

    public function viewWebsite()
    {
        session(['admin_mode' => true]);
        return redirect('/'); // Go to public site in admin mode
    }
}