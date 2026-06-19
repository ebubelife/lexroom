@extends('layouts.app')

@section('title', 'Support — First Mediator')
@section('page-title', 'Support')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold" style="color: var(--text-primary);">My Support Tickets</h2>
            <p class="text-sm mt-0.5" style="color: var(--text-secondary);">Track and manage your support requests.</p>
        </div>
        <a href="{{ route('support.create') }}"
           class="px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition-all"
           style="background: var(--gold); color: #fff;">
            + New Ticket
        </a>
    </div>

    @if(session('success'))
        <div class="mb-5 px-4 py-3 rounded-lg text-sm font-medium"
             style="background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.2); color: #4ade80;">
            {{ session('success') }}
        </div>
    @endif

    @if($tickets->isEmpty())
        <div class="text-center py-16 rounded-xl" style="background: var(--bg-secondary); border: 1px solid var(--border-color);">
            <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
            <p class="text-sm font-medium" style="color: var(--text-secondary);">No support tickets yet</p>
            <a href="{{ route('support.create') }}" class="mt-3 inline-block text-sm font-medium" style="color: var(--gold);">Submit your first ticket →</a>
        </div>
    @else
        <div class="rounded-xl overflow-hidden" style="background: var(--bg-secondary); border: 1px solid var(--border-color);">
            @foreach($tickets as $ticket)
            <a href="{{ route('support.show', $ticket->uuid) }}"
               class="flex items-start gap-4 px-5 py-4 transition-colors hover:bg-opacity-50 block"
               style="{{ !$loop->last ? 'border-bottom: 1px solid var(--border-color);' : '' }} hover:background: rgba(255,255,255,0.02);">

                {{-- Status dot --}}
                <div class="flex-shrink-0 mt-1">
                    @php $color = match($ticket->status) { 'open' => '#eab308', 'in_progress' => '#60a5fa', 'waiting' => '#a78bfa', 'resolved' => '#4ade80', default => '#6b7280' }; @endphp
                    <div class="w-2.5 h-2.5 rounded-full mt-1" style="background: {{ $color }};"></div>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-mono" style="color: var(--text-secondary);">#{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full border" style="{{ $ticket->statusColor() }}">{{ $ticket->statusLabel() }}</span>
                        <span class="text-xs" style="color: var(--text-secondary);">{{ $ticket->typeLabel() }}</span>
                    </div>
                    <p class="text-sm font-medium truncate" style="color: var(--text-primary);">{{ $ticket->subject }}</p>
                    @if($ticket->latestMessage)
                    <p class="text-xs mt-0.5 truncate" style="color: var(--text-secondary);">
                        {{ $ticket->latestMessage->sender_name }}: {{ Str::limit($ticket->latestMessage->body, 80) }}
                    </p>
                    @endif
                </div>

                <div class="flex-shrink-0 text-right">
                    <p class="text-xs" style="color: var(--text-secondary);">{{ $ticket->last_reply_at?->diffForHumans() ?? $ticket->created_at->diffForHumans() }}</p>
                    <p class="text-xs mt-0.5" style="color: var(--text-secondary);">{{ $ticket->messages_count }} {{ Str::plural('message', $ticket->messages_count) }}</p>
                    @if($ticket->status === 'waiting')
                        <span class="text-xs font-semibold" style="color: var(--gold);">Reply needed</span>
                    @endif
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-4">{{ $tickets->links() }}</div>
    @endif
</div>
@endsection
