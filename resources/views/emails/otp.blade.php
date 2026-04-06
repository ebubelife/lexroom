<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #0D1B2A;
            padding: 40px 30px;
            text-align: center;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            color: #C9A84C;
            margin-bottom: 10px;
        }
        .header-subtitle {
            color: #ffffff;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .greeting {
            font-size: 24px;
            color: #0D1B2A;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .message {
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 20px;
            font-size: 16px;
        }
        .otp-box {
            background-color: #F5EDD6;
            border: 2px dashed #C9A84C;
            padding: 30px;
            margin: 30px 0;
            border-radius: 8px;
            display: inline-block;
            min-width: 200px;
        }
        .otp-code {
            font-size: 42px;
            font-weight: bold;
            color: #0D1B2A;
            letter-spacing: 10px;
            font-family: monospace;
        }
        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 30px 0;
        }
        .footer {
            background-color: #f7fafc;
            padding: 30px;
            text-align: center;
            color: #718096;
            font-size: 14px;
        }
        .footer-link {
            color: #C9A84C;
            text-decoration: none;
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">First Mediator</div>
            <div class="header-subtitle">AI-Assisted Legal Mediation</div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">Verify Your Account</div>
            
            <p class="message">
                Hello,
            </p>
            
            <p class="message">
                Thank you for choosing First Mediator. To complete your registration and activate your account, please use the 6-digit verification code below:
            </p>

            <!-- OTP Code Display -->
            <div class="otp-box">
                <div class="otp-code">{{ $otpCode }}</div>
            </div>

            <p class="message" style="font-size: 14px; color: #718096;">
                This code will expire in 3 hours for security purposes.
            </p>

            <div class="divider"></div>

            <p class="message" style="font-size: 14px; color: #718096;">
                If you did not request this code, please ignore this email or contact our support team.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} First Mediator. All rights reserved.</p>
            <div style="margin-top: 15px;">
                <a href="{{ route('privacy') }}" class="footer-link">Privacy Policy</a>
                <a href="{{ route('terms') }}" class="footer-link">Terms of Service</a>
            </div>
        </div>
    </div>
</body>
</html>
