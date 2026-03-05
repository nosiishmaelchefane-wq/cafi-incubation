<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Roles\UserRolesController;
use App\Http\Controllers\UserManagement\UserManagementController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/roles', [UserRolesController::class, 'index'])->name('roles.index');
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
});

require __DIR__.'/auth.php';
