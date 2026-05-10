@extends('layouts.app')

@section('title', 'Dashboard — First Mediator')
@section('page-title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ modal: null }">

    {{-- Room Detail Modal --}}
    <div x-show="modal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.5);"
         @click.self="modal = null"
         @keydown.escape.window="modal = null">
        <div class="w-full max-w-lg rounded-2xl shadow-2xl p-6" style="background-color: var(--bg-secondary);" @click.stop>
            <div class="flex items-start justify-between mb-4">
                <div>
                    <span class="px-2 py-1 rounded text-xs font-medium" x-bind:style="`background-color: ${modal?.badge_bg}; color: ${modal?.badge_text};`" x-text="modal?.category"></span>
                    <span class="ml-2 px-2 py-1 rounded-full text-xs font-medium" x-bind:style="`background-color: ${modal?.status_bg}; color: ${modal?.status_text};`" x-text="modal?.status"></span>
                </div>
                <button @click="modal = null" style="color: var(--text-secondary);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <h3 class="font-serif text-lg mb-4" style="color: var(--text-primary);" x-text="modal?.title"></h3>

            <div class="space-y-3 mb-5">
                <div class="flex justify-between text-sm">
                    <span style="color: var(--text-secondary);">Jurisdiction</span>
                    <span style="color: var(--text-primary);" x-text="modal?.jurisdiction"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span style="color: var(--text-secondary);">Duration</span>
                    <span style="color: var(--text-primary);" x-text="modal?.duration + ' minutes'"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span style="color: var(--text-secondary);">Payment</span>
                    <span style="color: var(--text-primary);" x-text="modal?.payment_type"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span style="color: var(--text-secondary);">Other Party</span>
                    <span style="color: var(--text-primary);" x-text="modal?.party_b"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span style="color: var(--text-secondary);">Created</span>
                    <span style="color: var(--text-primary);" x-text="modal?.created_at"></span>
                </div>
            </div>

            <div class="p-3 rounded-lg mb-5 text-sm" style="background-color: var(--bg-primary); color: var(--text-secondary);" x-data="{ expanded: false }">
                <p x-text="expanded ? modal?.case_summary : (modal?.case_summary?.substring(0, 150) + (modal?.case_summary?.length > 150 ? '...' : ''))"></p>
                <button x-show="modal?.case_summary?.length > 150" @click="expanded = !expanded" class="text-xs mt-1 font-medium" style="color: var(--gold);" x-text="expanded ? 'See less \u2191' : 'See more \u2193'"></button>
            </div>

            <div class="flex gap-3">
                <template x-if="modal?.status_raw === 'pending'">
                    <a :href="modal?.checkout_url" class="flex-1 text-center py-2 rounded-lg text-sm font-medium text-white" style="background-color: #0D1B2A;">Complete Payment</a>
                </template>
                <template x-if="modal?.status_raw === 'active'">
                    <a :href="modal?.room_url" class="flex-1 text-center py-2 rounded-lg text-sm font-medium text-white" style="background-color: var(--gold);">Enter Room</a>
                </template>
                <template x-if="modal?.status_raw === 'completed'">
                    <a :href="modal?.report_url" class="flex-1 text-center py-2 rounded-lg text-sm font-medium text-white" style="background-color: var(--gold);">Download Report</a>
                </template>
                <button @click="modal = null" class="flex-1 py-2 rounded-lg text-sm font-medium" style="border: 1px solid var(--border-color); color: var(--text-secondary);">Close</button>
            </div>
        </div>
    </div>
    <!-- Welcome Header -->
    <div class="mb-3 animate-fade-up">
        <h1 class="text-2xl lg:text-3xl font-serif mb-1" style="color: var(--text-primary);">
            Welcome back, {{ auth()->user()->first_name }} 👋
        </h1>
        <p class="text-base lg:text-lg" style="color: var(--text-secondary);">
            Here's where your disputes stand today.
        </p>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-4">
        <!-- Total Sessions -->
        <div class="stats-card-gold animate-fade-up animate-fade-up-delay-1">
            <div class="flex items-start justify-between mb-2">
                <div class="p-2 rounded-lg" style="background-color: rgba(201, 168, 76, 0.1);">
                    <svg class="w-5 h-5" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10a2 2 0 002 2h4a2 2 0 002-2V11m-6 0V9a2 2 0 012-2h4a2 2 0 012 2v2m-6 0h8"></path>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-2xl lg:text-3xl font-bold mb-0.5" style="color: var(--text-primary);">{{ $stats['total'] }}</p>
                <p class="text-xs lg:text-sm font-medium" style="color: var(--text-secondary);">Total Sessions</p>
            </div>
        </div>

        <!-- Active Sessions -->
        <div class="stats-card-gold animate-fade-up animate-fade-up-delay-2">
            <div class="flex items-start justify-between mb-2">
                <div class="p-2 rounded-lg" style="background-color: rgba(34, 197, 94, 0.1);">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-2xl lg:text-3xl font-bold mb-0.5" style="color: var(--text-primary);">{{ $stats['active'] }}</p>
                <p class="text-xs lg:text-sm font-medium" style="color: var(--text-secondary);">Active Sessions</p>
            </div>
        </div>

        <!-- Resolved -->
        <div class="stats-card-gold animate-fade-up animate-fade-up-delay-3">
            <div class="flex items-start justify-between mb-2">
                <div class="p-2 rounded-lg" style="background-color: rgba(34, 197, 94, 0.1);">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-2xl lg:text-3xl font-bold mb-0.5" style="color: var(--text-primary);">{{ $stats['resolved'] }}</p>
                <p class="text-xs lg:text-sm font-medium" style="color: var(--text-secondary);">Resolved</p>
            </div>
        </div>

        <!-- Credits Balance -->
        <div class="stats-card-gold animate-fade-up" style="animation-delay: 0.4s;">
            <div class="flex items-start justify-between mb-2">
                <div class="p-2 rounded-lg" style="background-color: rgba(201, 168, 76, 0.1);">
                    <svg class="w-5 h-5" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-2xl lg:text-3xl font-bold mb-0.5" style="color: var(--text-primary);">{{ \App\Models\PlatformSetting::currencySymbol() }}{{ number_format($stats['credits']) }}</p>
                <p class="text-xs lg:text-sm font-medium" style="color: var(--text-secondary);">Credits Balance</p>
            </div>
        </div>
    </div>

    @if($activeSessions->count() > 0)
    <!-- Active Sessions Needing Attention -->
    <div class="mb-6 animate-fade-up" style="animation-delay: 0.5s;">
        <h2 class="text-lg lg:text-xl font-serif mb-3" style="color: var(--text-primary);">Needs your attention</h2>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @foreach($activeSessions as $session)
            <div class="p-6 rounded-xl hover-lift overflow-hidden" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color);">
                <!-- Category Badge -->
                <div class="flex items-center justify-between mb-4">
                    <span class="px-3 py-1 rounded-full text-xs font-medium" style="background-color: {{ $session->category_badge_color['bg'] }}; color: {{ $session->category_badge_color['text'] }};">
                        {{ ucfirst($session->category) }}
                    </span>
                    @if($session->status === 'active')
                    <span class="px-2 py-1 rounded-full text-xs font-medium animate-pulse-gold" style="background-color: {{ $session->status_badge_color['bg'] }}; color: {{ $session->status_badge_color['text'] }};">
                        {{ ucfirst(str_replace('_', ' ', $session->status)) }}
                    </span>
                    @else
                    <span class="px-2 py-1 rounded-full text-xs font-medium" style="background-color: {{ $session->status_badge_color['bg'] }}; color: {{ $session->status_badge_color['text'] }};">
                        {{ ucfirst(str_replace('_', ' ', $session->status)) }}
                    </span>
                    @endif
                </div>

                <h3 class="font-medium mb-2 truncate" style="color: var(--text-primary);">
                    {{ $session->case_summary ? Str::limit($session->case_summary, 50) : ucfirst($session->category) . ' Dispute' }}
                </h3>

                <p class="text-sm mb-4 truncate" style="color: var(--text-secondary);">
                    Other party: {{ $session->partyB?->name ?? 'Waiting for Party B' }}
                </p>

                @if($session->status === 'active')
                <a href="{{ route('rooms.show', $session->uuid) }}" class="w-full inline-flex justify-center py-2 px-4 rounded-lg text-sm font-medium transition-colors hover:opacity-90 mb-2" style="background-color: var(--gold); color: var(--white);">
                    Continue Session
                </a>
                @elseif($session->status === 'pending')
                <a href="{{ route('payment.checkout', $session->id) }}" class="w-full inline-flex justify-center py-2 px-4 rounded-lg text-sm font-medium transition-colors hover:opacity-90 mb-2" style="background-color: #0D1B2A; color: #ffffff;">
                    Complete Payment
                </a>
                @elseif($session->status === 'completed')
                <a href="{{ route('reports.index') }}" class="w-full inline-flex justify-center py-2 px-4 rounded-lg text-sm font-medium transition-colors hover:opacity-90 mb-2" style="background-color: var(--gold); color: var(--white);">
                    Download Report
                </a>
                @else
                <a href="{{ route('rooms.show', $session->uuid) }}" class="w-full inline-flex justify-center py-2 px-4 rounded-lg text-sm font-medium transition-colors hover:bg-opacity-10 hover:bg-gray-500 mb-2" style="border: 1px solid var(--border-color); color: var(--text-primary);">
                    Resend Invite
                </a>
                @endif

                {{-- View Details → opens modal --}}
                <button @click="modal = {
                    category: '{{ ucfirst($session->category) }}',
                    badge_bg: '{{ $session->category_badge_color['bg'] }}',
                    badge_text: '{{ $session->category_badge_color['text'] }}',
                    status: '{{ ucfirst(str_replace('_', ' ', $session->status)) }}',
                    status_raw: '{{ $session->status }}',
                    status_bg: '{{ $session->status_badge_color['bg'] }}',
                    status_text: '{{ $session->status_badge_color['text'] }}',
                    title: '{{ addslashes($session->title ?? ucfirst($session->category) . ' Dispute') }}',
                    case_summary: '{{ addslashes($session->case_summary ?? '') }}',
                    jurisdiction: '{{ $session->jurisdiction }}',
                    duration: '{{ $session->duration }}',
                    payment_type: '{{ ucfirst($session->payment_type) }}',
                    party_b: '{{ addslashes($session->partyB?->name ?? $session->party_b_email ?? 'Pending') }}',
                    created_at: '{{ $session->created_at->format('M j, Y') }}',
                    checkout_url: '{{ route('payment.checkout', $session->id) }}',
                    room_url: '{{ route('rooms.show', $session->uuid) }}',
                    report_url: '{{ route('reports.index') }}',
                }" class="w-full inline-flex justify-center py-2 px-4 rounded-lg text-sm font-medium mt-1 transition-colors hover:opacity-80" style="color: var(--gold);">
                    View Details →
                </button>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($stats['total'] > 0)
    <!-- My Rooms Tabs -->
    <div class="animate-fade-up" style="animation-delay: 0.6s;" x-data="{ activeTab: 'my-rooms' }">
        <!-- Tab Headers -->
        <div class="flex space-x-1 mb-6" style="border-bottom: 1px solid var(--border-color);">
            <button 
                @click="activeTab = 'my-rooms'"
                :class="activeTab === 'my-rooms' ? 'border-b-2 font-medium' : ''"
                class="px-4 py-2 text-sm transition-colors"
                :style="activeTab === 'my-rooms' ? 'border-color: var(--gold); color: var(--gold);' : 'color: var(--text-secondary);'"
            >
                My Rooms ({{ $myRooms->total() }})
            </button>
            <button 
                @click="activeTab = 'invited'"
                :class="activeTab === 'invited' ? 'border-b-2 font-medium' : ''"
                class="px-4 py-2 text-sm transition-colors"
                :style="activeTab === 'invited' ? 'border-color: var(--gold); color: var(--gold);' : 'color: var(--text-secondary);'"
            >
                Invited ({{ $invitedRooms->total() }})
            </button>
        </div>

        <!-- My Rooms Tab Content -->
        <div x-show="activeTab === 'my-rooms'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
            @if($myRooms->count() > 0)
            
            <!-- Mobile Card View -->
            <div class="block md:hidden space-y-3">
                @foreach($myRooms as $room)
                <div class="p-4 rounded-xl" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color);">
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2 py-1 rounded text-xs font-medium" style="background-color: {{ $room->category_badge_color['bg'] }}; color: {{ $room->category_badge_color['text'] }};">
                            {{ ucfirst($room->category) }}
                        </span>
                        <span class="px-2 py-1 rounded-full text-xs font-medium" style="background-color: {{ $room->status_badge_color['bg'] }}; color: {{ $room->status_badge_color['text'] }};">
                            {{ ucfirst(str_replace('_', ' ', $room->status)) }}
                        </span>
                    </div>
                    <p class="text-sm font-medium mb-2" style="color: var(--text-primary);">
                        {{ $room->case_summary ? Str::limit($room->case_summary, 60) : ucfirst($room->category) . ' Dispute' }}
                    </p>
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <p class="text-xs" style="color: var(--text-secondary);">Other Party</p>
                            <p class="text-sm" style="color: var(--text-primary);">{{ $room->partyB?->name ?? 'Pending' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs" style="color: var(--text-secondary);">Date</p>
                            <p class="text-sm" style="color: var(--text-primary);">{{ $room->created_at->format('M j, Y') }}</p>
                        </div>
                    </div>
                    @if($room->status === 'active')
                    <a href="{{ route('rooms.show', $room->uuid) }}" class="block w-full text-center px-3 py-2 rounded-lg text-sm font-medium transition-colors hover:opacity-90" style="background-color: var(--gold); color: var(--white);">
                        Enter Room
                    </a>
                    @elseif($room->status === 'pending')
                    <a href="{{ route('payment.checkout', $room->id) }}" class="block w-full text-center px-3 py-2 rounded-lg text-sm font-medium transition-colors hover:opacity-90" style="background-color: #0D1B2A; color: #ffffff;">
                        Complete Payment
                    </a>
                    @elseif($room->status === 'completed')
                    <a href="{{ route('rooms.show', $room->uuid) }}" class="block w-full text-center px-3 py-2 rounded-lg text-sm font-medium transition-colors hover:bg-opacity-10 hover:bg-gray-500" style="border: 1px solid var(--border-color); color: var(--text-primary);">
                        Download Report
                    </a>
                    @else
                    <a href="{{ route('rooms.show', $room->uuid) }}" class="block w-full text-center px-3 py-2 rounded-lg text-sm font-medium transition-colors hover:bg-opacity-10 hover:bg-gray-500" style="border: 1px solid var(--border-color); color: var(--text-primary);">
                        View Case
                    </a>
                    @endif
                </div>
                @endforeach
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-hidden rounded-xl" style="border: 1px solid var(--border-color);">
                <table class="min-w-full">
                    <thead style="background-color: var(--bg-secondary);">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Room</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Other Party</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="background-color: var(--bg-primary);">
                        @foreach($myRooms as $room)
                        <tr class="hover-lift" style="border-top: 1px solid var(--border-color);">
                            <td class="px-6 py-4">
                                <div class="flex items-center cursor-pointer group" @click="modal = {
                                        category: '{{ ucfirst($room->category) }}',
                                        badge_bg: '{{ $room->category_badge_color['bg'] }}',
                                        badge_text: '{{ $room->category_badge_color['text'] }}',
                                        status: '{{ ucfirst(str_replace('_', ' ', $room->status)) }}',
                                        status_raw: '{{ $room->status }}',
                                        status_bg: '{{ $room->status_badge_color['bg'] }}',
                                        status_text: '{{ $room->status_badge_color['text'] }}',
                                        title: '{{ addslashes($room->title ?? ucfirst($room->category) . ' Dispute') }}',
                                        case_summary: '{{ addslashes(Str::limit($room->case_summary ?? '', 300)) }}',
                                        jurisdiction: '{{ $room->jurisdiction }}',
                                        duration: '{{ $room->duration }}',
                                        payment_type: '{{ ucfirst($room->payment_type) }}',
                                        party_b: '{{ addslashes($room->partyB?->name ?? $room->party_b_email ?? 'Pending') }}',
                                        created_at: '{{ $room->created_at->format('M j, Y') }}',
                                        checkout_url: '{{ route('payment.checkout', $room->id) }}',
                                        room_url: '{{ route('rooms.show', $room->uuid) }}',
                                        report_url: '{{ route('reports.index') }}',
                                    }">
                                    <span class="px-2 py-1 rounded text-xs font-medium mr-3" style="background-color: {{ $room->category_badge_color['bg'] }}; color: {{ $room->category_badge_color['text'] }};">
                                        {{ ucfirst($room->category) }}
                                    </span>
                                    <span class="text-sm group-hover:underline" style="color: var(--text-primary);">
                                        {{ $room->case_summary ? Str::limit($room->case_summary, 40) : ucfirst($room->category) . ' Dispute' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm" style="color: var(--text-primary);">
                                {{ $room->partyB?->name ?? 'Pending' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium" style="background-color: {{ $room->status_badge_color['bg'] }}; color: {{ $room->status_badge_color['text'] }};">
                                    {{ ucfirst(str_replace('_', ' ', $room->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm" style="color: var(--text-secondary);">
                                {{ $room->created_at->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($room->status === 'active')
                                <a href="{{ route('rooms.show', $room->uuid) }}" class="px-3 py-1 rounded text-xs font-medium transition-colors hover:opacity-90" style="background-color: var(--gold); color: var(--white);">
                                    Enter Room
                                </a>
                                @elseif($room->status === 'pending')
                                <a href="{{ route('payment.checkout', $room->id) }}" class="px-3 py-1 rounded text-xs font-medium transition-colors hover:opacity-90" style="background-color: #0D1B2A; color: #ffffff;">
                                    Complete Payment
                                </a>
                                @elseif($room->status === 'completed')
                                <div class="flex space-x-2">
                                    <a href="{{ route('rooms.show', $room->uuid) }}" class="px-3 py-1 rounded text-xs font-medium transition-colors hover:bg-opacity-10 hover:bg-gray-500" style="border: 1px solid var(--border-color); color: var(--text-primary);">
                                        Download Report
                                    </a>
                                </div>
                                @else
                                <a href="{{ route('rooms.show', $room->uuid) }}" class="px-3 py-1 rounded text-xs font-medium transition-colors hover:bg-opacity-10 hover:bg-gray-500" style="border: 1px solid var(--border-color); color: var(--text-primary);">
                                    View Case
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($myRooms->hasPages())
            <div class="mt-6">
                {{ $myRooms->links() }}
            </div>
            @endif
            @else
            <div class="text-center py-12">
                <p class="text-lg" style="color: var(--text-secondary);">No rooms created yet.</p>
            </div>
            @endif
        </div>

        <!-- Invited Tab Content -->
        <div x-show="activeTab === 'invited'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
            @if($invitedRooms->count() > 0)
            
            <!-- Mobile Card View -->
            <div class="block md:hidden space-y-3">
                @foreach($invitedRooms as $room)
                <div class="p-4 rounded-xl" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color);">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 rounded text-xs font-medium" style="background-color: {{ $room->category_badge_color['bg'] }}; color: {{ $room->category_badge_color['text'] }};">
                                {{ ucfirst($room->category) }}
                            </span>
                            @if($room->party_b_user_id == auth()->id())
                                <span class="px-2 py-1 rounded text-xs font-medium" style="background-color: rgba(201,168,76,0.1); color: var(--gold);">
                                    Party B
                                </span>
                            @endif
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs font-medium" style="background-color: {{ $room->status_badge_color['bg'] }}; color: {{ $room->status_badge_color['text'] }};">
                            {{ ucfirst(str_replace('_', ' ', $room->status)) }}
                        </span>
                    </div>
                    <p class="text-sm font-medium mb-2" style="color: var(--text-primary);">
                        {{ $room->case_summary ? Str::limit($room->case_summary, 60) : ucfirst($room->category) . ' Dispute' }}
                    </p>
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <p class="text-xs" style="color: var(--text-secondary);">Created By</p>
                            <p class="text-sm" style="color: var(--text-primary);">{{ $room->partyA->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs" style="color: var(--text-secondary);">Date</p>
                            <p class="text-sm" style="color: var(--text-primary);">{{ $room->created_at->format('M j, Y') }}</p>
                        </div>
                    </div>
                    @if($room->status === 'active')
                    <a href="{{ route('rooms.show', $room->uuid) }}" class="block w-full text-center px-3 py-2 rounded-lg text-sm font-medium transition-colors hover:opacity-90" style="background-color: var(--gold); color: var(--white);">
                        Enter Room
                    </a>
                    @else
                    <a href="{{ route('rooms.show', $room->uuid) }}" class="block w-full text-center px-3 py-2 rounded-lg text-sm font-medium transition-colors hover:bg-opacity-10 hover:bg-gray-500" style="border: 1px solid var(--border-color); color: var(--text-primary);">
                        View Details
                    </a>
                    @endif
                </div>
                @endforeach
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-hidden rounded-xl" style="border: 1px solid var(--border-color);">
                <table class="min-w-full">
                    <thead style="background-color: var(--bg-secondary);">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Room</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Created By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="background-color: var(--bg-primary);">
                        @foreach($invitedRooms as $room)
                        <tr class="hover-lift" style="border-top: 1px solid var(--border-color);">
                            <td class="px-6 py-4">
                                <div class="flex items-center cursor-pointer group" @click="modal = {
                                        category: '{{ ucfirst($room->category) }}',
                                        badge_bg: '{{ $room->category_badge_color['bg'] }}',
                                        badge_text: '{{ $room->category_badge_color['text'] }}',
                                        status: '{{ ucfirst(str_replace('_', ' ', $room->status)) }}',
                                        status_raw: '{{ $room->status }}',
                                        status_bg: '{{ $room->status_badge_color['bg'] }}',
                                        status_text: '{{ $room->status_badge_color['text'] }}',
                                        title: '{{ addslashes($room->title ?? ucfirst($room->category) . ' Dispute') }}',
                                        case_summary: '{{ addslashes(Str::limit($room->case_summary ?? '', 300)) }}',
                                        jurisdiction: '{{ $room->jurisdiction }}',
                                        duration: '{{ $room->duration }}',
                                        payment_type: '{{ ucfirst($room->payment_type) }}',
                                        party_b: '{{ addslashes($room->partyB?->name ?? $room->party_b_email ?? 'Pending') }}',
                                        created_at: '{{ $room->created_at->format('M j, Y') }}',
                                        checkout_url: '{{ route('payment.checkout', $room->id) }}',
                                        room_url: '{{ route('rooms.show', $room->uuid) }}',
                                        report_url: '{{ route('reports.index') }}',
                                    }">
                                    <span class="px-2 py-1 rounded text-xs font-medium mr-3" style="background-color: {{ $room->category_badge_color['bg'] }}; color: {{ $room->category_badge_color['text'] }};">
                                        {{ ucfirst($room->category) }}
                                    </span>
                                    @if($room->party_b_user_id == auth()->id())
                                        <span class="px-2 py-1 rounded text-xs font-medium mr-3" style="background-color: rgba(201,168,76,0.1); color: var(--gold);">
                                            Party B
                                        </span>
                                    @endif
                                    <span class="text-sm group-hover:underline" style="color: var(--text-primary);">
                                        {{ $room->case_summary ? Str::limit($room->case_summary, 40) : ucfirst($room->category) . ' Dispute' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm" style="color: var(--text-primary);">
                                {{ $room->partyA->name }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium" style="background-color: {{ $room->status_badge_color['bg'] }}; color: {{ $room->status_badge_color['text'] }};">
                                    {{ ucfirst(str_replace('_', ' ', $room->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm" style="color: var(--text-secondary);">
                                {{ $room->created_at->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($room->status === 'active')
                                <a href="{{ route('rooms.show', $room->uuid) }}" class="px-3 py-1 rounded text-xs font-medium transition-colors hover:opacity-90" style="background-color: var(--gold); color: var(--white);">
                                    Enter Room
                                </a>
                                @else
                                <a href="{{ route('rooms.show', $room->uuid) }}" class="px-3 py-1 rounded text-xs font-medium transition-colors hover:bg-opacity-10 hover:bg-gray-500" style="border: 1px solid var(--border-color); color: var(--text-primary);">
                                    View Details
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($invitedRooms->hasPages())
            <div class="mt-6">
                {{ $invitedRooms->links() }}
            </div>
            @endif
            @else
            <div class="text-center py-12">
                <p class="text-lg" style="color: var(--text-secondary);">No invitations received yet.</p>
            </div>
            @endif
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="text-center py-16 animate-fade-up" style="animation-delay: 0.6s;">
        <div class="animate-float mb-8">
            <svg class="w-24 h-24 mx-auto" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l3-1m-3 1l-3-1"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-serif mb-4" style="color: var(--text-primary);">
            You haven't started any sessions yet.
        </h2>
        <p class="text-lg mb-8" style="color: var(--text-secondary);">
            When you create a room, your dispute sessions will appear here.
        </p>
        <a href="{{ route('rooms.create') }}" class="inline-flex items-center px-6 py-3 rounded-lg text-lg font-medium transition-colors hover:opacity-90" style="background-color: var(--gold); color: var(--white);">
            Create your first Room
        </a>
        <p class="mt-4 text-sm" style="color: var(--text-secondary);">
            Starts from £45 · Setup in 15 minutes · PDF report included
        </p>
    </div>
    @endif
</div>
@endsection