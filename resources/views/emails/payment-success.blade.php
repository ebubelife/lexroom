<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmed</title>
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
            <div class="success-icon" style="text-align: center;">✅</div>
            
            <h2 style="color: #0D1B2A; text-align: center; margin-bottom: 10px;">Payment Confirmed!</h2>
            <p style="text-align: center; color: #6c757d; margin-bottom: 30px;">
                Your payment has been successfully processed for the mediation session.
            </p>

            <div class="amount">{{ \App\Models\PlatformSetting::currencySymbol() }}{{ number_format($amount, 2) }}</div>

            <div class="case-details">
                <h3 style="margin-top: 0; color: #0D1B2A;">Session Details</h3>
                <div class="detail-row">
                    <span><strong>Case ID:</strong></span>
                    <span>{{ $room->case_id }}</span>
                </div>
                <div class="detail-row">
                    <span><strong>Category:</strong></span>
                    <span>{{ ucfirst($room->category) }}</span>
                </div>
                <div class="detail-row">
                    <span><strong>Duration:</strong></span>
                    <span>{{ $room->duration }} minutes</span>
                </div>
                <div class="detail-row">
                    <span><strong>Payment Type:</strong></span>
                    <span>{{ $room->payment_type === 'full' ? 'Full Payment' : 'Split Payment' }}</span>
                </div>
                <div class="detail-row">
                    <span><strong>Your Role:</strong></span>
                    <span>{{ $partyLabel }}</span>
                </div>
            </div>

            @if($room->payment_type === 'split')
                @if($party === 'party_a')
                    <p style="background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107;">
                        <strong>Next Step:</strong> We've sent the payment link to the other party. The session will begin once they complete their payment.
                    </p>
                @else
                    <p style="background: #d1ecf1; padding: 15px; border-radius: 8px; border-left: 4px solid #17a2b8;">
                        <strong>Ready to Start:</strong> Both parties have now paid. You can join the mediation session using the link below.
                    </p>
                @endif
            @else
                <p style="background: #d4edda; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745;">
                    <strong>Session Ready:</strong> The other party has been notified and can now join your mediation session.
                </p>
            @endif

            <div style="text-align: center;">
                @if($party === 'party_a')
                    <a href="{{ route('rooms.show', $room->uuid) }}" class="btn">Go to Session Room</a>
                @else
                    <a href="{{ route('rooms.show', ['uuid' => $room->uuid, 'token' => $room->invite_token]) }}" class="btn">Join Session Room</a>
                @endif
            </div>

            <p style="font-size: 14px; color: #6c757d; margin-top: 30px;">
                <strong>Important:</strong> Keep this email for your records. If you have any questions about your payment or the mediation process, please contact our support team.
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