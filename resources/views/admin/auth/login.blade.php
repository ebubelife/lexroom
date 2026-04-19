<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — FirstMediator</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/images/logos/FM_Icon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'DM Sans', sans-serif;
            background: #0D1B2A;
            color: #F0F4F8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .font-serif { font-family: 'Instrument Serif', serif; }
        input {
            width: 100%;
            padding: 0.625rem 0.875rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid #1e2f42;
            border-radius: 0.5rem;
            color: #F0F4F8;
            font-size: 0.875rem;
            outline: none;
            transition: border-color 0.15s;
        }
        input:focus { border-color: #C9A84C; }
        input::placeholder { color: #4a6070; }
    </style>
</head>
<body>
    <div class="w-full max-w-sm px-4">
        <div class="text-center mb-8">
            <p class="font-serif text-2xl" style="color: #C9A84C;">FirstMediator</p>
            <p class="text-sm mt-1" style="color: #8A9BB0;">Admin Panel</p>
        </div>

        <div class="rounded-xl p-6" style="background: #111f30; border: 1px solid #1e2f42;">
            <h1 class="text-lg font-semibold mb-5">Sign in to admin</h1>

            @if($errors->any())
                <div class="mb-4 p-3 rounded-lg text-sm" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #FCA5A5;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium mb-1.5" style="color: #8A9BB0;">Email address</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="admin@firstmediator.com" required autofocus>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1.5" style="color: #8A9BB0;">Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember"
                           class="w-4 h-4 rounded" style="width:1rem;height:1rem;padding:0;">
                    <label for="remember" class="text-sm" style="color: #8A9BB0;">Remember me</label>
                </div>
                <button type="submit"
                        class="w-full py-2.5 rounded-lg text-sm font-semibold transition-opacity hover:opacity-90"
                        style="background: #C9A84C; color: #0D1B2A;">
                    Sign in
                </button>
            </form>
        </div>

        <p class="text-center text-xs mt-4" style="color: #4a6070;">
            <a href="{{ route('login') }}" style="color: #8A9BB0;" class="hover:underline">← Back to main site</a>
        </p>
    </div>
</body>
</html>
