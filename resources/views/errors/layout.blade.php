<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — FirstMediator</title>
    <link rel="icon" type="image/svg+xml" href="/assets/images/logos/FM_Icon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #0D1B2A; --gold: #C9A84C; --gold-light: #E8C96A;
            --white: #ffffff; --gray-200: #E8E8E4; --gray-400: #ADADAA; --gray-600: #6B6B68;
            --bg-primary: var(--white); --text-primary: var(--navy);
            --text-secondary: var(--gray-600); --border-color: var(--gray-200);
        }
        [data-theme="dark"] {
            --bg-primary: var(--navy); --text-primary: var(--white);
            --text-secondary: var(--gray-400); --border-color: #2a3441;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--bg-primary); color: var(--text-primary);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
        }
        .font-serif { font-family: 'Instrument Serif', serif; }
        .container { max-width: 560px; width: 100%; padding: 2rem; text-align: center; position: relative; z-index: 10; }
        .error-code {
            font-size: clamp(6rem, 15vw, 10rem); line-height: 1; margin-bottom: 1rem;
            color: var(--gold); font-style: italic;
            animation: float 4s ease-in-out infinite;
            filter: drop-shadow(0 10px 15px rgba(201,168,76,0.2));
        }
        .error-title { font-size: clamp(1.5rem, 4vw, 2.2rem); margin-bottom: 1rem; opacity: 0; animation: fadeUp 0.8s ease-out 0.2s forwards; }
        .error-desc { font-size: 1rem; color: var(--text-secondary); margin-bottom: 2.5rem; opacity: 0; animation: fadeUp 0.8s ease-out 0.4s forwards; }
        .cta-group { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; opacity: 0; animation: fadeUp 0.8s ease-out 0.6s forwards; }
        .btn { display: inline-flex; align-items: center; padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s ease; font-size: 0.95rem; }
        .btn-primary { background-color: var(--gold); color: var(--navy); }
        .btn-primary:hover { background-color: var(--gold-light); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(201,168,76,0.3); }
        .btn-outline { border: 1px solid var(--border-color); color: var(--text-primary); }
        .btn-outline:hover { border-color: var(--gold); color: var(--gold); transform: translateY(-2px); }
        .blob { position: fixed; width: 350px; height: 350px; background: radial-gradient(circle, rgba(201,168,76,0.12) 0%, transparent 70%); border-radius: 50%; z-index: 0; filter: blur(50px); pointer-events: none; }
        .blob-1 { top: -150px; right: -150px; }
        .blob-2 { bottom: -150px; left: -150px; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-18px); } }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width: 480px) { .cta-group { flex-direction: column; align-items: stretch; } }
    </style>
    <script>(function(){const t=localStorage.getItem('firstmediator_theme')||(window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light');document.documentElement.setAttribute('data-theme',t);})();</script>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="container">
        <h1 class="error-code font-serif">@yield('code')</h1>
        <h2 class="error-title font-serif">@yield('title')</h2>
        <p class="error-desc">@yield('description')</p>
        <div class="cta-group">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
            @else
                <a href="/" class="btn btn-primary">Go to Homepage</a>
            @endauth
            <a href="mailto:support@firstmediator.com" class="btn btn-outline">Contact Support</a>
        </div>
    </div>
</body>
</html>
