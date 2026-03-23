<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Invitation</title>
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
        .info-box {
            background-color: #F5EDD6;
            border-left: 4px solid #C9A84C;
            padding: 20px;
            margin: 30px 0;
            border-radius: 4px;
        }
        .info-label {
            font-size: 12px;
            color: #6B6B68;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 16px;
            color: #0D1B2A;
            font-weight: 600;
        }
        .cta-button {
            display: inline-block;
            background-color: #C9A84C;
            color: #ffffff;
            text-decoration: none;
            padding: 16px 40px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            text-align: center;
        }
        .cta-button:hover {
            background-color: #B39743;
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
        .footer-links {
            margin-top: 15px;
        }
        .footer-link {
            color: #C9A84C;
            text-decoration: none;
            margin: 0 10px;
        }
        .badge {
            display: inline-block;
            background-color: #E8C96A;
            color: #0D1B2A;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">FirstMediator</div>
            <div class="header-subtitle">AI-Assisted Legal Mediation</div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">You've Been Invited to a Mediation Session</div>
            
            <p class="message">
                Hello,
            </p>
            
            <p class="message">
                <strong>{{ $room->partyA->name }}</strong> has invited you to participate in a mediation session on FirstMediator. 
                This is an AI-assisted dispute resolution platform designed to help both parties reach a fair resolution without expensive lawyers or lengthy court processes.
            </p>

            <!-- Session Details -->
            <div class="info-box">
                <div style="margin-bottom: 15px;">
                    <div class="info-label">Dispute Category</div>
                    <div class="info-value">
                        <span class="badge">{{ ucfirst($room->category) }}</span>
                    </div>
                </div>
                <div style="margin-bottom: 15px;">
                    <div class="info-label">Jurisdiction</div>
                    <div class="info-value">{{ $room->jurisdiction }}</div>
                </div>
                <div style="margin-bottom: 15px;">
                    <div class="info-label">Session Duration</div>
                    <div class="info-value">{{ $room->duration }} minutes</div>
                </div>
                <div>
                    <div class="info-label">Language</div>
                    <div class="info-value">{{ ucfirst($room->language) }}</div>
                </div>
            </div>

            <p class="message">
                <strong>What happens next?</strong>
            </p>
            <ul class="message" style="padding-left: 20px;">
                <li>Click the button below to join the session</li>
                <li>You can join as a guest (no account required)</li>
                <li>Present your side of the dispute to Lex, our AI mediator</li>
                <li>Upload any supporting evidence or documents</li>
                <li>Receive a detailed mediation report at the end</li>
            </ul>

            <!-- CTA Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $roomLink }}" class="cta-button">Join Mediation Session</a>
            </div>

            <div class="divider"></div>

            <p class="message" style="font-size: 14px; color: #718096;">
                <strong>Note:</strong> This session is confidential. All communications and evidence shared will only be accessible to you, the other party, and Lex AI. 
                The session will be recorded for transcript purposes only.
            </p>

            <p class="message" style="font-size: 14px; color: #718096;">
                If you believe you received this email in error, please ignore it or contact us at 
                <a href="mailto:info@kodeblooded.com.ng" style="color: #C9A84C;">info@kodeblooded.com.ng</a>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} FirstMediator. All rights reserved.</p>
            <p style="margin-top: 10px;">Resolving disputes the smart way. No lawyers needed.</p>
            <div class="footer-links">
                <a href="#" class="footer-link">Privacy Policy</a>
                <a href="#" class="footer-link">Terms of Service</a>
                <a href="#" class="footer-link">Help Center</a>
            </div>
        </div>
    </div>
</body>
</html>
