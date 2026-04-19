@extends('admin.layouts.app')

@section('title', 'Referrals')
@section('page-title', 'Referrals')

@section('content')
<div class="space-y-4">

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach([
            ['Total Rewards',   $stats['total'],         'rgba(201,168,76,0.12)', 'var(--gold)'],
            ['Completed',       $stats['completed'],     'rgba(74,222,128,0.12)', '#4ADE80'],
            ['Pending',         $stats['pending'],       'rgba(251,191,36,0.12)', '#FCD34D'],
            ['Minutes Awarded', $stats['total_minutes'], 'rgba(59,130,246,0.12)', '#60A5FA'],
        ] as [$label, $value, $bg, $color])
            <div class="stat-card">
                <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: var(--text-secondary);">{{ $label }}</p>
                <p class="text-2xl font-semibold" style="color: {{ $color }};">{{ number_format($value) }}</p>
            </div>
        @endforeach
    </div>

    {{-- Toolbar --}}
    <form method="GET" action="{{ route('admin.referrals.index') }}" class="flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search referrer or referred user…"
               class="flex-1 px-3 py-2 rounded-lg text-sm outline-none"
               style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">

        <select name="status" class="px-3 py-2 rounded-lg text-sm outline-none"
                style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">
            <option value="">All statuses</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
            <option value="revoked"   {{ request('status') === 'revoked'   ? 'selected' : '' }}>Revoked</option>
        </select>

        <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium flex-shrink-0"
                style="background: var(--gold); color: #0D1B2A;">Filter</button>

        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.referrals.index') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium flex-shrink-0 text-center"
               style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-secondary);">Clear</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="px-4 py-3" style="border-bottom: 1px solid var(--border-color);">
            <p class="text-sm" style="color: var(--text-secondary);">
                {{ number_format($rewards->total()) }} reward{{ $rewards->total() !== 1 ? 's' : '' }}
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full data-table">
                <thead>
                    <tr>
                        <th>Referrer</th>
                        <th>Referred User</th>
                        <th>Minutes</th>
                        <th>Status</th>
                        <th>Awarded At</th>
                        <th>Expires At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rewards as $reward)
                        <tr>
                            <td>
                                @if($reward->referrer)
                                    <a href="{{ route('admin.users.show', $reward->referrer) }}"
                                       class="text-sm font-medium hover:underline">{{ $reward->referrer->name }}</a>
                                    <p class="text-xs" style="color: var(--text-secondary);">{{ $reward->referrer->email }}</p>
                                @else
                                    <span style="color: var(--text-secondary);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($reward->referredUser)
                                    <a href="{{ route('admin.users.show', $reward->referredUser) }}"
                                       class="text-sm hover:underline">{{ $reward->referredUser->name }}</a>
                                    <p class="text-xs" style="color: var(--text-secondary);">{{ $reward->referredUser->email }}</p>
                                @else
                                    <span style="color: var(--text-secondary);">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-sm font-semibold" style="color: var(--gold);">
                                    {{ $reward->minutes_awarded }} min
                                </span>
                            </td>
                            <td>
                                @php
                                    $sc = [
                                        'completed' => ['rgba(74,222,128,0.12)',  '#4ADE80'],
                                        'pending'   => ['rgba(251,191,36,0.12)',  '#FCD34D'],
                                        'revoked'   => ['rgba(239,68,68,0.12)',   '#F87171'],
                                    ];
                                    [$bg, $text] = $sc[$reward->status] ?? $sc['pending'];
                                @endphp
                                <span class="badge" style="background: {{ $bg }}; color: {{ $text }};">
                                    {{ ucfirst($reward->status) }}
                                </span>
                            </td>
                            <td class="text-xs" style="color: var(--text-secondary);">
                                {{ $reward->awarded_at?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="text-xs" style="color: var(--text-secondary);">
                                {{ $reward->expires_at?->format('d M Y') ?? '—' }}
                            </td>
                            <td>
                                @if($reward->status === 'completed')
                                    <form method="POST" action="{{ route('admin.referrals.revoke', $reward) }}">
                                        @csrf
                                        <button type="submit"
                                                class="text-xs px-3 py-1.5 rounded-lg font-medium hover:opacity-80"
                                                style="background: rgba(239,68,68,0.1); color: #F87171; border: 1px solid rgba(239,68,68,0.25);"
                                                onclick="return confirm('Revoke this reward and deduct {{ $reward->minutes_awarded }} minutes from {{ addslashes($reward->referrer?->name ?? 'user') }}?')">
                                            Revoke
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs" style="color: var(--text-secondary);">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12" style="color: var(--text-secondary);">
                                No referral rewards found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rewards->hasPages())
            <div class="px-4 py-3 flex items-center justify-between" style="border-top: 1px solid var(--border-color);">
                <p class="text-xs" style="color: var(--text-secondary);">
                    Showing {{ $rewards->firstItem() }}–{{ $rewards->lastItem() }} of {{ $rewards->total() }}
                </p>
                <div class="flex gap-1">
                    @if($rewards->onFirstPage())
                        <span class="px-3 py-1.5 rounded-lg text-xs opacity-30" style="background: var(--bg-card); border: 1px solid var(--border-color);">← Prev</span>
                    @else
                        <a href="{{ $rewards->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg text-xs hover:opacity-80" style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">← Prev</a>
                    @endif
                    @if($rewards->hasMorePages())
                        <a href="{{ $rewards->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg text-xs hover:opacity-80" style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">Next →</a>
                    @else
                        <span class="px-3 py-1.5 rounded-lg text-xs opacity-30" style="background: var(--bg-card); border: 1px solid var(--border-color);">Next →</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
