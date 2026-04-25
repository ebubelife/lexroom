<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Ensure user has a wallet
        $wallet = $user->wallet ?: $user->wallet()->create(['credits_balance' => 0]);
        $balance = (float) $wallet->totalBalance();
        
        $transactions = $user->creditTransactions()
            ->latest()
            ->take(50)
            ->get();

        $topups = \App\Models\TopupPackage::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('wallet.index', compact('balance', 'transactions', 'topups'));
    }
}
