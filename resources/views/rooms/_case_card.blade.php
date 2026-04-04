@php
    $statusColors = [
        'pending'   => ['bg' => 'rgba(245,158,11,0.15)', 'text' => '#D97706'],
        'active'    => ['bg' => 'rgba(201,168,76,0.15)',  'text' => '#C9A84C'],
        'completed' => ['bg' => 'rgba(34,197,94,0.15)',   'text' => '#16A34A'],
        'cancelled' => ['bg' => 'rgba(220,38,38,0.12)',   'text' => '#DC2626'],
    ];
    $sc = $statusColors[$room->status] ?? ['bg' => 'rgba(107,107,104,0.12)', 'text' => '#6B6B68'];
    $catColor = $room->category_badge_color;
    $otherParty = $role === 'creator'
        ? ($room->partyB?->name ?? 'Waiting for Party B')
        : ($room->partyA?->name ?? '—');
    $otherLabel = $role === 'creator' ? 'Other Party' : 'Created By';
@endphp

<div class="flex flex-col rounded-2xl overflow-hidden hover-lift transition-shadow" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color);">
    {{-- Top colour strip --}}
    <div class="h-1 w-full" style="background-color: {{ $sc['text'] }};"></div>

    <div class="p-5 flex flex-col flex-1">
        {{-- Badges row --}}
        <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] font-mono opacity-60 tracking-wider uppercase" style="color: var(--text-secondary);">
                #{{ $room->case_id }}
            </span>
            <div class="flex gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-medium" style="background-color: {{ $catColor['bg'] }}; color: {{ $catColor['text'] }};">
                    {{ ucfirst($room->category) }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-medium" style="background-color: {{ $sc['bg'] }}; color: {{ $sc['text'] }};">
                    {{ ucfirst(str_replace('_', ' ', $room->status)) }}
                </span>
            </div>
        </div>

        {{-- Case title --}}
        <p class="text-sm font-semibold mb-1 line-clamp-2 leading-snug" title="{{ $room->case_summary }}" style="color: var(--text-primary);">
            {{ $room->case_summary ? Str::limit($room->case_summary, 80) : ucfirst($room->category) . ' Dispute' }}
        </p>

        {{-- Meta --}}
        <div class="flex items-center justify-between mt-2 mb-4 text-xs" style="color: var(--text-secondary);">
            <span>{{ $otherLabel }}: <strong style="color: var(--text-primary);">{{ $otherParty }}</strong></span>
            <span>{{ $room->created_at->format('M j, Y') }}</span>
        </div>

        {{-- File count badge --}}
        @if($room->evidenceFiles && $room->evidenceFiles->count() > 0)
        <div class="flex items-center gap-1 mb-4 text-xs" style="color: var(--text-secondary);">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
            </svg>
            {{ $room->evidenceFiles->count() }} file{{ $room->evidenceFiles->count() > 1 ? 's' : '' }} in vault
        </div>
        @endif

        {{-- Action button --}}
        <div class="mt-auto pt-2">
            <a href="{{ route('rooms.show', $room->uuid) }}"
               class="flex items-center justify-center gap-2 w-full py-2 rounded-lg text-sm font-medium transition-opacity hover:opacity-85"
               style="{{ $room->status === 'active' ? 'background-color: var(--gold); color: var(--white);' : 'border: 1px solid var(--border-color); color: var(--text-primary);' }}">
                @if($room->status === 'active')
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Enter Room
                @elseif($room->status === 'pending')
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                View Case
                @elseif($room->status === 'completed')
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                View Report
                @else
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                View Case
                @endif
            </a>
        </div>
    </div>
</div>
