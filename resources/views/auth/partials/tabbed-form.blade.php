@php
    $initialTab = $activeTab ?? 'signin';
    if (old('first_name') !== null || $errors->has('first_name') || $errors->has('last_name') || $errors->has('password_confirmation') || $errors->has('terms')) {
        $initialTab = 'signup';
    }
@endphp
<div class="p-8 rounded-xl shadow-lg" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color);" x-data="{ tab: '{{ $initialTab }}', showPassword: false, showConfirmPassword: false }">
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

    <!-- Tabs -->
    <div class="grid grid-cols-2 gap-1 p-1 rounded-lg mb-8" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
        <button
            type="button"
            @click="tab = 'signin'"
            class="py-2 px-4 rounded-md text-sm font-medium transition-colors"
            :style="tab === 'signin' ? 'background-color: var(--gold); color: var(--white);' : 'background-color: transparent; color: var(--text-secondary);'"
        >
            Sign In
        </button>
        <button
            type="button"
            @click="tab = 'signup'"
            class="py-2 px-4 rounded-md text-sm font-medium transition-colors"
            :style="tab === 'signup' ? 'background-color: var(--gold); color: var(--white);' : 'background-color: transparent; color: var(--text-secondary);'"
        >
            Create Account
        </button>
    </div>

    <!-- Header -->
    <div class="text-center mb-8">
        <template x-if="tab === 'signin'">
            <div>
                <h1 class="text-2xl font-serif mb-2" style="color: var(--text-primary);">Welcome back</h1>
                <p class="text-sm" style="color: var(--text-secondary);">Log in to continue to your First Mediator dashboard.</p>
            </div>
        </template>
        <template x-if="tab === 'signup'">
            <div>
                <h1 class="text-2xl font-serif mb-2" style="color: var(--text-primary);">Create your account</h1>
                <p class="text-sm" style="color: var(--text-secondary);">Join thousands resolving disputes the smart way.</p>
            </div>
        </template>
    </div>

    <!-- Status Messages -->
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

    <!-- Sign In Form -->
    <form method="POST" action="{{ route('login') }}" x-show="tab === 'signin'" x-cloak>
        @csrf

        <div class="mb-4">
            <label for="signin-email" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Email Address</label>
            <input
                type="email"
                id="signin-email"
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

        <div class="mb-4">
            <div class="flex items-center justify-between mb-2">
                <label for="signin-password" class="text-sm font-medium" style="color: var(--text-primary);">Password</label>
                <a href="{{ route('password.request') }}" class="text-sm hover:underline" style="color: var(--gold);">
                    Forgot your password?
                </a>
            </div>
            <input
                type="password"
                id="signin-password"
                name="password"
                required
                class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-opacity-50 transition-colors"
                style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary); focus:ring-color: var(--gold);"
                placeholder="Enter your password"
            >
            @error('password')
                <p class="mt-1 text-sm" style="color: #ef4444;">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="flex items-center">
                <input
                    type="checkbox"
                    name="remember"
                    value="1"
                    class="mr-3 rounded border focus:ring-2 focus:ring-opacity-50"
                    style="border-color: var(--border-color); focus:ring-color: var(--gold);"
                >
                <span class="text-sm" style="color: var(--text-secondary);">Remember me</span>
            </label>
        </div>

        <button
            type="submit"
            class="w-full py-3 px-4 rounded-lg font-medium transition-colors hover:opacity-90"
            style="background-color: var(--gold); color: var(--white);"
        >
            Log In
        </button>
    </form>

    <!-- Sign Up Form -->
    <form method="POST" action="{{ route('register') }}" x-show="tab === 'signup'" x-cloak>
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="signup-first_name" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">First Name</label>
                <input
                    type="text"
                    id="signup-first_name"
                    name="first_name"
                    value="{{ old('first_name') }}"
                    required
                    class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-opacity-50 transition-colors"
                    style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"
                    placeholder="First Name"
                >
                @error('first_name')
                    <p class="mt-1 text-sm" style="color: #ef4444;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="signup-last_name" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Last Name</label>
                <input
                    type="text"
                    id="signup-last_name"
                    name="last_name"
                    value="{{ old('last_name') }}"
                    required
                    class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-opacity-50 transition-colors"
                    style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"
                    placeholder="Last Name"
                >
                @error('last_name')
                    <p class="mt-1 text-sm" style="color: #ef4444;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label for="signup-email" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Email Address</label>
            <input
                type="email"
                id="signup-email"
                name="email"
                value="{{ old('email', $prefillEmail ?? '') }}"
                required
                class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-opacity-50 transition-colors"
                style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary); focus:ring-color: var(--gold);"
                placeholder="Enter your email address"
            >
            @error('email')
                <p class="mt-1 text-sm" style="color: #ef4444;">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="signup-password" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Password</label>
            <div class="relative">
                <input
                    :type="showPassword ? 'text' : 'password'"
                    id="signup-password"
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

        <div class="mb-6">
            <label for="signup-password_confirmation" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Confirm Password</label>
            <div class="relative">
                <input
                    :type="showConfirmPassword ? 'text' : 'password'"
                    id="signup-password_confirmation"
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

        <button
            type="submit"
            class="w-full py-3 px-4 rounded-lg font-medium transition-colors hover:opacity-90"
            style="background-color: var(--gold); color: var(--white);"
        >
            Create Account
        </button>
    </form>
</div>
