@extends('layouts.app')

@section('title', 'FMRefer — FirstMediator')
@section('page-title', 'FMRefer')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl lg:text-3xl font-serif mb-2" style="color: var(--text-primary);">
            Refer & Earn
        </h1>
        <p class="text-base" style="color: var(--text-secondary);">
            Invite friends and earn $1,000 credits for every successful referral
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="stats-card-gold">
            <p class="text-sm font-medium mb-2" style="color: var(--text-secondary);">Total Referrals</p>
            <p class="text-3xl font-bold" style="color: var(--text-primary);">{{ $stats['total'] }}</p>
        </div>
        <div class="stats-card-gold">
            <p class="text-sm font-medium mb-2" style="color: var(--text-secondary);">Successful</p>
            <p class="text-3xl font-bold" style="color: var(--gold);">{{ $stats['successful'] }}</p>
        </div>
        <div class="stats-card-gold">
            <p class="text-sm font-medium mb-2" style="color: var(--text-secondary);">Total Earned</p>
            <p class="text-3xl font-bold" style="color: var(--gold);">${{ number_format($stats['earned']) }}</p>
        </div>
    </div>

    <div class="p-8 rounded-xl mb-6" style="background: linear-gradient(135deg, var(--navy) 0%, #1a2f45 100%);">
        <h2 class="text-xl font-serif text-white mb-4">Your Referral Link</h2>
        <div class="flex items-center space-x-3">
            <input type="text" readonly value="{{ $referralLink }}" class="flex-1 px-4 py-3 rounded-lg text-sm" style="background-color: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2);">
            <button onclick="copyReferralLink()" class="px-6 py-3 rounded-lg text-sm font-medium transition-colors hover:opacity-90" style="background-color: var(--gold); color: var(--white);">
                Copy Link
            </button>
        </div>
        <p class="text-sm text-white opacity-75 mt-3">Share this link with friends. When they sign up and complete their first session, you both earn $1,000 credits!</p>
    </div>

    <div>
        <h2 class="text-xl font-serif mb-4" style="color: var(--text-primary);">Referral History</h2>
        
        <!-- Mobile Card View -->
        <div class="block md:hidden space-y-3">
            @foreach($referrals as $referral)
            <div class="p-4 rounded-xl" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color);">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <p class="text-sm font-medium mb-1" style="color: var(--text-primary);">{{ $referral['name'] }}</p>
                        <p class="text-xs" style="color: var(--text-secondary);">{{ $referral['date'] }}</p>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-medium" style="background-color: {{ $referral['status_color'] }}; color: white;">
                        {{ $referral['status'] }}
                    </span>
                </div>
                <div class="text-right">
                    <p class="text-lg font-bold" style="color: var(--gold);">{{ $referral['reward'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block rounded-xl overflow-hidden" style="border: 1px solid var(--border-color);">
            <table class="min-w-full">
                <thead style="background-color: var(--bg-secondary);">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Referred User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Reward</th>
                    </tr>
                </thead>
                <tbody style="background-color: var(--bg-primary);">
                    @foreach($referrals as $referral)
                    <tr style="border-top: 1px solid var(--border-color);">
                        <td class="px-6 py-4 text-sm" style="color: var(--text-secondary);">{{ $referral['date'] }}</td>
                        <td class="px-6 py-4 text-sm" style="color: var(--text-primary);">{{ $referral['name'] }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium" style="background-color: {{ $referral['status_color'] }}; color: white;">
                                {{ $referral['status'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-right font-medium" style="color: var(--gold);">
                            {{ $referral['reward'] }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function copyReferralLink() {
    const input = document.querySelector('input[readonly]');
    input.select();
    document.execCommand('copy');
    showToast('Referral link copied to clipboard!', 'success');
}
</script>
@endsection
