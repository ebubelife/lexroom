@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Users --}}
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Total Users</p>
                    <p class="text-2xl font-semibold">{{ number_format($stats['total_users']) }}</p>
                </div>
                <div class="p-2 rounded-lg" style="background: rgba(201,168,76,0.12);">
                    <svg class="w-5 h-5" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs mt-2" style="color: var(--text-secondary);">
                <span style="color: #4ADE80;">+{{ $stats['new_users_7d'] }}</span> this week
            </p>
        </div>

        {{-- Rooms --}}
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Total Rooms</p>
                    <p class="text-2xl font-semibold">{{ number_format($stats['total_rooms']) }}</p>
                </div>
                <div class="p-2 rounded-lg" style="background: rgba(59,130,246,0.12);">
                    <svg class="w-5 h-5" style="color: #60A5FA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs mt-2" style="color: var(--text-secondary);">
                <span style="color: #4ADE80;">{{ $stats['active_rooms'] }} active</span>
                · {{ $stats['pending_rooms'] }} pending
            </p>
        </div>

        {{-- Revenue --}}
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Total Revenue</p>
                    <p class="text-2xl font-semibold">₦{{ number_format($stats['total_revenue'], 0) }}</p>
                </div>
                <div class="p-2 rounded-lg" style="background: rgba(74,222,128,0.12);">
                    <svg class="w-5 h-5" style="color: #4ADE80;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs mt-2" style="color: var(--text-secondary);">
                <span style="color: #4ADE80;">+₦{{ number_format($stats['revenue_7d'], 0) }}</span> this week
            </p>
        </div>

        {{-- Files --}}
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Evidence Files</p>
                    <p class="text-2xl font-semibold">{{ number_format($stats['total_files']) }}</p>
                </div>
                <div class="p-2 rounded-lg" style="background: rgba(168,85,247,0.12);">
                    <svg class="w-5 h-5" style="color: #C084FC;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs mt-2" style="color: var(--text-secondary);">
                {{ $stats['pending_payments'] }} pending payments
            </p>
        </div>
    </div>

    {{-- Two column: Recent Rooms + Recent Users --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Recent Rooms --}}
        <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
            <div class="flex items-center justify-between px-4 py-3" style="border-bottom: 1px solid var(--border-color);">
                <h2 class="text-sm font-semibold">Recent Rooms</h2>
                <a href="#" class="text-xs hover:underline" style="color: var(--gold);">View all</a>
            </div>
            <table class="w-full data-table">
                <thead>
                    <tr>
                        <th>Case ID</th>
                        <th>Party A</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentRooms as $room)
                        <tr>
                            <td>
                                <span class="font-mono text-xs" style="color: var(--gold);">{{ $room->case_id }}</span>
                            </td>
                            <td class="text-sm">{{ $room->partyA?->name ?? '—' }}</td>
                            <td>
                                <span class="badge" style="background: rgba(201,168,76,0.12); color: var(--gold);">
                                    {{ str_replace('_', ' ', $room->status) }}
                                </span>
                            </td>
                            <td class="text-xs" style="color: var(--text-secondary);">
                                {{ $room->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-6" style="color: var(--text-secondary);">No rooms yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Recent Users --}}
        <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
            <div class="flex items-center justify-between px-4 py-3" style="border-bottom: 1px solid var(--border-color);">
                <h2 class="text-sm font-semibold">Recent Users</h2>
                <a href="#" class="text-xs hover:underline" style="color: var(--gold);">View all</a>
            </div>
            <table class="w-full data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Verified</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentUsers as $user)
                        <tr>
                            <td class="font-medium text-sm">{{ $user->name }}</td>
                            <td class="text-xs" style="color: var(--text-secondary);">{{ $user->email }}</td>
                            <td>
                                @if($user->email_verified_at)
                                    <span class="badge" style="background: rgba(74,222,128,0.12); color: #4ADE80;">Yes</span>
                                @else
                                    <span class="badge" style="background: rgba(239,68,68,0.12); color: #F87171;">No</span>
                                @endif
                            </td>
                            <td class="text-xs" style="color: var(--text-secondary);">
                                {{ $user->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-6" style="color: var(--text-secondary);">No users yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Admin Actions --}}
    @if($recentActions->isNotEmpty())
    <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="px-4 py-3" style="border-bottom: 1px solid var(--border-color);">
            <h2 class="text-sm font-semibold">Recent Admin Activity</h2>
        </div>
        <div class="divide-y" style="border-color: var(--border-color);">
            @foreach($recentActions as $action)
                <div class="flex items-center gap-3 px-4 py-3">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                         style="background: rgba(201,168,76,0.15); color: var(--gold);">
                        {{ strtoupper(substr($action->admin->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm">
                            <span class="font-medium">{{ $action->admin->name }}</span>
                            <span style="color: var(--text-secondary);"> {{ str_replace('_', ' ', $action->action) }}</span>
                            @if($action->target_type)
                                <span style="color: var(--text-secondary);"> on {{ $action->target_type }} #{{ $action->target_id }}</span>
                            @endif
                        </p>
                    </div>
                    <p class="text-xs flex-shrink-0" style="color: var(--text-secondary);">
                        {{ $action->created_at->diffForHumans() }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
