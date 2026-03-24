# cPanel Deployment Guide - FirstMediator

## 🚀 Complete cPanel Setup Instructions

### Prerequisites
- cPanel hosting account
- PHP 8.2+
- MySQL database
- Redis (optional, but recommended)

---

## 📦 Step 1: Upload Files

### Option A: Git (Recommended)
```bash
# SSH into cPanel
cd public_html
git clone https://github.com/yourusername/firstmediator.git .
```

### Option B: File Manager
1. Zip your project locally
2. Upload via cPanel File Manager
3. Extract in `public_html` or subdirectory

---

## 🔧 Step 2: Install Dependencies

```bash
# SSH into your cPanel
cd /home/username/public_html

# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets
npm install
npm run build
```

---

## 🗄️ Step 3: Database Setup

### Create Database
1. cPanel → MySQL Databases
2. Create database: `username_firstmediator`
3. Create user: `username_fmuser`
4. Add user to database with ALL PRIVILEGES

### Configure .env
```env
APP_NAME=FirstMediator
APP_ENV=production
APP_KEY=base64:YOUR_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=username_firstmediator
DB_USERNAME=username_fmuser
DB_PASSWORD=your_secure_password

QUEUE_CONNECTION=database
CACHE_STORE=database

CLAUDE_API_KEY=your_claude_api_key

MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=465
MAIL_USERNAME=info@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=info@yourdomain.com
MAIL_FROM_NAME="FirstMediator"
```

### Run Migrations
```bash
php artisan key:generate
php artisan migrate --force
php artisan storage:link
```

---

## ⏰ Step 4: Setup Cron Jobs (CRITICAL)

Go to **cPanel → Cron Jobs** and add these 2 jobs:

### Cron Job 1: Queue Worker (Processes Reports & Lex AI)
```bash
* * * * * cd /home/username/public_html && php artisan queue:work --stop-when-empty >> /dev/null 2>&1
```

**What it does:**
- Processes report generation jobs
- Handles Lex AI responses
- Runs every minute
- Exits when queue is empty (cPanel-friendly)

### Cron Job 2: Scheduler (Timer System)
```bash
* * * * * cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
```

**What it does:**
- Decrements room timers every second
- Auto-completes rooms when time expires
- Triggers report generation
- Runs every minute

### Important Notes:
- Replace `/home/username/public_html` with YOUR actual path
- Find your path: cPanel → File Manager → look at address bar
- If multiple PHP versions, specify: `/usr/local/bin/php8.2 artisan`

### With Logging (for debugging):
```bash
* * * * * cd /home/username/public_html && php artisan queue:work --stop-when-empty >> /home/username/queue.log 2>&1
* * * * * cd /home/username/public_html && php artisan schedule:run >> /home/username/schedule.log 2>&1
```

---

## 🔐 Step 5: File Permissions

```bash
chmod -R 755 storage bootstrap/cache
chown -R username:username storage bootstrap/cache
```

Or via cPanel File Manager:
- Right-click `storage` → Permissions → 755
- Right-click `bootstrap/cache` → Permissions → 755

---

## 🌐 Step 6: Configure Domain

### Option A: Main Domain
Point domain to `/public_html/public`

### Option B: Subdomain
1. cPanel → Subdomains
2. Create: `app.yourdomain.com`
3. Document Root: `/public_html/public`

### .htaccess (if needed)
Create in `public_html/.htaccess`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

---

## ✅ Step 7: Verify Installation

### Check Cron Jobs Running
Wait 2 minutes after setting up cron, then check:

```bash
# Via SSH
tail -f storage/logs/laravel.log

# Via File Manager
# Open storage/logs/laravel.log
# Should see cron activity
```

### Test Queue Processing
```bash
php artisan tinker
>>> \DB::table('jobs')->count();
>>> \DB::table('failed_jobs')->count();
>>> exit
```

### Test Application
1. Visit your domain
2. Register account
3. Verify email/phone
4. Create a test room
5. Start session
6. Check timer counts down
7. Let it expire or wait
8. Check if report generates

---

## 🐛 Troubleshooting

### Cron Not Running
**Check:**
```bash
# View cron logs
cat /home/username/queue.log
cat /home/username/schedule.log

# Check Laravel logs
tail -100 storage/logs/laravel.log
```

