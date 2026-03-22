@extends('layouts.app')

@section('title', 'Wallet & Credits — LexRoom')
@section('page-title', 'Wallet & Credits')

@section('content')
<div class="max-w-4xl mx-auto text-center py-16">
    <div class="animate-float mb-8">
        <svg class="w-16 h-16 mx-auto" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
        </svg>
    </div>
    <h1 class="text-2xl font-serif mb-4" style="color: var(--text-primary);">Wallet & Credits</h1>
    <p class="text-lg mb-8" style="color: var(--text-secondary);">Coming soon! This page will show your credit balance and transaction history.</p>
    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors hover:opacity-90" style="background-color: var(--gold); color: var(--white);">
        Back to Dashboard
    </a>
</div>
@endsection