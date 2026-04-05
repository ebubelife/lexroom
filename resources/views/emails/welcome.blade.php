<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to First Mediator</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #0D1B2A 0%, #1a2f45 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .logo {
            font-family: 'Georgia', serif;
            font-size: 32px;
            font-weight: bold;
            color: #C9A84C;
            margin: 0;
        }
        .content {
            padding: 40px 30px;
        }
        h1 {
            color: #0D1B2A;
            font-size: 24px;
            margin: 0 0 20px 0;
        }
        p {
            margin: 0 0 15px 0;
            color: #555;
        }
        .highlight {
            background: #F5EDD6;
            border-left: 4px solid #C9A84C;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            background: #C9A84C;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
        }
        .features {
            margin: 30px 0;
        }
        .feature {
            display: flex;
            align-items: start;
            margin-bottom: 15px;
        }
        .feature-icon {
            width: 24px;
            height: 24px;
            background: #C9A84C;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 12px;
            flex-shrink: 0;
        }
        .footer {
            background: #f8f8f8;
            padding: 30px;
            text-align: center;
            font-size: 14px;
            color: #777;
        }
        .footer a {
            color: #C9A84C;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="logo">First Mediator</h1>
        </div>
        
        <div class="content">
            <h1>Welcome to First Mediator, {{ $user->first_name }}! 🎉</h1>
            
            <p>Your account has been successfully verified and you're all set to start resolving disputes the smart way.</p>
            
            <div class="highlight">
                <strong>What is First Mediator?</strong><br>
                First Mediator is an AI-assisted legal dispute mediation platform that helps you resolve conflicts without expensive lawyers or lengthy court processes.
            </div>
            
            <div class="features">
                <div class="feature">
                    <div class="feature-icon">✓</div>
                    <div>
                        <strong>Create Mediation Rooms</strong><br>
                        <span style="color: #777; font-size: 14px;">Start a session and invite the other party to join</span>
                    </div>
                </div>
                <div class="feature">
                    <div class="feature-icon">✓</div>
                    <div>
                        <strong>AI-Powered Mediation</strong><br>
                        <span style="color: #777; font-size: 14px;">First Mediator AI guides both parties toward fair resolutions</span>
                    </div>
                </div>
                <div class="feature">
                    <div class="feature-icon">✓</div>
                    <div>
                        <strong>Evidence Management</strong><br>
                        <span style="color: #777; font-size: 14px;">Upload and share documents securely</span>
                    </div>
                </div>
                <div class="feature">
                    <div class="feature-icon">✓</div>
                    <div>
                        <strong>Get Reports</strong><br>
                        <span style="color: #777; font-size: 14px;">Receive detailed mediation reports and recommendations</span>
                    </div>
                </div>
            </div>
            
            <center>
                <a href="{{ route('dashboard') }}" class="button">Go to Dashboard</a>
            </center>
            
            <p style="margin-top: 30px;">Need help getting started? Check out our guides or contact our support team.</p>
            
            <p style="color: #999; font-size: 14px; margin-top: 30px;">
                If you didn't create this account, please ignore this email or contact us immediately.
            </p>
        </div>
        
        <div class="footer">
            <p><strong>First Mediator</strong> - Resolving disputes the smart way</p>
                <p>
                    <a href="{{ route('privacy') }}">Privacy Policy</a> &bull;
                    <a href="{{ route('terms') }}">Terms of Service</a> &bull;
                    <a href="mailto:hello@firstmediator.com">Contact Support</a>
                </p>
            <p style="margin-top: 15px; font-size: 12px;">
                © {{ date('Y') }} First Mediator. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
