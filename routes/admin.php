<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Dashboard;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ProjectController;

// Admin Dashboard
Route::get('/', Dashboard::class)->name('dashboard');
Route::get('/dashboard', Dashboard::class); // Alias

// User Management (Traditional MPA / wrapper)
Route::group(['middleware' => ['permission:manage users']], function () {
    Route::view('users', 'users.index')->name('users.index');
    Route::resource('users', UserController::class)->except(['index']);
});

// Client Management (wrapper)
Route::group(['middleware' => ['permission:manage clients']], function () {
    Route::view('clients', 'clients.index')->name('clients.index');
    Route::resource('clients', ClientController::class)->except(['index', 'show']);
});

// Role Management (wrapper)
Route::group(['middleware' => ['permission:manage roles']], function () {
    Route::view('roles', 'roles.index')->name('roles.index');
    Route::resource('roles', RoleController::class)->except(['index']);
});

// Department Management (wrapper)
Route::group(['middleware' => ['permission:view department stats']], function () {
    Route::view('departments', 'departments.index')->name('departments.index');
    Route::resource('departments', DepartmentController::class)->except(['index']);
});

// Project Management (wrapper)
Route::group(['middleware' => ['permission:manage projects']], function () {
    Route::view('projects', 'projects.index')->name('projects.index');
    Route::resource('projects', ProjectController::class)->except(['index', 'show']);
});