**Common Issues:**
- Wrong path in cron command
- Wrong PHP version
- File permissions (755 for storage)

### Queue Jobs Failing
**Check:**
```bash
php artisan tinker
>>> \DB::table('failed_jobs')->get();
```

**Common Causes:**
- CLAUDE_API_KEY missing or invalid
- Memory limit too low (increase in php.ini)
- Timeout issues (increase max_execution_time)

### Timer Not Working
**Check:**
- Scheduler cron job is running
- Redis connection (if using Redis)
- Room status is 'active'

**Test manually:**
```bash
php artisan rooms:decrement-timers
```

### Reports Not Generating
**Check:**
- Queue worker cron is running
- CLAUDE_API_KEY is valid
- Storage directory is writable
- DomPDF dependencies installed

**Test manually:**
```bash
php artisan tinker
>>> \App\Jobs\GenerateReportJob::dispatch(1);
```

---

## 🔄 Deployment Workflow

### Initial Deployment
1. Upload files
2. Install dependencies
3. Configure .env
4. Run migrations
5. Setup cron jobs
6. Test

### Updates/Changes
```bash
# Pull latest code
git pull origin main

# Update dependencies
composer install --no-dev
npm install && npm run build

# Run new migrations
php artisan migrate --force

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

---

## 📊 Performance Optimization

### Enable Caching
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Optimize Composer
```bash
composer install --optimize-autoloader --no-dev
```

### Enable OPcache
In cPanel → Select PHP Version → Options:
- Enable `opcache`
- Set `opcache.memory_consumption=128`
- Set `opcache.max_accelerated_files=10000`

---

## 🔒 Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] Strong `APP_KEY` generated
- [ ] Database user has limited privileges
- [ ] `.env` file is NOT in public directory
- [ ] SSL certificate installed (HTTPS)
- [ ] File permissions: 755 for directories, 644 for files
- [ ] `storage/` and `bootstrap/cache/` writable
- [ ] Disable directory listing in Apache

---

## 📱 Local Development vs cPanel

### Local (3 Terminals)
```bash
# Terminal 1
php artisan serve

# Terminal 2
php artisan queue:work

# Terminal 3
php artisan schedule:work
```

### cPanel (2 Cron Jobs)
```bash
# Cron 1: Queue
* * * * * cd /path && php artisan queue:work --stop-when-empty

# Cron 2: Scheduler
* * * * * cd /path && php artisan schedule:run
```

---

## 🎯 Quick Reference

### Essential Commands
```bash
# Clear all caches
php artisan optimize:clear

# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Check migration status
php artisan migrate:status

# View logs
tail -f storage/logs/laravel.log
```

### Important Paths
- Application: `/home/username/public_html`
- Public files: `/home/username/public_html/public`
- Logs: `/home/username/public_html/storage/logs`
- Reports: `/home/username/public_html/storage/app/reports`

### Important URLs
- Application: `https://yourdomain.com`
- Dashboard: `https://yourdomain.com/dashboard`
- Reports: `https://yourdomain.com/reports`
- FM Refer: `https://yourdomain.com/fmrefer`

---

## 📞 Support Checklist

When asking for help, provide:
1. Laravel version: `php artisan --version`
2. PHP version: `php -v`
3. Error logs: `storage/logs/laravel.log`
4. Cron logs: `/home/username/queue.log`
5. Failed jobs: `php artisan queue:failed`

---

## 🎉 Success Indicators

Your deployment is successful when:
- ✅ Website loads without errors
- ✅ Users can register and login
- ✅ Rooms can be created
- ✅ Timer counts down in real-time (±1 minute)
- ✅ Lex AI responds to messages
- ✅ Reports generate when rooms complete
- ✅ PDFs download successfully
- ✅ Emails are sent to users

---

**Last Updated:** March 23, 2026  
**Version:** 1.0  
**Status:** Production Ready 🚀

---

## Notes for Later

- Stripe integration deferred (add when ready)
- Landing page needs completion (Phase 02)
- Evidence enhancement pending (Phase 04)
- Admin dashboard pending (Phase 07)

**Current Progress:** 50% Complete
