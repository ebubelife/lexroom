<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Complete Payment — FirstMediator</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/images/logos/FM_Icon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'DM Sans', sans-serif; background: #F5EDD6; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .font-serif { font-family: 'Instrument Serif', serif; }
    </style>
</head>
<body>
    <div style="max-width: 480px; width: 100%; margin: 0 auto; padding: 20px;">
        <div style="background: white; border-radius: 16px; padding: 40px; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">
            <div style="text-align: center; margin-bottom: 30px;">
                <img src="{{ asset('assets/images/logos/fm-lightmode.png') }}" alt="FirstMediator" style="height: 60px;">
            </div>

            <h1 class="font-serif" style="color: #0D1B2A; font-size: 22px; margin-bottom: 8px; text-align: center;">Complete Your Payment</h1>
            <p style="color: #6B6B68; text-align: center; font-size: 14px; margin-bottom: 30px;">
                You've been invited to a mediation session. Pay your half to join.
            </p>

            <div style="background: #F5EDD6; border-radius: 10px; padding: 16px; margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: #6B6B68; font-size: 14px;">Category</span>
                    <span style="color: #0D1B2A; font-weight: 500; font-size: 14px;">{{ ucfirst($room->category) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: #6B6B68; font-size: 14px;">Duration</span>
                    <span style="color: #0D1B2A; font-weight: 500; font-size: 14px;">{{ $room->duration }} minutes</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: #6B6B68; font-size: 14px;">Jurisdiction</span>
                    <span style="color: #0D1B2A; font-weight: 500; font-size: 14px;">{{ $room->jurisdiction }}</span>
                </div>
                <div style="border-top: 1px solid #E8C96A; margin: 12px 0;"></div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #0D1B2A; font-weight: 600;">Your share</span>
                    <span style="color: #C9A84C; font-weight: 700; font-size: 18px;">${{ number_format($amount, 2) }}</span>
                </div>
            </div>

            <a href="{{ route('payment.party-b.pay', ['uuid' => $room->uuid, 'token' => request('token')]) }}"
               style="display: block; background: #C9A84C; color: white; padding: 14px; text-align: center; border-radius: 10px; font-weight: 600; text-decoration: none; font-size: 16px;">
                Pay ${{ number_format($amount, 2) }} & Join Session
            </a>

            <p style="text-align: center; font-size: 12px; color: #6B6B68; margin-top: 16px;">
                Secured by Stripe. Link expires {{ $room->party_b_payment_expires_at->diffForHumans() }}.
            </p>
        </div>
    </div>
</body>
</html>
