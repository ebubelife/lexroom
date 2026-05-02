@extends('layouts.app')

@section('title', 'Settings — First Mediator')
@section('page-title', 'Settings')

@section('content')
<div class="max-w-4xl mx-auto px-3 md:px-0" x-data="settingsPage()">

    {{-- Tabs --}}
    <div class="flex space-x-1 mb-6 overflow-x-auto" style="border-bottom: 1px solid var(--border-color);">
        <button @click="activeTab = 'profile'"
                class="px-4 py-2 text-sm whitespace-nowrap transition-colors"
                :class="activeTab === 'profile' ? 'border-b-2 font-medium' : ''"
                :style="activeTab === 'profile' ? 'border-color: var(--gold); color: var(--gold);' : 'color: var(--text-secondary);'">
            Profile
        </button>
        <button @click="activeTab = 'security'"
                class="px-4 py-2 text-sm whitespace-nowrap transition-colors"
                :class="activeTab === 'security' ? 'border-b-2 font-medium' : ''"
                :style="activeTab === 'security' ? 'border-color: var(--gold); color: var(--gold);' : 'color: var(--text-secondary);'">
            Security
        </button>
        <button @click="activeTab = 'subscription'"
                class="px-4 py-2 text-sm whitespace-nowrap transition-colors"
                :class="activeTab === 'subscription' ? 'border-b-2 font-medium' : ''"
                :style="activeTab === 'subscription' ? 'border-color: var(--gold); color: var(--gold);' : 'color: var(--text-secondary);'">
            Subscription
        </button>
    </div>

    {{-- ===== PROFILE TAB ===== --}}
    <div x-show="activeTab === 'profile'">
        <div class="rounded-xl shadow-sm border p-4 md:p-6 mb-6"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">

            <h2 class="text-lg md:text-xl font-serif mb-4 md:mb-6" style="color: var(--text-primary);">Profile Information</h2>

            {{-- Profile Image --}}
            <div class="mb-6">
                <label class="block text-sm font-medium mb-3" style="color: var(--text-primary);">Profile Image</label>
                <div class="flex flex-col sm:flex-row items-center sm:space-x-4 space-y-3 sm:space-y-0">
                    <div class="w-20 h-20 md:w-24 md:h-24 rounded-full overflow-hidden border-2 flex-shrink-0" style="border-color: var(--gold);">
                        @if(auth()->user()->profile_image_url)
                            <img id="avatar-preview" src="{{ auth()->user()->profile_image_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                        @else
                            <div id="avatar-initials" class="w-full h-full flex items-center justify-center text-white text-2xl font-bold" style="background-color: var(--gold);">
                                {{ auth()->user()->initials }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <input type="file" id="profile_image_input" class="hidden" accept="image/*" onchange="uploadAvatar(this)">
                        <button type="button" onclick="document.getElementById('profile_image_input').click()"
                                class="px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90"
                                style="background-color: var(--gold); color: #0D1B2A;">
                            Change Photo
                        </button>
                        <p class="text-xs mt-2" style="color: var(--text-secondary);">JPG, PNG or GIF. Max 2MB</p>
                    </div>
                </div>
            </div>

            {{-- Profile Form --}}
            <form action="{{ route('settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name', auth()->user()->first_name) }}" required
                               class="w-full px-4 py-3 text-sm rounded-lg border"
                               style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name', auth()->user()->last_name) }}" required
                               class="w-full px-4 py-3 text-sm rounded-lg border"
                               style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Email</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                           class="w-full px-4 py-3 text-sm rounded-lg border"
                           style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                </div>

                <div class="mb-6">
                    <div class="flex justify-between items-end mb-2">
                        <label class="block text-sm font-medium" style="color: var(--text-primary);">Phone (Optional)</label>
                        @if(auth()->user()->phone && !auth()->user()->hasVerifiedPhone())
                            <a href="{{ route('verification.notice') }}" class="text-xs hover:underline" style="color: var(--gold);">Verify Phone</a>
                        @elseif(auth()->user()->hasVerifiedPhone())
                            <span class="text-xs text-green-500">✓ Verified</span>
                        @endif
                    </div>
                    <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                           class="w-full px-4 py-3 text-sm rounded-lg border"
                           style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                </div>

                <button type="submit" class="px-6 py-3 rounded-lg text-white text-sm font-medium hover:opacity-90"
                        style="background-color: var(--gold);">
                    Save Changes
                </button>
            </form>
        </div>
    </div>

    {{-- ===== SECURITY TAB ===== --}}
    <div x-show="activeTab === 'security'">
        <div class="rounded-xl shadow-sm border p-4 md:p-6"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">

            <h2 class="text-lg md:text-xl font-serif mb-4 md:mb-6" style="color: var(--text-primary);">Change Password</h2>

            @if(auth()->user()->google_id && !auth()->user()->password)
                <div class="p-4 rounded-lg text-sm" style="background-color: rgba(201,168,76,0.08); border: 1px solid rgba(201,168,76,0.2); color: var(--text-secondary);">
                    You signed in with Google. To set a password, use the forgot password flow from the login page.
                </div>
            @else
                <form action="{{ route('settings.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Current Password</label>
                        <input type="password" name="current_password" required
                               class="w-full px-4 py-3 text-sm rounded-lg border"
                               style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">New Password</label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-3 text-sm rounded-lg border"
                               style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Confirm New Password</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full px-4 py-3 text-sm rounded-lg border"
                               style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                    </div>

                    <button type="submit" class="px-6 py-3 rounded-lg text-white text-sm font-medium hover:opacity-90"
                            style="background-color: var(--gold);">
                        Update Password
                    </button>
                </form>
            @endif

            {{-- Connected accounts --}}
            <div class="mt-6 pt-6" style="border-top: 1px solid var(--border-color);">
                <h3 class="text-sm font-semibold mb-3" style="color: var(--text-primary);">Connected Accounts</h3>
                <div class="flex items-center justify-between p-3 rounded-lg" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span class="text-sm" style="color: var(--text-primary);">Google</span>
                    </div>
                    @if(auth()->user()->google_id)
                        <span class="text-xs px-2 py-1 rounded-full" style="background-color: rgba(21,128,61,0.1); color: #15803D;">✓ Connected</span>
                    @else
                        <a href="{{ route('auth.google') }}" class="text-xs px-3 py-1.5 rounded-lg font-medium hover:opacity-90"
                           style="background-color: var(--gold); color: #0D1B2A;">Connect</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===== SUBSCRIPTION TAB ===== --}}
    <div x-show="activeTab === 'subscription'">

        {{-- Current plan --}}
        <div class="rounded-xl border p-4 md:p-6 mb-4"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <h2 class="text-lg font-serif mb-4" style="color: var(--text-primary);">Subscription</h2>

            @if($sub)
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-lg font-bold" style="color: var(--text-primary);">{{ $sub->plan->name }}</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                  style="background: rgba(74,222,128,0.12); color: #4ADE80;">Active</span>
                        </div>
                        <p class="text-sm" style="color: var(--text-secondary);">
                            {{ ucfirst($sub->billing_cycle) }} · Renews {{ $sub->current_period_end?->format('d M Y') }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('subscription.cancel') }}">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Cancel at end of billing period?')"
                                class="px-4 py-2 rounded-lg text-sm font-medium hover:opacity-80"
                                style="background: rgba(239,68,68,0.1); color: #F87171; border: 1px solid rgba(239,68,68,0.25);">
                            Cancel Plan
                        </button>
                    </form>
                </div>

                @php
                    $balance = auth()->user()->wallet?->credits_balance ?? 0;
                    $max     = $sub->plan->credits_per_cycle;
                    $pct     = $max > 0 ? min(100, round(($balance / $max) * 100)) : 0;
                @endphp
                <div class="p-4 rounded-xl" style="background: var(--bg-primary); border: 1px solid var(--border-color);">
                    <div class="flex justify-between text-sm mb-2">
                        <span style="color: var(--text-secondary);">Credits remaining</span>
                        <span class="font-bold" style="color: var(--gold);">£{{ number_format($balance, 2) }} / £{{ number_format($max, 2) }}</span>
                    </div>
                    <div class="h-2 rounded-full" style="background: var(--border-color);">
                        <div class="h-2 rounded-full" style="width: {{ $pct }}%; background: var(--gold);"></div>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-sm mb-4" style="color: var(--text-secondary);">You don't have an active subscription.</p>
                    <a href="{{ route('pricing') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold hover:opacity-90"
                       style="background: var(--gold); color: #0D1B2A;">
                        View Plans
                    </a>
                </div>
            @endif
        </div>

        {{-- Top-up packages --}}
        @if($topups->isNotEmpty())
        <div class="rounded-xl border p-4 md:p-6 mb-4"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <h3 class="text-base font-semibold mb-4" style="color: var(--text-primary);">Top-up Credits</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($topups as $pkg)
                    <div class="rounded-xl p-3 text-center" style="background: var(--bg-primary); border: 1px solid var(--border-color);">
                        <p class="text-xs font-semibold mb-1" style="color: var(--text-primary);">{{ $pkg->label }}</p>
                        <p class="text-xl font-bold" style="color: var(--gold);">£{{ number_format($pkg->price, 0) }}</p>
                        <p class="text-xs mb-2" style="color: var(--text-secondary);">
                            £{{ number_format($pkg->credits, 0) }} credits
                            @if($pkg->bonus_credits > 0)
                                <span style="color: #4ADE80;">+£{{ number_format($pkg->bonus_credits, 0) }}</span>
                            @endif
                        </p>
                        <form method="GET" action="{{ route('subscription.topup') }}">
                            <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                            <button type="submit" class="w-full py-1.5 rounded-lg text-xs font-bold hover:opacity-80"
                                    style="background: rgba(201,168,76,0.12); color: var(--gold); border: 1px solid rgba(201,168,76,0.3);">
                                Buy
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Transaction history --}}
        @if($transactions->isNotEmpty())
        <div class="rounded-xl border p-4 md:p-6"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <h3 class="text-base font-semibold mb-4" style="color: var(--text-primary);">Credit History</h3>
            <div class="space-y-2">
                @foreach($transactions as $tx)
                    <div class="flex items-center justify-between py-2 border-b last:border-0"
                         style="border-color: var(--border-color);">
                        <div>
                            <p class="text-sm" style="color: var(--text-primary);">{{ $tx->description }}</p>
                            <p class="text-xs" style="color: var(--text-secondary);">{{ $tx->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <span class="text-sm font-bold" style="color: {{ $tx->amount > 0 ? '#4ADE80' : '#F87171' }};">
                            {{ $tx->amount > 0 ? '+' : '' }}£{{ number_format(abs($tx->amount), 2) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($transactions->isEmpty() && !$sub)
        <div class="rounded-xl border p-8 text-center"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <p class="text-sm" style="color: var(--text-secondary);">No credit history yet.</p>
        </div>
        @endif
    </div>

</div>

<script>
function settingsPage() {
    return {
        activeTab: 'profile',
    }
}

async function uploadAvatar(input) {
    const file = input.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('profile_image', file);

    try {
        const res = await fetch('{{ route('settings.avatar') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            const preview = document.getElementById('avatar-preview');
            const initials = document.getElementById('avatar-initials');
            if (preview) preview.src = data.url;
            if (initials) initials.style.display = 'none';
            if (!preview) {
                const img = document.createElement('img');
                img.id = 'avatar-preview';
                img.src = data.url;
                img.className = 'w-full h-full object-cover';
                document.querySelector('.rounded-full.overflow-hidden').appendChild(img);
            }
            showToast('Photo updated!', 'success');
        } else {
            showToast(data.message || 'Could not update photo', 'error');
        }
    } catch (e) {
        showToast('Could not update photo', 'error');
    }
}
</script>

@if(session('success'))
<script>document.addEventListener('DOMContentLoaded', () => showToast('{{ session('success') }}', 'success'));</script>
@endif
@if($errors->any())
<script>document.addEventListener('DOMContentLoaded', () => showToast('{{ $errors->first() }}', 'error'));</script>
@endif

@endsection
