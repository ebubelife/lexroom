<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #0D1B2A; margin-bottom: 5px;">FirstMediator</h1>
        </div>

        <div style="background: #F5EDD6; padding: 25px; border-radius: 8px; text-align: center; margin-bottom: 25px;">
            <div style="font-size: 48px; margin-bottom: 10px;">🎉</div>
            <h2 style="color: #0D1B2A; margin: 0 0 10px;">You earned {{ $minutes }} free minutes!</h2>
            <p style="color: #6B6B68; margin: 0;">{{ $referredUser->name }} just completed their first session.</p>
        </div>

        <p>Hi {{ $referrer->first_name }},</p>
        <p>Great news! Your referral <strong>{{ $referredUser->name }}</strong> has completed their first paid mediation session on FirstMediator.</p>
        <p>As a thank you, we've added <strong>{{ $minutes }} free minutes</strong> to your account. You can use them to:</p>
        <ul>
            <li>Extend an active session</li>
            <li>Reduce the cost of your next session</li>
        </ul>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ config('app.url') }}/referrals" style="display: inline-block; background: #C9A84C; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">View My Referrals</a>
        </div>

        <p style="color: #6B6B68; font-size: 13px;">Keep sharing your referral link to earn more free minutes. Every successful referral earns you {{ $minutes }} minutes!</p>

        <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
            <p>© {{ now()->year }} FirstMediator. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
