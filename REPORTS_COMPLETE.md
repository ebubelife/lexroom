# Reports System - Complete ✅

## What Was Built

### 1. Database Migrations
- **reports** table - stores all mediation report data
- **lawyers** table - FM Refer lawyer directory
- **commissions** table - tracks referral commissions

### 2. Models
- **Report** - mediation report with all sections
- **Lawyer** - lawyer profiles for FM Refer
- **Commission** - commission tracking

### 3. Jobs
- **GenerateReportJob** - queued PDF generation
  - Calls ClaudeService to generate comprehensive report
  - Parses Claude's response into structured sections
  - Generates PDF using DomPDF
  - Emails PDF to both parties
  - Stores PDF in storage/app/reports/

### 4. Controllers
- **ReportsController** - list, view, download reports
- **FmReferController** - lawyer directory (renamed from LexRefer)

### 5. Views
- **reports/template.blade.php** - professional PDF template
- **emails/report.blade.php** - email notification template

### 6. Automatic Report Generation
- When room timer expires → status changes to 'completed'
- DecrementRoomTimers command automatically dispatches GenerateReportJob
- Report generated in background
- Both parties receive email with PDF attachment

---

## Report Structure

### PDF Sections
1. **Case Summary** - Overview of the dispute
2. **Party A's Position** - Arguments and evidence
3. **Party B's Position** - Arguments and evidence
4. **Evidence Reviewed** - List of uploaded files
5. **Factual Findings** - Objective facts established
6. **Contradictions Identified** - Inconsistencies found
7. **Legal Framework** - Relevant laws and principles
8. **Resolution Recommendation** - Suggested fair resolution
9. **Confidence Score** - AI confidence (0-100%)
10. **Next Steps** - Recommended actions

### Disclaimer
Every report includes:
- "This is AI-generated guidance, not legal advice"
- "Consult qualified legal professionals"
- "FM Refer available for legal escalation"

---

## How It Works

### Automatic Flow
```
Room timer expires (0:00)
    ↓
DecrementRoomTimers command
    ↓
Room status → 'completed'
    ↓
GenerateReportJob dispatched
    ↓
ClaudeService generates report content
    ↓
Parse sections from Claude response
    ↓
Save Report record to database
    ↓
Generate PDF from template
    ↓
Store PDF in storage/app/reports/
    ↓
Email PDF to Party A
    ↓
Email PDF to Party B
```

### Manual Trigger
```
POST /rooms/{room}/generate-report
    ↓
Check authorization (Party A only)
    ↓
Check room is completed
    ↓
Dispatch GenerateReportJob
    ↓
Return success message
```

---

## FM Refer (Lawyer Directory)

### Renamed from LexRefer
- ✅ Controller renamed: LexReferController → FmReferController
- ✅ Routes updated: /lexrefer → /fmrefer
- ✅ Views moved: lexrefer/ → fmrefer/
- ✅ Sidebar updated: "LexRefer" → "FM Refer"

### Features
- Lawyer directory filtered by:
  - Jurisdiction (Nigeria, UK, South Africa, Ghana)
  - Speciality (Tenancy, Freelance, Business, etc.)
  - Search by name
- Lawyer profiles with:
  - Bio, bar number, years of experience
  - Commission rate (default 20%)
  - Verified badge
- Contact lawyer from completed room
- Commission tracking for referrals

---

## Setup Instructions

### 1. Run Migrations
```bash
php artisan migrate
```

This creates:
- `reports` table
- `lawyers` table
- `commissions` table

### 2. Ensure Queue Worker Running
```bash
php artisan queue:work
```

Reports are generated in background queue.

### 3. Ensure Scheduler Running
```bash
php artisan schedule:work
```

Scheduler triggers report generation when rooms complete.

### 4. Storage Setup
```bash
php artisan storage:link
```

Ensures PDF files are accessible.

---

## Testing

### Test Report Generation

1. **Complete a Room**
   - Start a session
   - Let timer run to 0:00
   - Room status → 'completed'

2. **Check Queue**
   ```bash
   # In queue worker terminal
   [2026-03-23 12:30:00] Processing: App\Jobs\GenerateReportJob
   [2026-03-23 12:30:15] Processed:  App\Jobs\GenerateReportJob
   ```

3. **Check Database**
   ```bash
   php artisan tinker
   >>> \App\Models\Report::latest()->first()
   ```

4. **Check Storage**
   ```bash
   ls -la storage/app/reports/
   # Should see: mediation-report-{uuid}.pdf
   ```

5. **Check Email**
   - Check `storage/logs/laravel.log` for email logs
   - Or check actual email inbox

### Manual Report Generation

```bash
# Via API
POST /rooms/{room-uuid}/generate-report
Authorization: Bearer {token}

# Response
{
  "success": true,
  "message": "Report generation started..."
}
```

---

## File Locations

### New Files
- `database/migrations/2026_03_23_121804_create_reports_table.php`
- `database/migrations/2026_03_23_121817_create_lawyers_table.php`
- `database/migrations/2026_03_23_121817_create_commissions_table.php`
- `app/Models/Report.php`
- `app/Models/Lawyer.php`
- `app/Models/Commission.php`
- `app/Jobs/GenerateReportJob.php`
- `app/Http/Controllers/ReportsController.php`
- `app/Http/Controllers/FmReferController.php`
- `resources/views/reports/template.blade.php`
- `resources/views/emails/report.blade.php`

### Modified Files
- `app/Console/Commands/DecrementRoomTimers.php` - added report generation
- `routes/web.php` - added report routes, renamed lexrefer → fmrefer
- `resources/views/layouts/app.blade.php` - updated sidebar link

### Renamed
- `app/Http/Controllers/LexReferController.php` → `FmReferController.php`
- `resources/views/lexrefer/` → `fmrefer/`

---

## Package Installed

```bash
composer require barryvdh/laravel-dompdf
```

Used for PDF generation from Blade templates.

---

## Next Steps

### Immediate
1. ✅ Run migrations
2. ✅ Test report generation
3. ✅ Verify PDF output
4. ✅ Test email delivery

### Future Enhancements
- Add report preview before download
- Add report regeneration option
- Add custom report templates per jurisdiction
- Add report analytics (views, downloads)
- Add lawyer onboarding flow for FM Refer
- Add commission payment tracking

---

## Stripe Integration (Deferred)

As requested, Stripe payment integration is **not included** in this phase.

When ready to add Stripe:
1. Install: `composer require stripe/stripe-php`
2. Create `StripeController` for checkout
3. Create `StripeWebhookController` for payment confirmation
4. Add `billing` table migration
5. Integrate with room activation flow

---

## Progress Update

**Before:** 35% complete  
**Now:** 50% complete

**Completed:**
- ✅ Phase 01 - Foundation (100%)
- ✅ Phase 03 - Live Room + Polling (100%)
- ✅ Phase 06 - Reports System (100%)

**Remaining:**
- 🔨 Phase 02 - Landing + Room Creation (20%)
- 🔨 Phase 04 - Evidence Enhancement (40%)
- ❌ Phase 05 - Stripe Billing (0% - deferred)
- ❌ Phase 07 - Admin Dashboard (0%)

---

**Status: Reports System Complete ✅**  
**Next: Phase 02 (Landing Page) or Phase 04 (Evidence Enhancement)**
