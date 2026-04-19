<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #0D1B2A;">FirstMediator</h1>
        </div>

        <div style="background: #FFF3CD; border-left: 4px solid #FFC107; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
            <h2 style="color: #0D1B2A; margin-top: 0;">Session Expired — Party B Did Not Pay</h2>
            <p>Unfortunately, the other party did not complete their payment within 7 days.</p>
        </div>

        <p>Hi {{ $room->partyA->first_name }},</p>
        <p>Your mediation session for <strong>{{ ucfirst($room->category) }}</strong> dispute has expired because Party B did not complete their payment.</p>

        <div style="background: #F5EDD6; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <p style="margin: 0;"><strong>Your funds have been moved to your Escrow Balance.</strong></p>
            <p style="margin: 5px 0 0; font-size: 14px; color: #6B6B68;">You can request a refund or use the funds for a future session from your wallet.</p>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ config('app.url') }}/wallet" style="display: inline-block; background: #C9A84C; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">View My Wallet</a>
        </div>

        <p style="font-size: 13px; color: #6B6B68;">To request a bank refund, please contact support. Alternatively, your escrow balance can be applied to any future session.</p>

        <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
            <p>© {{ now()->year }} FirstMediator. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
