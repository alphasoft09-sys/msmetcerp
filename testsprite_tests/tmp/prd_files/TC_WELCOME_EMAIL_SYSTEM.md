# TC Welcome Email System

## Overview
The AAMSME system now includes a comprehensive welcome email system that automatically sends detailed onboarding emails to new Training Centers (TCs) when they are added by the Assessment Agency.

## How It Works

### 1. Email Trigger
**File:** `app/Http/Controllers/TcManagementController.php`

When the Assessment Agency creates a new TC Admin through the TC Management interface, the system automatically sends a welcome email to the new TC Admin's email address.

```php
// Send welcome email to the new TC Admin
try {
    Mail::to($tcAdmin->email)->send(new TcWelcomeEmail($tcAdmin, $user, $password));
    \Log::info('Welcome email sent to new TC Admin', [
        'tc_admin_email' => $tcAdmin->email,
        'tc_code' => $tcAdmin->from_tc,
        'tc_name' => $tcAdmin->tc_name
    ]);
} catch (\Exception $e) {
    \Log::error('Failed to send welcome email to TC Admin: ' . $e->getMessage());
}
```

### 2. Email Mailable Class
**File:** `app/Mail/TcWelcomeEmail.php`

A Laravel Mailable class that handles the email structure and data passing.

```php
class TcWelcomeEmail extends Mailable
{
    public $tcAdmin;
    public $assessmentAgencyUser;
    public $password;
    public $loginUrl;

    public function __construct(User $tcAdmin, User $assessmentAgencyUser, $password)
    {
        $this->tcAdmin = $tcAdmin;
        $this->assessmentAgencyUser = $assessmentAgencyUser;
        $this->password = $password;
        $this->loginUrl = route('login');
    }
}
```

### 3. Email Template
**File:** `resources/views/emails/tc-welcome.blade.php`

A comprehensive, professional HTML email template that includes:
- Welcome message and account details
- Login credentials
- Role explanations and permissions
- Step-by-step setup guide
- Workflow explanations
- Contact information

## Email Content Structure

### 1. Header Section
- **Welcome Message:** Personalized greeting with TC Admin name
- **System Introduction:** AAMSME (Assessment Agency MSME Management System)
- **Account Details:** TC Name, TC Code, and creator information

### 2. Login Credentials Section
- **Email Address:** The TC Admin's email
- **Password:** Temporary password (generated randomly)
- **Login URL:** Direct link to the login page
- **Security Notice:** Important reminder to change password

### 3. Role Explanation Section
**TC Admin Responsibilities:**
- Manage training center profile and settings
- Create and manage user accounts for the TC
- Add and manage training centers/centres
- Oversee exam schedule creation and approval process
- Monitor student registrations and progress
- Generate reports and analytics
- Manage qualifications and modules

### 4. User Management Guide
**Account Types That Can Be Created:**

#### TC Head (Role 2)
- **Limit:** One per training center
- **Responsibilities:**
  - Oversees exam schedule approvals
  - Manages faculty and exam cell accounts
  - Final approval authority for internal exams

#### Exam Cell (Role 3)
- **Limit:** Multiple accounts allowed
- **Responsibilities:**
  - Reviews exam schedules from faculty
  - Checks logistics and resources
  - Approves schedules for TC Head review

#### TC Faculty (Role 5)
- **Limit:** Multiple faculty accounts
- **Responsibilities:**
  - Creates exam schedules
  - Manages student attendance
  - Tracks student progress

### 5. Permission Matrix
A detailed table showing what each role can do:

| Feature | TC Admin | TC Head | Exam Cell | Faculty |
|---------|----------|---------|-----------|---------|
| Create User Accounts | ✓ Yes | ✓ Yes | ✗ No | ✗ No |
| Manage Centres | ✓ Yes | ✓ Yes | ✗ No | ✗ No |
| Create Exam Schedules | ✗ No | ✗ No | ✗ No | ✓ Yes |
| Approve Exam Schedules | ✓ Yes | ✓ Yes | ✓ Yes | ✗ No |
| Manage Students | ✓ Yes | ✓ Yes | ✓ Yes | ✓ Yes |
| View Reports | ✓ Yes | ✓ Yes | ✓ Yes | ✗ No |
| Manage Qualifications | ✓ Yes | ✓ Yes | ✗ No | ✗ No |

### 6. Getting Started Steps
**Step-by-step setup guide:**
1. Login to the system using provided credentials
2. Change password immediately for security
3. Create a TC Head account
4. Create Exam Cell accounts
5. Create Faculty accounts
6. Add Training Centres (if multiple locations)
7. Review and customize training center profile
8. Start creating exam schedules through faculty accounts

### 7. Exam Schedule Workflow
**Approval Flow:**
1. **Faculty** creates and submits exam schedule
2. **Exam Cell** reviews and approves (checks logistics)
3. **TC Head** reviews and approves
4. **Assessment Agency** approves (for Final/Special Final exams)
5. **File Number** is generated upon final approval

**Note:** Internal exams only require TC Head approval, while Final and Special Final exams require Assessment Agency approval.

