# LexRoom Deployment Guide

## Quick Deploy to Live Server

### Option 1: Using the Deployment Script (Recommended)

1. SSH into your live server:
   ```bash
   ssh your-user@aidev.kodeblooded.com.ng
   ```

2. Navigate to your project directory:
   ```bash
   cd /path/to/lexroom
   ```

3. Run the deployment script:
   ```bash
   ./deploy.sh
   ```

### Option 2: Manual Deployment

If the script doesn't work, run these commands manually:

```bash
# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage symlink
php artisan storage:link

# Set permissions
chmod -R 775 storage bootstrap/cache
```

## What's New in This Deployment

### Controllers Fixed
- ✅ ReportsController - Returns demo reports data
- ✅ WalletController - Returns balance and transactions
- ✅ LexReferController - Returns referral stats and history
- ✅ ProfileController - Handles profile updates and image uploads

### New Features
- Profile image upload (max 2MB)
- Google avatar integration
- Clickable profile links in sidebar and top bar
- Improved settings page with tabs
- Toast notifications everywhere

### Database Changes
- Added `profile_image` column to users table
- Added `google_avatar` column to users table

## Troubleshooting

### If you still see "Undefined variable" errors:

1. Check if controllers exist:
   ```bash
   ls -la app/Http/Controllers/ReportsController.php
   ls -la app/Http/Controllers/WalletController.php
   ls -la app/Http/Controllers/LexReferController.php
   ```

2. Clear all caches again:
   ```bash
   php artisan optimize:clear
   ```

3. Check file permissions:
   ```bash
   ls -la app/Http/Controllers/
   ```

### If profile images don't work:

1. Check storage symlink:
   ```bash
   ls -la public/storage
   ```

2. Create it manually if missing:
   ```bash
   php artisan storage:link
   ```

3. Set proper permissions:
   ```bash
   chmod -R 775 storage
   chown -R www-data:www-data storage
   ```

## Testing After Deployment

1. Visit https://aidev.kodeblooded.com.ng/dashboard
2. Click on Reports, Wallet, LexRefer - should all work
3. Click your avatar/name - should go to Settings
4. Upload a profile image - should save and display
5. Create a test room - should work with toast notifications

## Need Help?

If you encounter any issues:
1. Check Laravel logs: `tail -f storage/logs/laravel.log`
2. Check web server logs
3. Verify all files were pulled: `git status`
4. Ensure migrations ran: `php artisan migrate:status`
