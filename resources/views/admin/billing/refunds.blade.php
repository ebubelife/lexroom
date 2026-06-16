@extends('admin.layouts.app')

@section('title', 'Refunds')
@section('page-title', 'Refunds')

@section('content')
<div x-data="{ refundModal: false, selected: null }" class="space-y-4">

    {{-- Sub-nav --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.billing.index') }}"
           class="text-sm hover:underline" style="color: var(--gold);">← All Transactions</a>
        <span style="color: var(--text-secondary);">/</span>
        <span class="text-sm font-medium">Refunds</span>
    </div>

    {{-- Info banner --}}
    <div class="rounded-lg px-4 py-3 text-sm" style="background: rgba(251,191,36,0.08); border: 1px solid rgba(251,191,36,0.2); color: #FCD34D;">
        ⚠ Refunds are processed immediately via Stripe and cannot be undone. Every refund is logged to the audit trail.
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('admin.billing.refunds') }}" class="flex gap-3">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search case ID, user name or email…"
            class="flex-1 px-3 py-2 rounded-lg text-sm outline-none"
            style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);"
        >
        <button type="submit"
                class="px-4 py-2 rounded-lg text-sm font-medium flex-shrink-0"
                style="background: var(--gold); color: #0D1B2A;">
            Search
        </button>
        @if(request('search'))
            <a href="{{ route('admin.billing.refunds') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium flex-shrink-0 text-center"
               style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-secondary);">
                Clear
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="px-4 py-3" style="border-bottom: 1px solid var(--border-color);">
            <p class="text-sm" style="color: var(--text-secondary);">
                {{ number_format($billings->total()) }} refund-eligible transaction{{ $billings->total() !== 1 ? 's' : '' }}
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full data-table">
                <thead>
                    <tr>
                        <th>Case ID</th>
                        <th>User</th>
                        <th>Party</th>
                        <th>Plan</th>
                        <th>Amount</th>
                        <th>Stripe Intent</th>
                        <th>Paid At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($billings as $billing)
                        <tr>
                            <td>
                                @if($billing->room)
                                    <a href="{{ route('admin.rooms.show', $billing->room) }}"
                                       class="font-mono text-xs hover:underline" style="color: var(--gold);">
                                        {{ $billing->room->case_id }}
                                    </a>
                                @else
                                    <span class="text-xs" style="color: var(--text-secondary);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($billing->user)
                                    <a href="{{ route('admin.users.show', $billing->user) }}"
                                       class="text-sm hover:underline">{{ $billing->user->name }}</a>
                                    <p class="text-xs" style="color: var(--text-secondary);">{{ $billing->user->email }}</p>
                                @else
                                    <span class="text-sm" style="color: var(--text-secondary);">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge"
                                      style="background: {{ $billing->party === 'party_a' ? 'rgba(59,130,246,0.12)' : 'rgba(168,85,247,0.12)' }};
                                             color: {{ $billing->party === 'party_a' ? '#60A5FA' : '#C084FC' }};">
                                    {{ ucfirst(str_replace('_', ' ', $billing->party)) }}
                                </span>
                            </td>
                            <td class="text-sm" style="color: var(--text-secondary);">{{ $billing->plan ?? '—' }}</td>
                            <td class="text-sm font-semibold">£{{ number_format($billing->amount, 0) }}</td>
                            <td class="text-xs font-mono" style="color: var(--text-secondary);">
                                <span title="{{ $billing->stripe_payment_intent_id }}">
                                    {{ substr($billing->stripe_payment_intent_id, 0, 18) }}…
                                </span>
                            </td>
                            <td class="text-xs" style="color: var(--text-secondary);">
                                {{ $billing->paid_at?->format('d M Y H:i') ?? '—' }}
                            </td>
                            <td>
                                <button
                                    @click="selected = {{ $billing->id }}; refundModal = true"
                                    class="text-xs px-3 py-1.5 rounded-lg font-medium hover:opacity-80"
                                    style="background: rgba(239,68,68,0.1); color: #F87171; border: 1px solid rgba(239,68,68,0.25);">
                                    Refund
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12" style="color: var(--text-secondary);">
                                No refund-eligible transactions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($billings->hasPages())
            <div class="px-4 py-3 flex items-center justify-between" style="border-top: 1px solid var(--border-color);">
                <p class="text-xs" style="color: var(--text-secondary);">
                    Showing {{ $billings->firstItem() }}–{{ $billings->lastItem() }} of {{ $billings->total() }}
                </p>
                <div class="flex gap-1">
                    @if($billings->onFirstPage())
                        <span class="px-3 py-1.5 rounded-lg text-xs opacity-30"
                              style="background: var(--bg-card); border: 1px solid var(--border-color);">← Prev</span>
                    @else
                        <a href="{{ $billings->previousPageUrl() }}"
                           class="px-3 py-1.5 rounded-lg text-xs hover:opacity-80"
                           style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">← Prev</a>
                    @endif
                    @if($billings->hasMorePages())
                        <a href="{{ $billings->nextPageUrl() }}"
                           class="px-3 py-1.5 rounded-lg text-xs hover:opacity-80"
                           style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">Next →</a>
                    @else
                        <span class="px-3 py-1.5 rounded-lg text-xs opacity-30"
                              style="background: var(--bg-card); border: 1px solid var(--border-color);">Next →</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Refund Confirm Modal --}}
    <div x-show="refundModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.75);">
        <div class="w-full max-w-sm rounded-xl p-5 space-y-4"
             style="background: var(--bg-card); border: 1px solid rgba(239,68,68,0.3);"
             @click.stop>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                     style="background: rgba(239,68,68,0.12);">
                    <svg class="w-4 h-4" style="color: #F87171;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold">Issue Refund?</h3>
            </div>

            <p class="text-sm" style="color: var(--text-secondary);">
                This will immediately process a full refund via Stripe. This action cannot be undone.
            </p>

            <template x-for="billing in [{{ $billings->items() ? json_encode(collect($billings->items())->map(fn($b) => ['id' => $b->id, 'amount' => $b->amount])->values()) : '[]' }}]" :key="billing.id">
                <div x-show="billing.id === selected"
                     class="p-3 rounded-lg text-sm font-medium text-center"
                     style="background: rgba(239,68,68,0.08); color: #F87171;">
                    Refunding £<span x-text="Number(billing.amount).toLocaleString()"></span>
                </div>
            </template>

            {{-- One form per billing row, shown/hidden via Alpine --}}
            @foreach($billings as $billing)
                <form x-show="selected === {{ $billing->id }}"
                      method="POST"
                      action="{{ route('admin.billing.refund', $billing) }}"
                      class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">
                            Reason <span style="color: #F87171;">*</span>
                        </label>
                        <input type="text" name="reason" required
                               placeholder="e.g. Session failed to start, user request"
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="refundModal = false; selected = null"
                                class="flex-1 py-2 rounded-lg text-sm font-medium"
                                style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-secondary);">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 py-2 rounded-lg text-sm font-medium hover:opacity-80"
                                style="background: rgba(239,68,68,0.15); color: #F87171; border: 1px solid rgba(239,68,68,0.3);">
                            Confirm Refund
                        </button>
                    </div>
                </form>
            @endforeach
        </div>
    </div>

</div>
@endsection
