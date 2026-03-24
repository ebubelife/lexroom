# FirstMediator - Remaining Tasks

## ✅ COMPLETED (35% Done)

### Phase 01 - Foundation
- ✅ Laravel 12 setup
- ✅ Authentication (email/password + Google OAuth)
- ✅ Email & Phone OTP verification
- ✅ User, Room, Wallet, OTP, EvidenceFile models
- ✅ Dashboard with sidebar navigation
- ✅ Tailwind CSS design system
- ✅ Nigerian phone validation

### Phase 03 - Live Room (Polling-Based)
- ✅ SessionMessage model & migration
- ✅ ChatController with polling endpoints
- ✅ ProcessLexResponse job (queued Lex AI)
- ✅ Timer system (Redis + scheduler)
- ✅ Live chat UI (Alpine.js polling)
- ✅ ClaudeService integration
- ✅ Phase tracking (opening → resolution)

---

## 🔨 REMAINING TASKS (65% Left)

### Phase 02 - Landing Page + Room Creation (20% done)
**Priority: Medium**

#### Landing Page
- ❌ Hero section with CTA
- ❌ How it works (5-step flow)
- ❌ Pricing cards (Starter/Standard/Extended)
- ❌ Dispute categories showcase
- ❌ Social proof section

#### Room Creation
- ⚠️ Multi-step form (basic version exists, needs enhancement):
  - ❌ Step 1: Category selector (6 categories with icons)
  - ❌ Step 2: Jurisdiction + language picker
  - ❌ Step 3: Case summary textarea
  - ❌ Step 4: Duration + payment type selector
- ❌ Signed Party B invite links (guest access)
- ❌ Email invitation to Party B

**Files to create/modify:**
- `resources/views/welcome.blade.php` (enhance)
- `resources/views/rooms/create.blade.php` (multi-step Alpine.js)
- `app/Http/Controllers/RoomController.php` (enhance store method)

---

### Phase 04 - Evidence Vault + Lex AI (40% done)
**Priority: High**

#### Evidence Processing
- ⚠️ File upload (controller exists, needs enhancement):
  - ❌ Cloudflare R2 / S3 storage integration
  - ❌ PDF text extraction (`smalot/pdfparser`)
  - ❌ DOCX text extraction
  - ❌ Image OCR (Google Cloud Vision API)
  - ❌ Evidence locked after session ends

#### Lex AI Enhancement
- ⚠️ ClaudeService exists, needs:
  - ❌ Contradiction detection between statements
  - ❌ Evidence analysis integration
  - ❌ Cross-examination question generation
  - ❌ Lex prompts database (category × jurisdiction)

**Packages to install:**
```bash
composer require smalot/pdfparser
composer require spatie/pdf-to-text
composer require google/cloud-vision
composer require league/flysystem-aws-s3-v3
```

**Migrations needed:**
- `lex_prompts` table (category, jurisdiction, system_prompt)

**Files to create/modify:**
- `app/Services/EvidenceProcessingService.php`
- `app/Jobs/ProcessEvidenceFile.php`
- `database/migrations/xxxx_create_lex_prompts_table.php`

---

### Phase 05 - Session Billing (Paystack) 💰
**Priority: Critical (Required for Launch)**

#### Paystack Integration
- ❌ Paystack checkout initialization
- ❌ Webhook handler (verify signature)
- ❌ Split payment flow:
  - Party A pays → Party B invited
  - Room activates only after both payments
- ❌ Session extension payment
- ❌ Credits system (unused time → wallet)

**Packages to install:**
```bash
composer require spatie/laravel-webhook-client
```

**Migrations needed:**
- `billing` table (room_id, party, amount, plan, paystack_ref, status)

**Files to create:**
- `app/Http/Controllers/PaystackController.php`
- `app/Http/Controllers/PaystackWebhookController.php`
- `app/Services/PaystackService.php`
- `database/migrations/xxxx_create_billing_table.php`

**Routes to add:**
```php
Route::post('/rooms/{uuid}/payment/initialize', [PaystackController::class, 'initialize']);
Route::post('/webhooks/paystack', [PaystackWebhookController::class, 'handle']);
```

---

### Phase 06 - Mediation Report + LexRefer
**Priority: High**

#### Report Generation
- ❌ GenerateReportJob (queued PDF generation)
- ❌ Blade PDF template (formal legal document)
- ❌ PDF generation (`barryvdh/laravel-dompdf`)
- ❌ Email PDF to both parties
- ❌ Report storage and download

