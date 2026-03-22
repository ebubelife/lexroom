#!/bin/bash

# LexRoom Deployment Script
# Run this on your live server to deploy latest changes

echo "🚀 Starting LexRoom deployment..."

# Pull latest changes
echo "📥 Pulling latest code from GitHub..."
git pull origin main

# Install/Update Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# Install/Update NPM dependencies and build assets
echo "🎨 Building frontend assets..."
npm install
npm run build

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Clear all caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage symlink if not exists
echo "🔗 Creating storage symlink..."
php artisan storage:link

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo "✅ Deployment completed successfully!"
echo ""
echo "📋 Post-deployment checklist:"
echo "   - Test all pages (Dashboard, Reports, Wallet, LexRefer, Settings)"
echo "   - Upload a profile image to test storage"
echo "   - Create a test room"
echo "   - Check error logs if any issues"
