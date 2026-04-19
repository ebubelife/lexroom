<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Successful — FirstMediator</title>
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
        <div style="background: white; border-radius: 16px; padding: 40px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); text-align: center;">
            <div style="font-size: 56px; margin-bottom: 16px;">✅</div>
            <h1 class="font-serif" style="color: #0D1B2A; font-size: 24px; margin-bottom: 8px;">You're In!</h1>
            <p style="color: #6B6B68; font-size: 14px; margin-bottom: 24px;">
                Payment confirmed. Your mediation session is now active.
            </p>

            @auth
                <a href="{{ route('rooms.show', $room->uuid) }}"
                   style="display: inline-block; background: #C9A84C; color: white; padding: 12px 30px; border-radius: 10px; font-weight: 600; text-decoration: none;">
                    Enter Session Room
                </a>
            @else
                <p style="color: #6B6B68; font-size: 14px; margin-bottom: 16px;">Create an account or log in to access your session.</p>
                <a href="{{ route('register') }}"
                   style="display: inline-block; background: #C9A84C; color: white; padding: 12px 30px; border-radius: 10px; font-weight: 600; text-decoration: none;">
                    Create Account
                </a>
                <p style="margin-top: 12px;">
                    <a href="{{ route('login') }}" style="color: #6B6B68; font-size: 13px;">Already have an account? Log in</a>
                </p>
            @endauth
        </div>
    </div>
</body>
</html>
