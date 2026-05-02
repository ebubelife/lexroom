@php
    $statusColors = [
        'pending'   => ['bg' => 'rgba(245,158,11,0.1)', 'text' => '#D97706', 'border' => 'rgba(245,158,11,0.2)'],
        'active'    => ['bg' => 'rgba(201,168,76,0.1)',  'text' => '#C9A84C', 'border' => 'rgba(201,168,76,0.3)'],
        'completed' => ['bg' => 'rgba(34,197,94,0.1)',   'text' => '#16A34A', 'border' => 'rgba(34,197,94,0.2)'],
        'cancelled' => ['bg' => 'rgba(220,38,38,0.08)',  'text' => '#DC2626', 'border' => 'rgba(220,38,38,0.15)'],
    ];
    $sc = $statusColors[$room->status] ?? ['bg' => 'rgba(107,107,104,0.1)', 'text' => '#6B6B68', 'border' => 'rgba(107,107,104,0.2)'];
    $catColor = $room->category_badge_color;
    $otherParty = $role === 'creator'
        ? ($room->partyB?->name ?? 'Waiting for Party B')
        : ($room->partyA?->name ?? '—');
    $otherLabel = $role === 'creator' ? 'Opposing' : 'Initiated By';
@endphp

<div class="group relative flex flex-col rounded-2xl overflow-hidden transition-all duration-500 hover:-translate-y-1 hover:shadow-2xl" 
     style="background-color: var(--bg-secondary); border: 1px solid var(--border-color);">
    
    {{-- Glassy Overlay on Hover --}}
    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"
         style="background: radial-gradient(circle at top right, rgba(201, 168, 76, 0.05), transparent 70%);"></div>

    {{-- Top accent bar --}}
    <div class="h-1.5 w-full transition-all duration-500 group-hover:h-2" style="background-color: {{ $sc['text'] }};"></div>

    <div class="p-6 flex flex-col flex-1 relative z-10">
        {{-- Header row --}}
        <div class="flex items-start justify-between mb-4">
            <div class="space-y-1">
                <p class="text-[10px] font-mono font-bold tracking-widest uppercase opacity-50" style="color: var(--text-secondary);">
                    #{{ $room->case_id }}
                </p>
                <div class="flex gap-2">
                    <span class="px-2.5 py-1 rounded-md text-[9px] font-black uppercase tracking-tighter shadow-sm" 
                          style="background-color: {{ $catColor['bg'] }}; color: {{ $catColor['text'] }}; border: 1px solid rgba(255,255,255,0.1);">
                        {{ $room->category }}
                    </span>
                </div>
            </div>
            
            <div class="flex flex-col items-end gap-1">
                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border shadow-sm" 
                      style="background-color: {{ $sc['bg'] }}; color: {{ $sc['text'] }}; border-color: {{ $sc['border'] }};">
                    {{ str_replace('_', ' ', $room->status) }}
                </span>
            </div>
        </div>

        {{-- Case Information --}}
        <h3 class="text-base font-serif font-bold mb-3 line-clamp-2 leading-tight group-hover:text-[var(--gold)] transition-colors duration-300" style="color: var(--text-primary);">
            {{ $room->case_summary ?: ucfirst($room->category) . ' Dispute' }}
        </h3>

        <div class="space-y-2.5 mb-6">
            <div class="flex items-center gap-2 text-xs">
                <div class="w-2 h-2 rounded-full opacity-40" style="background-color: var(--gold);"></div>
                <span style="color: var(--text-secondary);">{{ $otherLabel }}:</span>
                <span class="font-bold truncate max-w-[120px]" style="color: var(--text-primary);">{{ $otherParty }}</span>
            </div>
            <div class="flex items-center justify-between text-[10px] uppercase tracking-wider opacity-60" style="color: var(--text-secondary);">
                <div class="flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ $room->created_at->format('M d, Y') }}
                </div>
                @if($room->evidenceFiles->count() > 0)
                <div class="flex items-center gap-1 px-2 py-0.5 rounded bg-black bg-opacity-5 dark:bg-white dark:bg-opacity-5">
                    <svg class="w-3 h-3 text-[var(--gold)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                    {{ $room->evidenceFiles->count() }}
                </div>
                @endif
            </div>
        </div>

        {{-- Footer Action --}}
        <div class="mt-auto border-t border-white border-opacity-5 pt-4 space-y-2">
            @php
                $btnStyle = $room->status === 'active' 
                    ? 'background: linear-gradient(135deg, var(--gold) 0%, #b38f36 100%); color: white;' 
                    : 'background-color: rgba(255,255,255,0.03); border: 1px solid var(--border-color); color: var(--text-primary);';
            @endphp
            <a href="{{ route('rooms.show', $room->uuid) }}"
               class="group/btn flex items-center justify-between w-full px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-widest transition-all duration-300 shadow-sm hover:shadow-md"
               style="{{ $btnStyle }}">
                
                <span>
                    @if($room->status === 'draft') Continue Draft
                    @elseif($room->status === 'active') Enter Room
                    @elseif($room->status === 'pending') Review Case
                    @elseif($room->status === 'completed') View Report
                    @else Case Details
                    @endif
                </span>

                <svg class="w-4 h-4 transform transition-transform duration-300 group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>

            @if($role === 'creator')
            <form method="POST" action="{{ route('rooms.destroy', $room) }}" onsubmit="return confirm('Move this case to trash?')" class="w-full">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-widest transition-all hover:opacity-80" style="background-color: rgba(220,38,38,0.08); color: #DC2626; border: 1px solid rgba(220,38,38,0.15);">
                    🗑 Move to Trash
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
