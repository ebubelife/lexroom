<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogViewerController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\PasswordResetLinkController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Log Viewer (for debugging)
Route::get('/logs', [LogViewerController::class, 'index'])->name('logs.index');
Route::get('/logs/clear', [LogViewerController::class, 'clear'])->name('logs.clear');

// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Google OAuth
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('/auth/google/complete', [AuthController::class, 'showGoogleComplete'])->name('auth.google.complete.form');
Route::post('/auth/google/complete', [AuthController::class, 'completeGoogleSignup'])->name('auth.google.complete');

// OTP Verification (authenticated but not necessarily verified)
Route::middleware('auth')->group(function () {
    Route::get('/verify', [OtpController::class, 'showVerification'])->name('verification.notice');
    Route::post('/verify/send', [OtpController::class, 'sendOtp'])->name('otp.send');
    Route::post('/verify/check', [OtpController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('/verify/resend', [OtpController::class, 'resendOtp'])->name('otp.resend');
});

// Authenticated and verified routes
Route::middleware(['auth', App\Http\Middleware\EnsureUserIsVerified::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Room routes
    Route::get('/rooms', [\App\Http\Controllers\RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/create', [\App\Http\Controllers\RoomController::class, 'create'])->name('rooms.create');
    Route::post('/rooms', [\App\Http\Controllers\RoomController::class, 'store'])->name('rooms.store');
    Route::get('/rooms/{uuid}', [\App\Http\Controllers\RoomController::class, 'show'])->name('rooms.show');
    
    // Stub routes for sidebar links
    Route::get('/reports', fn() => view('reports.index'))->name('reports.index');
    Route::get('/wallet', fn() => view('wallet.index'))->name('wallet.index');
    Route::get('/lexrefer', fn() => view('lexrefer.index'))->name('lexrefer.index');
    Route::get('/settings', fn() => view('settings.index'))->name('settings.index');
});
