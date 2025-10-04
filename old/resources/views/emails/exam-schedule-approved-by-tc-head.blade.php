<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exam Schedule Approved by TC Head</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #ffc107;
            color: #333;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 0 0 5px 5px;
        }
        .details {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
        }
        .button {
            display: inline-block;
            background-color: #ffc107;
            color: #333;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ Exam Schedule Approved by TC Head</h1>
        <p>Action Required: Assessment Agency Review</p>
    </div>
    
    <div class="content">
        <p>Dear <strong>{{ $assessmentAgencyUser->name }}</strong>,</p>
        
        <p>An exam schedule has been approved by the TC Head and is now ready for final review and approval by the Assessment Agency.</p>
        
        <div class="details">
            <h3>📚 Exam Schedule Details:</h3>
            <ul>
                <li><strong>Course:</strong> {{ $examSchedule->course_name }}</li>
                <li><strong>Batch Code:</strong> {{ $examSchedule->batch_code }}</li>
                <li><strong>Semester:</strong> {{ $examSchedule->semester }}</li>
                <li><strong>Exam Type:</strong> {{ $examSchedule->exam_type }}</li>
                <li><strong>Exam Period:</strong> {{ $examSchedule->exam_start_date->format('M d, Y') }} to {{ $examSchedule->exam_end_date->format('M d, Y') }}</li>
                <li><strong>Program Number:</strong> {{ $examSchedule->program_number }}</li>
                <li><strong>Exam Coordinator:</strong> {{ $examSchedule->exam_coordinator }}</li>
                <li><strong>TC Code:</strong> {{ $examSchedule->tc_code }}</li>
                <li><strong>Approved By:</strong> {{ $tcHeadUser->name }} (TC Head)</li>
                <li><strong>Approval Date:</strong> {{ $examSchedule->updated_at->format('M d, Y H:i') }}</li>
            </ul>
        </div>
        
        <p><strong>Next Steps:</strong></p>
        <ol>
            <li>Review the exam schedule details</li>
            <li>Verify all requirements are met</li>
            <li>Approve and assign file number, or reject if needed</li>
            <li>Add any comments if needed</li>
        </ol>
        
        <p style="text-align: center;">
            <a href="{{ url('/admin/aa/exam-schedules/' . $examSchedule->id) }}" class="button">
                🔍 Review Exam Schedule
            </a>
        </p>
        
        <div class="footer">
            <p><strong>Important Notes:</strong></p>
            <ul>
                <li>Please review this schedule within 48-72 hours</li>
                <li>Contact the TC Head if any clarification is needed</li>
                <li>This is an automated notification from the AAMSME system</li>
            </ul>
            
            <p style="margin-top: 20px;">
                <strong>System Information:</strong><br>
                AAMSME - Advanced Assessment Management System for MSME<br>
                TC Code: {{ $examSchedule->tc_code }}<br>
                Notification sent on: {{ now()->format('M d, Y H:i:s') }}
            </p>
        </div>
    </div>
</body>
</html> 