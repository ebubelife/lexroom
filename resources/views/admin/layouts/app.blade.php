<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — FirstMediator</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/images/logos/FM_Icon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --navy: #0D1B2A;
            --gold: #C9A84C;
            --gold-light: #E8C96A;
            --gold-pale: #F5EDD6;
        }

        /* Admin always uses dark theme */
        [data-theme="dark"] {
            --bg-primary: #0D1B2A;
            --bg-secondary: #111f30;
            --bg-sidebar: #091523;
            --bg-card: #111f30;
            --text-primary: #F0F4F8;
            --text-secondary: #8A9BB0;
            --border-color: #1e2f42;
        }

        body { font-family: 'DM Sans', sans-serif; background-color: var(--bg-primary); color: var(--text-primary); }
        .font-serif { font-family: 'Instrument Serif', serif; }
        [x-cloak] { display: none !important; }

        .nav-link {
            display: flex; align-items: center;
            padding: 0.6rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-secondary);
            transition: all 0.15s ease;
            gap: 0.625rem;
        }
        .nav-link:hover { background: rgba(255,255,255,0.05); color: var(--text-primary); }
        .nav-link.active {
            background: rgba(201,168,76,0.12);
            color: var(--gold);
            border-left: 3px solid var(--gold);
            padding-left: calc(0.75rem - 3px);
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.25rem;
            transition: border-color 0.2s;
        }
        .stat-card:hover { border-color: rgba(201,168,76,0.4); }

        .badge {
            display: inline-flex; align-items: center;
            padding: 0.2rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .toast {
            position: fixed; bottom: 1.5rem; right: 1.5rem;
            background: var(--bg-card); border: 1px solid var(--border-color);
            padding: 0.875rem 1.25rem; border-radius: 0.75rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
            display: flex; align-items: center; gap: 0.75rem;
            z-index: 9999; max-width: 380px;
            animation: slideIn 0.25s ease-out;
        }
        .toast.success { border-left: 3px solid var(--gold); }
        .toast.error   { border-left: 3px solid #EF4444; }
        .toast.info    { border-left: 3px solid #3B82F6; }

        @keyframes slideIn  { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(120%); opacity: 0; } }
        .toast.hiding { animation: slideOut 0.25s ease-out forwards; }

        .data-table th {
            padding: 0.75rem 1rem;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--text-secondary);
            border-bottom: 1px solid var(--border-color);
            text-align: left;
        }
        .data-table td {
            padding: 0.875rem 1rem;
            font-size: 0.875rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover td { background: rgba(255,255,255,0.02); }
    </style>
</head>
<body x-data="{ sidebarOpen: false }">

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/60 z-40 lg:hidden"
         x-transition:enter="transition-opacity duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-200"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    </div>

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-50 w-56 flex flex-col transition-transform duration-300 lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           style="background: var(--bg-sidebar); border-right: 1px solid var(--border-color);">

        {{-- Logo --}}
        <div class="h-14 flex items-center px-5 flex-shrink-0" style="border-bottom: 1px solid var(--border-color);">
            <span class="font-serif text-lg" style="color: var(--gold);">FirstMediator</span>
            <span class="ml-2 text-xs px-1.5 py-0.5 rounded" style="background: rgba(201,168,76,0.15); color: var(--gold);">Admin</span>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <div class="pt-3 pb-1">
                <p class="px-3 text-xs font-semibold uppercase tracking-widest" style="color: var(--text-secondary); opacity: 0.5;">Manage</p>
            </div>

            <a href="{{ route('admin.users.index') }}"
               class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Users
            </a>

            <a href="{{ route('admin.rooms.index') }}"
               class="nav-link {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Rooms
            </a>

            <a href="{{ route('admin.billing.index') }}"
               class="nav-link {{ request()->routeIs('admin.billing.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Billing
            </a>

            <a href="{{ route('admin.files.index') }}"
               class="nav-link {{ request()->routeIs('admin.files.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Files
            </a>

            <a href="{{ route('admin.reports.index') }}"
               class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Reports
            </a>

            <div class="pt-3 pb-1">
                <p class="px-3 text-xs font-semibold uppercase tracking-widest" style="color: var(--text-secondary); opacity: 0.5;">Finance</p>
            </div>

            <a href="{{ route('admin.wallets.index') }}"
               class="nav-link {{ request()->routeIs('admin.wallets.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Wallets
            </a>

            <a href="{{ route('admin.referrals.index') }}"
               class="nav-link {{ request()->routeIs('admin.referrals.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
                Referrals
            </a>

            <div class="pt-3 pb-1">
                <p class="px-3 text-xs font-semibold uppercase tracking-widest" style="color: var(--text-secondary); opacity: 0.5;">System</p>
            </div>

            <a href="{{ route('admin.settings.index') }}"
               class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Settings
            </a>
        </nav>

        {{-- Admin user --}}
        <div class="flex-shrink-0 p-3" style="border-top: 1px solid var(--border-color);">
            <div class="flex items-center gap-2.5 px-2 py-2 rounded-lg" style="background: rgba(255,255,255,0.03);">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                     style="background: var(--gold); color: var(--navy);">
                    {{ strtoupper(substr(auth('admin')->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium truncate" style="color: var(--text-primary);">{{ auth('admin')->user()->name }}</p>
                    <p class="text-xs truncate" style="color: var(--text-secondary);">{{ auth('admin')->user()->role }}</p>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" title="Logout" class="p-1 rounded hover:bg-white/10 transition-colors" style="color: var(--text-secondary);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main --}}
    <div class="lg:pl-56 flex flex-col min-h-screen">
        {{-- Topbar --}}
        <header class="h-14 flex items-center justify-between px-4 lg:px-6 flex-shrink-0"
                style="background: var(--bg-sidebar); border-bottom: 1px solid var(--border-color);">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="lg:hidden p-1.5 rounded-md hover:bg-white/10">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-base font-semibold" style="color: var(--text-primary);">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-3">
                @if(session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', () => showToast(@json(session('success')), 'success'));
                    </script>
                @endif
                @if(session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', () => showToast(@json(session('error')), 'error'));
                    </script>
                @endif
                <span class="text-xs px-2 py-1 rounded" style="background: rgba(201,168,76,0.12); color: var(--gold);">
                    Admin Panel
                </span>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 p-4 lg:p-6">
            @yield('content')
        </main>
    </div>

    <script>
        window.showToast = function(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `<span style="flex:1;font-size:0.875rem;">${message}</span>
                <button onclick="this.parentElement.remove()" style="opacity:0.5;hover:opacity:1;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>`;
            document.body.appendChild(toast);
            setTimeout(() => { toast.classList.add('hiding'); setTimeout(() => toast.remove(), 250); }, 3500);
        };
    </script>
</body>
</html>
