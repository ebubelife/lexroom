@extends('layouts.app')

@section('title', 'Ticket #' . str_pad($ticket->id, 5, '0', STR_PAD_LEFT) . ' — First Mediator')
@section('page-title', 'Support Ticket')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6 gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('support.index') }}" class="text-xs hover:opacity-80" style="color: var(--text-secondary);">← My Tickets</a>
                <span style="color: var(--border-color);">/</span>
                <span class="text-xs font-mono" style="color: var(--text-secondary);">#{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <h2 class="text-lg font-semibold" style="color: var(--text-primary);">{{ $ticket->subject }}</h2>
            <div class="flex items-center gap-2 mt-1 flex-wrap">
                <span class="text-xs px-2 py-0.5 rounded-full border" style="{{ $ticket->statusColor() }}">{{ $ticket->statusLabel() }}</span>
                <span class="text-xs" style="color: var(--text-secondary);">{{ $ticket->typeLabel() }}</span>
                <span class="text-xs" style="color: var(--text-secondary);">Opened {{ $ticket->created_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-5 px-4 py-3 rounded-lg text-sm font-medium"
             style="background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.2); color: #4ade80;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 px-4 py-3 rounded-lg text-sm"
             style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); color: #f87171;">
            {{ session('error') }}
        </div>
    @endif

    {{-- Thread --}}
    <div class="space-y-4 mb-6">
        @foreach($ticket->messages->where('is_internal', false) as $msg)
        <div class="flex gap-3 {{ $msg->isFromAdmin() ? '' : 'flex-row-reverse' }}">

            {{-- Avatar --}}
            <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-bold"
                 style="{{ $msg->isFromAdmin() ? 'background: rgba(201,168,76,0.2); color: var(--gold);' : 'background: rgba(255,255,255,0.08); color: var(--text-secondary);' }}">
                {{ strtoupper(substr($msg->sender_name, 0, 1)) }}
            </div>

            {{-- Bubble --}}
            <div class="flex-1 max-w-lg">
                <div class="flex items-center gap-2 mb-1 {{ $msg->isFromAdmin() ? '' : 'flex-row-reverse' }}">
                    <span class="text-xs font-semibold" style="color: var(--text-primary);">
                        {{ $msg->isFromAdmin() ? 'First Mediator Support' : $msg->sender_name }}
                    </span>
                    <span class="text-xs" style="color: var(--text-secondary);">{{ $msg->created_at->diffForHumans() }}</span>
                </div>
                <div class="px-4 py-3 rounded-xl text-sm whitespace-pre-wrap"
                     style="{{ $msg->isFromAdmin() ? 'background: rgba(201,168,76,0.08); border: 1px solid rgba(201,168,76,0.2); color: var(--text-primary);' : 'background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);' }}">{{ $msg->body }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Reply form (only if ticket is open) --}}
    @if($ticket->isOpen())
    <div class="rounded-xl p-5" style="background: var(--bg-secondary); border: 1px solid var(--border-color);">
        <h3 class="text-sm font-semibold mb-3" style="color: var(--text-primary);">Add a Reply</h3>
        <form method="POST" action="{{ route('support.reply', $ticket->uuid) }}">
            @csrf
            @if($errors->any())
            <div class="mb-3 px-3 py-2 rounded text-xs" style="background: rgba(239,68,68,0.08); color: #f87171;">
                {{ $errors->first('message') }}
            </div>
            @endif
            <textarea name="message" rows="4" required minlength="5" maxlength="5000"
                      placeholder="Type your reply…"
                      class="w-full px-4 py-3 rounded-lg text-sm resize-none mb-3"
                      style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;"></textarea>
            <div class="flex justify-end">
                <button type="submit"
                        class="px-5 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition-all"
                        style="background: var(--gold); color: #fff;">
                    Send Reply
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="text-center py-6 rounded-xl text-sm" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-secondary);">
        This ticket is {{ $ticket->statusLabel() }} and no longer accepting replies.
    </div>
    @endif

</div>
@endsection
