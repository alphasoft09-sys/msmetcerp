<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Verification Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #007bff;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .otp-code {
            background-color: #f8f9fa;
            border: 2px dashed #007bff;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 5px;
            color: #007bff;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 12px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ env('PROJECT_NAME', 'MSME Technology Center') }}</div>
            <h2>Login Verification Code</h2>
        </div>

        <p>Hello <strong>{{ $userName }}</strong>,</p>

        <p>We received a login request for your {{ env('PROJECT_NAME', 'MSME Technology Center') }} account. To complete the login process, please use the verification code below:</p>

        <div class="otp-code">
            {{ $otp }}
        </div>

        <p><strong>Important:</strong></p>
        <ul>
            <li>This code will expire in <strong>5 minutes</strong></li>
            <li>If you didn't request this code, please ignore this email</li>
            <li>Never share this code with anyone</li>
        </ul>

        <div class="warning">
            <strong>Security Notice:</strong> This verification code is for your {{ env('PROJECT_NAME', 'MSME Technology Center') }} login only. Our team will never ask for this code via phone or other communication channels.
        </div>

        <p>If you're having trouble logging in, please contact your system administrator.</p>

        <p>Best regards,<br>
        <strong>{{ env('PROJECT_NAME', 'MSME Technology Center') }} Team</strong></p>

        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ env('PROJECT_NAME', 'MSME Technology Center') }}. All rights reserved.</p>
        </div>
        <div class="random-number">
            <p>Message ID: {{ date('YmdHis') }}{{ rand(100000, 999999) }}</p>
        </div>      
    </div>
</body>
</html> 