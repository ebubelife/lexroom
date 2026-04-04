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
<body class="min-h-screen" style="background-color: var(--bg-primary); color: var(--text-primary);">
    <!-- Top Bar -->
    <header class="w-full py-4 px-6" style="border-bottom: 1px solid var(--border-color);">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <!-- Logo -->
            <a href="/" class="flex items-center">
                <img 
                    src="{{ asset('assets/images/logos/fm-lightmode.png') }}" 
                    alt="First Mediator" 
                    class="h-24"
                    style="display: var(--logo-light-display) !important;"
                >
                <img 
                    src="{{ asset('assets/images/logos/fm-darkmode.png') }}" 
                    alt="First Mediator" 
                    class="h-24"
                    style="display: var(--logo-dark-display) !important;"
                >
            </a>

            <!-- Theme Toggle -->
            <button 
                type="button" 
                id="theme-toggle"
                class="p-2 rounded-lg hover:bg-opacity-10 hover:bg-gray-500 transition-colors"
                aria-label="Toggle theme"
            >
                <svg class="w-5 h-5 theme-icon-sun" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                </svg>
                <svg class="w-5 h-5 theme-icon-moon hidden" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                </svg>
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-md animate-fade-up">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-12 px-6" style="border-top: 1px solid var(--border-color); background-color: var(--bg-primary);">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6 text-sm" style="color: var(--text-secondary);">
            <p>&copy; 2026 FirstMediator &middot; Not legal advice</p>
            <div class="flex flex-wrap justify-center gap-x-6 gap-y-3">
                <a href="{{ route('about') }}" class="hover:text-[var(--gold)] transition-colors">About Us</a>
                <a href="{{ route('privacy') }}" class="hover:text-[var(--gold)] transition-colors">Privacy Policy</a>
                <a href="{{ route('gdpr') }}" class="hover:text-[var(--gold)] transition-colors">GDPR Policy</a>
                <a href="{{ route('terms') }}" class="hover:text-[var(--gold)] transition-colors">Terms of Service</a>
                <a href="{{ route('disclaimer') }}" class="hover:text-[var(--gold)] transition-colors">Disclaimer</a>
                <a href="mailto:info@firstmediator.com" class="hover:text-[var(--gold)] transition-colors">Contact</a>
            </div>
        </div>
    </footer>

    <!-- Theme Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            const html = document.documentElement;
            const sunIcon = document.querySelector('.theme-icon-sun');
            const moonIcon = document.querySelector('.theme-icon-moon');

            function updateTheme() {
                const currentTheme = html.getAttribute('data-theme');
                const isDark = currentTheme === 'dark';
                
                // Update icons
                sunIcon.classList.toggle('hidden', isDark);
                moonIcon.classList.toggle('hidden', !isDark);
                
                // Update logo display
                html.style.setProperty('--logo-light-display', isDark ? 'none' : 'block');
                html.style.setProperty('--logo-dark-display', isDark ? 'block' : 'none');
            }

            themeToggle.addEventListener('click', function() {
                const currentTheme = html.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('firstmediator_theme', newTheme);
                updateTheme();
            });

            // Initialize theme display
            updateTheme();
        });
    </script>
</body>
</html>