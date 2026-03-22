@extends('layouts.app')

@section('title', 'LexRefer — LexRoom')
@section('page-title', 'LexRefer')

@section('content')
<div class="max-w-4xl mx-auto text-center py-16">
    <div class="animate-float mb-8">
        <svg class="w-16 h-16 mx-auto" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l3-1m-3 1l-3-1"></path>
        </svg>
    </div>
    <h1 class="text-2xl font-serif mb-4" style="color: var(--text-primary);">LexRefer</h1>
    <p class="text-lg mb-8" style="color: var(--text-secondary);">Coming soon! This page will show your referral program and earnings.</p>
    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors hover:opacity-90" style="background-color: var(--gold); color: var(--white);">
        Back to Dashboard
    </a>
</div>
@endsection