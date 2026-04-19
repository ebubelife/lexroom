@extends('admin.layouts.app')

@section('title', 'Users')
@section('page-title', 'Users')

@section('content')
<div class="space-y-4">

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-2 flex-1">
            <input type="hidden" name="filter" value="{{ request('filter') }}">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search name, email or phone…"
                class="flex-1 px-3 py-2 rounded-lg text-sm outline-none"
                style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);"
            >
            <button type="submit"
                    class="px-4 py-2 rounded-lg text-sm font-medium"
                    style="background: var(--gold); color: #0D1B2A;">
                Search
            </button>
        </form>

        {{-- Filter tabs --}}
        <div class="flex gap-1 flex-shrink-0">
            @foreach(['all' => 'All', 'verified' => 'Verified', 'unverified' => 'Unverified', 'suspended' => 'Suspended', 'new_7d' => 'New (7d)'] as $key => $label)
                <a href="{{ route('admin.users.index', array_merge(request()->except('filter', 'page'), $key !== 'all' ? ['filter' => $key] : [])) }}"
                   class="px-3 py-2 rounded-lg text-xs font-medium transition-colors"
                   style="{{ request('filter', 'all') === $key
                       ? 'background: rgba(201,168,76,0.15); color: var(--gold);'
                       : 'background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-secondary);' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Table --}}
    <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="flex items-center justify-between px-4 py-3" style="border-bottom: 1px solid var(--border-color);">
            <p class="text-sm" style="color: var(--text-secondary);">
                {{ number_format($users->total()) }} user{{ $users->total() !== 1 ? 's' : '' }}
                @if(request('search')) matching "<strong style="color: var(--text-primary);">{{ request('search') }}</strong>"@endif
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full data-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Phone</th>
                        <th>Verified</th>
                        <th>Status</th>
                        <th>Wallet</th>
                        <th>Rooms</th>
                        <th>Joined</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                                         style="background: rgba(201,168,76,0.15); color: var(--gold);">
                                        {{ $user->initials }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium">{{ $user->name }}</p>
                                        <p class="text-xs" style="color: var(--text-secondary);">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="text-sm" style="color: var(--text-secondary);">
                                {{ $user->phone ?? '—' }}
                            </td>
                            <td>
                                <div class="flex flex-col gap-1">
                                    <span class="badge {{ $user->email_verified_at ? 'text-green-400' : 'text-red-400' }}"
                                          style="{{ $user->email_verified_at
                                              ? 'background: rgba(74,222,128,0.1);'
                                              : 'background: rgba(239,68,68,0.1);' }}">
                                        Email {{ $user->email_verified_at ? '✓' : '✗' }}
                                    </span>
                                    <span class="badge {{ $user->phone_verified_at ? 'text-green-400' : 'text-red-400' }}"
                                          style="{{ $user->phone_verified_at
                                              ? 'background: rgba(74,222,128,0.1);'
                                              : 'background: rgba(239,68,68,0.1);' }}">
                                        Phone {{ $user->phone_verified_at ? '✓' : '✗' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                @if($user->suspended_at)
                                    <span class="badge" style="background: rgba(239,68,68,0.12); color: #F87171;">Suspended</span>
                                @else
                                    <span class="badge" style="background: rgba(74,222,128,0.12); color: #4ADE80;">Active</span>
                                @endif
                            </td>
                            <td class="text-sm">
                                £{{ number_format($user->wallet?->credits_balance ?? 0, 0) }}
                            </td>
                            <td class="text-sm text-center">
                                {{ $user->referrals_count ?? 0 }}
                            </td>
                            <td class="text-xs" style="color: var(--text-secondary);">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td>
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="text-xs px-3 py-1.5 rounded-lg font-medium transition-colors hover:opacity-80"
                                   style="background: rgba(201,168,76,0.12); color: var(--gold);">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12" style="color: var(--text-secondary);">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="px-4 py-3 flex items-center justify-between" style="border-top: 1px solid var(--border-color);">
                <p class="text-xs" style="color: var(--text-secondary);">
                    Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}
                </p>
                <div class="flex gap-1">
                    @if($users->onFirstPage())
                        <span class="px-3 py-1.5 rounded-lg text-xs opacity-30"
                              style="background: var(--bg-card); border: 1px solid var(--border-color);">← Prev</span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}"
                           class="px-3 py-1.5 rounded-lg text-xs hover:opacity-80"
                           style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">← Prev</a>
                    @endif

                    @if($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}"
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
