@extends('layouts.app')

@section('title', 'Reports — LexRoom')
@section('page-title', 'Reports')

@section('content')
<div class="max-w-4xl mx-auto text-center py-16">
    <div class="animate-float mb-8">
        <svg class="w-16 h-16 mx-auto" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
    </div>
    <h1 class="text-2xl font-serif mb-4" style="color: var(--text-primary);">Reports</h1>
    <p class="text-lg mb-8" style="color: var(--text-secondary);">Coming soon! This page will show your mediation reports and case summaries.</p>
    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors hover:opacity-90" style="background-color: var(--gold); color: var(--white);">
        Back to Dashboard
    </a>
</div>
@endsection