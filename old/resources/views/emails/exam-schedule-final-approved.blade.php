<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exam Schedule Finally Approved</title>
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
            background-color: #dc3545;
            color: white;
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
            border-left: 4px solid #dc3545;
        }
        .file-number {
            background-color: #dc3545;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 15px 0;
        }
        .button {
            display: inline-block;
            background-color: #dc3545;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
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
        <h1>🎉 Exam Schedule Finally Approved!</h1>
        <p>File Number Assigned: {{ $examSchedule->file_no }}</p>
    </div>
    
    <div class="content">
        <p>Dear <strong>{{ $tcHeadUser->name }}</strong>,</p>
        
        <p>Great news! An exam schedule has been finally approved by the Assessment Agency and a file number has been assigned.</p>
        
        <div class="file-number">
            📄 File Number: {{ $examSchedule->file_no }}
        </div>
        
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
                <li><strong>Finally Approved By:</strong> {{ $assessmentAgencyUser->name }} (Assessment Agency)</li>
                <li><strong>Approval Date:</strong> {{ $examSchedule->updated_at->format('M d, Y H:i') }}</li>
            </ul>
        </div>
        
        <p><strong>Approval Chain:</strong></p>
        <ol>
            <li><strong>Faculty:</strong> {{ $faculty->name }} - Created and submitted</li>
            <li><strong>Exam Cell:</strong> {{ $examCellUser->name }} - Reviewed and approved</li>
            <li><strong>TC Head:</strong> {{ $tcHeadUser->name }} - Reviewed and approved</li>
            <li><strong>Assessment Agency:</strong> {{ $assessmentAgencyUser->name }} - Finally approved and assigned file number</li>
        </ol>
        
        <p style="text-align: center;">
            <a href="{{ url('/admin/tc-head/exam-schedules/' . $examSchedule->id . '/fullview') }}" class="button">
                📄 View Final Exam Schedule
            </a>
        </p>
        
        <div class="footer">
            <p><strong>Important Notes:</strong></p>
            <ul>
                <li>The exam schedule is now officially approved and ready for execution</li>
                <li>File number {{ $examSchedule->file_no }} has been assigned for tracking</li>
                <li>All stakeholders have been notified of the approval</li>
                <li>This is an automated notification from the AAMSME system</li>
            </ul>
            
            <p style="margin-top: 20px;">
                <strong>System Information:</strong><br>
                AAMSME - Advanced Assessment Management System for MSME<br>
                TC Code: {{ $examSchedule->tc_code }}<br>
                File Number: {{ $examSchedule->file_no }}<br>
                Notification sent on: {{ now()->format('M d, Y H:i:s') }}
            </p>
        </div>
    </div>
</body>
</html> 