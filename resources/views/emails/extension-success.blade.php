<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Extended</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #F5EDD6; }
        .container { max-width: 600px; margin: 0 auto; background: white; }
        .header { background: #0D1B2A; color: white; padding: 30px; text-align: center; }
        .content { padding: 40px 30px; }
        .footer { background: #f8f9fa; padding: 20px 30px; text-align: center; font-size: 12px; color: #6c757d; }
        .btn { display: inline-block; background: #C9A84C; color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; font-weight: 600; margin: 20px 0; }
        .success-icon { font-size: 48px; margin-bottom: 20px; }
        .case-details { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; margin: 8px 0; }
        .amount { font-size: 24px; font-weight: bold; color: #C9A84C; text-align: center; margin: 20px 0; }
        .extension-highlight { background: #d1ecf1; padding: 20px; border-radius: 8px; border-left: 4px solid #17a2b8; margin: 20px 0; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1 style="margin: 0; font-size: 28px;">First Mediator</h1>
            <p style="margin: 5px 0 0 0; opacity: 0.8; font-size: 12px; text-transform: uppercase; letter-spacing: 2px;">Dispute Resolution Platform</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="success-icon" style="text-align: center;">⏰</div>
            
            <h2 style="color: #0D1B2A; text-align: center; margin-bottom: 10px;">Session Extended!</h2>
            <p style="text-align: center; color: #6c757d; margin-bottom: 30px;">
                Your payment has been processed and the mediation session has been extended.
            </p>

            <div class="extension-highlight">
                <h3 style="margin: 0; color: #0D1B2A; font-size: 20px;">+{{ $minutes }} Minutes Added</h3>
                <p style="margin: 10px 0 0 0; color: #6c757d;">Continue your mediation with additional time</p>
            </div>

            <div class="amount">{{ \App\Models\PlatformSetting::currencySymbol() }}{{ number_format($amount, 2) }}</div>

            <div class="case-details">
                <h3 style="margin-top: 0; color: #0D1B2A;">Session Details</h3>
                <div class="detail-row">
                    <span><strong>Case ID:</strong></span>
                    <span>{{ $room->case_id }}</span>
                </div>
                <div class="detail-row">
                    <span><strong>Original Duration:</strong></span>
                    <span>{{ $room->duration }} minutes</span>
                </div>
                <div class="detail-row">
                    <span><strong>Extension Added:</strong></span>
                    <span>{{ $minutes }} minutes</span>
                </div>
                <div class="detail-row">
                    <span><strong>Total Duration:</strong></span>
                    <span><strong>{{ $room->duration + $room->extended_minutes }} minutes</strong></span>
                </div>
                <div class="detail-row">
                    <span><strong>Paid By:</strong></span>
                    <span>{{ $party === 'party_a' ? 'Party A' : 'Party B' }}</span>
                </div>
            </div>

            <p style="background: #d4edda; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745;">
                <strong>Session Active:</strong> The timer has been updated and your mediation can continue. Return to the session room to resume your discussion.
            </p>

            <div style="text-align: center;">
                <a href="{{ route('rooms.show', $room->uuid) }}" class="btn">Return to Session</a>
            </div>

            <p style="font-size: 14px; color: #6c757d; margin-top: 30px;">
                <strong>Note:</strong> This extension payment has been processed successfully. Both parties can continue the mediation with the additional time purchased.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>First Mediator</strong> — Resolving disputes the smart way</p>
            <p>Questions? Contact us at <a href="mailto:hello@firstmediator.com" style="color: #C9A84C;">hello@firstmediator.com</a></p>
            <p style="margin-top: 15px;">
                This is an automated message. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>