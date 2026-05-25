<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Login;
use Illuminate\Support\Facades\Auth;

// Public login route (guests only)
Route::get('/login', Login::class)->name('login')->middleware('guest');

// Redirect root to admin dashboard
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
})->middleware('auth');

// Logout (authenticated users)
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->name('logout')->middleware('auth');
