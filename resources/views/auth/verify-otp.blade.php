@extends('layouts.auth')

@section('title', 'Verify Your Account — FirstMediator')
@section('description', 'Verify your email and phone number to complete your FirstMediator account setup.')

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
        <h1 class="text-2xl font-serif mb-2" style="color: var(--text-primary);">Verify your account</h1>
        <p class="text-sm" style="color: var(--text-secondary);">
            We need to verify your contact information before you can access your dashboard.
        </p>
    </div>

    <!-- Status Messages -->
    @if (session('success'))
        <div class="mb-6 p-4 rounded-lg" style="background-color: var(--gold-pale); color: var(--navy); border: 1px solid var(--gold);">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 rounded-lg" style="background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca;">
            {{ session('error') }}
        </div>
    @endif

    <div x-data="{ activeTab: '{{ $needsEmailVerification ? 'email' : 'phone' }}' }">
        <!-- Verification Status -->
        <div class="mb-6 space-y-3">
            <!-- Email Status -->
            <div class="flex items-center justify-between p-3 rounded-lg" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" style="color: var(--text-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium" style="color: var(--text-primary);">Email Address</p>
                        <p class="text-xs" style="color: var(--text-secondary);">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                @if(auth()->user()->hasVerifiedEmail())
                    <span class="px-2 py-1 rounded-full text-xs font-medium" style="background-color: #f0fdf4; color: #15803d;">
                        ✓ Verified
                    </span>
                @else
                    <button 
                        @click="activeTab = 'email'"
                        class="px-3 py-1 rounded text-xs font-medium transition-colors hover:opacity-90"
                        style="background-color: var(--gold); color: var(--white);"
                    >
                        Verify Now
                    </button>
                @endif
            </div>

            <!-- Phone Status -->
            <div class="flex items-center justify-between p-3 rounded-lg" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" style="color: var(--text-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium" style="color: var(--text-primary);">Phone Number</p>
                        <p class="text-xs" style="color: var(--text-secondary);">{{ auth()->user()->phone }}</p>
                    </div>
                </div>
                @if(auth()->user()->hasVerifiedPhone())
                    <span class="px-2 py-1 rounded-full text-xs font-medium" style="background-color: #f0fdf4; color: #15803d;">
                        ✓ Verified
                    </span>
                @else
                    <button 
                        @click="activeTab = 'phone'"
                        class="px-3 py-1 rounded text-xs font-medium transition-colors hover:opacity-90"
                        style="background-color: var(--gold); color: var(--white);"
                    >
                        Verify Now
                    </button>
                @endif
            </div>
        </div>

        @if($needsEmailVerification)
        <!-- Email Verification Tab -->
        <div x-show="activeTab === 'email'" x-transition>
            <div class="mb-6">
                <h3 class="text-lg font-medium mb-2" style="color: var(--text-primary);">Verify Email Address</h3>
                <p class="text-sm mb-4" style="color: var(--text-secondary);">
                    We'll send a 6-digit code to {{ auth()->user()->email }}
                </p>

                <!-- Send OTP Form -->
                <form method="POST" action="{{ route('otp.send') }}" class="mb-4">
                    @csrf
                    <input type="hidden" name="type" value="email">
                    <button 
                        type="submit" 
                        class="w-full py-3 px-4 rounded-lg font-medium transition-colors hover:opacity-90"
                        style="background-color: var(--gold); color: var(--white);"
                    >
                        Send Email OTP
                    </button>
                </form>

                <!-- Verify OTP Form -->
                <form method="POST" action="{{ route('otp.verify') }}">
                    @csrf
                    <input type="hidden" name="type" value="email">
                    
                    <div class="mb-4">
                        <label for="email_code" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Enter 6-digit code</label>
                        <input 
                            type="text" 
                            id="email_code" 
                            name="code" 
                            maxlength="6"
                            class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-opacity-50 transition-colors text-center text-lg font-mono"
                            style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary); focus:ring-color: var(--gold);"
                            placeholder="000000"
                        >
                        @error('code')
                            <p class="mt-1 text-sm" style="color: #ef4444;">{{ $message }}</p>
                        @enderror
                    </div>

                    <button 
                        type="submit" 
                        class="w-full py-3 px-4 rounded-lg font-medium transition-colors hover:opacity-90"
                        style="background-color: var(--gold); color: var(--white);"
                    >
                        Verify Email
                    </button>
                </form>
            </div>
        </div>
        @endif

        @if($needsPhoneVerification)
        <!-- Phone Verification Tab -->
        <div x-show="activeTab === 'phone'" x-transition>
            <div class="mb-6">
                <h3 class="text-lg font-medium mb-2" style="color: var(--text-primary);">Verify Phone Number</h3>
                <p class="text-sm mb-4" style="color: var(--text-secondary);">
                    We'll send a 6-digit code to {{ auth()->user()->phone }}
                </p>

                <!-- Send OTP Form -->
                <form method="POST" action="{{ route('otp.send') }}" class="mb-4">
                    @csrf
                    <input type="hidden" name="type" value="phone">
                    <button 
                        type="submit" 
                        class="w-full py-3 px-4 rounded-lg font-medium transition-colors hover:opacity-90"
                        style="background-color: var(--gold); color: var(--white);"
                    >
                        Send Phone OTP
                    </button>
                </form>

                <!-- Verify OTP Form -->
                <form method="POST" action="{{ route('otp.verify') }}">
                    @csrf
                    <input type="hidden" name="type" value="phone">
                    
                    <div class="mb-4">
                        <label for="phone_code" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Enter 6-digit code</label>
                        <input 
                            type="text" 
                            id="phone_code" 
                            name="code" 
                            maxlength="6"
                            class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-opacity-50 transition-colors text-center text-lg font-mono"
                            style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary); focus:ring-color: var(--gold);"
                            placeholder="111111"
                        >
                        @error('code')
                            <p class="mt-1 text-sm" style="color: #ef4444;">{{ $message }}</p>
                        @enderror
                    </div>

                    <button 
                        type="submit" 
                        class="w-full py-3 px-4 rounded-lg font-medium transition-colors hover:opacity-90"
                        style="background-color: var(--gold); color: var(--white);"
                    >
                        Verify Phone
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    <!-- Skip for now link -->
    <div class="mt-6 text-center">
        <a href="{{ route('dashboard') }}" class="text-sm hover:underline" style="color: var(--text-secondary);">
            Skip verification for now (not recommended)
        </a>
    </div>
</div>
@endsection