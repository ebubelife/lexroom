@extends('layouts.auth')

@section('title', 'Complete your LexRoom account — Google Sign Up')
@section('description', 'Complete your LexRoom account setup after signing up with Google.')

@section('content')
<div class="p-8 rounded-xl shadow-lg" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color);">
    <!-- Logo -->
    <div class="text-center mb-8">
        <img 
            src="{{ asset('assets/images/logos/FM_Logo_Dark.svg') }}" 
            alt="LexRoom" 
            class="h-10 mx-auto logo-light"
            style="display: var(--logo-light-display, block);"
        >
        <img 
            src="{{ asset('assets/images/logos/FM_Logo_Light.svg') }}" 
            alt="LexRoom" 
            class="h-10 mx-auto logo-dark"
            style="display: var(--logo-dark-display, none);"
        >
    </div>

    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-2xl font-serif mb-2" style="color: var(--text-primary);">Complete your account</h1>
        <p class="text-sm" style="color: var(--text-secondary);">Add your phone number to complete your LexRoom setup.</p>
    </div>

    <!-- Google Account Info -->
    <div class="mb-6 p-4 rounded-lg" style="background-color: var(--gold-pale); border: 1px solid var(--gold);">
        <div class="flex items-center">
            <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
            </div>
            <div>
                <p class="font-medium" style="color: var(--navy);">{{ session('google_user_name', 'Google Account') }}</p>
                <p class="text-sm" style="color: var(--gray-600);">{{ session('google_user_email', 'Connected') }}</p>
            </div>
        </div>
    </div>

    <!-- Completion Form -->
    <form method="POST" action="{{ route('auth.google.complete') }}">
        @csrf

        <!-- Phone Number -->
        <div class="mb-6">
            <label for="phone" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Phone Number</label>
            <input 
                type="tel" 
                id="phone" 
                name="phone" 
                value="{{ old('phone') }}" 
                required 
                autofocus
                class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-opacity-50 transition-colors"
                style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary); focus:ring-color: var(--gold);"
                placeholder="Enter your phone number"
            >
            @error('phone')
                <p class="mt-1 text-sm" style="color: #ef4444;">{{ $message }}</p>
            @enderror
        </div>

        <!-- Terms Checkbox -->
        <div class="mb-6">
            <label class="flex items-start">
                <input 
                    type="checkbox" 
                    name="terms" 
                    value="1" 
                    required 
                    class="mt-1 mr-3 rounded border focus:ring-2 focus:ring-opacity-50"
                    style="border-color: var(--border-color); focus:ring-color: var(--gold);"
                >
                <span class="text-sm" style="color: var(--text-secondary);">
                    I agree to the <a href="#" class="underline hover:no-underline" style="color: var(--gold);">Terms of Service</a> and <a href="#" class="underline hover:no-underline" style="color: var(--gold);">Privacy Policy</a>
                </span>
            </label>
            @error('terms')
                <p class="mt-1 text-sm" style="color: #ef4444;">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button 
            type="submit" 
            class="w-full py-3 px-4 rounded-lg font-medium transition-colors hover:opacity-90"
            style="background-color: var(--gold); color: var(--white);"
        >
            Complete Account Setup
        </button>
    </form>

    <!-- Divider -->
    <div class="my-6" style="border-top: 1px solid var(--border-color);"></div>

    <!-- Back Link -->
    <div class="text-center">
        <a href="{{ route('login') }}" class="text-sm hover:underline inline-flex items-center" style="color: var(--text-secondary);">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to login
        </a>
    </div>
</div>
@endsection