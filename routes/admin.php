<?php

use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EvidenceController;
use App\Http\Controllers\Admin\JurisdictionController;
use App\Http\Controllers\Admin\LawyerController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\CreditSettingsController;
use App\Http\Controllers\Admin\ReferralController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WalletController;
use Illuminate\Support\Facades\Route;

// Admin guest routes
Route::prefix('f-med/admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Protected admin routes
    Route::middleware('admin')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::post('/users/{user}/verify-email', [UserController::class, 'verifyEmail'])->name('users.verify-email');
        Route::post('/users/{user}/verify-phone', [UserController::class, 'verifyPhone'])->name('users.verify-phone');
        Route::post('/users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
        Route::post('/users/{user}/unsuspend', [UserController::class, 'unsuspend'])->name('users.unsuspend');
        Route::post('/users/{user}/adjust-wallet', [UserController::class, 'adjustWallet'])->name('users.adjust-wallet');

        // Rooms
        Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
        Route::get('/rooms/archived', [RoomController::class, 'archived'])->name('rooms.archived');
        Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
        Route::post('/rooms/{room}/force-lock', [RoomController::class, 'forceLock'])->name('rooms.force-lock');
        Route::post('/rooms/{room}/force-expire', [RoomController::class, 'forceExpire'])->name('rooms.force-expire');
        Route::post('/rooms/{room}/add-time', [RoomController::class, 'addTime'])->name('rooms.add-time');
        Route::post('/rooms/{room}/archive', [RoomController::class, 'archive'])->name('rooms.archive');
        Route::post('/rooms/{id}/restore', [RoomController::class, 'restore'])->name('rooms.restore');
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
        Route::delete('/rooms/{id}/force-delete', [RoomController::class, 'forceDelete'])->name('rooms.force-delete');

        // Billing
        Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
        Route::get('/billing/refunds', [BillingController::class, 'refunds'])->name('billing.refunds');
        Route::get('/billing/export', [BillingController::class, 'export'])->name('billing.export');
        Route::post('/billing/{billing}/refund', [BillingController::class, 'issueRefund'])->name('billing.refund');

        // Files
        Route::get('/files', [EvidenceController::class, 'index'])->name('files.index');
        Route::get('/files/{file}/download', [EvidenceController::class, 'download'])->name('files.download');
        Route::delete('/files/{file}', [EvidenceController::class, 'destroy'])->name('files.destroy');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{report}/download', [ReportController::class, 'download'])->name('reports.download');
        Route::post('/reports/{report}/regenerate', [ReportController::class, 'regenerate'])->name('reports.regenerate');
        Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');

        // Referrals
        Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals.index');
        Route::post('/referrals/{reward}/revoke', [ReferralController::class, 'revoke'])->name('referrals.revoke');

        // Wallets
        Route::get('/wallets', [WalletController::class, 'index'])->name('wallets.index');
        Route::post('/wallets/{wallet}/adjust', [WalletController::class, 'adjust'])->name('wallets.adjust');
        Route::post('/wallets/{wallet}/release-escrow', [WalletController::class, 'releaseEscrow'])->name('wallets.release-escrow');

        // Agents
        Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
        Route::put('/agents', [AgentController::class, 'update'])->name('agents.update');

        // Settings & Audit Log
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::put('/settings/packages/{package}', [SettingsController::class, 'updatePackage'])->name('settings.packages.update');
        Route::post('/settings/packages', [SettingsController::class, 'storePackage'])->name('settings.packages.store');
        Route::delete('/settings/packages/{package}', [SettingsController::class, 'destroyPackage'])->name('settings.packages.destroy');

        // Lawyers
        Route::get('/lawyers', [LawyerController::class, 'index'])->name('lawyers.index');
        Route::get('/lawyers/create', [LawyerController::class, 'create'])->name('lawyers.create');
        Route::post('/lawyers', [LawyerController::class, 'store'])->name('lawyers.store');
        Route::get('/lawyers/{lawyer}/edit', [LawyerController::class, 'edit'])->name('lawyers.edit');
        Route::put('/lawyers/{lawyer}', [LawyerController::class, 'update'])->name('lawyers.update');
        Route::post('/lawyers/{lawyer}/toggle', [LawyerController::class, 'toggle'])->name('lawyers.toggle');
        Route::delete('/lawyers/{lawyer}', [LawyerController::class, 'destroy'])->name('lawyers.destroy');

        // Jurisdictions
        Route::get('/jurisdictions', [JurisdictionController::class, 'index'])->name('jurisdictions.index');
        Route::post('/jurisdictions', [JurisdictionController::class, 'store'])->name('jurisdictions.store');
        Route::post('/jurisdictions/{jurisdiction}/toggle', [JurisdictionController::class, 'toggle'])->name('jurisdictions.toggle');
        Route::delete('/jurisdictions/{jurisdiction}', [JurisdictionController::class, 'destroy'])->name('jurisdictions.destroy');

        // Subscription Plans
        Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
        Route::post('/plans', [PlanController::class, 'store'])->name('plans.store');
        Route::put('/plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
        Route::post('/plans/{plan}/toggle', [PlanController::class, 'toggle'])->name('plans.toggle');

        // Credit Settings & Top-ups
        Route::get('/credits', [CreditSettingsController::class, 'index'])->name('credits.index');
        Route::put('/credits', [CreditSettingsController::class, 'update'])->name('credits.update');
        Route::post('/credits/topup', [CreditSettingsController::class, 'storeTopup'])->name('credits.topup.store');
        Route::put('/credits/topup/{package}', [CreditSettingsController::class, 'updateTopup'])->name('credits.topup.update');
        Route::post('/credits/topup/{package}/toggle', [CreditSettingsController::class, 'toggleTopup'])->name('credits.topup.toggle');
        Route::delete('/credits/topup/{package}', [CreditSettingsController::class, 'destroyTopup'])->name('credits.topup.destroy');
    });
});