### 8. Support & Contact
- Technical Support contact information
- Assessment Agency contact details
- Training center information

## Email Design Features

### 1. Professional Styling
- **Branded Header:** AAMSME gradient design
- **Responsive Layout:** Works on all devices
- **Clean Typography:** Easy to read and professional
- **Color Scheme:** Consistent with AAMSME branding

### 2. Visual Elements
- **Icons:** Emoji icons for visual appeal
- **Cards:** Organized information in card layouts
- **Tables:** Clear permission matrix
- **Buttons:** Call-to-action login button

### 3. Security Features
- **Password Display:** Clear temporary password
- **Security Warnings:** Important security notices
- **Login Link:** Direct access to system

## Implementation Details

### 1. Email Sending Process
```php
// In TcManagementController::store()
Mail::to($tcAdmin->email)->send(new TcWelcomeEmail($tcAdmin, $user, $password));
```

### 2. Error Handling
```php
try {
    Mail::to($tcAdmin->email)->send(new TcWelcomeEmail($tcAdmin, $user, $password));
    \Log::info('Welcome email sent successfully');
} catch (\Exception $e) {
    \Log::error('Failed to send welcome email: ' . $e->getMessage());
}
```

### 3. Logging
- **Success Logs:** Email sent successfully
- **Error Logs:** Failed email attempts
- **TC Creation Logs:** Complete TC creation process

## Benefits

### 1. User Onboarding
- **Immediate Welcome:** New TCs feel welcomed and supported
- **Clear Instructions:** Step-by-step setup guide
- **Role Understanding:** Clear explanation of responsibilities
- **System Familiarity:** Overview of features and workflows

### 2. Administrative Efficiency
- **Automated Process:** No manual email sending required
- **Consistent Information:** Standardized welcome message
- **Complete Documentation:** All necessary information included
- **Professional Appearance:** Branded, professional emails

### 3. System Adoption
- **Quick Setup:** Users can start using the system immediately
- **Reduced Support:** Comprehensive information reduces support requests
- **User Confidence:** Clear guidance builds user confidence
- **Proper Workflow:** Understanding of approval processes

## Testing Scenarios

### Scenario 1: New TC Creation
1. Assessment Agency creates a new TC Admin
2. Welcome email is automatically sent
3. TC Admin receives comprehensive onboarding information
4. TC Admin can immediately start using the system

### Scenario 2: Email Delivery Failure
1. Assessment Agency creates a new TC Admin
2. Email sending fails (network issues, invalid email, etc.)
3. Error is logged but TC creation continues
4. TC Admin can still access the system with provided credentials

### Scenario 3: Multiple TC Creation
1. Assessment Agency creates multiple TCs
2. Each TC receives personalized welcome email
3. Each email contains specific TC information
4. All emails are logged for tracking

## Customization Options

### 1. Email Content
- **Welcome Message:** Customize greeting and tone
- **Instructions:** Modify setup steps
- **Contact Information:** Update support details
- **Branding:** Adjust colors and styling

### 2. Email Timing
- **Immediate Sending:** Currently sends immediately after creation
- **Delayed Sending:** Could be queued for later delivery
- **Reminder Emails:** Could send follow-up emails

### 3. Email Recipients
- **Primary Recipient:** TC Admin (current)
- **CC Recipients:** Assessment Agency, other stakeholders
- **BCC Recipients:** System administrators

## Future Enhancements

### 1. Email Templates
- **Multiple Languages:** Support for different languages
- **Custom Templates:** Different templates for different TC types
- **Dynamic Content:** Personalized content based on TC profile

### 2. Email Tracking
- **Delivery Status:** Track email delivery and open rates
- **Click Tracking:** Monitor login button clicks
- **Response Tracking:** Track user actions after email receipt

### 3. Follow-up Emails
- **Setup Reminders:** Remind users to complete setup
- **Usage Encouragement:** Encourage system usage
- **Support Offers:** Offer additional support and training

### 4. Integration
- **SMS Notifications:** Send SMS with login credentials
- **WhatsApp Integration:** Send welcome message via WhatsApp
- **Push Notifications:** In-app notifications for new users

## Troubleshooting

### Common Issues
1. **Email Not Received:** Check spam folder, verify email address
2. **Email Format Issues:** Test email rendering in different clients
3. **Delivery Failures:** Check mail server configuration
4. **Template Errors:** Verify Blade template syntax

### Debugging Steps
1. Check Laravel logs for email errors
2. Verify mail configuration in `.env`
3. Test email sending manually
4. Check email template syntax
5. Verify recipient email address

## Configuration

### Environment Variables
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@aamsme.com
MAIL_FROM_NAME="AAMSME System"
```

### Mail Configuration
- **SMTP Settings:** Configure for reliable email delivery
- **From Address:** Professional sender address
- **Reply-To:** Support email address
- **Bounce Handling:** Configure for undelivered emails

The TC Welcome Email System provides a comprehensive, professional onboarding experience for new Training Centers, ensuring they have all the information needed to successfully use the AAMSME system. 