#### LexRefer (Lawyer Escalation)
- ❌ Lawyer directory (filtered by jurisdiction)
- ❌ Escalation flow (package transcript + evidence)
- ❌ Lawyer notification email
- ❌ Commission tracking (15-25% referral fee)

**Packages to install:**
```bash
composer require barryvdh/laravel-dompdf
```

**Migrations needed:**
- `reports` table (room_id, findings, resolution, confidence_score, pdf_path)
- `lawyers` table (name, email, jurisdiction, speciality, verified)
- `commissions` table (lawyer_id, room_id, amount, status)

**Files to create:**
- `app/Jobs/GenerateReportJob.php`
- `app/Services/ReportGenerationService.php`
- `app/Http/Controllers/EscalationController.php`
- `resources/views/reports/template.blade.php`
- `resources/views/lexrefer/directory.blade.php`

---

### Phase 07 - LexConsole Admin + QA + Launch
**Priority: Medium (Post-MVP)**

#### Admin Dashboard
- ❌ Admin middleware (role-based access)
- ❌ KPI dashboard (Chart.js):
  - Total sessions, revenue, resolution rate
  - Active rooms, completed rooms
- ❌ Room management (list, filter, view)
- ❌ User management
- ❌ Billing & revenue tracking
- ❌ Lex prompt editor (edit prompts per category × jurisdiction)
- ❌ Lawyer directory management

#### Laravel Horizon
- ❌ Install Laravel Horizon
- ❌ Configure queues (lex-processing, report-gen, notifications)
- ❌ Dashboard access control

**Packages to install:**
```bash
composer require laravel/horizon
```

**Files to create:**
- `app/Http/Middleware/EnsureAdminRole.php`
- `app/Http/Controllers/Admin/DashboardController.php`
- `app/Http/Controllers/Admin/RoomManagementController.php`
- `app/Http/Controllers/Admin/LexPromptController.php`
- `resources/views/admin/*`

---

## 📊 Progress Summary

| Phase | Status | Completion |
|-------|--------|------------|
| Phase 01 - Foundation | ✅ Complete | 100% |
| Phase 02 - Landing + Room Creation | ⚠️ Partial | 20% |
| Phase 03 - Live Room + Chat | ✅ Complete | 100% |
| Phase 04 - Evidence + Lex AI | ⚠️ Partial | 40% |
| Phase 05 - Paystack Billing | ❌ Not Started | 0% |
| Phase 06 - Reports + LexRefer | ❌ Not Started | 0% |
| Phase 07 - Admin + Launch | ❌ Not Started | 0% |

**Overall Progress: 35% Complete**

---

## 🎯 Recommended Build Order

### For MVP Launch (Minimum Viable Product):
1. **Phase 05 - Paystack Billing** (Critical - no revenue without this)
2. **Phase 02 - Complete Room Creation** (Better UX)
3. **Phase 06 - Report Generation** (Core value proposition)
4. **Phase 04 - Evidence Enhancement** (Improve Lex accuracy)
5. **Phase 07 - Admin Dashboard** (Operations management)

### Quick Win (Next 2-3 days):
1. Complete multi-step room creation form
2. Implement Paystack payment flow
3. Build basic PDF report generation
4. Test end-to-end session flow

---

## 🚀 Deployment Checklist (When Ready)

### cPanel Setup
- [ ] Upload files via Git/FTP
- [ ] Run `composer install --no-dev`
- [ ] Run `npm run build`
- [ ] Set up `.env` (production values)
- [ ] Run `php artisan migrate --force`
- [ ] Set up cron jobs:
  ```bash
  * * * * * cd /path && php artisan schedule:run
  * * * * * cd /path && php artisan queue:work --stop-when-empty
  ```
- [ ] Configure Redis (if available)
- [ ] Set up SSL certificate
- [ ] Configure Cloudflare (DNS, CDN, DDoS)

### Environment Variables
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `CLAUDE_API_KEY` (production key)
- [ ] `PAYSTACK_PUBLIC_KEY`
- [ ] `PAYSTACK_SECRET_KEY`
- [ ] `MAIL_*` (production SMTP)
- [ ] `DB_*` (production database)

---

**Current Status:** Phase 03 Complete ✅  
**Next Priority:** Phase 05 - Paystack Billing 💰  
**Estimated Time to MVP:** 2-3 weeks (with focused development)
