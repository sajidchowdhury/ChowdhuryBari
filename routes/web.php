<?php

use Illuminate\Support\Facades\Route;
require __DIR__.'/admin.php';


Route::get('/', function () { return view('welcome'); });


Route::middleware(['auth', 'verified', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    // routes here
});