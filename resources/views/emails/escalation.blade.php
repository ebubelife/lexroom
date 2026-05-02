<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background: #f4f4f2;">
    <div style="max-width: 600px; margin: 0 auto; padding: 30px 20px;">

        {{-- Header --}}
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #0D1B2A; margin: 0 0 4px; font-size: 24px;">FirstMediator</h1>
            <p style="color: #6B6B68; font-size: 13px; margin: 0;">AI-Assisted Legal Mediation Platform</p>
        </div>

        {{-- Hero banner --}}
        <div style="background: #F5EDD6; padding: 24px; border-radius: 10px; margin-bottom: 24px; border-left: 4px solid #C9A84C;">
            <h2 style="color: #0D1B2A; margin: 0 0 6px; font-size: 18px;">⚖️ New Case Referral via FM Refer</h2>
            <p style="color: #6B6B68; margin: 0; font-size: 14px;">A user has requested your legal assistance through FirstMediator.</p>
        </div>

        {{-- Greeting --}}
        <p style="font-size: 15px;">Hi <strong>{{ $lawyer->name }}</strong>,</p>
        <p style="font-size: 14px; color: #555;">
            <strong>{{ $user->name }}</strong> has reviewed your profile on FM Refer and would like to engage your legal services in connection with a completed mediation session on FirstMediator.
        </p>

        {{-- Case details --}}
        <div style="background: #ffffff; border: 1px solid #E8E8E4; border-radius: 10px; padding: 20px; margin: 20px 0;">
            <h3 style="color: #0D1B2A; margin: 0 0 14px; font-size: 14px; text-transform: uppercase; letter-spacing: 0.05em;">Case Details</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr>
                    <td style="padding: 6px 0; color: #6B6B68; width: 40%;">Case Title</td>
                    <td style="padding: 6px 0; color: #0D1B2A; font-weight: 600;">{{ $room->title ?? ucfirst($room->category) . ' Dispute' }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #6B6B68;">Category</td>
                    <td style="padding: 6px 0; color: #0D1B2A;">{{ ucfirst($room->category) }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #6B6B68;">Jurisdiction</td>
                    <td style="padding: 6px 0; color: #0D1B2A;">{{ $room->jurisdiction }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #6B6B68;">Session Date</td>
                    <td style="padding: 6px 0; color: #0D1B2A;">{{ $room->created_at->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #6B6B68;">Session ID</td>
                    <td style="padding: 6px 0; color: #0D1B2A; font-family: monospace; font-size: 12px;">{{ $room->uuid }}</td>
                </tr>
            </table>
        </div>

        {{-- User's message --}}
        <div style="background: #ffffff; border: 1px solid #E8E8E4; border-left: 4px solid #C9A84C; border-radius: 0 10px 10px 0; padding: 16px 20px; margin: 20px 0;">
            <p style="margin: 0 0 8px; font-size: 13px; color: #6B6B68; text-transform: uppercase; letter-spacing: 0.05em;">Message from {{ $user->name }}</p>
            <p style="margin: 0; font-size: 14px; color: #333; line-height: 1.7;">{{ $message }}</p>
        </div>

        {{-- Contact info --}}
        <div style="background: #f9f9f9; border-radius: 10px; padding: 16px 20px; margin: 20px 0;">
            <p style="margin: 0 0 6px; font-size: 13px; color: #6B6B68; text-transform: uppercase; letter-spacing: 0.05em;">Client Contact</p>
            <p style="margin: 0; font-size: 14px;"><strong>Name:</strong> {{ $user->name }}</p>
            <p style="margin: 4px 0 0; font-size: 14px;"><strong>Email:</strong> <a href="mailto:{{ $user->email }}" style="color: #C9A84C;">{{ $user->email }}</a></p>
        </div>

        {{-- CTA --}}
        <p style="font-size: 14px; color: #555;">
            Please respond to the client directly at <a href="mailto:{{ $user->email }}" style="color: #C9A84C;">{{ $user->email }}</a> within <strong>48–72 hours</strong>.
        </p>

        {{-- Disclaimer --}}
        <div style="background: #FFF3CD; border-left: 4px solid #FFC107; padding: 14px 16px; border-radius: 0 8px 8px 0; margin: 24px 0;">
            <p style="margin: 0; font-size: 12px; color: #555;">
                <strong>Note:</strong> This referral was made through FirstMediator's FM Refer directory. FirstMediator is not a party to any legal engagement between you and the client. All professional obligations and fees are between you and the client directly.
            </p>
        </div>

        {{-- Footer --}}
        <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #E8E8E4; color: #6B6B68; font-size: 12px;">
            <p style="margin: 0 0 4px;">© {{ now()->year }} FirstMediator. All rights reserved.</p>
            <p style="margin: 0;">Resolving disputes the smart way.</p>
        </div>

    </div>
</body>
</html>
