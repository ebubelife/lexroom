@extends('layouts.auth')

@section('title', 'Reset your password — FirstMediator')
@section('description', 'Enter your email to receive a password reset link for your FirstMediator account.')

@section('content')
<div class="p-8 rounded-xl shadow-lg" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color);">
    <!-- Logo -->
    <div class="text-center mb-8">
        <img 
            src="{{ asset('assets/images/logos/FM_Logo_Dark.svg') }}" 
            alt="FirstMediator" 
            class="h-10 mx-auto logo-light"
            style="display: var(--logo-light-display, block);"
        >
        <img 
            src="{{ asset('assets/images/logos/FM_Logo_Light.svg') }}" 
            alt="FirstMediator" 
            class="h-10 mx-auto logo-dark"
            style="display: var(--logo-dark-display, none);"
        >
    </div>

    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-2xl font-serif mb-2" style="color: var(--text-primary);">Reset your password</h1>
        <p class="text-sm" style="color: var(--text-secondary);">Enter your email and we'll send you a reset link.</p>
    </div>

    <!-- Status Message -->
    @if (session('status'))
        <div class="mb-6 p-4 rounded-lg" style="background-color: var(--gold-pale); color: var(--navy); border: 1px solid var(--gold);">
            We've sent a password reset link to your email.
        </div>
    @endif

    <!-- Reset Form -->
    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email -->
        <div class="mb-6">
            <label for="email" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Email Address</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autofocus
                class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-opacity-50 transition-colors"
                style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary); focus:ring-color: var(--gold);"
                placeholder="Enter your email address"
            >
            @error('email')
                <p class="mt-1 text-sm" style="color: #ef4444;">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button 
            type="submit" 
            class="w-full py-3 px-4 rounded-lg font-medium transition-colors hover:opacity-90"
            style="background-color: var(--gold); color: var(--white);"
        >
            Send Reset Link
        </button>
    </form>

    <!-- Back Link -->
    <div class="mt-6 text-center">
        <a href="{{ route('login') }}" class="text-sm hover:underline inline-flex items-center" style="color: var(--text-secondary);">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to login
        </a>
    </div>
</div>
@endsection