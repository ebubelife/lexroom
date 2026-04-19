@if ($paginator->hasPages())
<nav class="flex items-center justify-between mt-6" aria-label="Pagination">

    {{-- Count --}}
    <p class="text-xs" style="color: var(--text-secondary);">
        Showing <span style="color: var(--text-primary); font-weight: 500;">{{ $paginator->firstItem() }}</span>
        –
        <span style="color: var(--text-primary); font-weight: 500;">{{ $paginator->lastItem() }}</span>
        of
        <span style="color: var(--text-primary); font-weight: 500;">{{ $paginator->total() }}</span>
    </p>

    {{-- Page buttons --}}
    <div class="flex items-center gap-1">

        {{-- Prev --}}
        @if ($paginator->onFirstPage())
            <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;font-size:0.8rem;opacity:0.3;background:var(--bg-secondary);border:1px solid var(--border-color);color:var(--text-secondary);">
                ←
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;font-size:0.8rem;background:var(--bg-secondary);border:1px solid var(--border-color);color:var(--text-primary);text-decoration:none;transition:border-color 0.15s;"
               onmouseover="this.style.borderColor='var(--gold)'"
               onmouseout="this.style.borderColor='var(--border-color)'">
                ←
            </a>
        @endif

        {{-- Page numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;font-size:0.8rem;color:var(--text-secondary);">…</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;font-size:0.8rem;font-weight:600;background:var(--gold);color:#0D1B2A;border:1px solid var(--gold);">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;font-size:0.8rem;background:var(--bg-secondary);border:1px solid var(--border-color);color:var(--text-primary);text-decoration:none;transition:border-color 0.15s;"
                           onmouseover="this.style.borderColor='var(--gold)'"
                           onmouseout="this.style.borderColor='var(--border-color)'">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;font-size:0.8rem;background:var(--bg-secondary);border:1px solid var(--border-color);color:var(--text-primary);text-decoration:none;transition:border-color 0.15s;"
               onmouseover="this.style.borderColor='var(--gold)'"
               onmouseout="this.style.borderColor='var(--border-color)'">
                →
            </a>
        @else
            <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;font-size:0.8rem;opacity:0.3;background:var(--bg-secondary);border:1px solid var(--border-color);color:var(--text-secondary);">
                →
            </span>
        @endif

    </div>
</nav>
@endif
