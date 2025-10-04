# Authentication Redirect Fix

## Issue Identified
Users were getting **403 Unauthorized access** errors when trying to access exam schedule pages like:
- `http://127.0.0.1:8000/admin/aa/exam-schedules/25`

Instead of showing a 403 error page, users should be redirected to the login page with appropriate error messages.

## Root Cause
The `ExamScheduleController` methods (`show` and `fullview`) were using `abort(403, 'Unauthorized access')` which displays a 403 error page instead of redirecting users to the login page when:

1. **User is not authenticated** (session expired or not logged in)
2. **User doesn't have the correct role** for the requested resource
3. **User doesn't have permission** to access the specific exam schedule

## Solution Implemented

### 1. Updated ExamScheduleController Methods
**Files:** `app/Http/Controllers/ExamScheduleController.php`

#### **Updated Methods:**
- ✅ `show($id)` - Exam schedule details page
- ✅ `fullview($id)` - Exam schedule full view page

#### **Changes Made:**

##### **Before:**
```php
public function show($id)
{
    $user = Auth::user();
    
    // Check access based on role
    $examSchedule = ExamSchedule::with(['students', 'modules', 'faculty', 'qualification', 'centre'])->findOrFail($id);
    
    // Check access permissions based on user role and status
    switch ($user->user_role) {
        case 4: // Assessment Agency can see TC approved schedules only
            if (!in_array($examSchedule->status, ['tc_admin_approved', 'received', 'rejected'])) {
                abort(403, 'This exam schedule is not yet approved by TC Admin/Head');
            }
            break;
        default:
            abort(403, 'Unauthorized access');
    }
}
```

##### **After:**
```php
public function show($id)
{
    $user = Auth::user();
    
    // If user is not authenticated, redirect to login
    if (!$user) {
        return redirect()->route('admin.login')->with('error', 'Please login to access this page.');
    }
    
    // Check access based on role
    $examSchedule = ExamSchedule::with(['students', 'modules', 'faculty', 'qualification', 'centre'])->findOrFail($id);
    
    // Check access permissions based on user role and status
    switch ($user->user_role) {
        case 4: // Assessment Agency can see TC approved schedules only
            if (!in_array($examSchedule->status, ['tc_admin_approved', 'received', 'rejected'])) {
                return redirect()->route('admin.login')->with('error', 'This exam schedule is not yet approved by TC Admin/Head.');
            }
            break;
        default:
            return redirect()->route('admin.login')->with('error', 'You do not have permission to access this page.');
    }
}
```

### 2. Error Message Display
**File:** `resources/views/admin/login.blade.php`

The login page already had error message handling:

```php
<!-- Error messages from redirects -->
@if(session('error'))
<div class="alert alert-warning fade-in-up" role="alert" id="errorMessage">
    <i class="bi bi-exclamation-triangle me-2"></i>
    {{ session('error') }}
</div>
@endif
```

### 3. Test Route Added
**File:** `routes/web.php`

Added a test route to verify authentication and authorization:

```php
// Test route for authentication and authorization (remove in production)
Route::get('/test-auth-exam-schedule/{id}', function ($id) {
    try {
        $user = auth()->user();
        $examSchedule = \App\Models\ExamSchedule::find($id);
        
        return response()->json([
            'success' => true,
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_role' => $user->user_role,
                'from_tc' => $user->from_tc,
                'authenticated' => true
            ] : null,
            'exam_schedule' => [
                'id' => $examSchedule->id,
                'status' => $examSchedule->status,
                'tc_code' => $examSchedule->tc_code,
                'created_by' => $examSchedule->created_by,
                'course_name' => $examSchedule->course_name
            ],
            'access_check' => [
                'user_authenticated' => $user ? true : false,
                'user_has_role_4' => $user && $user->user_role == 4,
                'status_allowed_for_aa' => in_array($examSchedule->status, ['tc_admin_approved', 'received', 'rejected']),
                'should_have_access' => $user && $user->user_role == 4 && in_array($examSchedule->status, ['tc_admin_approved', 'received', 'rejected'])
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});
```

## User Role and Permission Matrix

### **Assessment Agency (Role 4) Access:**
- ✅ **Can access** exam schedules with status: `tc_admin_approved`, `received`, `rejected`
- ❌ **Cannot access** exam schedules with status: `draft`, `submitted`, `exam_cell_approved`, `hold`

