@extends('admin.layouts.app')

@section('title', $room->case_id)
@section('page-title', $room->case_id)

@section('content')
<div x-data="{ addTimeModal: false, deleteModal: false, activeTab: 'transcript' }" class="space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm" style="color: var(--text-secondary);">
        <a href="{{ route('admin.rooms.index') }}" class="hover:underline" style="color: var(--gold);">Rooms</a>
        <span>/</span>
        <span class="font-mono">{{ $room->case_id }}</span>
    </div>

    {{-- Top row: Meta + Actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Room metadata --}}
        <div class="lg:col-span-2 rounded-xl p-5 space-y-4" style="background: var(--bg-card); border: 1px solid var(--border-color);">
            <div class="flex items-start justify-between gap-3 flex-wrap">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-lg font-semibold">{{ $room->title ?? 'Untitled Room' }}</h2>
                        @php
                            $sc = [
                                'active'              => ['rgba(74,222,128,0.12)',  '#4ADE80'],
                                'pending'             => ['rgba(251,191,36,0.12)',  '#FCD34D'],
                                'waiting_for_party_b' => ['rgba(59,130,246,0.12)',  '#60A5FA'],
                                'locked'              => ['rgba(107,114,128,0.12)', '#9CA3AF'],
                                'completed'           => ['rgba(107,114,128,0.12)', '#9CA3AF'],
                                'paused'              => ['rgba(251,191,36,0.12)',  '#FCD34D'],
                                'expired'             => ['rgba(239,68,68,0.12)',   '#F87171'],
                                'escalated'           => ['rgba(201,168,76,0.12)',  '#C9A84C'],
                            ];
                            [$bg, $text] = $sc[$room->status] ?? ['rgba(107,114,128,0.12)', '#9CA3AF'];
                        @endphp
                        <span class="badge" style="background: {{ $bg }}; color: {{ $text }};">
                            {{ ucwords(str_replace('_', ' ', $room->status)) }}
                        </span>
                    </div>
                    <p class="text-xs mt-1 font-mono" style="color: var(--text-secondary);">{{ $room->uuid }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-3" style="border-top: 1px solid var(--border-color);">
                <div>
                    <p class="text-xs uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Category</p>
                    <p class="text-sm font-medium">{{ ucfirst($room->category) }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Jurisdiction</p>
                    <p class="text-sm">{{ $room->jurisdiction }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Duration</p>
                    <p class="text-sm">{{ $room->duration }}min
                        @if($room->extended_minutes > 0)
                            <span style="color: var(--gold);">+{{ $room->extended_minutes }}min</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Payment Type</p>
                    <p class="text-sm">{{ ucfirst($room->payment_type) }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Started</p>
                    <p class="text-sm">{{ $room->started_at?->format('d M Y H:i') ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Ended</p>
                    <p class="text-sm">{{ $room->ended_at?->format('d M Y H:i') ?? '—' }}</p>
                </div>
            </div>

            {{-- Parties --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-3" style="border-top: 1px solid var(--border-color);">
                <div class="p-3 rounded-lg" style="background: rgba(59,130,246,0.06); border: 1px solid rgba(59,130,246,0.15);">
                    <p class="text-xs font-semibold mb-2" style="color: #60A5FA;">PARTY A</p>
                    @if($room->partyA)
                        <a href="{{ route('admin.users.show', $room->partyA) }}"
                           class="text-sm font-medium hover:underline">{{ $room->partyA->name }}</a>
                        <p class="text-xs mt-0.5" style="color: var(--text-secondary);">{{ $room->partyA->email }}</p>
                    @else
                        <p class="text-sm" style="color: var(--text-secondary);">—</p>
                    @endif
                    <p class="text-xs mt-1.5">
                        Paid:
                        <span style="color: {{ $room->party_a_paid ? '#4ADE80' : '#F87171' }};">
                            {{ $room->party_a_paid ? 'Yes' : 'No' }}
                        </span>
                    </p>
                </div>
                <div class="p-3 rounded-lg" style="background: rgba(168,85,247,0.06); border: 1px solid rgba(168,85,247,0.15);">
                    <p class="text-xs font-semibold mb-2" style="color: #C084FC;">PARTY B</p>
                    @if($room->partyB)
                        <a href="{{ route('admin.users.show', $room->partyB) }}"
                           class="text-sm font-medium hover:underline">{{ $room->partyB->name }}</a>
                        <p class="text-xs mt-0.5" style="color: var(--text-secondary);">{{ $room->partyB->email }}</p>
                    @elseif($room->party_b_email)
                        <p class="text-sm" style="color: var(--text-secondary);">{{ $room->party_b_email }}</p>
                        <p class="text-xs mt-0.5" style="color: var(--text-secondary);">Not yet registered</p>
                    @else
                        <p class="text-sm" style="color: var(--text-secondary);">Not invited yet</p>
                    @endif
                    <p class="text-xs mt-1.5">
                        Paid:
                        <span style="color: {{ $room->party_b_paid ? '#4ADE80' : '#F87171' }};">
                            {{ $room->party_b_paid ? 'Yes' : 'No' }}
                        </span>
                    </p>
                </div>
            </div>

            @if($room->case_summary)
                <div class="pt-3" style="border-top: 1px solid var(--border-color);">
                    <p class="text-xs uppercase tracking-wider mb-2" style="color: var(--text-secondary);">Case Summary</p>
                    <p class="text-sm leading-relaxed" style="color: var(--text-secondary);">{{ $room->case_summary }}</p>
                </div>
            @endif
        </div>

        {{-- Actions panel --}}
        <div class="space-y-3">

            {{-- Quick actions --}}
            <div class="rounded-xl p-4 space-y-2" style="background: var(--bg-card); border: 1px solid var(--border-color);">
                <h3 class="text-sm font-semibold mb-3">Actions</h3>

                @if(!in_array($room->status, ['locked', 'expired', 'completed']))
                    <form method="POST" action="{{ route('admin.rooms.force-lock', $room) }}">
                        @csrf
                        <button type="submit"
                                class="w-full py-2 rounded-lg text-xs font-medium hover:opacity-80 text-left px-3"
                                style="background: rgba(107,114,128,0.12); color: #9CA3AF; border: 1px solid rgba(107,114,128,0.2);">
                            🔒 Force Lock Session
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.rooms.force-expire', $room) }}">
                        @csrf
                        <button type="submit"
                                class="w-full py-2 rounded-lg text-xs font-medium hover:opacity-80 text-left px-3"
                                style="background: rgba(239,68,68,0.08); color: #F87171; border: 1px solid rgba(239,68,68,0.2);">
                            ⏱ Force Expire Room
                        </button>
                    </form>
                @endif

                <button @click="addTimeModal = true"
                        class="w-full py-2 rounded-lg text-xs font-medium hover:opacity-80 text-left px-3"
                        style="background: rgba(201,168,76,0.1); color: var(--gold); border: 1px solid rgba(201,168,76,0.25);">
                    ➕ Add Time
                </button>

                <form method="POST" action="{{ route('admin.rooms.archive', $room) }}">
                    @csrf
                    <button type="submit"
                            class="w-full py-2 rounded-lg text-xs font-medium hover:opacity-80 text-left px-3"
                            style="background: rgba(168,85,247,0.08); color: #C084FC; border: 1px solid rgba(168,85,247,0.2);">
                        📦 Archive Room
                    </button>
                </form>

                <button @click="deleteModal = true"
                        class="w-full py-2 rounded-lg text-xs font-medium hover:opacity-80 text-left px-3"
                        style="background: rgba(239,68,68,0.08); color: #F87171; border: 1px solid rgba(239,68,68,0.2);">
                    🗑 Delete Room
                </button>
            </div>

            {{-- Stats --}}
            <div class="rounded-xl p-4 space-y-2" style="background: var(--bg-card); border: 1px solid var(--border-color);">
                <h3 class="text-sm font-semibold mb-3">Stats</h3>
                @foreach([
                    ['Messages',   $messages->count()],
                    ['Evidence Files', $room->evidenceFiles->count()],
                    ['Extensions', $room->extensions->count()],
                    ['Billing Records', $room->billing->count()],
                ] as [$label, $val])
                    <div class="flex justify-between text-sm">
                        <span style="color: var(--text-secondary);">{{ $label }}</span>
                        <span class="font-medium">{{ $val }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">

        {{-- Tab bar --}}
        <div class="flex" style="border-bottom: 1px solid var(--border-color);">
            @foreach(['transcript' => 'Transcript', 'evidence' => 'Evidence (' . $room->evidenceFiles->count() . ')', 'billing' => 'Billing (' . $room->billing->count() . ')', 'extensions' => 'Extensions (' . $room->extensions->count() . ')'] as $tab => $label)
                <button @click="activeTab = '{{ $tab }}'"
                        class="px-4 py-3 text-sm font-medium transition-colors"
                        :style="activeTab === '{{ $tab }}'
                            ? 'color: var(--gold); border-bottom: 2px solid var(--gold); margin-bottom: -1px;'
                            : 'color: var(--text-secondary);'">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Transcript --}}
        <div x-show="activeTab === 'transcript'" class="p-4">
            @if($messages->isEmpty())
                <p class="text-sm text-center py-8" style="color: var(--text-secondary);">No messages yet.</p>
            @else
                <div class="space-y-2 max-h-[500px] overflow-y-auto pr-1">
                    @foreach($messages as $msg)
                        @php
                            $isLex = $msg->sender_type === 'lex';
                            $isA   = $msg->sender_type === 'party_a';
                        @endphp
                        <div class="flex gap-3 {{ $isLex ? 'justify-center' : '' }}">
                            @if(!$isLex)
                                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5"
                                     style="background: {{ $isA ? 'rgba(59,130,246,0.2)' : 'rgba(168,85,247,0.2)' }}; color: {{ $isA ? '#60A5FA' : '#C084FC' }};">
                                    {{ $isA ? 'A' : 'B' }}
                                </div>
                            @endif
                            <div class="flex-1 {{ $isLex ? 'max-w-xl mx-auto' : '' }}">
                                <div class="rounded-lg px-3 py-2 text-sm"
                                     style="{{ $isLex
                                         ? 'background: rgba(201,168,76,0.08); border: 1px solid rgba(201,168,76,0.2); color: var(--gold);'
                                         : 'background: var(--bg-primary); border: 1px solid var(--border-color);' }}">
                                    @if($isLex)
                                        <span class="text-xs font-semibold block mb-1" style="color: var(--gold);">⚖ Lex AI</span>
                                    @endif
                                    <p style="color: {{ $isLex ? 'var(--text-primary)' : 'var(--text-primary)' }};">
                                        {{ $msg->content }}
                                    </p>
                                </div>
                                <p class="text-xs mt-0.5 px-1" style="color: var(--text-secondary);">
                                    {{ $msg->created_at->format('d M H:i') }}
                                    @if($msg->phase) · Phase {{ $msg->phase }} @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Evidence --}}
        <div x-show="activeTab === 'evidence'" x-cloak class="p-4">
            @if($room->evidenceFiles->isEmpty())
                <p class="text-sm text-center py-8" style="color: var(--text-secondary);">No evidence files.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full data-table">
                        <thead>
                            <tr>
                                <th>Filename</th>
                                <th>Party</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th>Uploaded</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($room->evidenceFiles as $file)
                                <tr>
                                    <td class="text-sm font-medium">{{ $file->original_filename }}</td>
                                    <td>
                                        <span class="badge"
                                              style="background: {{ $file->party === 'party_a' ? 'rgba(59,130,246,0.12)' : 'rgba(168,85,247,0.12)' }};
                                                     color: {{ $file->party === 'party_a' ? '#60A5FA' : '#C084FC' }};">
                                            {{ $file->party_label }}
                                        </span>
                                    </td>
                                    <td class="text-xs" style="color: var(--text-secondary);">{{ $file->mime_type }}</td>
                                    <td class="text-xs" style="color: var(--text-secondary);">{{ $file->formatted_size }}</td>
                                    <td class="text-xs" style="color: var(--text-secondary);">{{ $file->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Billing --}}
        <div x-show="activeTab === 'billing'" x-cloak class="p-4">
            @if($room->billing->isEmpty())
                <p class="text-sm text-center py-8" style="color: var(--text-secondary);">No billing records.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full data-table">
                        <thead>
                            <tr>
                                <th>Party</th>
                                <th>Plan</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Stripe Intent</th>
                                <th>Paid At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($room->billing as $bill)
                                <tr>
                                    <td><span class="badge" style="background: rgba(59,130,246,0.12); color: #60A5FA;">{{ ucfirst($bill->party) }}</span></td>
                                    <td class="text-sm">{{ $bill->plan ?? '—' }}</td>
                                    <td class="text-sm font-medium">£{{ number_format($bill->amount, 0) }}</td>
                                    <td>
                                        @php $bc = ['paid' => ['rgba(74,222,128,0.12)', '#4ADE80'], 'pending' => ['rgba(251,191,36,0.12)', '#FCD34D'], 'refunded' => ['rgba(239,68,68,0.12)', '#F87171']]; [$bbg, $btxt] = $bc[$bill->status] ?? $bc['pending']; @endphp
                                        <span class="badge" style="background: {{ $bbg }}; color: {{ $btxt }};">{{ ucfirst($bill->status) }}</span>
                                    </td>
                                    <td class="text-xs font-mono" style="color: var(--text-secondary);">
                                        {{ $bill->stripe_payment_intent_id ? substr($bill->stripe_payment_intent_id, 0, 20) . '…' : '—' }}
                                    </td>
                                    <td class="text-xs" style="color: var(--text-secondary);">{{ $bill->paid_at?->format('d M Y H:i') ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Extensions --}}
        <div x-show="activeTab === 'extensions'" x-cloak class="p-4">
            @if($room->extensions->isEmpty())
                <p class="text-sm text-center py-8" style="color: var(--text-secondary);">No extensions.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full data-table">
                        <thead>
                            <tr>
                                <th>Requested By</th>
                                <th>Minutes Added</th>
                                <th>Amount Paid</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($room->extensions as $ext)
                                <tr>
                                    <td class="text-sm">{{ $ext->user?->name ?? '—' }}</td>
                                    <td class="text-sm font-medium" style="color: var(--gold);">+{{ $ext->minutes_added }} min</td>
                                    <td class="text-sm">£{{ number_format($ext->amount_paid, 0) }}</td>
                                    <td>
                                        <span class="badge" style="background: rgba(74,222,128,0.12); color: #4ADE80;">{{ ucfirst($ext->status) }}</span>
                                    </td>
                                    <td class="text-xs" style="color: var(--text-secondary);">{{ $ext->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Add Time Modal --}}
    <div x-show="addTimeModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.7);">
        <div class="w-full max-w-sm rounded-xl p-5 space-y-4"
             style="background: var(--bg-card); border: 1px solid var(--border-color);" @click.stop>
            <h3 class="text-base font-semibold">Add Time to {{ $room->case_id }}</h3>
            <form method="POST" action="{{ route('admin.rooms.add-time', $room) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Minutes to add</label>
                    <input type="number" name="minutes" min="1" max="120" placeholder="e.g. 30"
                           class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                           style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Reason</label>
                    <input type="text" name="reason" placeholder="e.g. Technical issue compensation"
                           class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                           style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                </div>
                <div class="flex gap-2">
                    <button type="button" @click="addTimeModal = false"
                            class="flex-1 py-2 rounded-lg text-sm font-medium"
                            style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-secondary);">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 py-2 rounded-lg text-sm font-medium hover:opacity-80"
                            style="background: var(--gold); color: #0D1B2A;">
                        Add Time
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-show="deleteModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.7);">
        <div class="w-full max-w-sm rounded-xl p-5 space-y-4"
             style="background: var(--bg-card); border: 1px solid var(--border-color);" @click.stop>
            <h3 class="text-base font-semibold" style="color: #F87171;">Delete Room?</h3>
            <p class="text-sm" style="color: var(--text-secondary);">
                This is permanent. All messages, evidence files, billing records and reports for this room will be deleted.
            </p>
            <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" class="space-y-3">
                @csrf
                @method('DELETE')
                <div>
                    <label class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">
                        Type <span class="font-mono" style="color: #F87171;">{{ $room->case_id }}</span> to confirm
                    </label>
                    <input type="text" name="confirm_case_id" placeholder="{{ $room->case_id }}"
                           class="w-full px-3 py-2 rounded-lg text-sm outline-none font-mono"
                           style="background: var(--bg-primary); border: 1px solid rgba(239,68,68,0.4); color: var(--text-primary);">
                </div>
                <div class="flex gap-2">
                    <button type="button" @click="deleteModal = false"
                            class="flex-1 py-2 rounded-lg text-sm font-medium"
                            style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-secondary);">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 py-2 rounded-lg text-sm font-medium hover:opacity-80"
                            style="background: rgba(239,68,68,0.15); color: #F87171; border: 1px solid rgba(239,68,68,0.3);">
                        Delete Permanently
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
