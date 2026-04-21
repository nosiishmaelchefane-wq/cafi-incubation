<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Roles\UserRolesController;
use App\Http\Controllers\UserManagement\UserManagementController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Applications\ApplicationsController;
use App\Http\Controllers\Screening\ScreeningController;
use App\Http\Controllers\Evaluation\EvaluationController;
use App\Http\Controllers\Applications\Incubation\IncubationController;
use App\Http\Controllers\Cohorts\CohortsManagementController;
use App\Http\Controllers\Pitches\ShortlistingController;

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
    Route::get('/user/{id}', [UserManagementController::class, 'show'])->name('users.show');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/calls', [ApplicationsController::class, 'index'])->name('calls.index');
    Route::get('/call/{id}', [ApplicationsController::class, 'show'])->name('call.show');
    Route::get('/screening', [ScreeningController::class, 'index'])->name('screening.index');
    Route::get('/evaluation', [EvaluationController::class, 'index'])->name('evaluation.index');
    Route::get('/incubation/{id}', [IncubationController::class, 'show'])->name('incubation.show');
    Route::get('/cohorts', [CohortsManagementController::class, 'index'])->name('cohorts.index');
    Route::get('/cohort/show', [CohortsManagementController::class, 'show'])->name('cohorts.show');
    Route::get('/shortlisting', [ShortlistingController::class, 'index'])->name('shortlisting.index');
});

require __DIR__.'/auth.php';
