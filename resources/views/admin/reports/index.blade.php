@extends('admin.layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
<div class="space-y-4">

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Total Reports</p>
                    <p class="text-2xl font-semibold">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="p-2 rounded-lg" style="background: rgba(201,168,76,0.12);">
                    <svg class="w-5 h-5" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Generated</p>
                    <p class="text-2xl font-semibold" style="color: #4ADE80;">{{ number_format($stats['generated']) }}</p>
                </div>
                <div class="p-2 rounded-lg" style="background: rgba(74,222,128,0.12);">
                    <svg class="w-5 h-5" style="color: #4ADE80;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Pending / Failed</p>
                    <p class="text-2xl font-semibold" style="color: #FCD34D;">{{ number_format($stats['pending']) }}</p>
                </div>
                <div class="p-2 rounded-lg" style="background: rgba(251,191,36,0.12);">
                    <svg class="w-5 h-5" style="color: #FCD34D;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by case ID…"
               class="flex-1 px-3 py-2 rounded-lg text-sm outline-none"
               style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">

        <select name="status" class="px-3 py-2 rounded-lg text-sm outline-none"
                style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">
            <option value="">All statuses</option>
            <option value="generated" {{ request('status') === 'generated' ? 'selected' : '' }}>Generated</option>
            <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending / Failed</option>
        </select>

        <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium flex-shrink-0"
                style="background: var(--gold); color: #0D1B2A;">Filter</button>

        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.reports.index') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium flex-shrink-0 text-center"
               style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-secondary);">Clear</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="px-4 py-3" style="border-bottom: 1px solid var(--border-color);">
            <p class="text-sm" style="color: var(--text-secondary);">{{ number_format($reports->total()) }} report{{ $reports->total() !== 1 ? 's' : '' }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full data-table">
                <thead>
                    <tr>
                        <th>Case ID</th>
                        <th>Party A</th>
                        <th>Confidence</th>
                        <th>Status</th>
                        <th>Generated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr>
                            <td>
                                @if($report->room)
                                    <a href="{{ route('admin.rooms.show', $report->room) }}"
                                       class="font-mono text-xs hover:underline" style="color: var(--gold);">
                                        {{ $report->room->case_id }}
                                    </a>
                                @else
                                    <span class="text-xs" style="color: var(--text-secondary);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($report->room?->partyA)
                                    <a href="{{ route('admin.users.show', $report->room->partyA) }}"
                                       class="text-sm hover:underline">{{ $report->room->partyA->name }}</a>
                                @else
                                    <span style="color: var(--text-secondary);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($report->confidence_score)
                                    @php
                                        $score = $report->confidence_score;
                                        $color = $score >= 75 ? '#4ADE80' : ($score >= 50 ? '#FCD34D' : '#F87171');
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <div class="w-16 h-1.5 rounded-full" style="background: var(--border-color);">
                                            <div class="h-1.5 rounded-full" style="width: {{ $score }}%; background: {{ $color }};"></div>
                                        </div>
                                        <span class="text-xs font-medium" style="color: {{ $color }};">{{ $score }}%</span>
                                    </div>
                                @else
                                    <span class="text-xs" style="color: var(--text-secondary);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($report->pdf_path)
                                    <span class="badge" style="background: rgba(74,222,128,0.12); color: #4ADE80;">Generated</span>
                                @else
                                    <span class="badge" style="background: rgba(251,191,36,0.12); color: #FCD34D;">Pending</span>
                                @endif
                            </td>
                            <td class="text-xs" style="color: var(--text-secondary);">
                                {{ $report->generated_at?->format('d M Y H:i') ?? '—' }}
                            </td>
                            <td>
                                <div class="flex items-center gap-1.5">
                                    @if($report->pdf_path)
                                        <a href="{{ route('admin.reports.download', $report) }}"
                                           class="p-1.5 rounded-lg hover:opacity-80"
                                           style="background: rgba(74,222,128,0.1); color: #4ADE80;" title="Download PDF">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                    @endif
                                    <form method="POST" action="{{ route('admin.reports.regenerate', $report) }}">
                                        @csrf
                                        <button type="submit" class="p-1.5 rounded-lg hover:opacity-80"
                                                style="background: rgba(59,130,246,0.1); color: #60A5FA;" title="Regenerate">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reports.destroy', $report) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg hover:opacity-80"
                                                style="background: rgba(239,68,68,0.1); color: #F87171;" title="Delete"
                                                onclick="return confirm('Delete this report?')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12" style="color: var(--text-secondary);">No reports found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div class="px-4 py-3 flex items-center justify-between" style="border-top: 1px solid var(--border-color);">
                <p class="text-xs" style="color: var(--text-secondary);">
                    Showing {{ $reports->firstItem() }}–{{ $reports->lastItem() }} of {{ $reports->total() }}
                </p>
                <div class="flex gap-1">
                    @if($reports->onFirstPage())
                        <span class="px-3 py-1.5 rounded-lg text-xs opacity-30" style="background: var(--bg-card); border: 1px solid var(--border-color);">← Prev</span>
                    @else
                        <a href="{{ $reports->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg text-xs hover:opacity-80" style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">← Prev</a>
                    @endif
                    @if($reports->hasMorePages())
                        <a href="{{ $reports->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg text-xs hover:opacity-80" style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">Next →</a>
                    @else
                        <span class="px-3 py-1.5 rounded-lg text-xs opacity-30" style="background: var(--bg-card); border: 1px solid var(--border-color);">Next →</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
