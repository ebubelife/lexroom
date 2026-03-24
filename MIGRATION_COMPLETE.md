# Reports System - Migration Complete ✅

## All Migrations Successful

```
✅ session_messages table
✅ reports table  
✅ lawyers table
✅ commissions table
```

## Quick Test

### 1. Start Required Services

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 - Queue Worker:**
```bash
php artisan queue:work
```

**Terminal 3 - Scheduler:**
```bash
php artisan schedule:work
```

### 2. Test Report Generation

**Option A: Let Timer Expire**
1. Create a room with 1-minute duration
2. Start session
3. Wait for timer to hit 0:00
4. Room completes automatically
5. Report generation job dispatched
6. Check queue worker terminal for processing
7. Check `storage/app/reports/` for PDF

**Option B: Manual Trigger (via Tinker)**
```bash
php artisan tinker
```

```php
// Create a test room and complete it
$room = \App\Models\Room::first();
$room->update(['status' => 'completed', 'ended_at' => now()]);

// Dispatch report generation
\App\Jobs\GenerateReportJob::dispatch($room->id);

// Check job was queued
\DB::table('jobs')->count();
```

### 3. Verify Report Created

```bash
php artisan tinker
```

```php
// Check reports table
\App\Models\Report::latest()->first();

// Check PDF file exists
\Storage::exists('reports/mediation-report-{uuid}.pdf');

// List all reports
\Storage::files('reports');
```

### 4. Test Report Download

1. Go to `/reports` in browser
2. Click on a report
3. Click "Download PDF"
4. PDF should download

## Seed Test Data (Optional)

Create sample lawyers for FM Refer:

```bash
php artisan tinker
```

```php
\App\Models\Lawyer::create([
    'name' => 'Adebayo Ogunlesi',
    'email' => 'a.ogunlesi@lawfirm.ng',
    'phone' => '08012345678',
    'jurisdiction' => 'Nigeria',
    'speciality' => 'Tenancy',
    'bio' => 'Experienced tenancy lawyer with 15 years of practice.',
    'bar_number' => 'NBA/12345',
    'years_experience' => 15,
    'commission_rate' => 20.00,
    'verified' => true,
    'active' => true,
]);

\App\Models\Lawyer::create([
    'name' => 'Chioma Nwosu',
    'email' => 'c.nwosu@legalaid.ng',
    'phone' => '08098765432',
    'jurisdiction' => 'Nigeria',
    'speciality' => 'Freelance',
    'bio' => 'Specialist in freelance and contract disputes.',
    'bar_number' => 'NBA/67890',
    'years_experience' => 10,
    'commission_rate' => 18.00,
    'verified' => true,
    'active' => true,
]);

\App\Models\Lawyer::create([
    'name' => 'Emeka Okafor',
    'email' => 'e.okafor@businesslaw.ng',
    'phone' => '08055555555',
    'jurisdiction' => 'Nigeria',
    'speciality' => 'Business',
    'bio' => 'Corporate and business dispute resolution expert.',
    'bar_number' => 'NBA/11111',
    'years_experience' => 20,
    'commission_rate' => 25.00,
    'verified' => true,
    'active' => true,
]);
```

Then visit `/fmrefer` to see the lawyer directory.

## Troubleshooting

### Report not generating
- Check queue worker is running
- Check `storage/logs/laravel.log` for errors
- Verify CLAUDE_API_KEY in `.env`

### PDF not found
- Check `storage/app/reports/` directory exists
- Run `php artisan storage:link`
- Check file permissions

### Email not sending
- Check mail configuration in `.env`
- Check `storage/logs/laravel.log` for mail errors
- Test with `php artisan tinker`:
  ```php
  Mail::raw('Test', function($msg) {
      $msg->to('test@example.com')->subject('Test');
  });
  ```

## Next Steps

Now that reports are working:

1. **Test end-to-end flow:**
   - Create room → Start session → Complete → Report generated

2. **Customize report template:**
   - Edit `resources/views/reports/template.blade.php`
   - Add your branding, colors, etc.

3. **Add more lawyers:**
   - Seed lawyer data for FM Refer
   - Test lawyer directory filtering

4. **Move to next phase:**
   - Phase 02: Landing Page
   - Phase 04: Evidence Enhancement
   - Phase 07: Admin Dashboard

---

**Status: All Migrations Complete ✅**  
**Reports System: Ready to Test 🚀**
