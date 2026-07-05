<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// ====================== ADMIN SECTION ======================
Route::prefix('admin')->name('admin.')->group(function () {

    // Redirect /admin to the admin login page
    Route::get('/', function () {
        return redirect()->route('admin.login');
    });

    // Admin Login Routes
    Route::get('/login', [AdminController::class, 'loginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.post');

    // Protected Admin Routes — require both authentication AND admin role.
    // The is_admin middleware (App\Http\Middleware\IsAdmin) aborts with 403
    // for any authenticated user whose role is not 'admin'.
    Route::middleware(['auth', 'is_admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/our-area', [AdminController::class, 'ourArea'])->name('our-area');
        Route::post('/our-area', [AdminController::class, 'storeOurArea'])->name('our-area.store');
        Route::get('/website', [AdminController::class, 'viewWebsite'])->name('website');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');

        // User management
        Route::resource('users', UserController::class);
    });
});



// Logout
Route::post('/logout', function () {
    Auth::logout();
    session()->forget('admin_mode');
    return redirect()->route('admin.login');
})->name('logout');

// Fallback for old /login if needed
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');
