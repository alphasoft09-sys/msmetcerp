#!/bin/bash

# Production Deployment Script
# Run this script to deploy your Laravel application to production

set -e  # Exit on any error

echo "🚀 Starting production deployment..."

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    echo "❌ Please don't run this script as root"
    exit 1
fi

# Check if .env exists
if [ ! -f .env ]; then
    echo "❌ .env file not found. Please create it from .env.production"
    exit 1
fi

# Check if APP_ENV is set to production
if ! grep -q "APP_ENV=production" .env; then
    echo "❌ APP_ENV must be set to 'production' in .env"
    exit 1
fi

# Check if APP_DEBUG is set to false
if ! grep -q "APP_DEBUG=false" .env; then
    echo "❌ APP_DEBUG must be set to 'false' in .env"
    exit 1
fi

echo "✅ Environment configuration verified"

# Install/update dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader

# Clear all caches
echo "🧹 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper file permissions
echo "🔐 Setting file permissions..."
chmod 755 storage/
chmod 755 bootstrap/cache/
chmod 644 .env
chmod 644 config/*.php

# Create storage symlink if it doesn't exist
if [ ! -L public/storage ]; then
    echo "🔗 Creating storage symlink..."
    php artisan storage:link
fi

# Check for any remaining debug code
echo "🔍 Checking for debug code..."
if grep -r "console.log" resources/views/; then
    echo "⚠️  Warning: console.log statements found in views"
fi

if grep -r "alert(" resources/views/; then
    echo "⚠️  Warning: alert statements found in views"
fi

# Security checks
echo "🔒 Running security checks..."

# Check if APP_KEY is set
if ! grep -q "APP_KEY=base64:" .env; then
    echo "❌ APP_KEY not properly set"
    exit 1
fi

# Check if database connection works
echo "🗄️ Testing database connection..."
php artisan tinker --execute="echo 'Database connection: '; try { DB::connection()->getPdo(); echo 'OK'; } catch(Exception \$e) { echo 'FAILED'; exit(1); }"

echo "✅ Production deployment completed successfully!"
echo ""
echo "📋 Post-deployment checklist:"
echo "   - [ ] Test all authentication flows"
echo "   - [ ] Verify email functionality"
echo "   - [ ] Check file upload security"
echo "   - [ ] Test role-based access control"
echo "   - [ ] Monitor error logs"
echo "   - [ ] Set up monitoring and alerting"
echo "   - [ ] Configure automated backups"
echo ""
echo "🔒 Security reminders:"
echo "   - [ ] SSL certificate is configured"
echo "   - [ ] Database backups are set up"
echo "   - [ ] Rate limiting is configured"
echo "   - [ ] Error monitoring is active" 