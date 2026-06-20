@extends('admin.layouts.app')

@section('title', 'Ticket #' . str_pad($ticket->id, 5, '0', STR_PAD_LEFT))
@section('page-title', 'Support Ticket')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.support.index') }}" class="text-xs hover:opacity-80" style="color: var(--text-secondary);">← All Tickets</a>
                <span style="color: var(--border-color);">/</span>
                <span class="text-xs font-mono" style="color: var(--text-secondary);">#{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <h2 class="text-lg font-semibold" style="color: var(--text-primary);">{{ $ticket->subject }}</h2>
            <div class="flex items-center gap-3 mt-1 flex-wrap">
                <span class="text-xs px-2 py-0.5 rounded-full border" style="{{ $ticket->statusColor() }}">{{ $ticket->statusLabel() }}</span>
                <span class="text-xs font-semibold" style="{{ $ticket->priorityColor() }}">{{ $ticket->priorityLabel() }}</span>
                <span class="text-xs" style="color: var(--text-secondary);">{{ $ticket->typeLabel() }}</span>
                <span class="text-xs" style="color: var(--text-secondary);">{{ $ticket->created_at->format('d M Y, g:i A') }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Thread column --}}
        <div class="lg:col-span-2 space-y-4">

            @if(session('success'))
                <div class="px-4 py-3 rounded-lg text-sm font-medium"
                     style="background: rgba(34,197,94,0.12); border: 1px solid rgba(34,197,94,0.3); color: #4ade80;">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="px-4 py-3 rounded-lg text-sm"
                     style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #f87171;">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Messages --}}
            @foreach($ticket->messages as $msg)
            @if($msg->is_internal)
            <div class="px-4 py-3 rounded-xl border-dashed"
                 style="background: rgba(168,85,247,0.04); border: 1px dashed rgba(168,85,247,0.3);">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs font-semibold" style="color: #a78bfa;">Internal Note</span>
                    <span class="text-xs" style="color: var(--text-secondary);">{{ $msg->sender_name }}</span>
                    <span class="text-xs" style="color: var(--text-secondary);">{{ $msg->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm whitespace-pre-wrap" style="color: var(--text-primary);">{{ $msg->body }}</p>
            </div>
            @else
            <div class="flex gap-3 {{ $msg->isFromAdmin() ? 'flex-row-reverse' : '' }}">
                <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-bold"
                     style="{{ $msg->isFromAdmin() ? 'background: rgba(201,168,76,0.2); color: var(--gold);' : 'background: rgba(255,255,255,0.08); color: var(--text-secondary);' }}">
                    {{ strtoupper(substr($msg->sender_name, 0, 1)) }}
                </div>
                <div class="flex-1 max-w-lg">
                    <div class="flex items-center gap-2 mb-1 {{ $msg->isFromAdmin() ? 'flex-row-reverse' : '' }}">
                        <span class="text-xs font-semibold" style="color: var(--text-primary);">
                            {{ $msg->isFromAdmin() ? $msg->sender_name . ' (Support)' : $msg->sender_name }}
                        </span>
                        <span class="text-xs" style="color: var(--text-secondary);">{{ $msg->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="px-4 py-3 rounded-xl text-sm whitespace-pre-wrap"
                         style="{{ $msg->isFromAdmin() ? 'background: rgba(201,168,76,0.08); border: 1px solid rgba(201,168,76,0.2); color: var(--text-primary);' : 'background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);' }}">{{ $msg->body }}</div>
                </div>
            </div>
            @endif
            @endforeach

            {{-- Reply form --}}
            @if(auth('admin')->user()->hasAbility('admin.support.manage'))
            <div class="rounded-xl p-5 mt-2" style="background: var(--bg-card); border: 1px solid var(--border-color);" x-data="{ internal: false }">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold" style="color: var(--text-primary);">Reply</h3>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="internal" class="rounded" style="accent-color: #a78bfa;">
                        <span class="text-xs" style="color: var(--text-secondary);">Internal note (not sent to user)</span>
                    </label>
                </div>
                <form method="POST" action="{{ route('admin.support.reply', $ticket) }}">
                    @csrf
                    <input type="hidden" name="is_internal" :value="internal ? 1 : 0">

                    <textarea name="body" rows="5" required minlength="5"
                              :placeholder="internal ? 'Add an internal note…' : 'Type your reply to the user…'"
                              class="w-full px-4 py-3 rounded-lg text-sm resize-none mb-3"
                              :style="internal ? 'background: rgba(168,85,247,0.05); border: 1px dashed rgba(168,85,247,0.3); color: var(--text-primary); outline: none;' : 'background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;'"></textarea>

                    <div class="flex items-center justify-between gap-3 flex-wrap">
                        <div class="flex gap-2">
                            <select name="status" class="px-3 py-1.5 rounded-lg text-xs" style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
                                <option value="">Keep status</option>
                                @foreach($statuses as $v => $l)
                                    <option value="{{ $v }}" {{ $ticket->status === $v ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                            <select name="priority" class="px-3 py-1.5 rounded-lg text-xs" style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
                                <option value="">Keep priority</option>
                                @foreach($priorities as $v => $l)
                                    <option value="{{ $v }}" {{ $ticket->priority === $v ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit"
                                :class="internal ? '' : ''"
                                class="px-5 py-2 rounded-lg text-xs font-semibold hover:opacity-90 transition-all"
                                :style="internal ? 'background: rgba(168,85,247,0.2); color: #a78bfa; border: 1px solid rgba(168,85,247,0.3);' : 'background: var(--gold); color: var(--navy);'">
                            <span x-show="!internal">Send Reply & Email User</span>
                            <span x-show="internal" x-cloak>Save Internal Note</span>
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>

        {{-- Sidebar: ticket info + quick actions --}}
        <div class="space-y-4">

            {{-- Quick status update --}}
            @if(auth('admin')->user()->hasAbility('admin.support.manage'))
            <div class="rounded-xl p-4" style="background: var(--bg-card); border: 1px solid var(--border-color);">
                <h4 class="text-xs font-semibold uppercase tracking-widest mb-3" style="color: var(--text-secondary);">Quick Update</h4>
                <form method="POST" action="{{ route('admin.support.status', $ticket) }}">
                    @csrf
                    <div class="space-y-2">
                        <select name="status" class="w-full px-3 py-2 rounded-lg text-sm" style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
                            @foreach($statuses as $v => $l)
                                <option value="{{ $v }}" {{ $ticket->status === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                        <select name="priority" class="w-full px-3 py-2 rounded-lg text-sm" style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
                            @foreach($priorities as $v => $l)
                                <option value="{{ $v }}" {{ $ticket->priority === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="w-full py-2 rounded-lg text-xs font-semibold hover:opacity-90 transition-all"
                                style="background: rgba(255,255,255,0.06); border: 1px solid var(--border-color); color: var(--text-primary);">
                            Update Ticket
                        </button>
                    </div>
                </form>
            </div>
            @endif

            {{-- Ticket details --}}
            <div class="rounded-xl p-4 space-y-3" style="background: var(--bg-card); border: 1px solid var(--border-color);">
                <h4 class="text-xs font-semibold uppercase tracking-widest mb-1" style="color: var(--text-secondary);">Ticket Info</h4>

                <div>
                    <p class="text-xs" style="color: var(--text-secondary);">From</p>
                    <p class="text-sm font-medium" style="color: var(--text-primary);">{{ $ticket->name }}</p>
                    <p class="text-xs" style="color: var(--text-secondary);">{{ $ticket->email }}</p>
                </div>

                @if($ticket->user)
                <div>
                    <p class="text-xs" style="color: var(--text-secondary);">Linked Account</p>
                    <a href="{{ route('admin.users.show', $ticket->user) }}" class="text-xs hover:underline" style="color: var(--gold);">
                        View User Profile →
                    </a>
                </div>
                @endif

                <div>
                    <p class="text-xs" style="color: var(--text-secondary);">Category</p>
                    <p class="text-sm" style="color: var(--text-primary);">{{ $ticket->typeLabel() }}</p>
                </div>

                <div>
                    <p class="text-xs" style="color: var(--text-secondary);">Opened</p>
                    <p class="text-sm" style="color: var(--text-primary);">{{ $ticket->created_at->format('d M Y, g:i A') }}</p>
                </div>

                <div>
                    <p class="text-xs" style="color: var(--text-secondary);">Last Activity</p>
                    <p class="text-sm" style="color: var(--text-primary);">{{ $ticket->last_reply_at?->format('d M Y, g:i A') ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-xs" style="color: var(--text-secondary);">Messages</p>
                    <p class="text-sm" style="color: var(--text-primary);">{{ $ticket->messages->count() }}</p>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
