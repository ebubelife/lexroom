@extends('layouts.auth')

@section('title', 'Create your First Mediator account — Resolve disputes without a lawyer')
@section('description', 'Sign up to First Mediator and start resolving disputes with AI-assisted mediation. No lawyer needed. Setup in 15 minutes.')

@section('content')
<div class="p-8 rounded-xl shadow-lg" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color);">
    <!-- Logo -->
    <div class="text-center mb-8">
        <img 
            src="{{ asset('assets/images/logos/fm-lightmode.png') }}" 
            alt="First Mediator" 
            class="h-32 mx-auto"
            style="display: var(--logo-light-display) !important;"
        >
        <img 
            src="{{ asset('assets/images/logos/fm-darkmode.png') }}" 
            alt="First Mediator" 
            class="h-32 mx-auto"
            style="display: var(--logo-dark-display) !important;"
        >
    </div>

    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-2xl font-serif mb-2" style="color: var(--text-primary);">Create your account</h1>
        <p class="text-sm" style="color: var(--text-secondary);">Join thousands resolving disputes the smart way.</p>
    </div>

    <!-- Success Message -->
    @if (session('status'))
        <div class="mb-6 p-4 rounded-lg" style="background-color: var(--gold-pale); color: var(--navy); border: 1px solid var(--gold);">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 rounded-lg" style="background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Google OAuth Button -->
    <a 
        href="{{ route('auth.google') }}" 
        class="w-full flex items-center justify-center py-3 px-4 rounded-lg border font-medium transition-colors hover:bg-opacity-50 mb-6"
        style="border-color: var(--border-color); color: var(--text-primary); background-color: var(--bg-primary);"
    >
        <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        Continue with Google
    </a>

    <!-- Divider -->
    <div class="relative mb-6">
        <div style="border-top: 1px solid var(--border-color);"></div>
        <div class="absolute inset-0 flex justify-center">
            <span class="px-3 text-sm" style="background-color: var(--bg-secondary); color: var(--text-secondary);">or</span>
        </div>
    </div>

    <!-- Registration Form -->
    <form method="POST" action="{{ route('register') }}" x-data="{ showPassword: false, showConfirmPassword: false }">
        @csrf

        <!-- Full Name -->
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Full Name</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="{{ old('name') }}" 
                required 
                class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-opacity-50 transition-colors"
                style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary); focus:ring-color: var(--gold);"
                placeholder="Enter your full name"
            >
            @error('name')
                <p class="mt-1 text-sm" style="color: #ef4444;">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Email Address</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-opacity-50 transition-colors"
                style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary); focus:ring-color: var(--gold);"
                placeholder="Enter your email address"
            >
            @error('email')
                <p class="mt-1 text-sm" style="color: #ef4444;">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Password</label>
            <div class="relative">
                <input 
                    :type="showPassword ? 'text' : 'password'" 
                    id="password" 
                    name="password" 
                    required 
                    class="w-full px-4 py-3 pr-12 rounded-lg border focus:ring-2 focus:ring-opacity-50 transition-colors"
                    style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary); focus:ring-color: var(--gold);"
                    placeholder="Enter your password"
                >
                <button 
                    type="button" 
                    @click="showPassword = !showPassword"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-sm"
                    style="color: var(--text-secondary);"
                >
                    <span x-show="!showPassword" x-cloak>Show</span>
                    <span x-show="showPassword" x-cloak>Hide</span>
                </button>
            </div>
            @error('password')
                <p class="mt-1 text-sm" style="color: #ef4444;">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Confirm Password</label>
            <div class="relative">
                <input 
                    :type="showConfirmPassword ? 'text' : 'password'" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    required 
                    class="w-full px-4 py-3 pr-12 rounded-lg border focus:ring-2 focus:ring-opacity-50 transition-colors"
                    style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary); focus:ring-color: var(--gold);"
                    placeholder="Confirm your password"
                >
                <button 
                    type="button" 
                    @click="showConfirmPassword = !showConfirmPassword"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-sm"
                    style="color: var(--text-secondary);"
                >
                    <span x-show="!showConfirmPassword" x-cloak>Show</span>
                    <span x-show="showConfirmPassword" x-cloak>Hide</span>
                </button>
            </div>
            @error('password_confirmation')
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
                    I agree to the <a href="{{ route('terms') }}" class="underline hover:no-underline" style="color: var(--gold);">Terms of Service</a> and <a href="{{ route('privacy') }}" class="underline hover:no-underline" style="color: var(--gold);">Privacy Policy</a>
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
            Create Account
        </button>
    </form>

    <!-- Divider -->
    <div class="my-6" style="border-top: 1px solid var(--border-color);"></div>

    <!-- Login Link -->
    <div class="text-center">
        <p class="text-sm" style="color: var(--text-secondary);">
            Already have an account? 
            <a href="{{ route('login') }}" class="font-medium hover:underline" style="color: var(--gold);">Log in</a>
        </p>
    </div>
</div>
@endsection