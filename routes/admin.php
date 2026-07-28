<?php

use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\SupportController as AdminSupportController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Admin\CreditSettingsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EvidenceController;
use App\Http\Controllers\Admin\JurisdictionController;
use App\Http\Controllers\Admin\LawyerController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\PlanController;
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
        Route::middleware('admin.can:admin.users.view')->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        });
        Route::middleware('admin.can:admin.users.manage')->group(function () {
            Route::post('/users/{user}/verify-email', [UserController::class, 'verifyEmail'])->name('users.verify-email');
            Route::post('/users/{user}/verify-phone', [UserController::class, 'verifyPhone'])->name('users.verify-phone');
            Route::post('/users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
            Route::post('/users/{user}/unsuspend', [UserController::class, 'unsuspend'])->name('users.unsuspend');
            Route::post('/users/{user}/adjust-wallet', [UserController::class, 'adjustWallet'])->name('users.adjust-wallet');
        });

        // Rooms
        Route::middleware('admin.can:admin.rooms.view')->group(function () {
            Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
            Route::get('/rooms/archived', [RoomController::class, 'archived'])->name('rooms.archived');
            Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
        });
        Route::middleware('admin.can:admin.rooms.manage')->group(function () {
            Route::post('/rooms/{room}/force-lock', [RoomController::class, 'forceLock'])->name('rooms.force-lock');
            Route::post('/rooms/{room}/force-expire', [RoomController::class, 'forceExpire'])->name('rooms.force-expire');
            Route::post('/rooms/{room}/add-time', [RoomController::class, 'addTime'])->name('rooms.add-time');
            Route::post('/rooms/{room}/archive', [RoomController::class, 'archive'])->name('rooms.archive');
            Route::post('/rooms/{id}/restore', [RoomController::class, 'restore'])->name('rooms.restore');
        });
        Route::middleware('admin.can:admin.rooms.delete')->group(function () {
            Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
            Route::delete('/rooms/{id}/force-delete', [RoomController::class, 'forceDelete'])->name('rooms.force-delete');
        });

        // Billing
        Route::middleware('admin.can:admin.billing.view')->group(function () {
            Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
            Route::get('/billing/refunds', [BillingController::class, 'refunds'])->name('billing.refunds');
        });
        Route::middleware('admin.can:admin.billing.manage')->group(function () {
            Route::get('/billing/export', [BillingController::class, 'export'])->name('billing.export');
            Route::post('/billing/{billing}/refund', [BillingController::class, 'issueRefund'])->name('billing.refund');
        });

        // Files
        Route::middleware('admin.can:admin.files.view')->group(function () {
            Route::get('/files', [EvidenceController::class, 'index'])->name('files.index');
            Route::get('/files/{file}/download', [EvidenceController::class, 'download'])->name('files.download');
        });
        Route::delete('/files/{file}', [EvidenceController::class, 'destroy'])
            ->middleware('admin.can:admin.files.delete')
            ->name('files.destroy');

        // Reports
        Route::middleware('admin.can:admin.reports.view')->group(function () {
            Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/{report}/download', [ReportController::class, 'download'])->name('reports.download');
        });
        Route::middleware('admin.can:admin.reports.manage')->group(function () {
            Route::post('/reports/{report}/regenerate', [ReportController::class, 'regenerate'])->name('reports.regenerate');
            Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
        });

        // Referrals
        Route::get('/referrals', [ReferralController::class, 'index'])
            ->middleware('admin.can:admin.referrals.view')
            ->name('referrals.index');
        Route::post('/referrals/{reward}/revoke', [ReferralController::class, 'revoke'])
            ->middleware('admin.can:admin.referrals.manage')
            ->name('referrals.revoke');

        // Wallets
        Route::get('/wallets', [WalletController::class, 'index'])
            ->middleware('admin.can:admin.wallets.view')
            ->name('wallets.index');
        Route::middleware('admin.can:admin.wallets.manage')->group(function () {
            Route::post('/wallets/{wallet}/adjust', [WalletController::class, 'adjust'])->name('wallets.adjust');
            Route::post('/wallets/{wallet}/release-escrow', [WalletController::class, 'releaseEscrow'])->name('wallets.release-escrow');
        });

        // Agents
        Route::middleware('admin.can:admin.agents.manage')->group(function () {
            Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
            Route::put('/agents', [AgentController::class, 'update'])->name('agents.update');
        });

        // Settings & Audit Log
        Route::middleware('admin.can:admin.settings.manage')->group(function () {
            Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
            Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
            Route::put('/settings/packages/{package}', [SettingsController::class, 'updatePackage'])->name('settings.packages.update');
            Route::post('/settings/packages', [SettingsController::class, 'storePackage'])->name('settings.packages.store');
            Route::delete('/settings/packages/{package}', [SettingsController::class, 'destroyPackage'])->name('settings.packages.destroy');
        });

        // Lawyers
        Route::middleware('admin.can:admin.lawyers.view')->group(function () {
            Route::get('/lawyers', [LawyerController::class, 'index'])->name('lawyers.index');
        });
        Route::middleware('admin.can:admin.lawyers.manage')->group(function () {
            Route::get('/lawyers/create', [LawyerController::class, 'create'])->name('lawyers.create');
            Route::post('/lawyers', [LawyerController::class, 'store'])->name('lawyers.store');
            Route::get('/lawyers/{lawyer}/edit', [LawyerController::class, 'edit'])->name('lawyers.edit');
            Route::put('/lawyers/{lawyer}', [LawyerController::class, 'update'])->name('lawyers.update');
            Route::post('/lawyers/{lawyer}/toggle', [LawyerController::class, 'toggle'])->name('lawyers.toggle');
            Route::delete('/lawyers/{lawyer}', [LawyerController::class, 'destroy'])->name('lawyers.destroy');
        });

        // Newsletter subscribers
        Route::middleware('admin.can:admin.newsletter.view')->group(function () {
            Route::get('/newsletter', [NewsletterController::class, 'index'])->name('newsletter.index');
        });
        Route::middleware('admin.can:admin.newsletter.manage')->group(function () {
            Route::delete('/newsletter/{subscriber}', [NewsletterController::class, 'destroy'])->name('newsletter.destroy');
        });

        // Jurisdictions
        Route::get('/jurisdictions', [JurisdictionController::class, 'index'])
            ->middleware('admin.can:admin.jurisdictions.view')
            ->name('jurisdictions.index');
        Route::middleware('admin.can:admin.jurisdictions.manage')->group(function () {
            Route::post('/jurisdictions', [JurisdictionController::class, 'store'])->name('jurisdictions.store');
            Route::post('/jurisdictions/{jurisdiction}/toggle', [JurisdictionController::class, 'toggle'])->name('jurisdictions.toggle');
            Route::delete('/jurisdictions/{jurisdiction}', [JurisdictionController::class, 'destroy'])->name('jurisdictions.destroy');
        });

        // Subscription Plans
        Route::get('/plans', [PlanController::class, 'index'])
            ->middleware('admin.can:admin.plans.view')
            ->name('plans.index');
        Route::middleware('admin.can:admin.plans.manage')->group(function () {
            Route::post('/plans', [PlanController::class, 'store'])->name('plans.store');
            Route::put('/plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
            Route::post('/plans/{plan}/toggle', [PlanController::class, 'toggle'])->name('plans.toggle');
        });

        // Credit Settings & Top-ups
        Route::get('/credits', [CreditSettingsController::class, 'index'])
            ->middleware('admin.can:admin.credits.view')
            ->name('credits.index');
        Route::middleware('admin.can:admin.credits.manage')->group(function () {
            Route::put('/credits', [CreditSettingsController::class, 'update'])->name('credits.update');
            Route::post('/credits/topup', [CreditSettingsController::class, 'storeTopup'])->name('credits.topup.store');
            Route::put('/credits/topup/{package}', [CreditSettingsController::class, 'updateTopup'])->name('credits.topup.update');
            Route::post('/credits/topup/{package}/toggle', [CreditSettingsController::class, 'toggleTopup'])->name('credits.topup.toggle');
            Route::delete('/credits/topup/{package}', [CreditSettingsController::class, 'destroyTopup'])->name('credits.topup.destroy');
        });

        // Support Tickets
        Route::middleware('admin.can:admin.support.view')->group(function () {
            Route::get('/support', [AdminSupportController::class, 'index'])->name('support.index');
            Route::get('/support/{ticket}', [AdminSupportController::class, 'show'])->name('support.show');
        });
        Route::middleware('admin.can:admin.support.manage')->group(function () {
            Route::post('/support/{ticket}/reply', [AdminSupportController::class, 'reply'])->name('support.reply');
            Route::post('/support/{ticket}/status', [AdminSupportController::class, 'updateStatus'])->name('support.status');
        });

        // Admin User Management (Super Admin only)
        Route::middleware('admin.can:admin.admins.manage')->group(function () {
            Route::get('/admin-users', [AdminUserController::class, 'index'])->name('admin-users.index');
            Route::get('/admin-users/create', [AdminUserController::class, 'create'])->name('admin-users.create');
            Route::post('/admin-users', [AdminUserController::class, 'store'])->name('admin-users.store');
            Route::get('/admin-users/{adminUser}/edit', [AdminUserController::class, 'edit'])->name('admin-users.edit');
            Route::put('/admin-users/{adminUser}', [AdminUserController::class, 'update'])->name('admin-users.update');
            Route::delete('/admin-users/{adminUser}', [AdminUserController::class, 'destroy'])->name('admin-users.destroy');
        });
    });
});
