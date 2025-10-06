#!/bin/bash

# Script to fix storage link issues
echo "Checking storage link..."

# Check if storage directory exists in public folder
if [ -L "public/storage" ]; then
  echo "Storage symbolic link exists, removing it to recreate..."
  rm -f public/storage
fi

# Create storage link
echo "Creating storage link..."
php artisan storage:link

# Set permissions
echo "Setting permissions on storage directory..."
chmod -R 775 storage
chmod -R 775 public/storage

echo "Setting ownership (if running as root)..."
# Uncomment and adjust the following line if you know the web server user
# chown -R www-data:www-data storage public/storage

echo "Done!"
