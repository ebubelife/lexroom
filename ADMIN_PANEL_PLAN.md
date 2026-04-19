# FirstMediator — Admin Panel Plan

## Overview

A dedicated admin panel at `/admin` for platform operators to manage all aspects of FirstMediator. Protected by a separate `admin` role (already exists on the `users` table) and its own middleware stack.

---

## 1. Architecture

### Routing & Middleware
- All routes under `/admin` prefix
- `AdminMiddleware` — checks `auth` + `role === 'admin'`
- Separate layout: `resources/views/admin/layouts/app.blade.php`
- All controllers in `App\Http\Controllers\Admin\` namespace

### File Structure
```
app/Http/Controllers/Admin/
├── AdminController.php          # Dashboard overview
├── UserController.php           # User management
├── RoomController.php           # Room/session management
├── BillingController.php        # Payments & refunds
├── EvidenceController.php       # File management
├── ReportController.php         # Reports management
├── ReferralController.php       # Referral system
├── WalletController.php         # Wallet & credits
└── SettingsController.php       # Platform settings

resources/views/admin/
├── layouts/
│   └── app.blade.php            # Admin shell (sidebar + topbar)
├── dashboard/
│   └── index.blade.php          # Overview stats
├── users/
│   ├── index.blade.php          # Users table
│   └── show.blade.php           # User detail
├── rooms/
│   ├── index.blade.php          # All rooms table
│   └── show.blade.php           # Room detail + transcript
├── billing/
│   ├── index.blade.php          # All transactions
│   └── refunds.blade.php        # Refund management
├── files/
│   └── index.blade.php          # Evidence file browser
├── reports/
│   └── index.blade.php          # All generated reports
├── referrals/
│   └── index.blade.php          # Referral activity
├── wallets/
│   └── index.blade.php          # Wallet balances & adjustments
└── settings/
    └── index.blade.php          # Platform config
