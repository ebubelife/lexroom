@extends('layouts.app')

@section('title', 'My Rooms — FirstMediator')
@section('page-title', 'My Rooms')

@section('content')
<div class="max-w-4xl mx-auto text-center py-16">
    <div class="animate-float mb-8">
        <svg class="w-16 h-16 mx-auto" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M10.5 3L12 2l1.5 1H21v6H3V3h7.5z"></path>
        </svg>
    </div>
    <h1 class="text-2xl font-serif mb-4" style="color: var(--text-primary);">My Rooms</h1>
    <p class="text-lg mb-8" style="color: var(--text-secondary);">Coming soon! This page will show all your dispute rooms.</p>
    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors hover:opacity-90" style="background-color: var(--gold); color: var(--white);">
        Back to Dashboard
    </a>
</div>
@endsection