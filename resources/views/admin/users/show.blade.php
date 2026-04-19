@extends('admin.layouts.app')

@section('title', $user->name)
@section('page-title', $user->name)

@section('content')
<div x-data="{ suspendModal: false, walletModal: false }" class="space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm" style="color: var(--text-secondary);">
        <a href="{{ route('admin.users.index') }}" class="hover:underline" style="color: var(--gold);">Users</a>
        <span>/</span>
        <span>{{ $user->name }}</span>
    </div>

    {{-- Top: Profile + Actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Profile card --}}
        <div class="lg:col-span-2 rounded-xl p-5 space-y-4" style="background: var(--bg-card); border: 1px solid var(--border-color);">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold flex-shrink-0"
                     style="background: rgba(201,168,76,0.15); color: var(--gold);">
                    {{ $user->initials }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-lg font-semibold">{{ $user->name }}</h2>
                        @if($user->suspended_at)
                            <span class="badge" style="background: rgba(239,68,68,0.12); color: #F87171;">Suspended</span>
                        @else
                            <span class="badge" style="background: rgba(74,222,128,0.12); color: #4ADE80;">Active</span>
                        @endif
                    </div>
                    <p class="text-sm mt-0.5" style="color: var(--text-secondary);">{{ $user->email }}</p>
                    <p class="text-sm" style="color: var(--text-secondary);">{{ $user->phone ?? 'No phone' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-2" style="border-top: 1px solid var(--border-color);">
                <div>
                    <p class="text-xs uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Email</p>
                    @if($user->email_verified_at)
                        <p class="text-sm" style="color: #4ADE80;">✓ Verified</p>
                        <p class="text-xs" style="color: var(--text-secondary);">{{ $user->email_verified_at->format('d M Y') }}</p>
                    @else
                        <p class="text-sm" style="color: #F87171;">✗ Unverified</p>
                        <form method="POST" action="{{ route('admin.users.verify-email', $user) }}" class="mt-1">
                            @csrf
                            <button type="submit" class="text-xs px-2 py-1 rounded hover:opacity-80"
                                    style="background: rgba(74,222,128,0.12); color: #4ADE80;">
                                Verify now
                            </button>
                        </form>
                    @endif
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Phone</p>
                    @if($user->phone_verified_at)
                        <p class="text-sm" style="color: #4ADE80;">✓ Verified</p>
                        <p class="text-xs" style="color: var(--text-secondary);">{{ $user->phone_verified_at->format('d M Y') }}</p>
                    @else
                        <p class="text-sm" style="color: #F87171;">✗ Unverified</p>
                        <form method="POST" action="{{ route('admin.users.verify-phone', $user) }}" class="mt-1">
                            @csrf
                            <button type="submit" class="text-xs px-2 py-1 rounded hover:opacity-80"
                                    style="background: rgba(74,222,128,0.12); color: #4ADE80;">
                                Verify now
                            </button>
                        </form>
                    @endif
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Joined</p>
                    <p class="text-sm">{{ $user->created_at->format('d M Y') }}</p>
                    <p class="text-xs" style="color: var(--text-secondary);">{{ $user->created_at->diffForHumans() }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Referral Code</p>
                    <p class="text-sm font-mono" style="color: var(--gold);">{{ $user->referral_code ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Referred By</p>
                    @if($user->referredBy)
                        <a href="{{ route('admin.users.show', $user->referredBy) }}"
                           class="text-sm hover:underline" style="color: var(--gold);">
                            {{ $user->referredBy->name }}
                        </a>
                    @else
                        <p class="text-sm" style="color: var(--text-secondary);">—</p>
                    @endif
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Referrals Made</p>
                    <p class="text-sm">{{ $user->referrals->count() }}</p>
                </div>
            </div>

            {{-- BVN / NIN (masked) --}}
            @if($user->bvn || $user->nin)
                <div class="grid grid-cols-2 gap-3 pt-3" style="border-top: 1px solid var(--border-color);">
                    @if($user->bvn)
                        <div>
                            <p class="text-xs uppercase tracking-wider mb-1" style="color: var(--text-secondary);">BVN</p>
                            <p class="text-sm font-mono">{{ str_repeat('•', strlen($user->bvn) - 4) . substr($user->bvn, -4) }}</p>
                        </div>
                    @endif
                    @if($user->nin)
                        <div>
                            <p class="text-xs uppercase tracking-wider mb-1" style="color: var(--text-secondary);">NIN</p>
                            <p class="text-sm font-mono">{{ str_repeat('•', strlen($user->nin) - 4) . substr($user->nin, -4) }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Actions + Wallet --}}
        <div class="space-y-4">

            {{-- Wallet card --}}
            <div class="rounded-xl p-4 space-y-3" style="background: var(--bg-card); border: 1px solid var(--border-color);">
                <h3 class="text-sm font-semibold">Wallet</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span style="color: var(--text-secondary);">Credits</span>
                        <span class="font-medium">£{{ number_format($user->wallet?->credits_balance ?? 0, 0) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span style="color: var(--text-secondary);">Escrow</span>
                        <span class="font-medium">£{{ number_format($user->wallet?->escrow_balance ?? 0, 0) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span style="color: var(--text-secondary);">Referral mins</span>
                        <span class="font-medium">{{ $user->wallet?->referral_minutes ?? 0 }} min</span>
                    </div>
                </div>
                <button @click="walletModal = true"
                        class="w-full py-2 rounded-lg text-xs font-medium hover:opacity-80 transition-opacity"
                        style="background: rgba(201,168,76,0.12); color: var(--gold); border: 1px solid rgba(201,168,76,0.3);">
                    Adjust Balance
                </button>
            </div>

            {{-- Suspend / Unsuspend --}}
            <div class="rounded-xl p-4" style="background: var(--bg-card); border: 1px solid var(--border-color);">
                <h3 class="text-sm font-semibold mb-3">Account Control</h3>
                @if($user->suspended_at)
                    <p class="text-xs mb-3" style="color: var(--text-secondary);">
                        Suspended {{ $user->suspended_at->diffForHumans() }}
                    </p>
                    <form method="POST" action="{{ route('admin.users.unsuspend', $user) }}">
                        @csrf
                        <button type="submit"
                                class="w-full py-2 rounded-lg text-xs font-medium hover:opacity-80"
                                style="background: rgba(74,222,128,0.12); color: #4ADE80; border: 1px solid rgba(74,222,128,0.3);">
                            Unsuspend Account
                        </button>
                    </form>
                @else
                    <button @click="suspendModal = true"
                            class="w-full py-2 rounded-lg text-xs font-medium hover:opacity-80"
                            style="background: rgba(239,68,68,0.1); color: #F87171; border: 1px solid rgba(239,68,68,0.3);">
                        Suspend Account
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Rooms --}}
    <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="px-4 py-3" style="border-bottom: 1px solid var(--border-color);">
            <h3 class="text-sm font-semibold">Rooms ({{ $rooms->count() }})</h3>
        </div>
        @if($rooms->isEmpty())
            <p class="text-sm text-center py-8" style="color: var(--text-secondary);">No rooms yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full data-table">
                    <thead>
                        <tr>
                            <th>Case ID</th>
                            <th>Title</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rooms as $room)
                            <tr>
                                <td><span class="font-mono text-xs" style="color: var(--gold);">{{ $room->case_id }}</span></td>
                                <td class="text-sm">{{ $room->title ?? '—' }}</td>
                                <td>
                                    <span class="badge" style="background: rgba(59,130,246,0.12); color: #60A5FA;">
                                        {{ $room->party_a_id === $user->id ? 'Party A' : 'Party B' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge" style="background: rgba(201,168,76,0.12); color: var(--gold);">
                                        {{ str_replace('_', ' ', $room->status) }}
                                    </span>
                                </td>
                                <td class="text-xs" style="color: var(--text-secondary);">{{ $room->created_at->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Billing history --}}
    <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="px-4 py-3" style="border-bottom: 1px solid var(--border-color);">
            <h3 class="text-sm font-semibold">Billing History ({{ $billings->count() }})</h3>
        </div>
        @if($billings->isEmpty())
            <p class="text-sm text-center py-8" style="color: var(--text-secondary);">No billing records.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full data-table">
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Party</th>
                            <th>Plan</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($billings as $billing)
                            <tr>
                                <td><span class="font-mono text-xs" style="color: var(--gold);">{{ $billing->room?->case_id ?? '—' }}</span></td>
                                <td class="text-sm">{{ ucfirst($billing->party) }}</td>
                                <td class="text-sm">{{ $billing->plan ?? '—' }}</td>
                                <td class="text-sm font-medium">£{{ number_format($billing->amount, 0) }}</td>
                                <td>
                                    @php
                                        $colors = ['paid' => ['bg' => 'rgba(74,222,128,0.12)', 'text' => '#4ADE80'], 'pending' => ['bg' => 'rgba(251,191,36,0.12)', 'text' => '#FCD34D'], 'refunded' => ['bg' => 'rgba(239,68,68,0.12)', 'text' => '#F87171']];
                                        $c = $colors[$billing->status] ?? $colors['pending'];
                                    @endphp
                                    <span class="badge" style="background: {{ $c['bg'] }}; color: {{ $c['text'] }};">
                                        {{ ucfirst($billing->status) }}
                                    </span>
                                </td>
                                <td class="text-xs" style="color: var(--text-secondary);">
                                    {{ $billing->paid_at?->format('d M Y') ?? $billing->created_at->format('d M Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Suspend Modal --}}
    <div x-show="suspendModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.7);">
        <div class="w-full max-w-sm rounded-xl p-5 space-y-4"
             style="background: var(--bg-card); border: 1px solid var(--border-color);"
             @click.stop>
            <h3 class="text-base font-semibold">Suspend {{ $user->name }}?</h3>
            <p class="text-sm" style="color: var(--text-secondary);">
                This will prevent the user from accessing the platform.
            </p>
            <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Reason (optional)</label>
                    <input type="text" name="reason" placeholder="e.g. Violation of terms"
                           class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                           style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                </div>
                <div class="flex gap-2">
                    <button type="button" @click="suspendModal = false"
                            class="flex-1 py-2 rounded-lg text-sm font-medium"
                            style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-secondary);">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 py-2 rounded-lg text-sm font-medium hover:opacity-80"
                            style="background: rgba(239,68,68,0.15); color: #F87171; border: 1px solid rgba(239,68,68,0.3);">
                        Suspend
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Wallet Adjust Modal --}}
    <div x-show="walletModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.7);">
        <div class="w-full max-w-sm rounded-xl p-5 space-y-4"
             style="background: var(--bg-card); border: 1px solid var(--border-color);"
             @click.stop>
            <h3 class="text-base font-semibold">Adjust Wallet Balance</h3>
            <form method="POST" action="{{ route('admin.users.adjust-wallet', $user) }}" class="space-y-3">
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
                    <label class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Amount (£)</label>
                    <input type="number" name="amount" min="1" placeholder="0"
                           class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                           style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Reason</label>
                    <input type="text" name="reason" placeholder="e.g. Refund, bonus credit"
                           class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                           style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                </div>
                <div class="flex gap-2">
                    <button type="button" @click="walletModal = false"
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
        </div>
    </div>

</div>
@endsection