```

---

## 2. Modules

### 2.1 Dashboard (`/admin`)
Stats at a glance:
- Total users, new users (last 7/30 days)
- Total rooms, active/pending/resolved/expired
- Total revenue (Stripe), pending payouts
- Total evidence files, storage used
- Recent activity feed (last 10 actions across the platform)
- Charts: signups over time, revenue over time, rooms by status

### 2.2 User Management (`/admin/users`)

**List view:**
- Searchable, filterable table (by role, verification status, date joined)
- Columns: Name, Email, Phone, Verified, Role, Rooms count, Wallet balance, Joined
- Bulk actions: suspend, verify, export CSV

**User detail (`/admin/users/{id}`):**
- Full profile info (name, email, phone, BVN/NIN masked)
- Verification status (email + phone) with manual override buttons
- Role management (promote to admin, demote)
- Suspend / unsuspend account
- Wallet balance + transaction history
- All rooms the user is party to
- Referral tree (who they referred, who referred them)
- Login history (if implemented)

**Actions:**
- `POST /admin/users/{id}/verify-email` — manually verify email
- `POST /admin/users/{id}/verify-phone` — manually verify phone
- `POST /admin/users/{id}/suspend` — suspend account
- `POST /admin/users/{id}/unsuspend`
- `PUT /admin/users/{id}/role` — change role
- `POST /admin/users/{id}/adjust-wallet` — add/deduct credits

### 2.3 Room & Session Management (`/admin/rooms`)

**List view:**
- Filter by status (pending_payment, waiting, active, paused, locked, expired)
- Filter by date range, party A/B email
- Columns: Case ID, Title, Party A, Party B, Status, Plan, Created, Duration used

**Room detail (`/admin/rooms/{uuid}`):**
- Full room metadata (parties, plan, payment status, timers)
- Full session transcript (all `session_messages`)
- Evidence files attached
- Payment records (Party A + Party B billing rows)
- Session extensions history
- Pause/resume history
- Generated reports

**Actions:**
- `POST /admin/rooms/{uuid}/force-lock` — lock a session
- `POST /admin/rooms/{uuid}/force-expire` — expire a room
- `POST /admin/rooms/{uuid}/reset-timer` — reset session timer
- `DELETE /admin/rooms/{uuid}` — hard delete (with confirmation)
- `POST /admin/rooms/{uuid}/add-time` — manually add minutes

### 2.4 Billing & Payments (`/admin/billing`)

**List view:**
- All `billing` table records
- Filter by status (paid, pending, refunded), party, date range
- Columns: Case ID, User, Party, Plan, Amount, Stripe Intent ID, Status, Paid At

**Refund management (`/admin/billing/refunds`):**
- List of refund-eligible transactions (paid, not yet refunded)
- Issue refund via Stripe API (`stripe->refunds->create`)
- Track refund status
- Refund reason logging

**Actions:**
- `POST /admin/billing/{id}/refund` — trigger Stripe refund
- `GET /admin/billing/export` — export CSV of transactions

### 2.5 File Management (`/admin/files`)

**List view:**
- All `evidence_files` records across all rooms
- Filter by room, uploader, file type, date
- Columns: Filename, Room (Case ID), Uploaded by, Party, Size, Type, Uploaded At
- Preview/download any file
- Delete files with confirmation

**Storage overview:**
- Total files count
- Total storage used
- Breakdown by file type

**Actions:**
- `DELETE /admin/files/{id}` — delete evidence file
- `GET /admin/files/{id}/download` — admin download

### 2.6 Reports (`/admin/reports`)

**List view:**
- All generated `reports` records
- Filter by room, status (pending, generating, completed, failed)
- Download any report PDF
- Re-trigger failed report generation

**Actions:**
- `POST /admin/reports/{id}/regenerate`
- `GET /admin/reports/{id}/download`
- `DELETE /admin/reports/{id}`

### 2.7 Referrals (`/admin/referrals`)

**List view:**
- All `referral_rewards` records
- Columns: Referrer, Referred User, Minutes Awarded, Status, Created At
- Total referral minutes awarded platform-wide

**Actions:**
- `POST /admin/referrals/{id}/revoke` — revoke a referral reward
- `POST /admin/referrals/{id}/award` — manually award referral minutes

### 2.8 Wallet Management (`/admin/wallets`)

**List view:**
- All wallets with balances
- Filter by balance range, users with escrow
- Columns: User, Balance (minutes), Referral Minutes, Escrow Balance

**Actions:**
- `POST /admin/wallets/{id}/adjust` — add or deduct minutes with reason
- `POST /admin/wallets/{id}/release-escrow` — manually release escrow

### 2.9 Platform Settings (`/admin/settings`)

Configurable values stored in a `settings` table (key/value):
- Platform maintenance mode toggle
- Default session plan prices
- Max file upload size
- Referral reward minutes amount
- OTP expiry time
- Support email address
- Feature flags (e.g. enable/disable referrals, FM Refer, etc.)

---

## 3. Database Changes

### New: `admin_actions` table (audit log)
```
id, admin_id, action, target_type, target_id, meta (json), created_at
```
Every destructive or sensitive admin action is logged here.

### New: `settings` table
```
id, key (unique), value, created_at, updated_at
```

### New column: `users.suspended_at` (nullable timestamp)

---

## 4. Security

- `AdminMiddleware` on all `/admin/*` routes — hard redirect to `/login` if not admin
- All state-changing actions require `POST`/`PUT`/`DELETE` (CSRF protected)
- Every action logged to `admin_actions` audit table
- Refund actions double-confirmed (modal + reason required)
- Hard deletes require typed confirmation (room case ID or user email)
- Rate limiting on admin login

---

## 5. UI Design

- Reuses the existing Navy (`#0D1B2A`) + Gold (`#C9A84C`) design system
- Tailwind CSS + Alpine.js (same stack)
- Sidebar navigation with collapsible sections
- Data tables with server-side pagination, search, and filters
- Toast notifications for actions
- Confirmation modals for destructive actions
- Responsive (works on tablet for on-the-go management)

---

## 6. Build Order

| Phase | What |
|-------|------|
| 1 | Middleware + layout + dashboard overview |
| 2 | User management (list + detail + actions) |
| 3 | Room management (list + detail + transcript view) |
| 4 | Billing + refunds |
| 5 | File management |
| 6 | Reports + Referrals + Wallets |
| 7 | Platform settings + audit log viewer |

---

## 7. Routes Summary

```php
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('admin.users.show');
    Route::post('/users/{user}/verify-email', [UserController::class, 'verifyEmail']);
    Route::post('/users/{user}/verify-phone', [UserController::class, 'verifyPhone']);
    Route::post('/users/{user}/suspend', [UserController::class, 'suspend']);
    Route::post('/users/{user}/unsuspend', [UserController::class, 'unsuspend']);
    Route::put('/users/{user}/role', [UserController::class, 'updateRole']);
    Route::post('/users/{user}/adjust-wallet', [UserController::class, 'adjustWallet']);

    // Rooms
    Route::get('/rooms', [RoomController::class, 'index'])->name('admin.rooms.index');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('admin.rooms.show');
    Route::post('/rooms/{room}/force-lock', [RoomController::class, 'forceLock']);
    Route::post('/rooms/{room}/force-expire', [RoomController::class, 'forceExpire']);
    Route::post('/rooms/{room}/add-time', [RoomController::class, 'addTime']);
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy']);

    // Billing
    Route::get('/billing', [BillingController::class, 'index'])->name('admin.billing.index');
    Route::get('/billing/refunds', [BillingController::class, 'refunds'])->name('admin.billing.refunds');
    Route::post('/billing/{billing}/refund', [BillingController::class, 'issueRefund']);
    Route::get('/billing/export', [BillingController::class, 'export']);

    // Files
    Route::get('/files', [EvidenceController::class, 'index'])->name('admin.files.index');
    Route::get('/files/{file}/download', [EvidenceController::class, 'download']);
    Route::delete('/files/{file}', [EvidenceController::class, 'destroy']);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::post('/reports/{report}/regenerate', [ReportController::class, 'regenerate']);
    Route::delete('/reports/{report}', [ReportController::class, 'destroy']);

    // Referrals
    Route::get('/referrals', [ReferralController::class, 'index'])->name('admin.referrals.index');
    Route::post('/referrals/{reward}/revoke', [ReferralController::class, 'revoke']);

    // Wallets
    Route::get('/wallets', [WalletController::class, 'index'])->name('admin.wallets.index');
    Route::post('/wallets/{wallet}/adjust', [WalletController::class, 'adjust']);
    Route::post('/wallets/{wallet}/release-escrow', [WalletController::class, 'releaseEscrow']);

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
    Route::put('/settings', [SettingsController::class, 'update']);
});
```

---

*Last updated: planning phase — ready to build.*
Email: admin@firstmediator.com

Password: Admin@1234!

URL: http://127.0.0.1:8000/admin/login