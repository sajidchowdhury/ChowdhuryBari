<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;

Route::middleware(['auth', 'verified', 'is_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // পরে User Management যোগ করবো
    });