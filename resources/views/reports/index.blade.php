@extends('layouts.app')

@section('title', 'Reports — First Mediator')
@section('page-title', 'Reports')

@section('content')
<div class="max-w-4xl mx-auto animate-fade-up">

    <div class="mb-6">
        <h1 class="text-2xl font-serif mb-1" style="color: var(--text-primary);">Mediation Reports</h1>
        <p class="text-sm" style="color: var(--text-secondary);">Download completed session reports for cases you were part of</p>
    </div>

    @if(session('error'))
        <div class="mb-4 px-4 py-3 rounded-lg text-sm" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #F87171;">
            {{ session('error') }}
        </div>
    @endif

    @if($reports->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 rounded-2xl"
             style="background-color: var(--bg-secondary); border: 1px dashed var(--border-color);">
            <svg class="w-12 h-12 mb-4 opacity-30" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-sm font-medium mb-1" style="color: var(--text-primary);">No reports yet</p>
            <p class="text-xs" style="color: var(--text-secondary);">Reports are generated after a session ends</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($reports as $report)
                @php
                    $room    = $report->room;
                    $isPartyA = auth()->id() === $room->party_a_id;
                    $catColors = [
                        'tenancy'    => ['#EFF6FF', '#1D4ED8'],
                        'freelance'  => ['#F0FDF4', '#15803D'],
                        'business'   => ['#FFF7ED', '#C2410C'],
                        'ecommerce'  => ['#FDF4FF', '#7E22CE'],
                        'employment' => ['#FFF1F2', '#BE123C'],
                        'debt'       => ['#F0FDF4', '#0F766E'],
                    ];
                    [$catBg, $catText] = $catColors[$room->category] ?? ['#F4F4F2', '#6B6B68'];
                @endphp
                <div class="rounded-xl p-5" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color);">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">

                        {{-- Left: info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-2">
                                <span class="font-mono text-xs font-bold" style="color: var(--gold);">
                                    {{ $room->case_id }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-xs font-semibold"
                                      style="background: {{ $catBg }}; color: {{ $catText }};">
                                    {{ ucfirst($room->category) }}
                                </span>
                                @if($report->confidence_score)
                                    <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                                          style="background: rgba(201,168,76,0.12); color: var(--gold);">
                                        {{ $report->confidence_score }}% confidence
                                    </span>
                                @endif
                            </div>

                            <p class="text-sm font-medium mb-1" style="color: var(--text-primary);">
                                {{ $room->title ?: ucfirst($room->category) . ' Dispute' }}
                            </p>

                            @if($report->case_summary)
                                <p class="text-xs mb-3 line-clamp-2" style="color: var(--text-secondary);">
                                    {{ $report->case_summary }}
                                </p>
                            @endif

                            <div class="flex flex-wrap gap-4 text-xs" style="color: var(--text-secondary);">
                                <span>
                                    <span class="font-medium" style="color: var(--text-primary);">Duration:</span>
                                    {{ $room->duration }}min
                                    @if($room->extended_minutes > 0)
                                        <span style="color: var(--gold);">+{{ $room->extended_minutes }}min</span>
                                    @endif
                                </span>
                                <span>
                                    <span class="font-medium" style="color: var(--text-primary);">Jurisdiction:</span>
                                    {{ $room->jurisdiction }}
                                </span>
                                <span>
                                    <span class="font-medium" style="color: var(--text-primary);">Generated:</span>
                                    {{ $report->generated_at?->format('d M Y') ?? '—' }}
                                </span>
                            </div>
                        </div>

                        {{-- Right: action --}}
                        <div class="flex-shrink-0 flex flex-col gap-2 sm:items-end">
                            @if($report->pdf_path || $report->resolution_recommendation)
                                <a href="{{ route('reports.download', $report) }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-opacity"
                                   style="background-color: var(--gold); color: #0D1B2A;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Download PDF
                                </a>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs"
                                      style="background: rgba(251,191,36,0.1); color: #FCD34D; border: 1px solid rgba(251,191,36,0.2);">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Generating…
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{ $reports->links() }}
    @endif

</div>
@endsection
