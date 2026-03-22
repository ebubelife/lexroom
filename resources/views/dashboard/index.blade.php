@extends('layouts.app')

@section('title', 'Dashboard — LexRoom')
@section('page-title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Welcome Header -->
    <div class="mb-8 animate-fade-up">
        <h1 class="text-3xl font-serif mb-2" style="color: var(--text-primary);">
            Welcome back, {{ auth()->user()->first_name }} 👋
        </h1>
        <p class="text-lg" style="color: var(--text-secondary);">
            Here's where your disputes stand today.
        </p>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
        <!-- Total Sessions -->
        <div class="stats-card hover-lift animate-fade-up animate-fade-up-delay-1">
            <div class="flex items-start justify-between mb-3">
                <div class="p-2.5 rounded-lg" style="background-color: rgba(201, 168, 76, 0.1);">
                    <svg class="w-5 h-5" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10a2 2 0 002 2h4a2 2 0 002-2V11m-6 0V9a2 2 0 012-2h4a2 2 0 012 2v2m-6 0h8"></path>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-3xl font-bold mb-1" style="color: var(--text-primary);">{{ $stats['total'] }}</p>
                <p class="text-sm font-medium" style="color: var(--text-secondary);">Total Sessions</p>
            </div>
        </div>

        <!-- Active Sessions -->
        <div class="stats-card hover-lift animate-fade-up animate-fade-up-delay-2">
            <div class="flex items-start justify-between mb-3">
                <div class="p-2.5 rounded-lg" style="background-color: rgba(34, 197, 94, 0.1);">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-3xl font-bold mb-1" style="color: var(--text-primary);">{{ $stats['active'] }}</p>
                <p class="text-sm font-medium" style="color: var(--text-secondary);">Active Sessions</p>
            </div>
        </div>

        <!-- Resolved -->
        <div class="stats-card hover-lift animate-fade-up animate-fade-up-delay-3">
            <div class="flex items-start justify-between mb-3">
                <div class="p-2.5 rounded-lg" style="background-color: rgba(34, 197, 94, 0.1);">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-3xl font-bold mb-1" style="color: var(--text-primary);">{{ $stats['resolved'] }}</p>
                <p class="text-sm font-medium" style="color: var(--text-secondary);">Resolved</p>
            </div>
        </div>

        <!-- Credits Balance -->
        <div class="stats-card hover-lift animate-fade-up" style="animation-delay: 0.4s;">
            <div class="flex items-start justify-between mb-3">
                <div class="p-2.5 rounded-lg" style="background-color: rgba(201, 168, 76, 0.1);">
                    <svg class="w-5 h-5" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-3xl font-bold mb-1" style="color: var(--text-primary);">₦{{ number_format($stats['credits']) }}</p>
                <p class="text-sm font-medium" style="color: var(--text-secondary);">Credits Balance</p>
            </div>
        </div>
    </div>

    @if($activeSessions->count() > 0)
    <!-- Active Sessions Needing Attention -->
    <div class="mb-8 animate-fade-up" style="animation-delay: 0.5s;">
        <h2 class="text-xl font-serif mb-4" style="color: var(--text-primary);">Needs your attention</h2>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @foreach($activeSessions as $session)
            <div class="p-6 rounded-xl hover-lift" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color);">
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

                <h3 class="font-medium mb-2" style="color: var(--text-primary);">
                    {{ $session->case_summary ? Str::limit($session->case_summary, 60) : ucfirst($session->category) . ' Dispute' }}
                </h3>

                <p class="text-sm mb-4" style="color: var(--text-secondary);">
                    Other party: {{ $session->partyB?->name ?? 'Waiting for Party B' }}
                </p>

                @if($session->status === 'active')
                <a href="#" class="w-full inline-flex justify-center py-2 px-4 rounded-lg text-sm font-medium transition-colors hover:opacity-90" style="background-color: var(--gold); color: var(--white);">
                    Continue Session
                </a>
                @elseif($session->status === 'pending')
                <a href="#" class="w-full inline-flex justify-center py-2 px-4 rounded-lg text-sm font-medium transition-colors hover:opacity-90" style="background-color: #f59e0b; color: var(--white);">
                    Complete Payment
                </a>
                @else
                <a href="#" class="w-full inline-flex justify-center py-2 px-4 rounded-lg text-sm font-medium transition-colors hover:bg-opacity-10 hover:bg-gray-500" style="border: 1px solid var(--border-color); color: var(--text-primary);">
                    Resend Invite
                </a>
                @endif
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
            <div class="overflow-hidden rounded-xl" style="border: 1px solid var(--border-color);">
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
                                <div class="flex items-center">
                                    <span class="px-2 py-1 rounded text-xs font-medium mr-3" style="background-color: {{ $room->category_badge_color['bg'] }}; color: {{ $room->category_badge_color['text'] }};">
                                        {{ ucfirst($room->category) }}
                                    </span>
                                    <span class="text-sm" style="color: var(--text-primary);">
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
                                <a href="#" class="px-3 py-1 rounded text-xs font-medium transition-colors hover:opacity-90" style="background-color: var(--gold); color: var(--white);">
                                    Enter Room
                                </a>
                                @elseif($room->status === 'pending')
                                <a href="#" class="px-3 py-1 rounded text-xs font-medium transition-colors hover:opacity-90" style="background-color: #f59e0b; color: var(--white);">
                                    Complete Payment
                                </a>
                                @elseif($room->status === 'completed')
                                <div class="flex space-x-2">
                                    <a href="#" class="px-3 py-1 rounded text-xs font-medium transition-colors hover:bg-opacity-10 hover:bg-gray-500" style="border: 1px solid var(--border-color); color: var(--text-primary);">
                                        Download Report
                                    </a>
                                </div>
                                @else
                                <a href="#" class="px-3 py-1 rounded text-xs font-medium transition-colors hover:bg-opacity-10 hover:bg-gray-500" style="border: 1px solid var(--border-color); color: var(--text-primary);">
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
            <div class="overflow-hidden rounded-xl" style="border: 1px solid var(--border-color);">
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
                                <div class="flex items-center">
                                    <span class="px-2 py-1 rounded text-xs font-medium mr-3" style="background-color: {{ $room->category_badge_color['bg'] }}; color: {{ $room->category_badge_color['text'] }};">
                                        {{ ucfirst($room->category) }}
                                    </span>
                                    <span class="text-sm" style="color: var(--text-primary);">
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
                                <a href="#" class="px-3 py-1 rounded text-xs font-medium transition-colors hover:opacity-90" style="background-color: var(--gold); color: var(--white);">
                                    Enter Room
                                </a>
                                @else
                                <a href="#" class="px-3 py-1 rounded text-xs font-medium transition-colors hover:bg-opacity-10 hover:bg-gray-500" style="border: 1px solid var(--border-color); color: var(--text-primary);">
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
        <a href="#" class="inline-flex items-center px-6 py-3 rounded-lg text-lg font-medium transition-colors hover:opacity-90" style="background-color: var(--gold); color: var(--white);">
            Create your first Room
        </a>
        <p class="mt-4 text-sm" style="color: var(--text-secondary);">
            Starts from ₦4,500 · Setup in 15 minutes · PDF report included
        </p>
    </div>
    @endif
</div>
@endsection