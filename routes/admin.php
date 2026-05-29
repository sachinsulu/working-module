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

// User Management
Route::view('users', 'users.index')->name('users.index')->middleware('permission:users.view');
Route::resource('users', UserController::class)->except(['index']);

// Client Management
Route::view('clients', 'clients.index')->name('clients.index')->middleware('permission:clients.view');
Route::resource('clients', ClientController::class)->except(['index', 'show']);

// Role Management
Route::view('roles', 'roles.index')->name('roles.index')->middleware('permission:roles.view');
Route::resource('roles', RoleController::class)->except(['index']);

// Department Management
Route::view('departments', 'departments.index')->name('departments.index')->middleware('permission:departments.view');
Route::resource('departments', DepartmentController::class)->except(['index']);

// Project Management
Route::view('projects', 'projects.index')->name('projects.index')->middleware('permission:projects.view');
Route::resource('projects', ProjectController::class)->except(['index', 'show']);
