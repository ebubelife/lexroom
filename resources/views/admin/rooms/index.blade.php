@extends('admin.layouts.app')

@section('title', 'Rooms')
@section('page-title', 'Rooms')

@section('content')
<div class="space-y-4">

    {{-- Toolbar --}}
    <form method="GET" action="{{ route('admin.rooms.index') }}" class="flex flex-col sm:flex-row gap-3">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search case ID, title, party name or email…"
            class="flex-1 px-3 py-2 rounded-lg text-sm outline-none"
            style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);"
        >

        <select name="status"
                class="px-3 py-2 rounded-lg text-sm outline-none"
                style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">
            <option value="">All statuses</option>
            @foreach($statuses as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                    {{ ucwords(str_replace('_', ' ', $s)) }}
                </option>
            @endforeach
        </select>

        <select name="category"
                class="px-3 py-2 rounded-lg text-sm outline-none"
                style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">
            <option value="">All categories</option>
            @foreach($categories as $c)
                <option value="{{ $c }}" {{ request('category') === $c ? 'selected' : '' }}>
                    {{ ucfirst($c) }}
                </option>
            @endforeach
        </select>

        <button type="submit"
                class="px-4 py-2 rounded-lg text-sm font-medium flex-shrink-0"
                style="background: var(--gold); color: #0D1B2A;">
            Filter
        </button>

        @if(request()->hasAny(['search', 'status', 'category']))
            <a href="{{ route('admin.rooms.index') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium flex-shrink-0 text-center"
               style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-secondary);">
                Clear
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="px-4 py-3" style="border-bottom: 1px solid var(--border-color);">
            <p class="text-sm" style="color: var(--text-secondary);">
                {{ number_format($rooms->total()) }} room{{ $rooms->total() !== 1 ? 's' : '' }}
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full data-table">
                <thead>
                    <tr>
                        <th>Case ID</th>
                        <th>Title</th>
                        <th>Party A</th>
                        <th>Party B</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Msgs</th>
                        <th>Files</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                        <tr>
                            <td>
                                <span class="font-mono text-xs" style="color: var(--gold);">{{ $room->case_id }}</span>
                            </td>
                            <td class="text-sm max-w-[140px] truncate">{{ $room->title ?? '—' }}</td>
                            <td>
                                @if($room->partyA)
                                    <a href="{{ route('admin.users.show', $room->partyA) }}"
                                       class="text-sm hover:underline" style="color: var(--text-primary);">
                                        {{ $room->partyA->name }}
                                    </a>
                                @else
                                    <span style="color: var(--text-secondary);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($room->partyB)
                                    <a href="{{ route('admin.users.show', $room->partyB) }}"
                                       class="text-sm hover:underline" style="color: var(--text-primary);">
                                        {{ $room->partyB->name }}
                                    </a>
                                @elseif($room->party_b_email)
                                    <span class="text-xs" style="color: var(--text-secondary);">{{ $room->party_b_email }}</span>
                                @else
                                    <span style="color: var(--text-secondary);">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge" style="background: rgba(99,102,241,0.12); color: #A5B4FC;">
                                    {{ ucfirst($room->category) }}
                                </span>
                            </td>
                            <td>
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
                            </td>
                            <td>
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-xs {{ $room->party_a_paid ? 'text-green-400' : 'text-red-400' }}">
                                        A {{ $room->party_a_paid ? '✓' : '✗' }}
                                    </span>
                                    <span class="text-xs {{ $room->party_b_paid ? 'text-green-400' : 'text-red-400' }}">
                                        B {{ $room->party_b_paid ? '✓' : '✗' }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-sm text-center" style="color: var(--text-secondary);">
                                {{ $room->messages_count }}
                            </td>
                            <td class="text-sm text-center" style="color: var(--text-secondary);">
                                {{ $room->evidence_files_count }}
                            </td>
                            <td class="text-xs" style="color: var(--text-secondary);">
                                {{ $room->created_at->format('d M Y') }}
                            </td>
                            <td>
                                <a href="{{ route('admin.rooms.show', $room) }}"
                                   class="text-xs px-3 py-1.5 rounded-lg font-medium hover:opacity-80"
                                   style="background: rgba(201,168,76,0.12); color: var(--gold);">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-12" style="color: var(--text-secondary);">
                                No rooms found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($rooms->hasPages())
            <div class="px-4 py-3 flex items-center justify-between" style="border-top: 1px solid var(--border-color);">
                <p class="text-xs" style="color: var(--text-secondary);">
                    Showing {{ $rooms->firstItem() }}–{{ $rooms->lastItem() }} of {{ $rooms->total() }}
                </p>
                <div class="flex gap-1">
                    @if($rooms->onFirstPage())
                        <span class="px-3 py-1.5 rounded-lg text-xs opacity-30"
                              style="background: var(--bg-card); border: 1px solid var(--border-color);">← Prev</span>
                    @else
                        <a href="{{ $rooms->previousPageUrl() }}"
                           class="px-3 py-1.5 rounded-lg text-xs hover:opacity-80"
                           style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">← Prev</a>
                    @endif
                    @if($rooms->hasMorePages())
                        <a href="{{ $rooms->nextPageUrl() }}"
                           class="px-3 py-1.5 rounded-lg text-xs hover:opacity-80"
                           style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">Next →</a>
                    @else
                        <span class="px-3 py-1.5 rounded-lg text-xs opacity-30"
                              style="background: var(--bg-card); border: 1px solid var(--border-color);">Next →</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
