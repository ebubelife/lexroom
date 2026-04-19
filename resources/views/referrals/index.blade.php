@extends('layouts.app')

@section('title', 'Referrals — FirstMediator')
@section('page-title', 'Referrals')

@section('content')
<div class="max-w-4xl mx-auto" x-data="referralPage()">

    {{-- Tabs --}}
    <div class="flex space-x-1 mb-6 border-b" style="border-color: var(--border-color);">
        <button @click="tab = 'overview'"
                class="px-4 py-2 text-sm font-medium border-b-2 transition-colors"
                :style="tab === 'overview' ? 'border-color: var(--gold); color: var(--gold);' : 'border-color: transparent; color: var(--text-secondary);'">
            My Referrals
        </button>
        <button @click="tab = 'leaderboard'"
                class="px-4 py-2 text-sm font-medium border-b-2 transition-colors"
                :style="tab === 'leaderboard' ? 'border-color: var(--gold); color: var(--gold);' : 'border-color: transparent; color: var(--text-secondary);'">
            Leaderboard
        </button>
        <button @click="tab = 'howto'"
                class="px-4 py-2 text-sm font-medium border-b-2 transition-colors"
                :style="tab === 'howto' ? 'border-color: var(--gold); color: var(--gold);' : 'border-color: transparent; color: var(--text-secondary);'">
            How It Works
        </button>
    </div>

    {{-- ===== OVERVIEW TAB ===== --}}
    <div x-show="tab === 'overview'" x-cloak>

        {{-- Referral Link Card --}}
        <div class="rounded-xl border p-5 mb-5" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <h2 class="font-serif text-lg mb-1" style="color: var(--text-primary);">Share & Earn Free Session Time</h2>
            <p class="text-sm mb-4" style="color: var(--text-secondary);">
                Earn <strong style="color: var(--gold);">{{ $minutesPerRefer }} free minutes</strong> for every friend who completes their first paid session.
            </p>

            <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Your Referral Link</label>
            <div class="flex items-center gap-2">
                <input type="text" readonly value="{{ $referralLink }}"
                       class="flex-1 px-3 py-2 rounded-lg border text-sm"
                       style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"
                       x-ref="linkInput">
                <button @click="copyLink()"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-white transition-opacity hover:opacity-90 flex items-center gap-2"
                        style="background-color: var(--gold);">
                    <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <svg x-show="copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                </button>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
            <div class="rounded-xl border p-4 text-center" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="text-2xl font-bold font-serif" style="color: var(--gold);">{{ $stats['total'] }}</div>
                <div class="text-xs mt-1" style="color: var(--text-secondary);">Total Referrals</div>
            </div>
            <div class="rounded-xl border p-4 text-center" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="text-2xl font-bold font-serif" style="color: #15803D;">{{ $stats['completed'] }}</div>
                <div class="text-xs mt-1" style="color: var(--text-secondary);">Successful</div>
            </div>
            <div class="rounded-xl border p-4 text-center" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="text-2xl font-bold font-serif" style="color: #B45309;">{{ $stats['pending'] }}</div>
                <div class="text-xs mt-1" style="color: var(--text-secondary);">Pending</div>
            </div>
            <div class="rounded-xl border p-4 text-center" style="background-color: var(--bg-secondary); border-color: var(--gold); border-width: 2px;">
                <div class="text-2xl font-bold font-serif" style="color: var(--gold);">{{ $minutesBalance }}m</div>
                <div class="text-xs mt-1" style="color: var(--text-secondary);">Free Minutes</div>
            </div>
        </div>

        {{-- Free Minutes Balance --}}
        @if($minutesBalance > 0)
        <div class="rounded-xl border p-4 mb-5 flex items-center justify-between"
             style="background-color: rgba(201,168,76,0.08); border-color: var(--gold);">
            <div>
                <p class="text-sm font-medium" style="color: var(--text-primary);">
                    🎁 You have <strong style="color: var(--gold);">{{ $minutesBalance }} free minutes</strong> available
                </p>
                @if($minutesExpiry)
                <p class="text-xs mt-0.5" style="color: var(--text-secondary);">
                    Expires {{ $minutesExpiry->format('M d, Y') }}
                </p>
                @endif
            </div>
            <a href="{{ route('rooms.create') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium text-white"
               style="background-color: var(--gold);">
                Use Now
            </a>
        </div>
        @endif

        {{-- Referral History --}}
        <div class="rounded-xl border overflow-hidden" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <div class="px-5 py-4 border-b" style="border-color: var(--border-color);">
                <h3 class="font-medium text-sm" style="color: var(--text-primary);">Referral History</h3>
            </div>

            @if($rewards->isEmpty())
                <div class="px-5 py-12 text-center">
                    <div class="text-4xl mb-3">👥</div>
                    <p class="text-sm font-medium mb-1" style="color: var(--text-primary);">No referrals yet</p>
                    <p class="text-xs" style="color: var(--text-secondary);">Share your link above to start earning free minutes!</p>
                </div>
            @else
                <div class="divide-y" style="border-color: var(--border-color);">
                    @foreach($rewards as $reward)
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                 style="background-color: var(--gold);">
                                {{ $reward->referredUser?->initials ?? '??' }}
                            </div>
                            <div>
                                <p class="text-sm font-medium" style="color: var(--text-primary);">
                                    {{ $reward->referredUser?->name ?? 'Unknown User' }}
                                </p>
                                <p class="text-xs" style="color: var(--text-secondary);">
                                    Joined {{ $reward->created_at->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            @if($reward->status === 'completed')
                                <span class="text-xs font-medium px-2 py-1 rounded-full"
                                      style="background-color: rgba(21,128,61,0.1); color: #15803D;">
                                    ✅ +{{ $reward->minutes_awarded }} mins
                                </span>
                            @elseif($reward->status === 'pending')
                                <span class="text-xs font-medium px-2 py-1 rounded-full"
                                      style="background-color: rgba(180,83,9,0.1); color: #B45309;">
                                    ⏳ Pending
                                </span>
                            @else
                                <span class="text-xs font-medium px-2 py-1 rounded-full"
                                      style="background-color: rgba(107,107,104,0.1); color: var(--text-secondary);">
                                    Expired
                                </span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ===== LEADERBOARD TAB ===== --}}
    <div x-show="tab === 'leaderboard'" x-cloak>
        <div class="rounded-xl border overflow-hidden" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <div class="px-5 py-4 border-b" style="border-color: var(--border-color);">
                <h3 class="font-medium text-sm" style="color: var(--text-primary);">Top Referrers This Month</h3>
            </div>

            @if($leaderboard->isEmpty())
                <div class="px-5 py-12 text-center">
                    <div class="text-4xl mb-3">🏆</div>
                    <p class="text-sm" style="color: var(--text-secondary);">No referrals this month yet. Be the first!</p>
                </div>
            @else
                <div class="divide-y" style="border-color: var(--border-color);">
                    @foreach($leaderboard as $index => $entry)
                    <div class="px-5 py-3 flex items-center justify-between"
                         style="{{ $entry->referrer_id === auth()->id() ? 'background-color: rgba(201,168,76,0.05);' : '' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-lg w-8 text-center">
                                @if($index === 0) 🥇
                                @elseif($index === 1) 🥈
                                @elseif($index === 2) 🥉
                                @else <span class="text-sm font-bold" style="color: var(--text-secondary);">{{ $index + 1 }}</span>
                                @endif
                            </span>
                            <p class="text-sm font-medium" style="color: var(--text-primary);">
                                {{ $entry->referrer?->name ?? 'Unknown' }}
                                @if($entry->referrer_id === auth()->id())
                                    <span class="text-xs ml-1" style="color: var(--gold);">(You)</span>
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold" style="color: var(--gold);">{{ $entry->total_referrals }} referrals</p>
                            <p class="text-xs" style="color: var(--text-secondary);">{{ $entry->total_minutes }} mins earned</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ===== HOW IT WORKS TAB ===== --}}
    <div x-show="tab === 'howto'" x-cloak>
        <div class="rounded-xl border p-6 space-y-6" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            @foreach([
                ['step' => '1', 'title' => 'Share Your Link', 'desc' => 'Copy your unique referral link and share it with friends, colleagues, landlords, freelancers — anyone who might need mediation.'],
                ['step' => '2', 'title' => 'Friend Signs Up', 'desc' => 'Your friend registers using your link. Their account is now linked to you. You\'ll see them appear as "Pending" in your referral history.'],
                ['step' => '3', 'title' => 'Friend Completes First Session', 'desc' => 'Once your friend pays for and completes their first mediation session, the referral is marked successful.'],
                ['step' => '4', 'title' => 'You Earn Free Minutes', 'desc' => 'We instantly add ' . $minutesPerRefer . ' free minutes to your account. You\'ll get an email notification too!'],
                ['step' => '5', 'title' => 'Use Your Free Minutes', 'desc' => 'Apply your free minutes when creating a new session to reduce the cost, or use them to extend an active session.'],
            ] as $item)
            <div class="flex gap-4">
                <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-white text-sm font-bold"
                     style="background-color: var(--gold);">{{ $item['step'] }}</div>
                <div>
                    <p class="font-medium text-sm mb-1" style="color: var(--text-primary);">{{ $item['title'] }}</p>
                    <p class="text-sm" style="color: var(--text-secondary);">{{ $item['desc'] }}</p>
                </div>
            </div>
            @endforeach

            <div class="mt-4 p-4 rounded-lg" style="background-color: rgba(201,168,76,0.08); border: 1px solid var(--gold);">
                <p class="text-xs" style="color: var(--text-secondary);">
                    <strong style="color: var(--text-primary);">Important:</strong>
                    Free minutes expire 12 months after being earned. Pending referrals expire after 90 days if your friend hasn't completed a session.
                    Self-referrals are not allowed.
                </p>
            </div>
        </div>
    </div>

</div>

<script>
function referralPage() {
    return {
        tab: 'overview',
        copied: false,
        copyLink() {
            navigator.clipboard.writeText(this.$refs.linkInput.value).then(() => {
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            });
        }
    }
}
</script>
@endsection
