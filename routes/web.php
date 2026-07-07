<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiteSettingController;
use App\Models\AboutInfo;
use App\Models\Building;
use App\Models\Flat;
use App\Models\GalleryItem;
use App\Models\Member;
use App\Models\Notice;
use App\Models\Road;

Route::get('/', function () {
    $roads = Road::with(['buildings.flats'])->orderBy('name')->get();
    $buildings = Building::with('road')->get();
    $totalFlats = Flat::count();
    $members = Member::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
    $notices = Notice::currentlyActive()->latestFirst()->take(10)->get();
    $galleryItems = GalleryItem::active()->latestFirst()->take(10)->get();
    $about = AboutInfo::current();

    return view('welcome', [
        'roads'          => $roads,
        'totalBuildings' => $buildings->count(),
        'totalRoads'     => $roads->count(),
        'totalFlats'     => $totalFlats,
        'members'        => $members,
        'notices'        => $notices,
        'galleryItems'   => $galleryItems,
        'about'          => $about,
    ]);
})->name('home');

// ====================== ADMIN SECTION ======================
Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/', function () {
        return redirect()->route('admin.login');
    });

    Route::get('/login', [AdminController::class, 'loginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.post');

    Route::middleware(['auth', 'is_admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/our-area', [AdminController::class, 'ourArea'])->name('our-area');
        Route::post('/our-area', [AdminController::class, 'storeOurArea'])->name('our-area.store');
        Route::get('/website', [AdminController::class, 'viewWebsite'])->name('website');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');

        // User management
        Route::resource('users', UserController::class);

        // Building / Flat / Meter management
        Route::get('/buildings/{building}', [BuildingController::class, 'show'])->name('buildings.show');
        Route::post('/roads/{road}/buildings', [BuildingController::class, 'store'])->name('buildings.store');
        Route::put('/buildings/{building}', [BuildingController::class, 'update'])->name('buildings.update');
        Route::delete('/buildings/{building}', [BuildingController::class, 'destroy'])->name('buildings.destroy');

        // Flats (ad-hoc add for garage/rooftop + edit resident info)
        Route::post('/buildings/{building}/flats', [BuildingController::class, 'storeFlat'])->name('flats.store');
        Route::put('/flats/{flat}', [BuildingController::class, 'updateFlat'])->name('flats.update');
        Route::delete('/flats/{flat}', [BuildingController::class, 'destroyFlat'])->name('flats.destroy');

        // Meters — per-floor bulk add + single add + delete
        Route::post('/buildings/{building}/floor-meters', [BuildingController::class, 'storeFloorMeters'])->name('meters.store-floor');
        Route::post('/flats/{flat}/meters', [BuildingController::class, 'storeMeter'])->name('meters.store');
        Route::delete('/meters/{meter}', [BuildingController::class, 'destroyMeter'])->name('meters.destroy');

        // Meter readings (single recharge records)
        Route::post('/meters/{meter}/readings', [BuildingController::class, 'storeReading'])->name('readings.store');

        // Team members
        Route::get('/members', [MemberController::class, 'index'])->name('members.index');
        Route::post('/members', [MemberController::class, 'store'])->name('members.store');
        Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');
        Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');

        // Notices
        Route::get('/notices', [NoticeController::class, 'index'])->name('notices.index');
        Route::post('/notices', [NoticeController::class, 'store'])->name('notices.store');
        Route::put('/notices/{notice}', [NoticeController::class, 'update'])->name('notices.update');
        Route::delete('/notices/{notice}', [NoticeController::class, 'destroy'])->name('notices.destroy');

        // Gallery — upload image + short caption
        Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
        Route::post('/gallery', [GalleryController::class, 'store'])->name('gallery.store');
        Route::delete('/gallery/{gallery}', [GalleryController::class, 'destroy'])->name('gallery.destroy');

        // About Us — singleton content (headline + image + short description)
        Route::get('/about', [AboutController::class, 'edit'])->name('about.edit');
        Route::put('/about', [AboutController::class, 'update'])->name('about.update');
        Route::post('/about', [AboutController::class, 'update'])->name('about.update.post');

        // Get In Touch — contact info + recipient email for form submissions
        Route::get('/contact', [ContactController::class, 'edit'])->name('contact.edit');
        Route::put('/contact', [ContactController::class, 'update'])->name('contact.update');
        Route::post('/contact', [ContactController::class, 'update'])->name('contact.update.post');

        // Navigation & Footer — logo, nav color, social links, address
        Route::get('/settings', [SiteSettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SiteSettingController::class, 'update'])->name('settings.update');
        Route::post('/settings', [SiteSettingController::class, 'update'])->name('settings.update.post');
    });
});

// Public contact form submission (sends mail to admin-configured recipient)
Route::post('/contact/submit', [ContactController::class, 'submit'])->name('contact.submit');

// Logout
Route::post('/logout', function () {
    Auth::logout();
    session()->forget('admin_mode');
    return redirect()->route('admin.login');
})->name('logout');
