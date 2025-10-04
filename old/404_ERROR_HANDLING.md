# 404 Error Handling System

## Overview
The AAMSME system now includes a comprehensive 404 error handling system that automatically redirects users to appropriate pages based on their authentication status and role.

## How It Works

### 1. Exception Handler (Laravel 11)
**File:** `bootstrap/app.php`

The system uses Laravel 11's new exception handling structure to catch `NotFoundHttpException` errors and handle them appropriately.

```php
->withExceptions(function (Exceptions $exceptions): void {
    // Handle 404 errors by redirecting to login page
    $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) {
        // Logic for handling 404 errors
    });
})
```

### 2. Custom Exception Handler (Backup)
**File:** `app/Exceptions/Handler.php`

A traditional Laravel exception handler that provides the same functionality as a backup.

### 3. Custom 404 Error Page
**File:** `resources/views/errors/404.blade.php`

A beautiful, professional 404 error page that:
- Shows a user-friendly error message
- Auto-redirects to login page after 5 seconds
- Allows immediate redirect on click
- Matches the AAMSME design system

## Error Handling Logic

### For API Requests
If the request expects JSON (API calls):
```json
{
    "error": "Page not found",
    "message": "The requested resource was not found"
}
```

### For Authenticated Users
If the user is already logged in, redirect to their appropriate dashboard:

| User Role | Route | Description |
|-----------|-------|-------------|
| 1 (TC Admin) | `admin.tc-admin.dashboard` | TC Admin Dashboard |
| 2 (TC Head) | `admin.tc-head.dashboard` | TC Head Dashboard |
| 3 (Exam Cell) | `admin.exam-cell.dashboard` | Exam Cell Dashboard |
| 4 (Assessment Agency) | `admin.aa.dashboard` | Assessment Agency Dashboard |
| 5 (Faculty) | `admin.tc-faculty.dashboard` | Faculty Dashboard |

### For Unauthenticated Users
If the user is not logged in:
- Redirect to login page (`route('login')`)
- Show error message: "Page not found. Please login to continue."

## User Experience Features

### 1. Custom 404 Page
- **Professional Design:** Matches AAMSME branding
- **Auto-Redirect:** Countdown timer (5 seconds)
- **Click to Redirect:** Any click redirects immediately
- **Responsive:** Works on all devices

### 2. Login Page Integration
- **Error Message Display:** Shows 404 error message on login page
- **Session Flash:** Error message persists through redirect
- **User-Friendly:** Clear explanation of what happened

### 3. Role-Based Redirects
- **Smart Routing:** Users go to their appropriate dashboard
- **No Confusion:** Authenticated users don't see login page unnecessarily
- **Seamless Experience:** Maintains user context

## Implementation Details

### Exception Handling Priority
1. **API Requests:** Return JSON error response
2. **Authenticated Users:** Redirect to role-specific dashboard
3. **Unauthenticated Users:** Redirect to login with error message

### Error Message Flow
```
404 Error → Exception Handler → Check Auth Status → Redirect with Message
```

### Session Flash Messages
- **Key:** `session('error')`
- **Message:** "Page not found. Please login to continue."
- **Display:** Warning alert on login page

## Testing Scenarios

### Scenario 1: Invalid URL (Unauthenticated)
1. Visit a non-existent URL while logged out
2. Should redirect to login page
3. Should show error message: "Page not found. Please login to continue."

### Scenario 2: Invalid URL (Authenticated - TC Admin)
1. Visit a non-existent URL while logged in as TC Admin
2. Should redirect to TC Admin dashboard
3. No error message (user is already authenticated)

### Scenario 3: Invalid URL (Authenticated - Faculty)
1. Visit a non-existent URL while logged in as Faculty
2. Should redirect to Faculty dashboard
3. No error message (user is already authenticated)

### Scenario 4: API Request to Invalid Endpoint
1. Make API request to non-existent endpoint
2. Should return JSON error response
3. Should not redirect

## Benefits

### 1. User Experience
- **No Dead Ends:** Users always have a way forward
- **Context Preservation:** Authenticated users stay in their workflow
- **Clear Communication:** Users understand what happened

### 2. Security
- **No Information Leakage:** 404 errors don't expose system details
- **Proper Authentication:** Unauthenticated users are directed to login
- **Role-Based Access:** Users go to appropriate areas

### 3. Professional Appearance
- **Branded Error Pages:** Consistent with AAMSME design
- **Professional Messaging:** Clear, helpful error messages
- **Smooth Transitions:** Seamless redirects without jarring experiences

### 4. Maintenance
- **Centralized Logic:** All 404 handling in one place
- **Easy Updates:** Simple to modify behavior
- **Comprehensive Logging:** All errors are logged for debugging

## Configuration

### Environment Variables
No special configuration required. The system works with standard Laravel setup.

### Customization Options
1. **Redirect Timing:** Modify countdown timer in 404 page
2. **Error Messages:** Update messages in exception handler
3. **Dashboard Routes:** Modify route names in exception handler
4. **Styling:** Update CSS in 404 error page

## Troubleshooting

### Common Issues
1. **Infinite Redirect Loop:** Check route names in exception handler
2. **Missing Error Messages:** Verify session flash messages
3. **API Errors:** Ensure JSON responses for API requests

### Debugging
- Check Laravel logs for exception details
- Verify route names exist in `routes/web.php`
- Test with different user roles and authentication states

## Future Enhancements

1. **Analytics:** Track 404 errors for system improvement
2. **Custom Messages:** Different messages for different error types
3. **Email Notifications:** Alert administrators of frequent 404 errors
4. **Search Suggestions:** Suggest similar pages when 404 occurs
5. **Breadcrumb Recovery:** Help users navigate back to valid pages 