@extends('admin.layouts.app')

@section('title', 'Billing')
@section('page-title', 'Billing')

@section('content')
<div class="space-y-4">

    {{-- Summary stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Total Revenue</p>
                    <p class="text-2xl font-semibold">£{{ number_format($totals['paid'], 0) }}</p>
                </div>
                <div class="p-2 rounded-lg" style="background: rgba(74,222,128,0.12);">
                    <svg class="w-5 h-5" style="color: #4ADE80;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs mt-2" style="color: var(--text-secondary);">All paid transactions</p>
        </div>

        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Pending Payments</p>
                    <p class="text-2xl font-semibold">{{ number_format($totals['pending']) }}</p>
                </div>
                <div class="p-2 rounded-lg" style="background: rgba(251,191,36,0.12);">
                    <svg class="w-5 h-5" style="color: #FCD34D;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs mt-2" style="color: var(--text-secondary);">Awaiting payment</p>
        </div>

        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Total Refunded</p>
                    <p class="text-2xl font-semibold">£{{ number_format($totals['refunded'], 0) }}</p>
                </div>
                <div class="p-2 rounded-lg" style="background: rgba(239,68,68,0.12);">
                    <svg class="w-5 h-5" style="color: #F87171;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs mt-2" style="color: var(--text-secondary);">
                <a href="{{ route('admin.billing.refunds') }}" style="color: var(--gold);" class="hover:underline">Manage refunds →</a>
            </p>
        </div>
    </div>

    {{-- Toolbar --}}
    <form method="GET" action="{{ route('admin.billing.index') }}" class="flex flex-col sm:flex-row gap-3">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search case ID, user, email, Stripe intent…"
            class="flex-1 px-3 py-2 rounded-lg text-sm outline-none"
            style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);"
        >

        <select name="status"
                class="px-3 py-2 rounded-lg text-sm outline-none"
                style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">
            <option value="">All statuses</option>
            <option value="paid"     {{ request('status') === 'paid'     ? 'selected' : '' }}>Paid</option>
            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
            <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
        </select>

        <select name="party"
                class="px-3 py-2 rounded-lg text-sm outline-none"
                style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">
            <option value="">All parties</option>
            <option value="party_a" {{ request('party') === 'party_a' ? 'selected' : '' }}>Party A</option>
            <option value="party_b" {{ request('party') === 'party_b' ? 'selected' : '' }}>Party B</option>
        </select>

        <button type="submit"
                class="px-4 py-2 rounded-lg text-sm font-medium flex-shrink-0"
                style="background: var(--gold); color: #0D1B2A;">
            Filter
        </button>

        {{-- Export --}}
        <a href="{{ route('admin.billing.export', request()->only('status')) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium flex-shrink-0 text-center hover:opacity-80"
           style="background: rgba(74,222,128,0.12); color: #4ADE80; border: 1px solid rgba(74,222,128,0.2);">
            ↓ Export CSV
        </a>

        @if(request()->hasAny(['search', 'status', 'party']))
            <a href="{{ route('admin.billing.index') }}"
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
                {{ number_format($billings->total()) }} transaction{{ $billings->total() !== 1 ? 's' : '' }}
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
                        <th>Status</th>
                        <th>Stripe Intent</th>
                        <th>Paid At</th>
                        <th>Created</th>
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
                            <td>
                                @php
                                    $sc = [
                                        'paid'     => ['rgba(74,222,128,0.12)',  '#4ADE80'],
                                        'pending'  => ['rgba(251,191,36,0.12)',  '#FCD34D'],
                                        'refunded' => ['rgba(239,68,68,0.12)',   '#F87171'],
                                    ];
                                    [$bg, $text] = $sc[$billing->status] ?? $sc['pending'];
                                @endphp
                                <span class="badge" style="background: {{ $bg }}; color: {{ $text }};">
                                    {{ ucfirst($billing->status) }}
                                </span>
                            </td>
                            <td class="text-xs font-mono" style="color: var(--text-secondary);">
                                @if($billing->stripe_payment_intent_id)
                                    <span title="{{ $billing->stripe_payment_intent_id }}">
                                        {{ substr($billing->stripe_payment_intent_id, 0, 18) }}…
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-xs" style="color: var(--text-secondary);">
                                {{ $billing->paid_at?->format('d M Y H:i') ?? '—' }}
                            </td>
                            <td class="text-xs" style="color: var(--text-secondary);">
                                {{ $billing->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-12" style="color: var(--text-secondary);">
                                No transactions found.
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

</div>
@endsection
