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

Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/about', 'about')->name('about');
Route::view('/gdpr', 'gdpr')->name('gdpr');
Route::view('/terms', 'terms')->name('terms');
Route::view('/disclaimer', 'disclaimer')->name('disclaimer');

// Test Email Dispatch — sends both emails to kongonut@gmail.com
Route::get('/test-email', function () {
    $testTo = 'kongonut@gmail.com';
    $results = [];

    // Grab the most recent room to use as sample data
    $room = \App\Models\Room::with(['partyA', 'partyB'])->latest()->first();

    if (!$room) {
        return response('<h2 style="font-family:sans-serif;color:#c00">⚠️ No rooms found in the database.<br>Create a room first, then come back here.</h2>', 200);
    }

    // 1. Send Party B invitation email
    try {
        \Illuminate\Support\Facades\Mail::to($testTo)->send(new \App\Mail\RoomInvitation($room));
        $results[] = '✅ <strong>Room Invitation</strong> (Party B) sent to ' . $testTo;
    } catch (\Exception $e) {
        $results[] = '❌ <strong>Room Invitation</strong> FAILED: ' . $e->getMessage();
    }

    // 2. Send Party A confirmation email
    try {
        \Illuminate\Support\Facades\Mail::to($testTo)->send(new \App\Mail\RoomConfirmation($room));
        $results[] = '✅ <strong>Room Confirmation</strong> (Party A) sent to ' . $testTo;
    } catch (\Exception $e) {
        $results[] = '❌ <strong>Room Confirmation</strong> FAILED: ' . $e->getMessage();
    }

    $html  = '<div style="font-family:sans-serif;max-width:600px;margin:60px auto;padding:30px;border-radius:12px;border:1px solid #e2e8f0;background:#fff;">';
    $html .= '<h2 style="color:#0D1B2A;margin-bottom:6px;">📨 Email Test Results</h2>';
    $html .= '<p style="color:#718096;margin-top:0;">Using room <code style="background:#f5f5f5;padding:2px 6px;border-radius:4px;">' . $room->case_id . '</code> as sample data</p>';
    $html .= '<ul style="line-height:2.2;padding-left:20px;">';
    foreach ($results as $r) {
        $html .= '<li>' . $r . '</li>';
    }
    $html .= '</ul>';
    $html .= '<p style="color:#718096;font-size:14px;margin-top:20px;">Check <strong>' . $testTo . '</strong> inbox (and spam folder).</p>';
    $html .= '</div>';

    return response($html, 200);
})->name('test.email');

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
    Route::delete('/rooms/{room}', [\App\Http\Controllers\RoomController::class, 'destroy'])->name('rooms.destroy');
    Route::post('/rooms/{uuid}/resend-invite', [\App\Http\Controllers\RoomController::class, 'resendInvite'])->name('rooms.resend-invite');
    
    // Settings routes
    Route::get('/settings', [\App\Http\Controllers\ProfileController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\ProfileController::class, 'update'])->name('settings.update');
    Route::post('/settings/avatar', [\App\Http\Controllers\ProfileController::class, 'updateAvatar'])->name('settings.avatar');
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
    Route::post('/rooms/{uuid}/messages', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('rooms.messages.send');
    Route::post('/rooms/{uuid}/clock-in', [\App\Http\Controllers\ChatController::class, 'clockIn'])->name('rooms.clock-in');
    Route::post('/rooms/{uuid}/start', [\App\Http\Controllers\ChatController::class, 'startSession'])->name('rooms.start');
    Route::post('/rooms/{uuid}/pause-request', [\App\Http\Controllers\ChatController::class, 'requestPause'])->name('rooms.pause-request');
    Route::post('/rooms/{uuid}/pause-accept', [\App\Http\Controllers\ChatController::class, 'acceptPause'])->name('rooms.pause-accept');
    Route::post('/rooms/{uuid}/resume', [\App\Http\Controllers\ChatController::class, 'resumeSession'])->name('rooms.resume');
Route::post('/rooms/{uuid}/phase', [\App\Http\Controllers\ChatController::class, 'changePhase'])->name('chat.phase');
Route::post('/rooms/{uuid}/lock', [\App\Http\Controllers\ChatController::class, 'lockSession'])->name('rooms.lock');
Route::post('/rooms/{uuid}/extend', [\App\Http\Controllers\ChatController::class, 'extendSession'])->name('rooms.extend');

// Evidence routes (guest or authenticated)
Route::post('/rooms/{uuid}/evidence', [\App\Http\Controllers\EvidenceController::class, 'upload'])->name('evidence.upload');
Route::get('/rooms/{uuid}/evidence', [\App\Http\Controllers\EvidenceController::class, 'index'])->name('evidence.index');
Route::get('/rooms/{uuid}/evidence/{file}', [\App\Http\Controllers\EvidenceController::class, 'download'])->name('evidence.download');
Route::delete('/rooms/{uuid}/evidence/{file}', [\App\Http\Controllers\EvidenceController::class, 'delete'])->name('evidence.delete');
