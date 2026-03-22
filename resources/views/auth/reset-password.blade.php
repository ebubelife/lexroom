@extends('layouts.auth')

@section('title', 'Create a new password — FirstMediator')
@section('description', 'Set a new secure password for your FirstMediator account.')

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
        <h1 class="text-2xl font-serif mb-2" style="color: var(--text-primary);">Create a new password</h1>
        <p class="text-sm" style="color: var(--text-secondary);">Choose a strong password for your account.</p>
    </div>

    <!-- Reset Form -->
    <form method="POST" action="{{ route('password.update') }}" x-data="{ showPassword: false, showConfirmPassword: false }">
        @csrf

        <!-- Hidden Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email (hidden) -->
        <input type="hidden" name="email" value="{{ $request->email }}">

        <!-- New Password -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">New Password</label>
            <div class="relative">
                <input 
                    :type="showPassword ? 'text' : 'password'" 
                    id="password" 
                    name="password" 
                    required 
                    autofocus
                    class="w-full px-4 py-3 pr-12 rounded-lg border focus:ring-2 focus:ring-opacity-50 transition-colors"
                    style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary); focus:ring-color: var(--gold);"
                    placeholder="Enter your new password"
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

        <!-- Confirm New Password -->
        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Confirm New Password</label>
            <div class="relative">
                <input 
                    :type="showConfirmPassword ? 'text' : 'password'" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    required 
                    class="w-full px-4 py-3 pr-12 rounded-lg border focus:ring-2 focus:ring-opacity-50 transition-colors"
                    style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary); focus:ring-color: var(--gold);"
                    placeholder="Confirm your new password"
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

        <!-- Submit Button -->
        <button 
            type="submit" 
            class="w-full py-3 px-4 rounded-lg font-medium transition-colors hover:opacity-90"
            style="background-color: var(--gold); color: var(--white);"
        >
            Update Password
        </button>
    </form>
</div>
@endsection