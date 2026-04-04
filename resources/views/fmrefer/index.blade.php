@extends('layouts.app')

@section('title', 'FMRefer — First Mediator')
@section('page-title', 'FMRefer')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl lg:text-3xl font-serif mb-2" style="color: var(--text-primary);">
            Refer & Earn
        </h1>
        <p class="text-base" style="color: var(--text-secondary);">
            Invite friends to LexRoom and earn $1,000 for every successful mediation.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="rounded-2xl p-6 shadow-sm border transition-shadow hover:shadow-md" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2 rounded-lg bg-blue-500 bg-opacity-10 text-blue-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <p class="text-sm font-medium" style="color: var(--text-secondary);">Total Referrals</p>
            </div>
            <p class="text-4xl font-serif font-bold" style="color: var(--text-primary);">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-2xl p-6 shadow-sm border transition-shadow hover:shadow-md" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2 rounded-lg bg-green-500 bg-opacity-10 text-green-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-sm font-medium" style="color: var(--text-secondary);">Successful</p>
            </div>
            <p class="text-4xl font-serif font-bold" style="color: #16A34A;">{{ $stats['successful'] }}</p>
        </div>
        <div class="rounded-2xl p-6 shadow-sm border transition-shadow hover:shadow-md" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2 rounded-lg bg-gold bg-opacity-10 text-[var(--gold)]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-sm font-medium" style="color: var(--text-secondary);">Total Earned</p>
            </div>
            <p class="text-4xl font-serif font-bold" style="color: var(--gold);">${{ number_format($stats['earned']) }}</p>
        </div>
    </div>

    <div class="p-8 rounded-3xl mb-8 relative overflow-hidden group shadow-xl" style="background: linear-gradient(135deg, var(--navy) 0%, #1a2f45 100%);">
        <div class="absolute top-0 right-0 p-8 transform translate-x-8 -translate-y-8 opacity-10 group-hover:translate-x-4 transition-transform duration-700">
            <svg class="w-48 h-48 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
            </svg>
        </div>

        <h2 class="text-2xl font-serif text-white mb-6 relative z-10">Invite & Earn Credits</h2>
        <div class="flex flex-col md:flex-row items-center gap-4 relative z-10">
            <div class="relative flex-1 w-full">
                <input type="text" readonly value="{{ $referralLink }}" class="w-full px-6 py-4 rounded-2xl text-sm font-mono border-0 focus:ring-2 focus:ring-[var(--gold)]" style="background-color: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2);">
                <span class="absolute top-[-10px] left-4 bg-[var(--gold)] text-white text-[10px] font-bold px-2 py-0.5 rounded">YOUR UNIQUE CODE</span>
            </div>
            <button onclick="copyReferralLink()" class="w-full md:w-auto px-8 py-4 rounded-2xl text-sm font-bold uppercase tracking-widest transition-all hover:scale-105 active:scale-95 shadow-lg" style="background-color: var(--gold); color: var(--white);">
                Copy Link
            </button>
        </div>
        <p class="text-sm text-white opacity-80 mt-6 max-w-2xl font-medium">When someone signs up using your code and completes their first mediation, you both receive $1,000 in platform credits. Referral rewards are calculated automatically after each session report is issued.</p>
    </div>

    <div>
        <h2 class="text-2xl font-serif mb-6" style="color: var(--text-primary);">Referral History</h2>
        
        @if($referrals->isEmpty())
        <div class="p-12 rounded-3xl text-center border-2 border-dashed transition-colors" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <div class="w-16 h-16 rounded-full bg-opacity-10 flex items-center justify-center mx-auto mb-4" style="background-color: var(--gold);">
                <svg class="w-8 h-8" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <h3 class="text-lg font-serif mb-2" style="color: var(--text-primary);">No referrals yet</h3>
            <p class="text-sm opacity-60 mb-6" style="color: var(--text-secondary);">Share your referral link to start earning credits!</p>
            <button onclick="copyReferralLink()" class="px-6 py-2.5 rounded-xl border-2 font-bold text-xs uppercase tracking-widest transition-all hover:bg-[var(--gold)] hover:text-white hover:border-[var(--gold)]" style="color: var(--gold); border-color: var(--gold);">
                Copy My Invite Link
            </button>
        </div>
        @else
        <!-- Mobile Card View -->
        <div class="block md:hidden space-y-4">
            @foreach($referrals as $referral)
            <div class="p-5 rounded-3xl shadow-sm transition-transform active:scale-[0.98]" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color);">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white uppercase" style="background-color: var(--gold); font-size: 14px;">{{ substr($referral['name'], 0, 1) }}</div>
                        <div>
                            <p class="text-sm font-bold" style="color: var(--text-primary);">{{ $referral['name'] }}</p>
                            <p class="text-[10px] opacity-60" style="color: var(--text-secondary);">{{ $referral['date'] }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider text-white" style="background-color: {{ $referral['status_color'] }};">
                        {{ $referral['status'] }}
                    </span>
                </div>
                <div class="flex items-center justify-between border-t border-opacity-5 border-white pt-4">
                    <span class="text-xs font-medium opacity-60" style="color: var(--text-secondary);">Reward</span>
                    <p class="text-xl font-bold" style="color: var(--gold);">{{ $referral['reward'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block rounded-3xl overflow-hidden shadow-sm border" style="border-color: var(--border-color);">
            <table class="min-w-full">
                <thead style="background-color: var(--bg-secondary);">
                    <tr>
                        <th class="px-8 py-5 text-left text-xs font-black uppercase tracking-widest" style="color: var(--text-secondary);">Date</th>
                        <th class="px-8 py-5 text-left text-xs font-black uppercase tracking-widest" style="color: var(--text-secondary);">User Name</th>
                        <th class="px-8 py-5 text-left text-xs font-black uppercase tracking-widest" style="color: var(--text-secondary);">Status</th>
                        <th class="px-8 py-5 text-right text-xs font-black uppercase tracking-widest" style="color: var(--text-secondary);">Referral Credit</th>
                    </tr>
                </thead>
                <tbody style="background-color: var(--bg-primary);">
                    @foreach($referrals as $referral)
                    <tr class="transition-colors hover:bg-black hover:bg-opacity-5" style="border-top: 1px solid var(--border-color);">
                        <td class="px-8 py-5 text-sm" style="color: var(--text-secondary);">{{ $referral['date'] }}</td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background-color: var(--gold);">{{ substr($referral['name'], 0, 1) }}</div>
                                <span class="text-sm font-bold" style="color: var(--text-primary);">{{ $referral['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest text-white shadow-sm" style="background-color: {{ $referral['status_color'] }};">
                                {{ $referral['status'] }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-sm text-right font-black" style="color: var(--gold);">
                            {{ $referral['reward'] }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
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
