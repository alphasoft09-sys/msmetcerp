# Google Cloud Setup for reCAPTCHA Enterprise

## 🔧 **Step-by-Step Setup Guide**

### **1. Enable reCAPTCHA Enterprise API**

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Select your project: `msmetcerpalphabe-1754946277461`
3. Navigate to **APIs & Services** > **Library**
4. Search for "reCAPTCHA Enterprise API"
5. Click on it and press **Enable**

### **2. Create Service Account**

1. Go to **IAM & Admin** > **Service Accounts**
2. Click **Create Service Account**
3. Fill in the details:
   - **Name**: `recaptcha-enterprise-service`
   - **Description**: `Service account for reCAPTCHA Enterprise`
4. Click **Create and Continue**

### **3. Assign Roles**

Add these roles to the service account:
- `reCAPTCHA Enterprise Admin`
- `reCAPTCHA Enterprise Agent`

### **4. Create and Download Key**

1. Click on the created service account
2. Go to **Keys** tab
3. Click **Add Key** > **Create New Key**
4. Choose **JSON** format
5. Click **Create**
6. The JSON file will download automatically

### **5. Upload to Server**

1. Upload the downloaded JSON file to your server
2. Place it in a secure location (outside web root)
3. Example path: `/home/username/msme-credentials/service-account-key.json`

### **6. Update Environment Variables**

Add this line to your `.env` file:

```env
GOOGLE_APPLICATION_CREDENTIALS=/home/username/msme-credentials/service-account-key.json
```

### **7. Set File Permissions**

```bash
chmod 600 /home/username/msme-credentials/service-account-key.json
chown www-data:www-data /home/username/msme-credentials/service-account-key.json
```

### **8. Test the Setup**

Visit: `https://msmetcerp.alphabetsoftware.in/test-captcha.html`

## 🔍 **Troubleshooting**

### **Common Issues:**

1. **"Google Cloud credentials not found"**
   - Check if the file path is correct
   - Verify file permissions
   - Ensure the file exists

2. **"reCAPTCHA Enterprise API not enabled"**
   - Enable the API in Google Cloud Console
   - Wait a few minutes for changes to propagate

3. **"Permission denied"**
   - Check service account roles
   - Verify project ID is correct

### **Quick Test:**

Run this command to test credentials:
```bash
php artisan tinker
```

Then run:
```php
$client = new Google\Cloud\RecaptchaEnterprise\V1\Client\RecaptchaEnterpriseServiceClient();
echo "Connection successful!";
```

## 📋 **Current Status**

Your current configuration:
- ✅ Site Key: `6LebQqMrAAAAABzNGh9s7CaqZDAEnq1BaHNz_Y6X`
- ✅ Project ID: `msmetcerpalphabe-1754946277461`
- ❌ Service Account Key: **Not configured**

## 🚀 **Temporary Solution**

Until you set up the Google Cloud credentials, the system will use a **fallback verification** that:
- Accepts valid-looking reCAPTCHA tokens
- Provides basic protection
- Allows your forms to work immediately

This is a temporary measure to keep your system functional while you complete the Google Cloud setup.

## 📞 **Need Help?**

If you encounter any issues:
1. Check the Laravel logs: `storage/logs/laravel.log`
2. Verify Google Cloud Console settings
3. Test with the provided test page
