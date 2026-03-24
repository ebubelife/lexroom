<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogViewerController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\PasswordResetLinkController;
use App\Http\Controllers\VaultController;
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
    
    // Settings routes
    Route::get('/settings', [\App\Http\Controllers\ProfileController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\ProfileController::class, 'update'])->name('settings.update');
    Route::put('/settings/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('settings.password');
    
    // Reports, Wallet, FM Refer routes
    Route::get('/reports', [\App\Http\Controllers\ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [\App\Http\Controllers\ReportsController::class, 'show'])->name('reports.show');
    Route::get('/reports/{report}/download', [\App\Http\Controllers\ReportsController::class, 'download'])->name('reports.download');
    Route::post('/rooms/{room}/generate-report', [\App\Http\Controllers\ReportsController::class, 'generate'])->name('rooms.generate-report');
    
    Route::get('/wallet', [\App\Http\Controllers\WalletController::class, 'index'])->name('wallet.index');
    Route::get('/fmrefer', [\App\Http\Controllers\FmReferController::class, 'index'])->name('fmrefer.index');
    Route::get('/fmrefer/{lawyer}', [\App\Http\Controllers\FmReferController::class, 'show'])->name('fmrefer.show');
    Route::post('/fmrefer/{lawyer}/contact', [\App\Http\Controllers\FmReferController::class, 'contact'])->name('fmrefer.contact');

    // Vault routes
    Route::get('/vault', [VaultController::class, 'index'])->name('vault.index');
    Route::get('/vault/download/{file}', [VaultController::class, 'download'])->name('vault.download');
});

// Room access (guest or authenticated)
Route::get('/rooms/{uuid}', [\App\Http\Controllers\RoomController::class, 'show'])->name('rooms.show');

// Chat polling endpoints (guest or authenticated)
Route::get('/rooms/{uuid}/poll', [\App\Http\Controllers\ChatController::class, 'poll'])->name('chat.poll');
Route::post('/rooms/{uuid}/messages', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');
Route::post('/rooms/{uuid}/start', [\App\Http\Controllers\ChatController::class, 'startSession'])->name('chat.start');
Route::post('/rooms/{uuid}/phase', [\App\Http\Controllers\ChatController::class, 'changePhase'])->name('chat.phase');

// Evidence routes (guest or authenticated)
Route::post('/rooms/{uuid}/evidence', [\App\Http\Controllers\EvidenceController::class, 'upload'])->name('evidence.upload');
Route::get('/rooms/{uuid}/evidence', [\App\Http\Controllers\EvidenceController::class, 'index'])->name('evidence.index');
Route::get('/rooms/{uuid}/evidence/{file}', [\App\Http\Controllers\EvidenceController::class, 'download'])->name('evidence.download');
Route::delete('/rooms/{uuid}/evidence/{file}', [\App\Http\Controllers\EvidenceController::class, 'delete'])->name('evidence.delete');
