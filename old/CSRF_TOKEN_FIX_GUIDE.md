# CSRF Token Mismatch Fix Guide

## Problem Description
You're experiencing "CSRF token mismatch" errors when trying to login. This is a common Laravel security feature that prevents cross-site request forgery attacks.

## Root Causes & Solutions

### 1. **Missing CSRF Token in AJAX Headers** ✅ FIXED
**Problem**: AJAX requests were missing the `X-CSRF-TOKEN` header.

**Solution**: Added CSRF token to all AJAX request headers:
```javascript
headers: {
    'X-Requested-With': 'XMLHttpRequest',
    'Accept': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
}
```

### 2. **Session Configuration Issues** ✅ FIXED
**Problem**: Sessions might not be properly configured or working.

**Solution**: 
- Verified sessions table exists and is migrated
- Added session testing endpoints
- Added CSRF token refresh functionality

### 3. **Token Expiration** ✅ FIXED
**Problem**: CSRF tokens expire after a certain time.

**Solution**: 
- Added automatic CSRF token refresh every 30 minutes
- Added graceful handling of expired tokens
- Added page refresh on token expiration

### 4. **Exception Handling** ✅ FIXED
**Problem**: CSRF exceptions weren't being handled gracefully.

**Solution**: 
- Added custom exception handler for TokenMismatchException
- Returns proper JSON responses for AJAX requests
- Shows user-friendly error messages

## Testing Your Fix

### Step 1: Use the Debug Tool
1. Open: `http://your-domain/debug-login.html`
2. Test each component:
   - **Test Session**: Verifies session functionality
   - **Test CSRF Token**: Validates CSRF token works
   - **Test Login Endpoints**: Checks if login routes are accessible

### Step 2: Check Browser Console
1. Open Developer Tools (F12)
2. Go to Console tab
3. Try logging in and watch for error messages
4. Look for "CSRF token refreshed" messages

### Step 3: Test Login Flow
1. Try logging in with valid credentials
2. If you get a CSRF error, the page should automatically refresh after 3 seconds
3. Check if the error message is now more specific

## Additional Debugging Steps

### If Still Having Issues:

1. **Check Session Storage**:
   ```bash
   php artisan tinker
   >>> Session::getId()
   >>> csrf_token()
   ```

2. **Verify Database Sessions**:
   ```sql
   SELECT * FROM sessions ORDER BY last_activity DESC LIMIT 5;
   ```

3. **Check Environment Variables**:
   ```bash
   php artisan config:show session
   ```

4. **Clear All Caches**:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

## Prevention Measures

### 1. **Automatic Token Refresh**
- CSRF tokens are automatically refreshed every 30 minutes
- Prevents token expiration during long sessions

### 2. **Graceful Error Handling**
- Specific error messages for different types of failures
- Automatic page refresh on token expiration
- User-friendly error messages

### 3. **Session Management**
- Proper session configuration
- Database-based session storage
- Session lifetime management

## Common Scenarios & Solutions

### Scenario 1: "CSRF token mismatch" on first login attempt
**Cause**: Session not properly initialized
**Solution**: Refresh the page and try again

### Scenario 2: "CSRF token mismatch" after being idle
**Cause**: Token expired due to inactivity
**Solution**: Page will automatically refresh after 3 seconds

### Scenario 3: "CSRF token mismatch" in multiple tabs
**Cause**: Different sessions in different tabs
**Solution**: Use only one tab for login, or refresh all tabs

### Scenario 4: "CSRF token mismatch" after server restart
**Cause**: Sessions cleared on server restart
**Solution**: Refresh the page to get a new session

## Files Modified

1. **Login Forms**:
   - `resources/views/admin/login.blade.php`
   - `resources/views/student/login.blade.php`
   - `resources/views/admin/verify-otp.blade.php`

2. **Backend**:
   - `bootstrap/app.php` (exception handling)
   - `routes/web.php` (new endpoints)
   - `app/Http/Controllers/SessionTestController.php` (new)

3. **Debug Tools**:
   - `public/debug-login.html` (enhanced)
   - `CSRF_TOKEN_FIX_GUIDE.md` (this file)

## Testing Checklist

- [ ] Session test passes
- [ ] CSRF token test passes
- [ ] Admin login works
- [ ] Student login works
- [ ] OTP verification works
- [ ] Error messages are specific
- [ ] Page refreshes on token expiration
- [ ] No more "Network error" messages

## If Problems Persist

1. Check the Laravel logs: `storage/logs/laravel.log`
2. Use the debug tool to identify specific issues
3. Verify your web server configuration
4. Check if your database is accessible
5. Ensure all migrations are run: `php artisan migrate:status`

## Support

If you continue to experience issues:
1. Run the debug tool and share the results
2. Check the browser console for error messages
3. Review the Laravel logs for server-side errors
4. Verify your environment configuration
