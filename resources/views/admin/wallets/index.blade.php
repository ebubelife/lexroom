@extends('admin.layouts.app')

@section('title', 'Wallets')
@section('page-title', 'Wallets')

@section('content')
<div x-data="{ adjustModal: false, selectedWallet: null, selectedUser: '' }" class="space-y-4">

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Total Credits</p>
                    <p class="text-2xl font-semibold">₦{{ number_format($stats['total_credits'], 0) }}</p>
                </div>
                <div class="p-2 rounded-lg" style="background: rgba(74,222,128,0.12);">
                    <svg class="w-5 h-5" style="color: #4ADE80;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs mt-2" style="color: var(--text-secondary);">Across all user wallets</p>
        </div>
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Total Escrow</p>
                    <p class="text-2xl font-semibold">₦{{ number_format($stats['total_escrow'], 0) }}</p>
                </div>
                <div class="p-2 rounded-lg" style="background: rgba(251,191,36,0.12);">
                    <svg class="w-5 h-5" style="color: #FCD34D;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs mt-2" style="color: var(--text-secondary);">Held in escrow</p>
        </div>
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Referral Minutes</p>
                    <p class="text-2xl font-semibold">{{ number_format($stats['total_referral']) }} min</p>
                </div>
                <div class="p-2 rounded-lg" style="background: rgba(201,168,76,0.12);">
                    <svg class="w-5 h-5" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs mt-2" style="color: var(--text-secondary);">Across all users</p>
        </div>
    </div>

    {{-- Toolbar --}}
    <form method="GET" action="{{ route('admin.wallets.index') }}" class="flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search user name or email…"
               class="flex-1 px-3 py-2 rounded-lg text-sm outline-none"
               style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">

        <select name="filter" class="px-3 py-2 rounded-lg text-sm outline-none"
                style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">
            <option value="">All wallets</option>
            <option value="has_escrow"   {{ request('filter') === 'has_escrow'   ? 'selected' : '' }}>Has Escrow</option>
            <option value="has_referral" {{ request('filter') === 'has_referral' ? 'selected' : '' }}>Has Referral Minutes</option>
            <option value="zero_balance" {{ request('filter') === 'zero_balance' ? 'selected' : '' }}>Zero Balance</option>
        </select>

        <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium flex-shrink-0"
                style="background: var(--gold); color: #0D1B2A;">Filter</button>

        @if(request()->hasAny(['search', 'filter']))
            <a href="{{ route('admin.wallets.index') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium flex-shrink-0 text-center"
               style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-secondary);">Clear</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="px-4 py-3" style="border-bottom: 1px solid var(--border-color);">
            <p class="text-sm" style="color: var(--text-secondary);">{{ number_format($wallets->total()) }} wallet{{ $wallets->total() !== 1 ? 's' : '' }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full data-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Credits Balance</th>
                        <th>Escrow</th>
                        <th>Referral Minutes</th>
                        <th>Referral Expiry</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wallets as $wallet)
                        <tr>
                            <td>
                                @if($wallet->user)
                                    <a href="{{ route('admin.users.show', $wallet->user) }}"
                                       class="text-sm font-medium hover:underline">{{ $wallet->user->name }}</a>
                                    <p class="text-xs" style="color: var(--text-secondary);">{{ $wallet->user->email }}</p>
                                @else
                                    <span style="color: var(--text-secondary);">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-sm font-semibold" style="color: #4ADE80;">
                                    ₦{{ number_format($wallet->credits_balance, 0) }}
                                </span>
                            </td>
                            <td>
                                @if($wallet->escrow_balance > 0)
                                    <span class="text-sm font-medium" style="color: #FCD34D;">
                                        ₦{{ number_format($wallet->escrow_balance, 0) }}
                                    </span>
                                @else
                                    <span class="text-sm" style="color: var(--text-secondary);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($wallet->referral_minutes > 0)
                                    <span class="text-sm font-medium" style="color: var(--gold);">
                                        {{ $wallet->referral_minutes }} min
                                    </span>
                                @else
                                    <span class="text-sm" style="color: var(--text-secondary);">—</span>
                                @endif
                            </td>
                            <td class="text-xs" style="color: var(--text-secondary);">
                                {{ $wallet->referral_minutes_expires_at?->format('d M Y') ?? '—' }}
                            </td>
                            <td>
                                <div class="flex items-center gap-1.5">
                                    {{-- Adjust balance --}}
                                    <button
                                        @click="selectedWallet = {{ $wallet->id }}; selectedUser = '{{ addslashes($wallet->user?->name ?? '') }}'; adjustModal = true"
                                        class="text-xs px-2.5 py-1.5 rounded-lg font-medium hover:opacity-80"
                                        style="background: rgba(201,168,76,0.12); color: var(--gold); border: 1px solid rgba(201,168,76,0.25);">
                                        Adjust
                                    </button>

                                    {{-- Release escrow --}}
                                    @if($wallet->escrow_balance > 0)
                                        <form method="POST" action="{{ route('admin.wallets.release-escrow', $wallet) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="text-xs px-2.5 py-1.5 rounded-lg font-medium hover:opacity-80"
                                                    style="background: rgba(251,191,36,0.1); color: #FCD34D; border: 1px solid rgba(251,191,36,0.25);"
                                                    onclick="return confirm('Release ₦{{ number_format($wallet->escrow_balance, 0) }} from escrow to credits?')">
                                                Release Escrow
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12" style="color: var(--text-secondary);">No wallets found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($wallets->hasPages())
            <div class="px-4 py-3 flex items-center justify-between" style="border-top: 1px solid var(--border-color);">
                <p class="text-xs" style="color: var(--text-secondary);">
                    Showing {{ $wallets->firstItem() }}–{{ $wallets->lastItem() }} of {{ $wallets->total() }}
                </p>
                <div class="flex gap-1">
                    @if($wallets->onFirstPage())
                        <span class="px-3 py-1.5 rounded-lg text-xs opacity-30" style="background: var(--bg-card); border: 1px solid var(--border-color);">← Prev</span>
                    @else
                        <a href="{{ $wallets->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg text-xs hover:opacity-80" style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">← Prev</a>
                    @endif
                    @if($wallets->hasMorePages())
                        <a href="{{ $wallets->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg text-xs hover:opacity-80" style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">Next →</a>
                    @else
                        <span class="px-3 py-1.5 rounded-lg text-xs opacity-30" style="background: var(--bg-card); border: 1px solid var(--border-color);">Next →</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Adjust Balance Modal --}}
    <div x-show="adjustModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.75);">
        <div class="w-full max-w-sm rounded-xl p-5 space-y-4"
             style="background: var(--bg-card); border: 1px solid var(--border-color);" @click.stop>
            <h3 class="text-base font-semibold">
                Adjust Wallet — <span x-text="selectedUser" style="color: var(--gold);"></span>
            </h3>

            @foreach($wallets as $wallet)
                <form x-show="selectedWallet === {{ $wallet->id }}"
                      method="POST"
                      action="{{ route('admin.wallets.adjust', $wallet) }}"
                      class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Type</label>
                        <select name="type" class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                                style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                            <option value="add">Add credits</option>
                            <option value="deduct">Deduct credits</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Amount (₦)</label>
                        <input type="number" name="amount" min="1" placeholder="0"
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Reason <span style="color: #F87171;">*</span></label>
                        <input type="text" name="reason" required placeholder="e.g. Refund, bonus, correction"
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="adjustModal = false; selectedWallet = null"
                                class="flex-1 py-2 rounded-lg text-sm font-medium"
                                style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-secondary);">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 py-2 rounded-lg text-sm font-medium hover:opacity-80"
                                style="background: var(--gold); color: #0D1B2A;">
                            Apply
                        </button>
                    </div>
                </form>
            @endforeach
        </div>
    </div>

</div>
@endsection
