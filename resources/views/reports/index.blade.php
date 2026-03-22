@extends('layouts.app')

@section('title', 'Reports — FirstMediator')
@section('page-title', 'Reports')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl lg:text-3xl font-serif mb-2" style="color: var(--text-primary);">
            Mediation Reports
        </h1>
        <p class="text-base" style="color: var(--text-secondary);">
            Download and view your completed mediation session reports
        </p>
    </div>

    <div class="grid grid-cols-1 gap-4">
        @foreach($reports as $report)
        <div class="p-6 rounded-xl hover-lift" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color);">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-3">
                        <span class="px-3 py-1 rounded text-xs font-medium mr-3" style="background-color: {{ $report['badge_color'] }}; color: white;">
                            {{ $report['category'] }}
                        </span>
                        <span class="text-sm" style="color: var(--text-secondary);">
                            {{ $report['date'] }}
                        </span>
                    </div>
                    <h3 class="text-lg font-medium mb-2" style="color: var(--text-primary);">
                        {{ $report['title'] }}
                    </h3>
                    <p class="text-sm mb-4" style="color: var(--text-secondary);">
                        {{ $report['summary'] }}
                    </p>
                    <div class="flex items-center space-x-4 text-sm" style="color: var(--text-secondary);">
                        <span>Duration: {{ $report['duration'] }} mins</span>
                        <span>•</span>
                        <span>Outcome: <strong style="color: var(--gold);">{{ $report['outcome'] }}</strong></span>
                    </div>
                </div>
                <div class="ml-6">
                    <button class="px-4 py-2 rounded-lg text-sm font-medium transition-colors hover:opacity-90" style="background-color: var(--gold); color: var(--white);">
                        Download PDF
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
