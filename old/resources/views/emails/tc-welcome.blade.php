<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to AAMSME</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .welcome-section {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 0 8px 8px 0;
        }
        .welcome-section h2 {
            color: #667eea;
            margin: 0 0 15px 0;
            font-size: 22px;
        }
        .login-details {
            background-color: #e8f4fd;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .login-details h3 {
            color: #0c5460;
            margin: 0 0 15px 0;
            font-size: 18px;
        }
        .login-details p {
            margin: 8px 0;
            font-family: 'Courier New', monospace;
            background-color: #f8f9fa;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h3 {
            color: #495057;
            border-bottom: 2px solid #667eea;
            padding-bottom: 8px;
            margin-bottom: 20px;
            font-size: 20px;
        }
        .role-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .role-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .role-card h4 {
            color: #667eea;
            margin: 0 0 15px 0;
            font-size: 16px;
        }
        .role-card ul {
            margin: 0;
            padding-left: 20px;
        }
        .role-card li {
            margin-bottom: 8px;
        }
        .permissions-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .permissions-table th {
            background-color: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        .permissions-table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }
        .permissions-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .permissions-table .yes {
            color: #28a745;
            font-weight: 600;
        }
        .permissions-table .no {
            color: #dc3545;
            font-weight: 600;
        }
        .steps {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .steps h4 {
            color: #856404;
            margin: 0 0 15px 0;
        }
        .steps ol {
            margin: 0;
            padding-left: 20px;
        }
        .steps li {
            margin-bottom: 10px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .footer p {
            margin: 5px 0;
            opacity: 0.8;
        }
        .important-note {
            background-color: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .important-note h4 {
            color: #c53030;
            margin: 0 0 10px 0;
        }
        .important-note p {
            margin: 0;
            color: #742a2a;
        }
        @media (max-width: 768px) {
            .container {
                margin: 10px;
            }
            .header, .content, .footer {
                padding: 20px 15px;
            }
            .role-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Welcome to AAMSME!</h1>
            <p>Assessment Agency MSME Management System</p>
        </div>

        <div class="content">
            <div class="welcome-section">
                <h2>Dear {{ $tcAdmin->name }},</h2>
                <p>Welcome to the AAMSME (Assessment Agency MSME Management System)! Your Training Center account has been successfully created by the Assessment Agency.</p>
                <p><strong>Training Center:</strong> {{ $tcAdmin->tc_name }}</p>
                <p><strong>TC Code:</strong> {{ $tcAdmin->from_tc }}</p>
                <p><strong>Created by:</strong> {{ $assessmentAgencyUser->name }} (Assessment Agency)</p>
            </div>

            <div class="login-details">
                <h3>🔐 Your Login Credentials</h3>
                <p><strong>Email:</strong> {{ $tcAdmin->email }}</p>
                <p><strong>Password:</strong> {{ $password }}</p>
                <p><strong>Login URL:</strong> <a href="{{ $loginUrl }}" style="color: #667eea;">{{ $loginUrl }}</a></p>
            </div>

            <div class="important-note">
                <h4>⚠️ Important Security Notice</h4>
                <p>Please change your password immediately after your first login for security purposes. Your current password is temporary and should not be shared with anyone.</p>
            </div>

            <div class="section">
                <h3>👥 Your Role: TC Admin</h3>
                <p>As a <strong>Training Center Administrator</strong>, you have comprehensive control over your training center's operations. Here's what you can do:</p>
                
                <div class="role-card">
                    <h4>🎯 Primary Responsibilities:</h4>
                    <ul>
                        <li>Manage your training center's profile and settings</li>
                        <li>Create and manage user accounts for your TC</li>
                        <li>Add and manage training centers/centres</li>
                        <li>Oversee exam schedule creation and approval process</li>
                        <li>Monitor student registrations and progress</li>
                        <li>Generate reports and analytics</li>
                        <li>Manage qualifications and modules</li>
                    </ul>
                </div>
            </div>

            <div class="section">
                <h3>👥 User Management Guide</h3>
                <p>As a TC Admin, you can create the following user accounts for your training center:</p>
                
                <div class="role-grid">
                    <div class="role-card">
                        <h4>🏢 TC Head (Role 2)</h4>
                        <ul>
                            <li>One per training center</li>
                            <li>Oversees exam schedule approvals</li>
                            <li>Manages faculty and exam cell accounts</li>
                            <li>Final approval authority for internal exams</li>
                        </ul>
                    </div>
                    
                    <div class="role-card">
                        <h4>📋 Exam Cell (Role 3)</h4>
                        <ul>
                            <li>Multiple accounts allowed</li>
                            <li>Reviews exam schedules from faculty</li>
                            <li>Checks logistics and resources</li>
                            <li>Approves schedules for TC Head review</li>
                        </ul>
                    </div>
                    
                    <div class="role-card">
                        <h4>👨‍🏫 TC Faculty (Role 5)</h4>
                        <ul>
                            <li>Multiple faculty accounts</li>
                            <li>Creates exam schedules</li>
                            <li>Manages student attendance</li>
                            <li>Tracks student progress</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="section">
                <h3>🔐 Permission Matrix</h3>
                <p>Here's a detailed breakdown of what each role can do in your training center:</p>
                
                <table class="permissions-table">
                    <thead>
                        <tr>
                            <th>Feature</th>
                            <th>TC Admin</th>
                            <th>TC Head</th>
                            <th>Exam Cell</th>
                            <th>Faculty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Create User Accounts</td>
                            <td class="yes">✓ Yes</td>
                            <td class="yes">✓ Yes</td>
                            <td class="no">✗ No</td>
                            <td class="no">✗ No</td>
                        </tr>
                        <tr>
                            <td>Manage Centres</td>
                            <td class="yes">✓ Yes</td>
                            <td class="yes">✓ Yes</td>
                            <td class="no">✗ No</td>
                            <td class="no">✗ No</td>
                        </tr>
                        <tr>
                            <td>Create Exam Schedules</td>
                            <td class="no">✗ No</td>
                            <td class="no">✗ No</td>
                            <td class="no">✗ No</td>
                            <td class="yes">✓ Yes</td>
                        </tr>
                        <tr>
                            <td>Approve Exam Schedules</td>
                            <td class="yes">✓ Yes</td>
                            <td class="yes">✓ Yes</td>
                            <td class="yes">✓ Yes</td>
                            <td class="no">✗ No</td>
                        </tr>
                        <tr>
                            <td>Manage Students</td>
                            <td class="yes">✓ Yes</td>
                            <td class="yes">✓ Yes</td>
                            <td class="yes">✓ Yes</td>
                            <td class="yes">✓ Yes</td>
                        </tr>
                        <tr>
                            <td>View Reports</td>
                            <td class="yes">✓ Yes</td>
                            <td class="yes">✓ Yes</td>
                            <td class="yes">✓ Yes</td>
                            <td class="no">✗ No</td>
                        </tr>
                        <tr>
                            <td>Manage Qualifications</td>
                            <td class="yes">✓ Yes</td>
                            <td class="yes">✓ Yes</td>
                            <td class="no">✗ No</td>
                            <td class="no">✗ No</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="section">
                <h3>🚀 Getting Started Steps</h3>
                <div class="steps">
                    <h4>Follow these steps to set up your training center:</h4>
                    <ol>
                        <li><strong>Login to the system</strong> using your credentials provided above</li>
                        <li><strong>Change your password</strong> immediately for security</li>
                        <li><strong>Create a TC Head account</strong> (Admin Management → Add Admin → Select TC Head role)</li>
                        <li><strong>Create Exam Cell accounts</strong> for staff who will review exam schedules</li>
                        <li><strong>Create Faculty accounts</strong> for teachers who will create exam schedules</li>
                        <li><strong>Add Training Centres</strong> if your TC has multiple locations</li>
                        <li><strong>Review and customize</strong> your training center profile</li>
                        <li><strong>Start creating exam schedules</strong> through your faculty accounts</li>
                    </ol>
                </div>
            </div>

            <div class="section">
                <h3>📋 Exam Schedule Workflow</h3>
                <p>Understanding the exam schedule approval process:</p>
                
                <div class="steps">
                    <h4>Approval Flow:</h4>
                    <ol>
                        <li><strong>Faculty</strong> creates and submits exam schedule</li>
                        <li><strong>Exam Cell</strong> reviews and approves (checks logistics)</li>
                        <li><strong>TC Head</strong> reviews and approves</li>
                        <li><strong>Assessment Agency</strong> approves (for Final/Special Final exams)</li>
                        <li><strong>File Number</strong> is generated upon final approval</li>
                    </ol>
                </div>
                
                <p><strong>Note:</strong> Internal exams only require TC Head approval, while Final and Special Final exams require Assessment Agency approval.</p>
            </div>

            <div class="section">
                <h3>📞 Support & Contact</h3>
                <p>If you need assistance or have questions:</p>
                <ul>
                    <li><strong>Technical Support:</strong> Contact the Assessment Agency</li>
                    <li><strong>System Access:</strong> {{ $assessmentAgencyUser->email }}</li>
                    <li><strong>Training Center:</strong> {{ $tcAdmin->tc_name }}</li>
                    <li><strong>TC Code:</strong> {{ $tcAdmin->from_tc }}</li>
                </ul>
            </div>

            <div style="text-align: center; margin: 40px 0;">
                <a href="{{ $loginUrl }}" class="cta-button">
                    🚀 Login to AAMSME Now
                </a>
            </div>
        </div>

        <div class="footer">
            <p><strong>AAMSME - Assessment Agency MSME Management System</strong></p>
            <p>Training Center: {{ $tcAdmin->tc_name }}</p>
            <p>TC Code: {{ $tcAdmin->from_tc }}</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html> 