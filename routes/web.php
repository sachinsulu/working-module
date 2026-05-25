<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Login;
use Illuminate\Support\Facades\Auth;

// Public login route (guests only)
Route::get('/login', Login::class)->name('login')->middleware('guest');

// Logout (authenticated users)
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->name('logout')->middleware('auth');
