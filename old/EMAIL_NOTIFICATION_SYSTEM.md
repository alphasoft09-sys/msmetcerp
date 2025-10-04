# Email Notification System for Exam Schedule Approval Workflow

## Overview
The AAMSME system now includes a comprehensive email notification system that automatically sends emails to relevant stakeholders at each stage of the exam schedule approval process.

## Email Flow

### 1. Faculty Submits Exam Schedule → Exam Cell
**Trigger:** When faculty submits a draft exam schedule
**Email Class:** `ExamScheduleSubmitted`
**Recipient:** Exam Cell user for the respective TC
**Subject:** "New Exam Schedule Submitted for Approval - [Course Name]"

**Email Content:**
- Exam schedule details (course, batch, dates, etc.)
- Faculty information
- Direct link to review the schedule
- Next steps for Exam Cell

### 2. Exam Cell Approves → TC Head
**Trigger:** When Exam Cell approves the submitted schedule
**Email Class:** `ExamScheduleApprovedByExamCell`
**Recipient:** TC Head user for the respective TC
**Subject:** "Exam Schedule Approved by Exam Cell - [Course Name]"

**Email Content:**
- Exam schedule details
- Exam Cell approval information
- Direct link to review the schedule
- Next steps for TC Head

### 3. TC Head Approves → Assessment Agency (Final/Special Final) OR Final Approval (Internal)
**Trigger:** When TC Head approves the schedule
**Email Class:** `ExamScheduleApprovedByTCHead` (for Final/Special Final) OR `ExamScheduleFinalApproved` (for Internal)
**Recipient:** Assessment Agency (Final/Special Final) OR TC Head with CC (Internal)
**Subject:** "Exam Schedule Approved by TC Head - [Course Name]" OR "Exam Schedule Finally Approved - [Course Name] (File No: [File Number])"

**Email Content:**
- Exam schedule details
- TC Head approval information
- Direct link to review the schedule
- Next steps for Assessment Agency (Final/Special Final)
- Final approval notification with file number (Internal)

### 4. Assessment Agency Approves → Final Notification
**Trigger:** When Assessment Agency gives final approval
**Email Class:** `ExamScheduleFinalApproved`
**Recipient:** TC Head (primary), CC to Exam Cell and Faculty
**Subject:** "Exam Schedule Finally Approved - [Course Name] (File No: [File Number])"

**Email Content:**
- Exam schedule details
- File number assignment
- Complete approval chain information
- Direct link to view final schedule
- Final approval notification

## Email Templates

### Template Features:
- **Professional Design:** Clean, government-portal appropriate styling
- **Responsive Layout:** Works on desktop and mobile devices
- **Color-Coded Headers:** Different colors for different approval stages
- **Detailed Information:** Complete exam schedule details
- **Action Buttons:** Direct links to review schedules
- **System Information:** AAMSME branding and technical details

### Template Files:
1. `resources/views/emails/exam-schedule-submitted.blade.php`
2. `resources/views/emails/exam-schedule-approved-by-exam-cell.blade.php`
3. `resources/views/emails/exam-schedule-approved-by-tc-head.blade.php`
4. `resources/views/emails/exam-schedule-final-approved.blade.php`

## Email Classes

### 1. ExamScheduleSubmitted
- **Purpose:** Notify Exam Cell of new submission
- **Data:** Exam schedule, faculty, exam cell user
- **Styling:** Blue header (#007bff)

### 2. ExamScheduleApprovedByExamCell
- **Purpose:** Notify TC Head of Exam Cell approval
- **Data:** Exam schedule, exam cell user, TC head user
- **Styling:** Green header (#28a745)

### 3. ExamScheduleApprovedByTCHead
- **Purpose:** Notify Assessment Agency of TC Head approval
- **Data:** Exam schedule, TC head user, assessment agency user
- **Styling:** Yellow header (#ffc107)

### 4. ExamScheduleFinalApproved
- **Purpose:** Final approval notification with file number
- **Data:** Exam schedule, assessment agency user, faculty, exam cell user, TC head user
- **Styling:** Red header (#dc3545) with file number highlight

## Implementation Details

### Controller Integration
The email system is integrated into the `ExamScheduleController`:

1. **Submit Method:** Sends email when faculty submits
2. **Approve Method:** Sends emails based on approval stage
3. **sendApprovalEmails Method:** Handles all approval email logic

### User Lookup Logic
```php
// Find users by role and TC code
$examCellUser = User::where('user_role', 3)->where('from_tc', $examSchedule->tc_code)->first();
$tcHeadUser = User::where('user_role', 2)->where('from_tc', $examSchedule->tc_code)->first();
$assessmentAgencyUser = User::where('user_role', 4)->first();
```

### Error Handling
- All email sending is wrapped in try-catch blocks
- Failed emails are logged but don't break the approval process
- Comprehensive logging for debugging

### Logging
The system logs all email activities:
```php
\Log::info('Email sent to Exam Cell for exam schedule submission', [
    'exam_schedule_id' => $examSchedule->id,
    'exam_cell_email' => $examCellUser->email,
    'faculty_email' => $faculty->email
]);
```

## Special Cases

### Internal Exams
- **No Assessment Agency Approval:** Internal exams skip Assessment Agency review
- **Direct Final Approval:** TC Head approval triggers final notification
- **CC Recipients:** Exam Cell and Faculty are CC'd on final approval

### Final/Special Final Exams
- **Full Workflow:** Goes through all approval stages
- **Assessment Agency Required:** Must be approved by Assessment Agency
- **File Number Assignment:** Assessment Agency assigns file number

## Email Configuration

### Laravel Mail Configuration
Ensure your `.env` file has proper mail configuration:
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

### Queue Configuration (Optional)
For better performance, consider using Laravel queues:
```env
QUEUE_CONNECTION=database
```

## Testing

### Test Scenarios
1. **Faculty Submission:** Verify Exam Cell receives email
2. **Exam Cell Approval:** Verify TC Head receives email
3. **TC Head Approval (Internal):** Verify final approval email with CC
4. **TC Head Approval (Final):** Verify Assessment Agency receives email
5. **Assessment Agency Approval:** Verify final notification with file number

### Manual Testing
```php
// Test email sending manually
Mail::to('test@example.com')->send(new ExamScheduleSubmitted($examSchedule, $faculty, $examCellUser));
```

## Benefits

1. **Automated Workflow:** No manual notification required
2. **Professional Communication:** Standardized email templates
3. **Audit Trail:** Complete email history for compliance
4. **User Experience:** Clear next steps and direct links
5. **Error Resilience:** Failed emails don't break approval process
6. **Comprehensive Logging:** Full tracking of email activities

## Future Enhancements

1. **Email Preferences:** Allow users to configure notification preferences
2. **SMS Notifications:** Add SMS alerts for urgent approvals
3. **Email Templates:** Allow customization of email templates
4. **Bulk Notifications:** Send notifications for multiple schedules
5. **Reminder Emails:** Automatic reminders for pending approvals 