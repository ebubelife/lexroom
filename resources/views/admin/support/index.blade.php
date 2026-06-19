@extends('admin.layouts.app')

@section('title', 'Support Tickets')
@section('page-title', 'Support Tickets')

@section('content')
<div class="px-4 py-6">

    {{-- Status tabs --}}
    <div class="flex items-center gap-1 mb-6 flex-wrap">
        @php
            $tabStatuses = [null => 'All', 'open' => 'Open', 'in_progress' => 'In Progress', 'waiting' => 'Waiting', 'resolved' => 'Resolved', 'closed' => 'Closed'];
            $current = request('status');
        @endphp
        @foreach($tabStatuses as $val => $label)
        <a href="{{ request()->fullUrlWithQuery(['status' => $val ?: null]) }}"
           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all"
           style="{{ $current === $val || ($val === null && !$current) ? 'background: rgba(201,168,76,0.15); color: var(--gold); border: 1px solid rgba(201,168,76,0.3);' : 'background: rgba(255,255,255,0.04); color: var(--text-secondary); border: 1px solid var(--border-color);' }}">
            {{ $label }}
            @if($val !== null && isset($counts[$val]) && $counts[$val] > 0)
                <span class="ml-1 px-1.5 rounded-full text-xs" style="background: rgba(255,255,255,0.08);">{{ $counts[$val] }}</span>
            @endif
        </a>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex gap-3 mb-5 flex-wrap">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, subject…"
               class="px-3 py-2 rounded-lg text-sm flex-1 min-w-48"
               style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
        <select name="type" class="px-3 py-2 rounded-lg text-sm" style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
            <option value="">All Types</option>
            @foreach($types as $v => $l)
                <option value="{{ $v }}" {{ request('type') === $v ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
        </select>
        <select name="priority" class="px-3 py-2 rounded-lg text-sm" style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
            <option value="">All Priorities</option>
            @foreach($priorities as $v => $l)
                <option value="{{ $v }}" {{ request('priority') === $v ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90"
                style="background: var(--gold); color: var(--navy);">Filter</button>
        @if(request()->hasAny(['search','type','priority','status']))
            <a href="{{ route('admin.support.index') }}" class="px-4 py-2 rounded-lg text-sm" style="color: var(--text-secondary); border: 1px solid var(--border-color);">Clear</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <table class="data-table w-full">
            <thead>
                <tr>
                    <th>Ticket</th>
                    <th>From</th>
                    <th>Type</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Last Activity</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                <tr>
                    <td>
                        <div>
                            <span class="text-xs font-mono" style="color: var(--text-secondary);">#{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</span>
                            <p class="text-sm font-medium mt-0.5" style="color: var(--text-primary);">{{ Str::limit($ticket->subject, 55) }}</p>
                            @if($ticket->latestMessage && $ticket->latestMessage->sender_type === 'user')
                                <p class="text-xs mt-0.5 flex items-center gap-1" style="color: var(--gold);">
                                    <span class="w-1.5 h-1.5 rounded-full inline-block" style="background: var(--gold);"></span>
                                    Awaiting reply
                                </p>
                            @endif
                        </div>
                    </td>
                    <td>
                        <p class="text-sm font-medium" style="color: var(--text-primary);">{{ $ticket->name }}</p>
                        <p class="text-xs" style="color: var(--text-secondary);">{{ $ticket->email }}</p>
                    </td>
                    <td><span class="text-xs" style="color: var(--text-secondary);">{{ $ticket->typeLabel() }}</span></td>
                    <td>
                        <span class="text-xs font-semibold" style="{{ $ticket->priorityColor() }}">{{ $ticket->priorityLabel() }}</span>
                    </td>
                    <td>
                        <span class="text-xs px-2 py-0.5 rounded-full border" style="{{ $ticket->statusColor() }}">{{ $ticket->statusLabel() }}</span>
                    </td>
                    <td>
                        <p class="text-xs" style="color: var(--text-secondary);">{{ $ticket->last_reply_at?->diffForHumans() ?? $ticket->created_at->diffForHumans() }}</p>
                        <p class="text-xs" style="color: var(--text-secondary);">{{ $ticket->messages_count }} {{ Str::plural('msg', $ticket->messages_count) }}</p>
                    </td>
                    <td>
                        <a href="{{ route('admin.support.show', $ticket) }}"
                           class="text-xs px-3 py-1.5 rounded-lg transition-colors"
                           style="background: rgba(255,255,255,0.05); color: var(--text-secondary);"
                           onmouseover="this.style.color='var(--text-primary)'"
                           onmouseout="this.style.color='var(--text-secondary)'">
                            View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-10" style="color: var(--text-secondary);">No tickets found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $tickets->links() }}</div>
</div>
@endsection
