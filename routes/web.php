<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\ProfileController;
use App\Models\Road;

Route::get('/', function () {
    return view('welcome', [
        'roads' => Road::with('buildings')->orderBy('name')->get(),
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
    });
});

// Logout
Route::post('/logout', function () {
    Auth::logout();
    session()->forget('admin_mode');
    return redirect()->route('admin.login');
})->name('logout');
