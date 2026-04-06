<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Account</title>
    <style>
        body {
            font-family: 'Georgia', serif;
            line-height: 1.6;
            color: #0D1B2A;
            margin: 0;
            padding: 0;
            background-color: #F8F9FA;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #E2E8F0;
        }
        .header {
            background-color: #0D1B2A;
            padding: 40px 20px;
            text-align: center;
        }
        .content {
            padding: 40px;
            text-align: center;
        }
        .footer {
            background-color: #F1F5F9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #64748B;
        }
        h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            letter-spacing: 1px;
        }
        .otp-code {
            font-size: 48px;
            font-weight: bold;
            color: #D4AF37; /* Gold color */
            letter-spacing: 8px;
            margin: 30px 0;
            padding: 20px;
            background: #FFFDF5;
            border: 1px dashed #D4AF37;
            border-radius: 8px;
            display: inline-block;
        }
        .message {
            margin-bottom: 20px;
            font-size: 16px;
        }
        .button {
            display: inline-block;
            padding: 14px 28px;
            background-color: #D4AF37;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>LEXROOM</h1>
        </div>
        <div class="content">
            <div class="message">
                <h2 style="font-family: serif; color: #0D1B2A;">Verify Your Email</h2>
                <p>Thank you for joining LexRoom. Use the following 6-digit code to verify your account and complete your registration.</p>
            </div>
            
            <div class="otp-code">
                {{ $otpCode }}
            </div>
            
            <p style="font-size: 14px; color: #64748B;">This code will expire in 10 minutes.</p>
            
            <p>If you didn't request this code, you can safely ignore this email.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} LexRoom. All rights reserved.
        </div>
    </div>
</body>
</html>
