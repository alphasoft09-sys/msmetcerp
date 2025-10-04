#!/bin/bash

# Production Cleanup Script
# This script removes debug code and sets proper permissions for production

echo "🔧 Starting production cleanup..."

# Remove console.log statements
echo "📝 Removing console.log statements..."
find resources/views -name "*.blade.php" -exec sed -i 's/console\.log([^;]*);/\/\/ console.log removed for production/g' {} \;

# Remove alert statements
echo "📝 Removing alert statements..."
find resources/views -name "*.blade.php" -exec sed -i 's/alert([^;]*);/\/\/ alert removed for production/g' {} \;

# Set proper file permissions
echo "🔐 Setting file permissions..."
chmod 755 storage/
chmod 755 bootstrap/cache/
chmod 644 .env
chmod 644 config/*.php

# Clear caches
echo "🧹 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Production cleanup completed!"
echo "⚠️  Remember to:"
echo "   - Update .env with production values"
echo "   - Set APP_ENV=production"
echo "   - Set APP_DEBUG=false"
echo "   - Configure SSL certificate"
echo "   - Set up database backups" 