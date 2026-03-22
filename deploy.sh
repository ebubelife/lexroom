#!/bin/bash

# LexRoom Deployment Script for cPanel
# Run this script on your server for manual deployment

echo "🚀 Setting up LexRoom on cPanel..."

# Navigate to project directory
cd /home/kodebloo/aidev.kodeblooded.com.ng

# Copy production environment file if it exists
if [ -f ".env.production" ]; then
    echo "📝 Setting up production environment..."
    cp .env.production .env
fi

# Check if composer exists
if ! command -v composer &> /dev/null; then
    echo "❌ Composer not found. Please install Composer first."
    echo "💡 You can download it from: https://getcomposer.org/download/"
    exit 1
fi

# Install/Update Composer dependencies (production only)
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "⚠️  .env file not found. Copying from .env.example..."
    cp .env.example .env
    echo "📝 Please update .env with your production settings!"
fi

# Generate application key if not exists
echo "🔑 Generating application key..."
php artisan key:generate --force

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Cache configuration for production performance
echo "⚡ Caching configuration for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create symbolic link for storage (if needed)
echo "🔗 Creating storage link..."
php artisan storage:link

# Set proper file permissions
echo "📁 Setting file permissions..."
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 600 .env

echo "✅ Deployment completed successfully!"
echo "🌐 Your site should now be available at: https://aidev.kodeblooded.com.ng"
echo ""
echo "📋 Next steps:"
echo "   1. Update .env with your database credentials"
echo "   2. Update .env with your Google OAuth credentials:"
echo "      GOOGLE_REDIRECT_URI=https://aidev.kodeblooded.com.ng/auth/google/callback"
echo "   3. Test the site functionality"
echo "   4. Set up your domain to point to the public folder"