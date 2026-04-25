<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard — First Mediator')</title>
    <meta name="description" content="@yield('description', 'First Mediator dashboard - manage your dispute sessions and mediation reports')">

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

        .stats-card-gold {
            background-color: var(--bg-secondary);
            border: 2px solid var(--gold);
            border-radius: 0.75rem;
            padding: 1rem;
            transition: all 0.3s ease;
            min-height: 100px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .stats-card-gold:hover {
            border-color: var(--gold-light);
            transform: translateY(-2px);
        }

        /* Toast Notifications */
        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background-color: var(--navy);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            z-index: 9999;
            animation: slideIn 0.3s ease-out;
            max-width: 400px;
        }

        [data-theme="dark"] .toast {
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-color);
        }

        .toast.success {
            border-left: 4px solid var(--gold);
        }

        .toast.error {
            border-left: 4px solid #DC2626;
        }

        .toast.info {
            border-left: 4px solid #3B82F6;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        .toast.hiding {
            animation: slideOut 0.3s ease-out forwards;
        }

        <!-- Stats cards improvements */
        .stats-card {
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            min-height: 100px;
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
            const theme = localStorage.getItem('firstmediator_theme') || 
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
        class="fixed inset-y-0 left-0 z-50 w-60 transform transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        style="background-color: var(--bg-sidebar); border-right: 1px solid var(--border-color);"
    >
        <!-- Logo -->
        <div class="flex items-center justify-center h-24 px-6 flex-shrink-0" style="border-bottom: 1px solid var(--border-color);">
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <img 
                    src="{{ asset('assets/images/logos/fm-lightmode.png') }}" 
                    alt="First Mediator" 
                    class="h-24 logo-light"
                    style="display: var(--logo-light-display, block);"
                >
                <img 
                    src="{{ asset('assets/images/logos/fm-darkmode.png') }}" 
                    alt="First Mediator" 
                    class="h-24 logo-dark"
                    style="display: var(--logo-dark-display, none);"
                >
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 mt-6 px-3 overflow-y-auto pb-4">
            <div class="space-y-4">
                @auth
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" class="nav-link flex items-center px-3 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'nav-link-active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>

                    <!-- Cases -->
                    <a href="{{ route('rooms.index') }}" class="nav-link flex items-center px-3 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('rooms.*') ? 'nav-link-active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Cases
                    </a>

                    <!-- Evidence Vault -->
                    <a href="{{ route('vault.index') }}" class="nav-link flex items-center px-3 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('vault.*') ? 'nav-link-active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Evidence Vault
                    </a>

                    <!-- Wallet -->
                    <a href="{{ route('wallet.index') }}" class="nav-link flex items-center px-3 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('wallet.*') ? 'nav-link-active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        Wallet
                    </a>

                    <!-- Referrals -->
                    <a href="{{ route('referrals.index') }}" class="nav-link flex items-center px-3 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('referrals.*') ? 'nav-link-active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Referrals
                        @if(auth()->user()->wallet?->referral_minutes > 0)
                            <span class="ml-auto text-xs px-1.5 py-0.5 rounded-full font-medium" style="background-color: rgba(201,168,76,0.15); color: var(--gold);">{{ auth()->user()->wallet->referral_minutes }}m</span>
                        @endif
                    </a>

                    <!-- Legal Reports -->
                    <a href="{{ route('reports.index') }}" class="nav-link flex items-center px-3 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('reports.*') ? 'nav-link-active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Reports
                    </a>

                    <!-- FM Refer -->
                    <a href="{{ route('fmrefer.index') }}" class="nav-link flex items-center px-3 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('fmrefer.*') ? 'nav-link-active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l3-1m-3 1l-3-1"></path>
                        </svg>
                        FM Refer
                    </a>

                    <!-- Settings -->
                    <a href="{{ route('settings.index') }}" class="nav-link flex items-center px-3 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('settings.*') ? 'nav-link-active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Settings
                    </a>
                @endauth

                @guest
                    <div class="px-3 py-6 mt-4 rounded-xl border border-dashed text-center" style="border-color: var(--gold); background: rgba(201, 168, 76, 0.05);">
                        <p class="text-xs font-serif mb-3" style="color: var(--text-primary);">New to First Mediator?</p>
                        <a href="{{ route('register') }}" class="block w-full px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest text-white transition-all hover:scale-105" style="background: var(--gold);">
                            Create Account
                        </a>
                        <a href="{{ route('login') }}" class="block mt-2 text-xs opacity-60 hover:opacity-100 transition-opacity" style="color: var(--text-primary);">
                            Already have one? Login
                        </a>
                    </div>
                @endguest
            </div>
        </nav>

        @auth
            <!-- User Info -->
            <div class="flex-shrink-0 p-4" style="border-top: 1px solid var(--border-color);">
                <a href="{{ route('settings.index') }}" class="flex items-center hover:opacity-80 transition-opacity">
                    @if(auth()->user()->profile_image_url)
                        <img src="{{ auth()->user()->profile_image_url }}" alt="{{ auth()->user()->name }}" class="w-10 h-10 rounded-full object-cover border-2" style="border-color: var(--gold);">
                    @else
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-medium" style="background-color: var(--gold);">
                            {{ auth()->user()->initials }}
                        </div>
                    @endif
                    <div class="ml-3 flex-1 min-w-0">
                        <p class="text-sm font-medium truncate" style="color: var(--text-primary);">{{ auth()->user()->name }}</p>
                        <p class="text-xs truncate" style="color: var(--text-secondary);">{{ auth()->user()->email }}</p>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full px-3 py-2 rounded-lg text-sm font-medium transition-colors hover:bg-opacity-10 hover:bg-red-500" style="color: #DC2626; border: 1px solid var(--border-color);">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        @endauth
    </div>

    <!-- Main Content -->
    <div class="lg:pl-60 flex-1 flex flex-col min-h-screen">
        <!-- Top Bar -->
        <header class="h-16 flex items-center justify-between px-4 lg:px-6 flex-shrink-0" style="background-color: var(--bg-primary); border-bottom: 1px solid var(--border-color);">
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
                @auth
                    <!-- Wallet Credits -->
                    <a href="{{ route('wallet.index') }}" class="hidden sm:flex px-3 py-1.5 rounded-lg text-sm font-medium transition-colors hover:opacity-90" style="background-color: var(--gold); color: var(--white);">
                        ${{ number_format(auth()->user()->wallet?->credits_balance ?? 0) }} credits
                    </a>

                    <!-- Create Room Button -->
                    <a href="{{ route('rooms.create') }}" class="hidden sm:flex px-4 py-2 rounded-lg text-sm font-medium transition-colors hover:opacity-90" style="background-color: var(--gold); color: var(--white);">
                        Create a Room
                    </a>

                    <!-- Mobile Create Room Button -->
                    <a href="{{ route('rooms.create') }}" class="sm:hidden p-2 rounded-lg transition-colors hover:opacity-90" style="background-color: var(--gold); color: var(--white);" title="Create Room">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </a>
                @endauth

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

                @auth
                    <!-- User Avatar -->
                    <a href="{{ route('settings.index') }}" class="hover:opacity-80 transition-opacity" title="Profile Settings">
                        @if(auth()->user()->profile_image_url)
                            <img src="{{ auth()->user()->profile_image_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover border-2" style="border-color: var(--gold);">
                        @else
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium" style="background-color: var(--gold);">
                                {{ auth()->user()->initials }}
                            </div>
                        @endif
                    </a>
                @endauth

                @guest
                    <a href="{{ route('login') }}" class="text-sm font-medium hover:opacity-80 transition-opacity px-3 py-1.5 rounded-lg" style="color: var(--gold); border: 1px solid var(--gold);">
                        Login
                    </a>
                @endguest
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 px-4 lg:px-6 pt-4 pb-6">
            @yield('content')
        </main>
    </div>

    <!-- Theme Toggle Script -->
    <script>
        // Toast notification function
        window.showToast = function(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            const icon = type === 'success' ? 
                '<svg class="w-5 h-5" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' :
                type === 'error' ?
                '<svg class="w-5 h-5" style="color: #DC2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' :
                '<svg class="w-5 h-5" style="color: #3B82F6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
            
            toast.innerHTML = `
                ${icon}
                <span style="flex: 1;">${message}</span>
                <button onclick="this.parentElement.remove()" style="opacity: 0.7; hover:opacity: 1;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('hiding');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        };

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