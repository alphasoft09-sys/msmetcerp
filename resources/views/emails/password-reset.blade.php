<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - {{ $projectName }}</title>
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #000000 0%, #333333 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            display: block;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 500;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #000;
        }
        .message {
            margin-bottom: 30px;
            color: #666;
        }
        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 6px;
            font-weight: 500;
            text-align: center;
            margin: 20px 0;
            transition: all 0.3s ease;
        }
        .reset-button:hover {
            background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .warning strong {
            color: #dc3545;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .footer p {
            margin: 5px 0;
        }
        .expiry-notice {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #721c24;
        }
        .user-type {
            display: inline-block;
            background-color: #000;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('msme_logo/favicon-96x96.png') }}" alt="MSME Logo" class="logo">
            <h1>{{ $projectName }}</h1>
            <div class="user-type">{{ ucfirst($userType) }} Portal</div>
        </div>

        <div class="content">
            <div class="greeting">
                Hello <strong>{{ $userName }}</strong>,
            </div>

            <div class="message">
                We received a password reset request for your {{ $projectName }} {{ ucfirst($userType) }} account. 
                If you didn't make this request, you can safely ignore this email.
            </div>

            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="reset-button">
                    Reset Your Password
                </a>
            </div>

            <div class="expiry-notice">
                <strong>Important:</strong> This password reset link will expire in <strong>60 minutes</strong> for security reasons.
            </div>

            <div class="warning">
                <strong>Security Notice:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Never share this link with anyone</li>
                    <li>Our team will never ask for your password via email or phone</li>
                    <li>If you didn't request this reset, please contact your administrator immediately</li>
                </ul>
            </div>

            <div class="message">
                If the button above doesn't work, you can copy and paste this link into your browser:
                <br><br>
                <a href="{{ $resetUrl }}" style="color: #dc3545; word-break: break-all;">{{ $resetUrl }}</a>
            </div>
        </div>

        <div class="footer">
            <p><strong>{{ $projectName }}</strong></p>
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ $projectName }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html> 