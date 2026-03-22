@extends('layouts.app')

@section('title', 'Wallet & Credits — LexRoom')
@section('page-title', 'Wallet & Credits')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2">
            <div class="p-8 rounded-xl" style="background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);">
                <p class="text-sm font-medium text-white mb-2">Available Balance</p>
                <h2 class="text-4xl font-bold text-white mb-4">₦{{ number_format($balance) }}</h2>
                <p class="text-sm text-white opacity-90">Enough for {{ floor($balance / 7500) }} standard sessions</p>
            </div>
        </div>
        <div class="p-6 rounded-xl" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color);">
            <p class="text-sm font-medium mb-2" style="color: var(--text-secondary);">Quick Top-up</p>
            <button class="w-full px-4 py-3 rounded-lg text-sm font-medium transition-colors hover:opacity-90 mb-2" style="background-color: var(--gold); color: var(--white);">
                Add ₦10,000
            </button>
            <button class="w-full px-4 py-3 rounded-lg text-sm font-medium transition-colors hover:bg-opacity-10 hover:bg-gray-500" style="border: 1px solid var(--border-color); color: var(--text-primary);">
                Custom Amount
            </button>
        </div>
    </div>

    <div class="mb-6">
        <h2 class="text-xl font-serif mb-4" style="color: var(--text-primary);">Transaction History</h2>
        <div class="rounded-xl overflow-hidden" style="border: 1px solid var(--border-color);">
            <table class="min-w-full">
                <thead style="background-color: var(--bg-secondary);">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Type</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Amount</th>
                    </tr>
                </thead>
                <tbody style="background-color: var(--bg-primary);">
                    @foreach($transactions as $transaction)
                    <tr style="border-top: 1px solid var(--border-color);">
                        <td class="px-6 py-4 text-sm" style="color: var(--text-secondary);">{{ $transaction['date'] }}</td>
                        <td class="px-6 py-4 text-sm" style="color: var(--text-primary);">{{ $transaction['description'] }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium" style="background-color: {{ $transaction['type_color'] }}; color: white;">
                                {{ $transaction['type'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-right font-medium" style="color: {{ $transaction['amount_color'] }};">
                            {{ $transaction['amount'] }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
