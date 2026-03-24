# Testing cPanel Cron Jobs on Localhost

## The Problem
- **Localhost:** You run `php artisan queue:work` manually
- **cPanel:** Cron jobs run automatically every minute
- **Question:** How to test that cron jobs will work on cPanel?

---

## ✅ Solution: Test Cron Behavior Locally

### Option 1: Use the Test Script (Easiest)

I've created `test-cron.sh` that simulates cPanel cron behavior:

```bash
# Make it executable (already done)
chmod +x test-cron.sh

# Run it
./test-cron.sh
```

**What it does:**
- Runs `queue:work --stop-when-empty` (processes all jobs, then stops)
- Runs `schedule:run` (executes scheduled tasks)
- Waits 60 seconds
- Repeats forever (until you press Ctrl+C)

**This is EXACTLY how cPanel cron works!**

---

### Option 2: Manual Testing (More Control)

Open 2 terminals:

**Terminal 1: Simulate Queue Cron**
```bash
# Run this every minute manually
php artisan queue:work --stop-when-empty

# Or automate it:
while true; do php artisan queue:work --stop-when-empty; sleep 60; done
```

**Terminal 2: Simulate Scheduler Cron**
```bash
# Run this every minute manually
php artisan schedule:run

# Or automate it:
while true; do php artisan schedule:run; sleep 60; done
```

---

## 🧪 Testing Checklist

### 1. Test Queue Processing

**Create a test job:**
```bash
php artisan tinker
```

```php
// Dispatch a test job
\App\Jobs\ProcessLexResponse::dispatch(1, 1);

// Check it's queued
\DB::table('jobs')->count(); // Should be 1

// Exit tinker
exit
```

**Now test cron behavior:**
```bash
# Run the cron simulation
./test-cron.sh

# Or manually
php artisan queue:work --stop-when-empty
```

**Verify:**
```bash
php artisan tinker
>>> \DB::table('jobs')->count(); // Should be 0 (processed)
>>> \DB::table('failed_jobs')->count(); // Should be 0 (no failures)
```

✅ **If this works, it will work on cPanel!**

---

### 2. Test Scheduler (Timer System)

**Start a room session:**
1. Create a room (1 minute duration)
2. Start the session
3. Note the room ID

**Run cron simulation:**
```bash
./test-cron.sh
```

**Watch the timer:**
- Every 60 seconds, the script runs `schedule:run`
- `schedule:run` executes `rooms:decrement-timers` every second for 60 seconds
- Timer decrements in Redis
- After 1 minute, room should complete

**Verify:**
```bash
php artisan tinker
>>> $room = \App\Models\Room::find(1);
>>> $room->status; // Should be 'completed' after timer expires
```

✅ **If this works, it will work on cPanel!**

---

### 3. Test Report Generation

**Complete a room:**
```bash
php artisan tinker
```

```php
$room = \App\Models\Room::first();
$room->update(['status' => 'completed', 'ended_at' => now()]);

// Manually trigger what the timer does
\App\Jobs\GenerateReportJob::dispatch($room->id);

exit
```

**Run cron simulation:**
```bash
./test-cron.sh
```

**Wait 1 minute, then check:**
```bash
php artisan tinker
>>> \App\Models\Report::latest()->first(); // Should exist
>>> \Storage::files('reports'); // Should show PDF
```

✅ **If this works, it will work on cPanel!**

---

## 🎯 The Key Difference

### ❌ Don't Do This (Won't Work on cPanel)
```bash
# This runs forever - cPanel can't do this
php artisan queue:work
```

### ✅ Do This (Works on cPanel)
```bash
# This processes jobs then exits - perfect for cron
php artisan queue:work --stop-when-empty
```

**Why?**
- cPanel cron jobs must **start and finish**
- They can't run forever like `queue:work`
- `--stop-when-empty` processes all jobs, then exits
- Next cron cycle (1 minute later) processes new jobs

---

## 📋 Complete Testing Workflow

### Step 1: Start Laravel Server
```bash
# Terminal 1
php artisan serve
```

### Step 2: Start Cron Simulation
```bash
# Terminal 2
./test-cron.sh
```

### Step 3: Use the App
1. Register/login
2. Create a room
3. Start session
4. Send messages (Lex responds within 1 minute)
5. Let timer expire (room completes, report generates within 1 minute)

### Step 4: Monitor
Watch Terminal 2 for cron activity:
```
[2026-03-23 14:30:00] Running cron cycle...
  → Processing queue...
  → Running scheduler...
  ✓ Cycle complete

[2026-03-23 14:31:00] Running cron cycle...
  → Processing queue...
  [Processing: App\Jobs\ProcessLexResponse]
  [Processed:  App\Jobs\ProcessLexResponse]
  → Running scheduler...
  ✓ Cycle complete
```

---

## 🔍 Debugging

### Check Queue Status
```bash
php artisan queue:failed  # See failed jobs
php artisan queue:retry all  # Retry failed jobs
```

### Check Scheduler Status
```bash
php artisan schedule:list  # See all scheduled tasks
php artisan schedule:test  # Test scheduler
```

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

---

## 🚀 Deployment Confidence

**Before deploying to cPanel, verify:**

- ✅ `./test-cron.sh` runs without errors
- ✅ Queue jobs process within 1 minute
- ✅ Timer decrements and rooms complete
- ✅ Reports generate automatically
- ✅ No failed jobs in database
- ✅ Logs show no errors

**If all these pass, your cPanel cron jobs will work perfectly!**

---

## 📝 cPanel Cron Jobs (Reminder)

When you deploy, add these in cPanel:

```bash
# Queue Worker (every minute)
* * * * * cd /home/username/public_html && php artisan queue:work --stop-when-empty >> /dev/null 2>&1

# Scheduler (every minute)
* * * * * cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
```

**These are IDENTICAL to what `test-cron.sh` does!**

---

## 💡 Pro Tips

### 1. Test with Short Durations
Create rooms with 1-2 minute durations for faster testing.

### 2. Monitor Redis
```bash
redis-cli
> KEYS room:*:timer
> GET room:1:timer
```

### 3. Clear Queue Between Tests
```bash
php artisan queue:flush  # Clear all queued jobs
```

### 4. Test Failed Jobs
Intentionally break something (e.g., invalid Claude API key) and verify failed jobs are logged:
```bash
php artisan queue:failed
```

---

## 🎬 Quick Test Script

Run this to test everything at once:

```bash
# Test queue processing
php artisan tinker --execute="
\App\Jobs\ProcessLexResponse::dispatch(1, 1);
echo 'Job queued' . PHP_EOL;
"

# Process it (like cron would)
php artisan queue:work --stop-when-empty

# Verify
php artisan tinker --execute="
echo 'Jobs remaining: ' . \DB::table('jobs')->count() . PHP_EOL;
echo 'Failed jobs: ' . \DB::table('failed_jobs')->count() . PHP_EOL;
"
```

Expected output:
```
Job queued
[Processing: App\Jobs\ProcessLexResponse]
[Processed:  App\Jobs\ProcessLexResponse]
Jobs remaining: 0
Failed jobs: 0
```

✅ **If you see this, cPanel will work!**

---

## Summary

**The secret:** `--stop-when-empty` flag

- **Localhost testing:** `./test-cron.sh` simulates cPanel
- **cPanel production:** Cron jobs run the same commands
- **No code changes needed:** Works everywhere!

**Test locally with cron simulation → Deploy to cPanel with confidence!** 🚀
