<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Mediation Room is Ready</title>
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
            background: linear-gradient(135deg, #0D1B2A 0%, #1a2f45 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .logo {
            font-family: 'Georgia', serif;
            font-size: 32px;
            font-weight: bold;
            color: #C9A84C;
            margin: 0 0 8px;
        }
        .header-subtitle {
            color: #ffffff;
            font-size: 14px;
            opacity: 0.8;
            margin: 0;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 24px;
            color: #0D1B2A;
            margin: 0 0 20px;
            font-weight: 600;
        }
        .message {
            color: #4a5568;
            line-height: 1.7;
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
        .info-row {
            margin-bottom: 14px;
        }
        .info-row:last-child {
            margin-bottom: 0;
        }
        .info-label {
            font-size: 11px;
            color: #6B6B68;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 3px;
        }
        .info-value {
            font-size: 15px;
            color: #0D1B2A;
            font-weight: 600;
        }
        .badge {
            display: inline-block;
            background-color: #E8C96A;
            color: #0D1B2A;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
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
            text-align: center;
        }
        .step-list {
            margin: 20px 0;
            padding: 0;
            list-style: none;
        }
        .step-list li {
            display: flex;
            align-items: flex-start;
            margin-bottom: 14px;
            color: #4a5568;
            font-size: 15px;
            line-height: 1.5;
        }
        .step-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background-color: #C9A84C;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            margin-right: 12px;
            flex-shrink: 0;
            margin-top: 2px;
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
        .footer a {
            color: #C9A84C;
            text-decoration: none;
            margin: 0 8px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <p class="logo">First Mediator</p>
            <p class="header-subtitle">AI-Assisted Legal Mediation</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">Your Room is Ready, {{ $room->partyA->first_name }}! ⚖️</div>

            <p class="message">
                Your mediation room has been created and an invitation has been sent to
                <strong>{{ $room->party_b_email }}</strong>. Once they join, you'll both be ready to begin your session.
            </p>

            <!-- Case Details -->
            <div class="info-box">
                <div class="info-row">
                    <div class="info-label">Case ID</div>
                    <div class="info-value" style="font-family: monospace;">{{ $room->case_id }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Dispute Category</div>
                    <div class="info-value"><span class="badge">{{ ucfirst($room->category) }}</span></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Jurisdiction</div>
                    <div class="info-value">{{ $room->jurisdiction }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Session Duration</div>
                    <div class="info-value">{{ $room->duration }} minutes</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Invited Party</div>
                    <div class="info-value">{{ $room->party_b_email }}</div>
                </div>
            </div>

            <p class="message"><strong>What happens next?</strong></p>
            <ul class="step-list">
                <li><span class="step-num">1</span>Party B will receive their invitation link via email</li>
                <li><span class="step-num">2</span>Once they join, you can start the session from your room</li>
                <li><span class="step-num">3</span>First Mediator AI will guide both parties through the mediation</li>
                <li><span class="step-num">4</span>A detailed PDF report will be generated at the end</li>
            </ul>

            <!-- CTA -->
            <div style="text-align: center; margin: 35px 0;">
                <a href="{{ $roomUrl }}" class="cta-button">Go to Your Room</a>
            </div>

            <div class="divider"></div>

            <p class="message" style="font-size: 14px; color: #718096;">
                <strong>Note:</strong> This session is confidential. All communications and evidence shared are only accessible to you, the other party, and First Mediator AI.
            </p>

            <p class="message" style="font-size: 14px; color: #718096;">
                If you have any questions, contact us at
                <a href="mailto:info@kodeblooded.com.ng" style="color: #C9A84C;">info@kodeblooded.com.ng</a>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} First Mediator. All rights reserved.</p>
            <p style="margin-top: 8px;">Resolving disputes the smart way. No lawyers needed.</p>
            <div style="margin-top: 15px;">
                <a href="{{ config('app.url') }}">Visit Website</a>
                <a href="mailto:info@kodeblooded.com.ng">Contact Support</a>
            </div>
        </div>
    </div>
</body>
</html>
