#!/bin/bash

# Script to fix image permissions
echo "Fixing image permissions..."

# Set paths
STORAGE_PATH="storage/app/public"
PUBLIC_STORAGE_PATH="public/storage"

# Check if storage directory exists
if [ -d "$STORAGE_PATH" ]; then
  echo "Setting permissions on $STORAGE_PATH..."
  find "$STORAGE_PATH" -type d -exec chmod 755 {} \;
  find "$STORAGE_PATH" -type f -exec chmod 644 {} \;
  echo "Done setting permissions on $STORAGE_PATH"
else
  echo "Warning: $STORAGE_PATH directory not found!"
fi

# Check if public/storage directory exists
if [ -d "$PUBLIC_STORAGE_PATH" ]; then
  echo "Setting permissions on $PUBLIC_STORAGE_PATH..."
  find "$PUBLIC_STORAGE_PATH" -type d -exec chmod 755 {} \;
  find "$PUBLIC_STORAGE_PATH" -type f -exec chmod 644 {} \;
  echo "Done setting permissions on $PUBLIC_STORAGE_PATH"
else
  echo "Warning: $PUBLIC_STORAGE_PATH directory not found!"
fi

# Check if storage link exists
if [ ! -L "$PUBLIC_STORAGE_PATH" ]; then
  echo "Storage link does not exist. Creating..."
  php artisan storage:link
else
  echo "Storage link exists."
fi

# Set ownership if running as root (uncomment and modify as needed)
# echo "Setting ownership..."
# chown -R www-data:www-data "$STORAGE_PATH"
# chown -R www-data:www-data "$PUBLIC_STORAGE_PATH"

echo "All done!"
