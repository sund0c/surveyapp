<?php

use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RatingController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

// === Guest routes ===
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

    Route::get('/login/two-factor', [LoginController::class, 'showTwoFactorForm'])->name('login.2fa');
    Route::post('/login/two-factor', [LoginController::class, 'verifyTwoFactor'])->name('login.2fa.verify');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// === Protected admin routes ===
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/create', [ApplicationController::class, 'create'])->name('applications.create');
    Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');
    Route::post('/applications/{application}/toggle-active', [ApplicationController::class, 'toggleActive'])->name('applications.toggle-active');
    Route::post('/applications/{application}/regenerate', [ApplicationController::class, 'regenerateCredentials'])->name('applications.regenerate');

    Route::get('/ratings', [RatingController::class, 'index'])->name('ratings.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/profile/2fa/setup', [ProfileController::class, 'setupTwoFactor'])->name('profile.2fa.setup');
    Route::post('/profile/2fa/confirm', [ProfileController::class, 'confirmTwoFactor'])->name('profile.2fa.confirm');
    Route::delete('/profile/2fa', [ProfileController::class, 'disableTwoFactor'])->name('profile.2fa.disable');
});
