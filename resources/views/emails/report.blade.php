<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #0D1B2A; margin-bottom: 5px;">FirstMediator</h1>
            <p style="color: #666; font-size: 14px;">Your Mediation Report is Ready</p>
        </div>

        <div style="background: #F5EDD6; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h2 style="color: #0D1B2A; margin-top: 0;">Mediation Session Complete</h2>
            <p>Your mediation session has concluded, and Lex has generated a comprehensive report analyzing the discussion and providing recommendations.</p>
        </div>

        <div style="margin-bottom: 20px;">
            <h3 style="color: #0D1B2A;">Session Details:</h3>
            <ul style="list-style: none; padding: 0;">
                <li style="padding: 5px 0;"><strong>Session ID:</strong> {{ $room->uuid }}</li>
                <li style="padding: 5px 0;"><strong>Category:</strong> {{ ucfirst($room->category) }}</li>
                <li style="padding: 5px 0;"><strong>Jurisdiction:</strong> {{ $room->jurisdiction }}</li>
                <li style="padding: 5px 0;"><strong>Date:</strong> {{ $report->generated_at->format('F d, Y') }}</li>
            </ul>
        </div>

        <div style="background: #FFF3CD; border-left: 4px solid #FFC107; padding: 15px; margin-bottom: 20px;">
            <p style="margin: 0; font-size: 14px;">
                <strong>Note:</strong> The attached report contains AI-generated analysis and recommendations. This is not legal advice. Please consult with a qualified attorney for legal guidance.
            </p>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <p>The mediation report is attached to this email as a PDF document.</p>
        </div>

        <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-top: 30px;">
            <h3 style="color: #0D1B2A; margin-top: 0;">Need Legal Assistance?</h3>
            <p>If you need professional legal representation or a formal legal opinion, visit the <strong>FM Refer</strong> section in your dashboard to connect with qualified lawyers in your jurisdiction.</p>
            <p style="text-align: center; margin-top: 20px;">
                <a href="{{ config('app.url') }}/fmrefer" style="display: inline-block; background: #C9A84C; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">Browse Lawyers</a>
            </p>
        </div>

        <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
            <p>© {{ now()->year }} FirstMediator. All rights reserved.</p>
            <p>Resolving disputes the smart way.</p>
        </div>
    </div>
</body>
</html>
