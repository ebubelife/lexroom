<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #0D1B2A;">FirstMediator</h1>
        </div>

        <div style="background: #F5EDD6; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
            <h2 style="color: #0D1B2A; margin-top: 0;">You've been invited to a mediation session</h2>
            <p style="margin: 0;">To join, please complete your half of the payment.</p>
        </div>

        <p>You have been invited to a <strong>{{ ucfirst($room->category) }}</strong> mediation session on FirstMediator.</p>

        <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <p style="margin: 0 0 5px;"><strong>Session Details:</strong></p>
            <p style="margin: 0; font-size: 14px;">Category: {{ ucfirst($room->category) }}</p>
            <p style="margin: 0; font-size: 14px;">Duration: {{ $room->duration }} minutes</p>
            <p style="margin: 0; font-size: 14px;">Jurisdiction: {{ $room->jurisdiction }}</p>
            <p style="margin: 0; font-size: 14px;">Payment link expires: {{ $room->party_b_payment_expires_at->format('M d, Y') }}</p>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $paymentUrl }}" style="display: inline-block; background: #C9A84C; color: white; padding: 14px 35px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">
                Complete Payment & Join Session
            </a>
        </div>

        <p style="font-size: 13px; color: #6B6B68;">This payment link expires in 7 days. If you do not pay within this time, the session will be cancelled.</p>

        <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
            <p>© {{ now()->year }} FirstMediator. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
