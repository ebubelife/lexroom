@extends('layouts.app')

@section('title', 'FM Refer — Find a Lawyer')
@section('page-title', 'FM Refer')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl lg:text-3xl font-serif mb-2" style="color: var(--text-primary);">Find a Lawyer</h1>
        <p class="text-base" style="color: var(--text-secondary);">
            Need professional legal advice? Connect with verified lawyers in your jurisdiction.
        </p>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('fmrefer.index') }}" class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by name..."
               class="flex-1 min-w-[180px] px-4 py-2.5 rounded-lg border text-sm outline-none"
               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">

        <select name="jurisdiction"
                class="px-4 py-2.5 rounded-lg border text-sm outline-none"
                style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
            <option value="">All Jurisdictions</option>
            @foreach($jurisdictions as $j)
                <option value="{{ $j }}" {{ request('jurisdiction') === $j ? 'selected' : '' }}>{{ $j }}</option>
            @endforeach
        </select>

        <select name="speciality"
                class="px-4 py-2.5 rounded-lg border text-sm outline-none"
                style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
            <option value="">All Specialities</option>
            @foreach($specialities as $s)
                <option value="{{ $s }}" {{ request('speciality') === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>

        <button type="submit" class="px-5 py-2.5 rounded-lg text-sm font-medium hover:opacity-90"
                style="background-color: var(--gold); color: #0D1B2A;">Search</button>

        @if(request()->hasAny(['search', 'jurisdiction', 'speciality']))
            <a href="{{ route('fmrefer.index') }}"
               class="px-5 py-2.5 rounded-lg text-sm font-medium"
               style="border: 1px solid var(--border-color); color: var(--text-secondary);">Clear</a>
        @endif
    </form>

    {{-- Lawyer Grid --}}
    @if($lawyers->isEmpty())
        <div class="text-center py-20 rounded-xl border-2 border-dashed"
             style="border-color: var(--border-color); background-color: var(--bg-secondary);">
            <div class="text-5xl mb-4">⚖️</div>
            <h3 class="font-serif text-lg mb-2" style="color: var(--text-primary);">No lawyers found</h3>
            <p class="text-sm" style="color: var(--text-secondary);">Try adjusting your filters.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($lawyers as $lawyer)
            <div class="rounded-xl border p-5 hover-lift"
                 style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0"
                         style="background-color: #0D1B2A;">
                        {{ strtoupper(substr($lawyer->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-sm truncate" style="color: var(--text-primary);">{{ $lawyer->name }}</h3>
                        <p class="text-xs mt-0.5" style="color: var(--text-secondary);">{{ $lawyer->speciality }} · {{ $lawyer->jurisdiction }}</p>
                        <p class="text-xs mt-0.5" style="color: var(--text-secondary);">{{ $lawyer->years_experience }} years experience</p>
                    </div>
                    @if($lawyer->verified)
                        <span class="text-xs px-2 py-0.5 rounded-full flex-shrink-0"
                              style="background-color: rgba(21,128,61,0.1); color: #15803D;">✓ Verified</span>
                    @endif
                </div>

                @if($lawyer->bio)
                    <p class="text-xs mb-4 line-clamp-2" style="color: var(--text-secondary);">{{ $lawyer->bio }}</p>
                @endif

                <div class="flex items-center justify-between pt-3 border-t" style="border-color: var(--border-color);">
                    <span class="text-xs" style="color: var(--text-secondary);">Bar No: {{ $lawyer->bar_number ?? 'N/A' }}</span>
                    <a href="{{ route('fmrefer.show', $lawyer) }}"
                       class="px-4 py-1.5 rounded-lg text-xs font-medium hover:opacity-90"
                       style="background-color: var(--gold); color: #0D1B2A;">View Profile</a>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($lawyers->hasPages())
            <div class="mt-6">{{ $lawyers->links() }}</div>
        @endif
    @endif

    {{-- Disclaimer --}}
    <div class="mt-8 p-4 rounded-xl text-xs" style="background-color: rgba(201,168,76,0.08); border: 1px solid rgba(201,168,76,0.2); color: var(--text-secondary);">
        <strong style="color: var(--text-primary);">Disclaimer:</strong>
        FM Refer connects you with independent legal professionals. FirstMediator does not provide legal advice and is not responsible for the services provided by referred lawyers.
    </div>
</div>
@endsection
