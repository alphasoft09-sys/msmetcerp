<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Alert</title>
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
            border: 1px solid #dee2e6;
            border-radius: 0 0 5px 5px;
        }
        .alert-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }
        .details {
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }
        .detail-label {
            font-weight: bold;
            color: #495057;
        }
        .detail-value {
            color: #6c757d;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
        .warning {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚨 SECURITY ALERT</h1>
        <p>Multiple Failed Login Attempts Detected</p>
    </div>

    <div class="content">
        <div class="alert-box">
            <h3>⚠️ Immediate Action Required</h3>
            <p class="warning">
                Multiple failed login attempts have been detected for the same email address and IP address combination. 
                This may indicate a potential security breach or brute force attack.
            </p>
        </div>

        <div class="details">
            <h3>📊 Attempt Details</h3>
            
            <div class="detail-row">
                <span class="detail-label">Email Address:</span>
                <span class="detail-value">{{ $email }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">IP Address:</span>
                <span class="detail-value">{{ $ipAddress }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Login Type:</span>
                <span class="detail-value">{{ ucfirst($guard) }} Login</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Failed Attempts:</span>
                <span class="detail-value warning">{{ $failedAttempts }} attempts</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Last Attempt:</span>
                <span class="detail-value">{{ $attemptedAt->format('Y-m-d H:i:s T') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Attempted Password:</span>
                <span class="detail-value">{{ $attemptedPassword ?: 'Not captured' }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">User Agent:</span>
                <span class="detail-value">{{ $userAgent ?: 'Not available' }}</span>
            </div>
        </div>

        <div class="alert-box">
            <h3>🔒 Recommended Actions</h3>
            <ul>
                <li><strong>Review Account Security:</strong> Check if the account has been compromised</li>
                <li><strong>Password Reset:</strong> Consider forcing a password reset for the affected account</li>
                <li><strong>IP Investigation:</strong> Investigate the source IP address for suspicious activity</li>
                <li><strong>Account Lockout:</strong> Consider temporarily locking the account</li>
                <li><strong>Monitor Activity:</strong> Closely monitor the account for unusual activity</li>
            </ul>
        </div>

        <div class="details">
            <h3>📈 Statistics</h3>
            <div class="detail-row">
                <span class="detail-label">Time Window:</span>
                <span class="detail-value">Last 24 hours</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Alert Threshold:</span>
                <span class="detail-value">10 failed attempts</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">System:</span>
                <span class="detail-value">{{ env('PROJECT_NAME', 'MSME Technology Center') }} Security System</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>
            <strong>This is an automated security alert from your {{ env('PROJECT_NAME', 'MSME Technology Center') }}.</strong><br>
            Please take immediate action to investigate and secure the affected account.<br>
            <small>Generated on {{ now()->format('Y-m-d H:i:s T') }}</small>
        </p>
    </div>
</body>
</html> 