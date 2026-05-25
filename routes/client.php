<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Client\Dashboard;

// Client Dashboard (empty placeholder – requirements TBD)
Route::get('/', Dashboard::class)->name('dashboard');
Route::get('/dashboard', Dashboard::class); // alias
