<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #0D1B2A;">FirstMediator</h1>
        </div>

        <div style="background: #F5EDD6; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
            <h2 style="color: #0D1B2A; margin-top: 0;">New Case Referral</h2>
            <p style="margin: 0;">A user has requested your legal assistance through FM Refer.</p>
        </div>

        <p>Hi {{ $lawyer->name }},</p>
        <p><strong>{{ $user->name }}</strong> has escalated a case to you via FirstMediator FM Refer.</p>

        <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <p style="margin: 0 0 8px;"><strong>Case:</strong> {{ $room->title ?? ucfirst($room->category) . ' Dispute' }}</p>
            <p style="margin: 0 0 8px;"><strong>Category:</strong> {{ ucfirst($room->category) }}</p>
            <p style="margin: 0 0 8px;"><strong>Jurisdiction:</strong> {{ $room->jurisdiction }}</p>
            <p style="margin: 0 0 8px;"><strong>Session Date:</strong> {{ $room->created_at->format('M d, Y') }}</p>
            <p style="margin: 0;"><strong>User Email:</strong> {{ $user->email }}</p>
        </div>

        <div style="background: #fff; border-left: 4px solid #C9A84C; padding: 15px; margin: 20px 0;">
            <p style="margin: 0 0 8px; font-weight: bold;">Message from {{ $user->name }}:</p>
            <p style="margin: 0;">{{ $message }}</p>
        </div>

        <p>Please contact the user directly at <a href="mailto:{{ $user->email }}">{{ $user->email }}</a> within 48–72 hours.</p>

        <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
            <p>© {{ now()->year }} FirstMediator. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
