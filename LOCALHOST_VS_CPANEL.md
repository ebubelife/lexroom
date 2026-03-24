# Localhost vs cPanel - Quick Reference

## 🏠 LOCALHOST (Development)

### Option 1: Manual (3 Terminals)
```bash
# Terminal 1: Web Server
php artisan serve

# Terminal 2: Queue Worker
php artisan queue:work

# Terminal 3: Scheduler
php artisan schedule:work
```

### Option 2: Test Cron Behavior (2 Terminals)
```bash
# Terminal 1: Web Server
php artisan serve

# Terminal 2: Cron Simulation
./test-cron.sh
```

**Use Option 2 to test cPanel behavior!**

---

## 🌐 CPANEL (Production)

### Web Server
✅ **Automatic** - Apache/Nginx handles this

### Queue Worker
Add cron job in cPanel:
```bash
* * * * * cd /home/username/public_html && php artisan queue:work --stop-when-empty
```

### Scheduler
Add cron job in cPanel:
```bash
* * * * * cd /home/username/public_html && php artisan schedule:run
```

---

## 🔑 The Magic Flag

### ❌ This (Localhost Only)
```bash
php artisan queue:work
# Runs forever - good for development
# Won't work on cPanel (process gets killed)
```

### ✅ This (Works Everywhere)
```bash
php artisan queue:work --stop-when-empty
# Processes all jobs, then exits
# Perfect for cPanel cron jobs
# Test this locally before deploying!
```

---

## 🧪 Test Before Deploy

### 1. Stop your normal queue worker
Press Ctrl+C in Terminal 2

### 2. Run cron simulation
```bash
./test-cron.sh
```

### 3. Test the app
- Create room
- Start session
- Send messages
- Wait for Lex response (within 1 minute)
- Let timer expire
- Check report generates (within 1 minute)

### 4. If everything works...
✅ **Your cPanel cron jobs will work perfectly!**

---

## 📊 Timing Comparison

| Action | Localhost (queue:work) | Localhost (cron sim) | cPanel (cron) |
|--------|------------------------|----------------------|---------------|
| Lex Response | 5-15 seconds | 5-60 seconds | 5-60 seconds |
| Report Generation | Immediate | Within 1 minute | Within 1 minute |
| Timer Decrement | Every second | Every minute | Every minute |
| Room Completion | Immediate | Within 1 minute | Within 1 minute |

**Slight delays are normal and acceptable for mediation!**

---

## 🎯 Bottom Line

**For Development:**
```bash
# Terminal 1
php artisan serve

# Terminal 2 (choose one)
php artisan queue:work              # Fast, instant processing
./test-cron.sh                      # Realistic, tests cPanel behavior
```

**For Production (cPanel):**
- Add 2 cron jobs (queue + scheduler)
- No code changes needed
- Everything just works!

**Test with `./test-cron.sh` before deploying to be 100% sure!** ✅
