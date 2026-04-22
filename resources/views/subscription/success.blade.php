@extends('layouts.app')
@section('title', 'Payment Successful')
@section('page-title', 'Payment Successful')

@section('content')
<div class="max-w-lg mx-auto text-center py-16">
    <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6"
         style="background: rgba(201,168,76,0.15);">
        <svg class="w-10 h-10" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    @if($type === 'topup')
        <h1 class="text-2xl font-serif mb-3" style="color: var(--text-primary);">Credits Added!</h1>
        <p class="text-sm mb-8" style="color: var(--text-secondary);">
            Your credits have been added to your wallet and are ready to use.
        </p>
    @else
        <h1 class="text-2xl font-serif mb-3" style="color: var(--text-primary);">Subscription Active!</h1>
        <p class="text-sm mb-8" style="color: var(--text-secondary);">
            Your subscription is now active and credits have been added to your wallet.
            You can start creating mediation sessions right away.
        </p>
    @endif

    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('dashboard') }}"
           class="px-6 py-3 rounded-xl text-sm font-bold hover:opacity-90 transition-opacity"
           style="background: var(--gold); color: #0D1B2A;">
            Go to Dashboard
        </a>
        <a href="{{ route('rooms.create') }}"
           class="px-6 py-3 rounded-xl text-sm font-bold hover:opacity-90 transition-opacity"
           style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
            Create a Room
        </a>
    </div>
</div>
@endsection
