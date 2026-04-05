<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found — First Mediator</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital,wght@0,400;1,400&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet" />

    <style>
        :root {
            --navy: #0D1B2A;
            --gold: #C9A84C;
            --gold-light: #E8C96A;
            --gold-pale: #F5EDD6;
            --white: #ffffff;
            --off-white: #FAFAF8;
            --gray-100: #F4F4F2;
            --gray-200: #E8E8E4;
            --gray-400: #ADADAA;
            --gray-600: #6B6B68;
            --gray-800: #2E2E2C;
            --bg-primary: var(--white);
            --text-primary: var(--navy);
            --text-secondary: var(--gray-600);
            --border-color: var(--gray-200);
        }

        [data-theme="dark"] {
            --bg-primary: var(--navy);
            --bg-secondary: #1a2332;
            --text-primary: var(--white);
            --text-secondary: var(--gray-400);
            --border-color: #2a3441;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        .font-serif {
            font-family: 'Instrument Serif', serif;
        }

        .container {
            max-width: 600px;
            width: 100%;
            padding: 2rem;
            text-align: center;
            position: relative;
            z-index: 10;
        }

        .error-code {
            font-size: clamp(6rem, 15vw, 10rem);
            line-height: 1;
            margin-bottom: 1rem;
            color: var(--gold);
            font-style: italic;
            animation: float 4s ease-in-out infinite;
            filter: drop-shadow(0 10px 15px rgba(201,168,76,0.2));
        }

        .error-title {
            font-size: clamp(2rem, 5vw, 3rem);
            margin-bottom: 1.5rem;
            opacity: 0;
            animation: fadeUp 0.8s ease-out forwards;
            animation-delay: 0.2s;
        }

        .error-desc {
            font-size: 1.1rem;
            color: var(--text-secondary);
            margin-bottom: 3rem;
            max-width: 450px;
            margin-left: auto;
            margin-right: auto;
            opacity: 0;
            animation: fadeUp 0.8s ease-out forwards;
            animation-delay: 0.4s;
        }

        .cta-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            opacity: 0;
            animation: fadeUp 0.8s ease-out forwards;
            animation-delay: 0.6s;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }

        .btn-primary {
            background-color: var(--gold);
            color: var(--navy);
        }

        .btn-primary:hover {
            background-color: var(--gold-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(201,168,76,0.3);
        }

        .btn-outline {
            border: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .btn-outline:hover {
            border-color: var(--gold);
            color: var(--gold);
            transform: translateY(-2px);
        }

        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Decorative blobs */
        .blob {
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(201,168,76,0.15) 0%, rgba(201,168,76,0) 70%);
            border-radius: 50%;
            z-index: -1;
            filter: blur(40px);
        }

        .blob-1 { top: -150px; right: -150px; }
        .blob-2 { bottom: -150px; left: -150px; }

        @media (max-width: 480px) {
            .cta-group {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body x-data>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="container">
        <h1 class="error-code font-serif">404</h1>
        <h2 class="error-title font-serif">The page has evaporated.</h2>
        <p class="error-desc">We couldn't find the page you're looking for. It might have been moved, deleted, or never existed in the first place.</p>
        
        <div class="cta-group">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
            @else
                <a href="/" class="btn btn-primary">Go to Homepage</a>
            @endauth
            
            <a href="mailto:support@firstmediator.com" class="btn btn-outline">Contact Support</a>
        </div>
    </div>

    <!-- Theme persistence script -->
    <script>
        (function() {
            const theme = localStorage.getItem('firstmediator_theme') || 
                         (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</body>
</html>
