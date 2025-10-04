# Server Setup Guide for File Storage

## 1. Create Storage Link
Run this command on your server:
```bash
php artisan storage:link
```

## 2. Set Proper Permissions
```bash
# Set permissions for storage directory
chmod -R 755 storage/
chmod -R 755 public/storage/

# Set ownership (replace www-data with your web server user)
chown -R www-data:www-data storage/
chown -R www-data:www-data public/storage/
```

## 3. Create Required Directories
```bash
# Create header_layouts directory if it doesn't exist
mkdir -p storage/app/public/header_layouts
chmod 755 storage/app/public/header_layouts
```

## 4. Test Storage Setup
Visit this URL to test your storage setup:
```
https://msmetcerp.alphabetsoftware.in/admin/tc-header-layouts/test/storage
```

## 5. Check File Permissions
Make sure your web server can:
- Read from `storage/app/public/`
- Write to `storage/app/public/header_layouts/`
- Access `public/storage/` (symbolic link)

## 6. Common Issues and Solutions

### Issue: 403 Forbidden
- Check file permissions
- Ensure symbolic link exists
- Verify web server user has access

### Issue: 404 Not Found
- Verify storage link is created
- Check if file actually exists in storage
- Ensure URL path is correct

### Issue: File Upload Works But Not Displaying
- Check storage link
- Verify file permissions
- Test direct file access

## 7. Test File Access
After setup, test if you can access a file directly:
```
https://msmetcerp.alphabetsoftware.in/storage/header_layouts/tc_t100_1753519955.png
```

## 8. Debug Information
The storage test endpoint will show you:
- Storage paths
- Directory existence
- File permissions
- URL configuration
- Write permissions 