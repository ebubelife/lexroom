# FirstMediator (FM) Deployment Guide for cPanel

This guide provides the specific steps and commands needed to deploy FirstMediator to your live server at `/home/firstwomm/firstmediator.com/`.

## 1. Environment Configuration (.env)
Upload your `.env` file to the server and update these critical production settings:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://firstmediator.com`
- `DB_DATABASE=your_production_db_name`
- `DB_USERNAME=your_production_db_user`
- `DB_PASSWORD=your_production_db_password`
- `CACHE_STORE=database` (Recommended for cPanel)
- `QUEUE_CONNECTION=sync` (Best for cPanel: FM responds instantly, but 'Send' takes 5-10s)
- **Alternative**: `QUEUE_CONNECTION=database` (Only if you can run a 1-min cron job)

## 2. cPanel Cron Jobs
If you chose `QUEUE_CONNECTION=database`, set up these cron jobs:

### **Job 1: Task Scheduler (The "Heartbeat")**
**Frequency:** Once Per Minute (`* * * * *`) or Every 5 Minutes (`*/5 * * * *`) if restricted.
**Command:**
```bash
/usr/local/bin/php /home/firstwomm/firstmediator.com/artisan schedule:run >> /dev/null 2>&1
```

### **Job 2: FM AI Worker (The "Brain")**
**Note**: Skip this if you used `QUEUE_CONNECTION=sync`.
**Frequency:** Once Per Minute (`* * * * *`)
**Command:**
```bash
/usr/local/bin/php /home/firstwomm/firstmediator.com/artisan queue:work --stop-when-empty >> /dev/null 2>&1
```

## 3. Post-Deployment Commands
Run these commands via the cPanel **Terminal** or SSH after uploading your code:

```bash
# Move to the project directory
cd /home/firstwomm/firstmediator.com/

# Install/Update dependencies (if you have composer access)
composer install --optimize-autoloader --no-dev

# Run migrations to update the database
php artisan migrate --force

# Link the storage for evidence files and profile images
php artisan storage:link

# Clear all caches for a fresh start
php artisan optimize:clear
```

## 4. Verification Checklist
- [ ] **Login**: Can you log in to your dashboard?
- [ ] **Create Case**: Can you create a new mediation room?
- [ ] **Invite**: Does the invite link generated for Party B work?
- [ ] **FM Awareness**: When Party B "clocks in," does FM send the arrival message?
- [ ] **Timer**: Does the live countdown timer start and sync correctly?

## Common Issues
- **PHP Version**: If the `artisan` commands fail, try using the full path to PHP 8.4 (e.g., `/opt/cpanel/ea-php84/root/usr/bin/php`).
- **File Permissions**: Ensure that `storage` and `bootstrap/cache` folders are writable by the server (usually CHMOD 775).
