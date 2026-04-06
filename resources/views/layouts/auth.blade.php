<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'First Mediator')</title>
    <meta name="description" content="@yield('description', 'AI-assisted legal dispute mediation platform')">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/images/logos/FM_Icon.svg') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital,wght@0,400;1,400&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/shared-layout.css') }}">
    <script src="{{ asset('js/shared-layout.js') }}"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
        }

        html {
            --logo-light-display: block;
            --logo-dark-display: none;
        }

        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }

        body {
            font-family: 'DM Sans', sans-serif;
        }

        .font-serif {
            font-family: 'Instrument Serif', serif;
        }

        /* Light mode (default) */
        [data-theme="light"] {
            --bg-primary: var(--white);
            --bg-secondary: var(--off-white);
            --text-primary: var(--navy);
            --text-secondary: var(--gray-600);
            --border-color: var(--gray-200);
        }

        /* Dark mode */
        [data-theme="dark"] {
            --bg-primary: var(--navy);
            --bg-secondary: #1a2332;
            --text-primary: var(--white);
            --text-secondary: var(--gray-400);
            --border-color: #2a3441;
        }

        .animate-fade-up {
            animation: fadeUp 0.6s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Alpine.js cloak */
        [x-cloak] {
            display: none !important;
        }
    </style>

    <!-- Theme persistence script - runs before page renders -->
    <script>
        (function() {
            const theme = localStorage.getItem('firstmediator_theme') || 
                         (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
            
            const isDark = theme === 'dark';
            document.documentElement.style.setProperty('--logo-light-display', isDark ? 'none' : 'block');
            document.documentElement.style.setProperty('--logo-dark-display', isDark ? 'block' : 'none');
        })();
    </script>
</head>
<body class="min-h-screen flex flex-col" style="background-color: var(--bg-primary); color: var(--text-primary);">
    <!-- Top Bar -->
    @include('partials.navbar')

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center px-6 py-12" style="margin-top: 72px; min-height: calc(100vh - 72px - 300px);">
        <div class="w-full max-w-md animate-fade-up">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    @include('partials.footer')

    <!-- JavaScript handled in shared-layout.js -->
</body>
</html>