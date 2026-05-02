@extends('admin.layouts.app')

@section('title', 'Archived Rooms')
@section('page-title', 'Archived Rooms')

@section('content')
<div class="space-y-4">

    {{-- Toolbar --}}
    <div class="flex items-center justify-between">
        <form method="GET" action="{{ route('admin.rooms.archived') }}" class="flex flex-col sm:flex-row gap-3 flex-1">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search case ID, title, party name or email…"
                class="flex-1 px-3 py-2 rounded-lg text-sm outline-none"
                style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);"
            >

            <button type="submit"
                    class="px-4 py-2 rounded-lg text-sm font-medium flex-shrink-0"
                    style="background: var(--gold); color: #0D1B2A;">
                Search
            </button>

            @if(request('search'))
                <a href="{{ route('admin.rooms.archived') }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium flex-shrink-0 text-center"
                   style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-secondary);">
                    Clear
                </a>
            @endif
        </form>

        <a href="{{ route('admin.rooms.index') }}"
           class="ml-3 px-4 py-2 rounded-lg text-sm font-medium flex-shrink-0"
           style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">
            ← Active Rooms
        </a>
    </div>

    {{-- Table --}}
    <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="px-4 py-3" style="border-bottom: 1px solid var(--border-color);">
            <p class="text-sm" style="color: var(--text-secondary);">
                {{ number_format($rooms->total()) }} archived room{{ $rooms->total() !== 1 ? 's' : '' }}
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
                        <th>Archived</th>
                        <th>Deleted</th>
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
                                    <span class="text-sm">{{ $room->partyA->name }}</span>
                                @else
                                    <span style="color: var(--text-secondary);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($room->partyB)
                                    <span class="text-sm">{{ $room->partyB->name }}</span>
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
                            <td class="text-xs" style="color: var(--text-secondary);">
                                {{ $room->archived_at ? $room->archived_at->format('d M Y') : '—' }}
                            </td>
                            <td class="text-xs" style="color: var(--text-secondary);">
                                {{ $room->deleted_at ? $room->deleted_at->format('d M Y') : '—' }}
                            </td>
                            <td>
                                <div class="flex gap-2" x-data="{ showConfirm: false }">
                                    <button @click="showConfirm = !showConfirm"
                                            class="text-xs px-3 py-1.5 rounded-lg font-medium hover:opacity-80"
                                            style="background: rgba(59,130,246,0.12); color: #60A5FA;">
                                        Actions
                                    </button>

                                    <div x-show="showConfirm" 
                                         x-cloak
                                         @click.away="showConfirm = false"
                                         class="absolute mt-8 right-4 rounded-lg shadow-lg p-2 space-y-1 z-10"
                                         style="background: var(--bg-card); border: 1px solid var(--border-color); min-width: 150px;">
                                        
                                        <form method="POST" action="{{ route('admin.rooms.restore', $room->id) }}" class="w-full">
                                            @csrf
                                            <button type="submit"
                                                    class="w-full text-left px-3 py-2 rounded text-xs hover:opacity-80"
                                                    style="background: rgba(74,222,128,0.12); color: #4ADE80;">
                                                ✓ Restore
                                            </button>
                                        </form>

                                        <form method="POST" 
                                              action="{{ route('admin.rooms.force-delete', $room->id) }}"
                                              onsubmit="return confirm('PERMANENTLY DELETE {{ $room->case_id }}? This cannot be undone!')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-full text-left px-3 py-2 rounded text-xs hover:opacity-80"
                                                    style="background: rgba(239,68,68,0.12); color: #F87171;">
                                                ✗ Delete Forever
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-12" style="color: var(--text-secondary);">
                                No archived rooms found.
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