### **Other Roles Access:**
- **Faculty (Role 5):** Can only access their own exam schedules (all statuses)
- **Exam Cell (Role 3):** Can access submitted and approved schedules from their TC
- **TC Admin (Role 1):** Can access exam cell approved schedules from their TC
- **TC Head (Role 2):** Can access exam cell approved schedules from their TC

## Error Messages

### **Authentication Errors:**
- `"Please login to access this page."` - When user is not authenticated
- `"You do not have permission to access this page."` - When user has wrong role

### **Authorization Errors:**
- `"You do not have permission to access this exam schedule."` - When user doesn't have access to specific schedule
- `"This exam schedule is not yet approved by TC Admin/Head."` - When AA tries to access unapproved schedule
- `"This exam schedule is not yet approved by Exam Cell."` - When TC Admin/Head tries to access unapproved schedule
- `"This exam schedule is not yet submitted for review."` - When Exam Cell tries to access unsubmitted schedule

## Testing

### 1. Test Authentication and Authorization
Visit: `http://your-domain.com/test-auth-exam-schedule/25`

**Expected Output:**
```json
{
    "success": true,
    "user": {
        "id": 1,
        "name": "Assessment Agency User",
        "email": "aa@example.com",
        "user_role": 4,
        "from_tc": null,
        "authenticated": true
    },
    "exam_schedule": {
        "id": 25,
        "status": "tc_admin_approved",
        "tc_code": "T100",
        "created_by": 5,
        "course_name": "P102023--SAP Business ONE"
    },
    "access_check": {
        "user_authenticated": true,
        "user_has_role_4": true,
        "status_allowed_for_aa": true,
        "should_have_access": true
    }
}
```

### 2. Test Unauthenticated Access
1. Logout from the system
2. Try to access: `http://your-domain.com/admin/aa/exam-schedules/25`
3. Should redirect to login page with message: `"Please login to access this page."`

### 3. Test Wrong Role Access
1. Login as a different role (e.g., Faculty)
2. Try to access: `http://your-domain.com/admin/aa/exam-schedules/25`
3. Should redirect to login page with message: `"You do not have permission to access this page."`

### 4. Test Unapproved Schedule Access
1. Login as Assessment Agency
2. Try to access an exam schedule with status `submitted`
3. Should redirect to login page with message: `"This exam schedule is not yet approved by TC Admin/Head."`

## Implementation Details

### **Authentication Flow:**
1. **User requests** exam schedule page
2. **Controller checks** if user is authenticated
3. **If not authenticated** → Redirect to login with error message
4. **If authenticated** → Check user role and permissions
5. **If unauthorized** → Redirect to login with specific error message
6. **If authorized** → Display the requested page

### **Error Handling Flow:**
1. **Controller detects** unauthorized access
2. **Redirects to login** with `session('error')` message
3. **Login page displays** error message to user
4. **User can** login again or contact administrator

## Benefits

### **User Experience:**
- ✅ **Clear error messages** instead of generic 403 errors
- ✅ **Automatic redirect** to login page
- ✅ **Session-based messages** that persist across redirects
- ✅ **User-friendly interface** with proper styling

### **Security:**
- ✅ **Proper authentication checks** before authorization
- ✅ **Role-based access control** maintained
- ✅ **No sensitive information** exposed in error messages
- ✅ **Consistent error handling** across all methods

### **Maintainability:**
- ✅ **Centralized error handling** in controller methods
- ✅ **Reusable redirect logic** for all authorization failures
- ✅ **Clear error messages** for debugging
- ✅ **Test routes** for verification

## Future Enhancements

### **Potential Improvements:**
1. **Custom error pages** for different error types
2. **Logging** of unauthorized access attempts
3. **Rate limiting** for failed access attempts
4. **Email notifications** for suspicious access patterns
5. **Audit trail** for all access attempts

### **Additional Error Types:**
```php
// Example for different error scenarios
if ($examSchedule->is_deleted) {
    return redirect()->route('admin.login')->with('error', 'This exam schedule has been deleted.');
}

if ($examSchedule->is_expired) {
    return redirect()->route('admin.login')->with('error', 'This exam schedule has expired.');
}
```

## Conclusion

The authentication redirect fix resolves the 403 error issues by:

- ✅ **Replacing `abort(403)`** with proper redirects to login page
- ✅ **Adding authentication checks** before authorization
- ✅ **Providing clear error messages** for different scenarios
- ✅ **Maintaining security** while improving user experience
- ✅ **Adding test functionality** for verification

Users will now be properly redirected to the login page with helpful error messages instead of seeing generic 403 errors! 🎉 