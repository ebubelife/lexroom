#!/bin/bash

# LexRoom Deployment Script for cPanel
# Run this script on your server after first deployment

echo "🚀 Setting up LexRoom on cPanel..."

# Navigate to project directory
cd /home/kodebloo/aidev.kodeblooded.com.ng

# Set proper permissions
echo "📁 Setting file permissions..."
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 777 storage
chmod -R 777 bootstrap/cache
chmod 644 .env

# Install/Update Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# Generate application key if not exists
echo "🔑 Generating application key..."
php artisan key:generate --force

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Cache configuration for production
echo "⚡ Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create symbolic link for storage (if needed)
echo "🔗 Creating storage link..."
php artisan storage:link

echo "✅ Deployment completed successfully!"
echo "🌐 Your site should now be available at: https://aidev.kodeblooded.com.ng"