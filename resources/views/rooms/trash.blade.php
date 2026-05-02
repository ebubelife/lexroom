@extends('layouts.app')

@section('title', 'Trash — First Mediator')
@section('page-title', 'Trash')

@section('content')
<div class="max-w-7xl mx-auto animate-fade-up">

    {{-- Header Row --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-serif" style="color: var(--text-primary);">Trash</h1>
            <p class="text-sm mt-0.5" style="color: var(--text-secondary);">Cases you've deleted — restore or permanently remove them</p>
        </div>
        <a href="{{ route('rooms.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-opacity" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Cases
        </a>
    </div>

    {{-- Search Bar --}}
    @if($trashedRooms->total() > 0)
    <form method="GET" action="{{ route('rooms.trash') }}" class="mb-6">
        <div class="relative">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2" style="color: var(--text-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Search trashed cases…"
                class="w-full pl-9 pr-4 py-2.5 rounded-lg text-sm border focus:outline-none focus:ring-2"
                style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary); --tw-ring-color: var(--gold);"
            >
        </div>
    </form>
    @endif

    {{-- Trashed Cases Grid --}}
    @if($trashedRooms->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">
            @foreach($trashedRooms as $room)
                @php
                    $statusColors = [
                        'draft'     => ['bg' => 'rgba(107,107,104,0.1)', 'text' => '#6B6B68', 'border' => 'rgba(107,107,104,0.2)'],
                        'pending'   => ['bg' => 'rgba(245,158,11,0.1)', 'text' => '#D97706', 'border' => 'rgba(245,158,11,0.2)'],
                        'active'    => ['bg' => 'rgba(201,168,76,0.1)',  'text' => '#C9A84C', 'border' => 'rgba(201,168,76,0.3)'],
                        'completed' => ['bg' => 'rgba(34,197,94,0.1)',   'text' => '#16A34A', 'border' => 'rgba(34,197,94,0.2)'],
                    ];
                    $sc = $statusColors[$room->status] ?? ['bg' => 'rgba(107,107,104,0.1)', 'text' => '#6B6B68', 'border' => 'rgba(107,107,104,0.2)'];
                    $catColor = $room->category_badge_color;
                @endphp

                <div class="relative flex flex-col rounded-2xl overflow-hidden opacity-60 hover:opacity-100 transition-opacity" 
                     style="background-color: var(--bg-secondary); border: 1px solid var(--border-color);">
                    
                    {{-- Deleted indicator --}}
                    <div class="h-1.5 w-full" style="background-color: #DC2626;"></div>

                    <div class="p-6 flex flex-col flex-1">
                        {{-- Header --}}
                        <div class="flex items-start justify-between mb-4">
                            <div class="space-y-1">
                                <p class="text-[10px] font-mono font-bold tracking-widest uppercase opacity-50" style="color: var(--text-secondary);">
                                    #{{ $room->case_id }}
                                </p>
                                <span class="inline-block px-2.5 py-1 rounded-md text-[9px] font-black uppercase tracking-tighter shadow-sm" 
                                      style="background-color: {{ $catColor['bg'] }}; color: {{ $catColor['text'] }}; border: 1px solid rgba(255,255,255,0.1);">
                                    {{ $room->category }}
                                </span>
                            </div>
                            
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border shadow-sm" 
                                  style="background-color: {{ $sc['bg'] }}; color: {{ $sc['text'] }}; border-color: {{ $sc['border'] }};">
                                {{ str_replace('_', ' ', $room->status) }}
                            </span>
                        </div>

                        {{-- Title --}}
                        <h3 class="text-base font-serif font-bold mb-3 line-clamp-2 leading-tight" style="color: var(--text-primary);">
                            {{ $room->title ?? ($room->case_summary ?: ucfirst($room->category) . ' Dispute') }}
                        </h3>

                        {{-- Meta --}}
                        <div class="space-y-2 mb-6">
                            <div class="flex items-center gap-2 text-xs">
                                <div class="w-2 h-2 rounded-full" style="background-color: #DC2626;"></div>
                                <span style="color: var(--text-secondary);">Deleted:</span>
                                <span class="font-medium" style="color: var(--text-primary);">{{ $room->user_deleted_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs opacity-60">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span style="color: var(--text-secondary);">Created {{ $room->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="mt-auto flex gap-2">
                            <form method="POST" action="{{ route('rooms.restore', $room->id) }}" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest transition-all hover:opacity-80" style="background-color: rgba(34,197,94,0.12); color: #16A34A; border: 1px solid rgba(34,197,94,0.25);">
                                    ↺ Restore
                                </button>
                            </form>
                            
                            <button onclick="confirmDelete{{ $room->id }}()" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest transition-all hover:opacity-80" style="background-color: rgba(220,38,38,0.12); color: #DC2626; border: 1px solid rgba(220,38,38,0.25);">
                                ✗ Delete
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Delete Confirmation Modal --}}
                <div id="deleteModal{{ $room->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.7);">
                    <div class="w-full max-w-sm rounded-xl p-5 space-y-4" style="background: var(--bg-card); border: 1px solid var(--border-color);">
                        <h3 class="text-base font-semibold" style="color: #DC2626;">Permanently Delete Case?</h3>
                        <p class="text-sm" style="color: var(--text-secondary);">
                            This will permanently delete <strong>{{ $room->case_id }}</strong> and all its data. This cannot be undone.
                        </p>
                        <div class="flex gap-2">
                            <button onclick="closeDelete{{ $room->id }}()" class="flex-1 py-2 rounded-lg text-sm font-medium" style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-secondary);">
                                Cancel
                            </button>
                            <form method="POST" action="{{ route('rooms.destroy', $room) }}" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full py-2 rounded-lg text-sm font-medium hover:opacity-80" style="background: rgba(220,38,38,0.15); color: #DC2626; border: 1px solid rgba(220,38,38,0.3);">
                                    Delete Forever
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <script>
                    function confirmDelete{{ $room->id }}() {
                        document.getElementById('deleteModal{{ $room->id }}').classList.remove('hidden');
                    }
                    function closeDelete{{ $room->id }}() {
                        document.getElementById('deleteModal{{ $room->id }}').classList.add('hidden');
                    }
                </script>
            @endforeach
        </div>

        {{-- Pagination --}}
        {{ $trashedRooms->links() }}
    @else
        {{-- Empty State --}}
        <div class="flex flex-col items-center justify-center py-20 rounded-2xl" style="background-color: var(--bg-secondary); border: 1px dashed var(--border-color);">
            <svg class="w-16 h-16 mb-4" style="color: var(--text-secondary); opacity: .3;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <p class="text-base font-medium mb-1" style="color: var(--text-primary);">Trash is empty</p>
            <p class="text-sm" style="color: var(--text-secondary);">Deleted cases will appear here</p>
        </div>
    @endif

</div>
@endsection
