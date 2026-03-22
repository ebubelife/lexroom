<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard — LexRoom')</title>
    <meta name="description" content="@yield('description', 'LexRoom dashboard - manage your dispute sessions and mediation reports')">

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
            --bg-sidebar: var(--white);
            --text-primary: var(--navy);
            --text-secondary: var(--gray-600);
            --border-color: var(--gray-200);
        }

        /* Dark mode */
        [data-theme="dark"] {
            --bg-primary: var(--navy);
            --bg-secondary: #1a2332;
            --bg-sidebar: #0B1929;
            --text-primary: var(--white);
            --text-secondary: var(--gray-400);
            --border-color: #2a3441;
        }

        /* Alpine.js cloak */
        [x-cloak] {
            display: none !important;
        }

        /* Animations */
        .animate-fade-up {
            animation: fadeUp 0.6s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        .animate-fade-up-delay-1 { animation-delay: 0.1s; }
        .animate-fade-up-delay-2 { animation-delay: 0.2s; }
        .animate-fade-up-delay-3 { animation-delay: 0.3s; }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .animate-pulse-gold {
            animation: pulseGold 2s ease-in-out infinite;
        }

        @keyframes pulseGold {
            0%, 100% { box-shadow: 0 0 0 0 rgba(201, 168, 76, 0.4); }
            50% { box-shadow: 0 0 0 8px rgba(201, 168, 76, 0); }
        }

        /* Hover effects */
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        [data-theme="dark"] .hover-lift:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        /* Sidebar improvements */
        .nav-link-active {
            border-left: 3px solid var(--gold);
            background-color: rgba(201, 168, 76, 0.1);
            color: var(--gold) !important;
        }

        .nav-link {
            transition: all 0.2s ease;
            color: var(--text-secondary);
        }

        .nav-link:hover {
            background-color: rgba(107, 107, 104, 0.1);
            color: var(--text-primary);
        }

        [data-theme="dark"] .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        /* Stats cards improvements */
        .stats-card {
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.25rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        [data-theme="dark"] .stats-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
    </style>

    <!-- Theme persistence script -->
    <script>
        (function() {
            const theme = localStorage.getItem('lexroom_theme') || 
                         (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>
<body class="min-h-screen" style="background-color: var(--bg-primary); color: var(--text-primary);" x-data="{ sidebarOpen: false }">
    <!-- Mobile Sidebar Overlay -->
    <div 
        x-show="sidebarOpen" 
        x-cloak
        @click="sidebarOpen = false"
        class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    ></div>

    <!-- Sidebar -->
    <div 
        class="fixed inset-y-0 left-0 z-50 w-60 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        style="background-color: var(--bg-sidebar); border-right: 1px solid var(--border-color);"
    >
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 px-6 flex-shrink-0" style="border-bottom: 1px solid var(--border-color);">
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <img 
                    src="{{ asset('assets/images/logos/FM_Logo_Dark.svg') }}" 
                    alt="LexRoom" 
                    class="h-8 logo-light"
                    style="display: var(--logo-light-display, block);"
                >
                <img 
                    src="{{ asset('assets/images/logos/FM_Logo_Light.svg') }}" 
                    alt="LexRoom" 
                    class="h-8 logo-dark"
                    style="display: var(--logo-dark-display, none);"
                >
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 mt-6 px-3 overflow-y-auto">
            <div class="space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="nav-link flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'nav-link-active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                    </svg>
                    Dashboard
                </a>

                <!-- My Rooms -->
                <a href="{{ route('rooms.index') }}" class="nav-link flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('rooms.*') ? 'nav-link-active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M10.5 3L12 2l1.5 1H21v6H3V3h7.5z"></path>
                    </svg>
                    My Rooms
                </a>

                <!-- Reports -->
                <a href="{{ route('reports.index') }}" class="nav-link flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('reports.*') ? 'nav-link-active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Reports
                </a>

                <!-- Wallet & Credits -->
                <a href="{{ route('wallet.index') }}" class="nav-link flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('wallet.*') ? 'nav-link-active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    Wallet & Credits
                </a>

                <!-- LexRefer -->
                <a href="{{ route('lexrefer.index') }}" class="nav-link flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('lexrefer.*') ? 'nav-link-active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l3-1m-3 1l-3-1"></path>
                    </svg>
                    LexRefer
                </a>

                <!-- Settings -->
                <a href="{{ route('settings.index') }}" class="nav-link flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('settings.*') ? 'nav-link-active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Settings
                </a>
            </div>
        </nav>

        <!-- User Info -->
        <div class="flex-shrink-0 p-4" style="border-top: 1px solid var(--border-color);">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-medium" style="background-color: var(--gold);">
                    {{ auth()->user()->initials }}
                </div>
                <div class="ml-3 flex-1 min-w-0">
                    <p class="text-sm font-medium truncate" style="color: var(--text-primary);">{{ auth()->user()->name }}</p>
                    <p class="text-xs truncate" style="color: var(--text-secondary);">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="ml-2">
                    @csrf
                    <button type="submit" class="p-1 rounded hover:bg-opacity-10 hover:bg-gray-500" title="Logout">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="lg:pl-60">
        <!-- Top Bar -->
        <header class="h-16 flex items-center justify-between px-4 lg:px-6" style="background-color: var(--bg-primary); border-bottom: 1px solid var(--border-color);">
            <!-- Left: Mobile menu + Page title -->
            <div class="flex items-center">
                <button 
                    @click="sidebarOpen = true"
                    class="lg:hidden p-2 rounded-md hover:bg-opacity-10 hover:bg-gray-500 mr-3"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="text-xl font-semibold" style="color: var(--text-primary);">@yield('page-title', 'Dashboard')</h1>
            </div>

            <!-- Right: Wallet + Create Room + Theme + User -->
            <div class="flex items-center space-x-2 lg:space-x-4">
                <!-- Wallet Credits -->
                <a href="{{ route('wallet.index') }}" class="hidden sm:flex px-3 py-1.5 rounded-full text-sm font-medium transition-colors hover:opacity-90" style="background-color: var(--gold); color: var(--white);">
                    ₦{{ number_format(auth()->user()->wallet?->credits_balance ?? 0) }} credits
                </a>

                <!-- Create Room Button -->
                <a href="#" class="hidden sm:flex px-4 py-2 rounded-lg text-sm font-medium transition-colors hover:opacity-90" style="background-color: var(--gold); color: var(--white);">
                    Create a Room
                </a>

                <!-- Mobile Create Room Button -->
                <a href="#" class="sm:hidden p-2 rounded-lg transition-colors hover:opacity-90" style="background-color: var(--gold); color: var(--white);" title="Create Room">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
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

                <!-- User Avatar -->
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium" style="background-color: var(--gold);">
                    {{ auth()->user()->initials }}
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-4 lg:p-6">
            @yield('content')
        </main>
    </div>

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
                localStorage.setItem('lexroom_theme', newTheme);
                updateTheme();
            });

            // Initialize theme display
            updateTheme();
        });
    </script>
</body>
</html>