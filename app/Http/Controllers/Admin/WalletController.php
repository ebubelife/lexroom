<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $query = Wallet::with('user')->latest();

        if ($search = $request->input('search')) {
            $query->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")
                                                   ->orWhere('email', 'like', "%{$search}%"));
        }

        if ($filter = $request->input('filter')) {
            match ($filter) {
                'has_escrow'   => $query->where('escrow_balance', '>', 0),
                'has_referral' => $query->where('referral_minutes', '>', 0),
                'zero_balance' => $query->where('credits_balance', 0),
                default        => null,
            };
        }

        $wallets = $query->paginate(30)->withQueryString();

        $stats = [
            'total_credits'  => Wallet::sum('credits_balance'),
            'total_escrow'   => Wallet::sum('escrow_balance'),
            'total_referral' => Wallet::sum('referral_minutes'),
        ];

        return view('admin.wallets.index', compact('wallets', 'stats'));
    }

    public function adjust(Request $request, Wallet $wallet)
    {
        $request->validate([
            'type'   => 'required|in:add,deduct',
            'amount' => 'required|numeric|min:1',
            'reason' => 'required|string|max:255',
        ]);

        $amount = (float) $request->input('amount');

        if ($request->input('type') === 'add') {
            $wallet->increment('credits_balance', $amount);
        } else {
            if ($wallet->credits_balance < $amount) {
                return back()->with('error', 'Insufficient wallet balance to deduct.');
            }
            $wallet->decrement('credits_balance', $amount);
        }

        auth('admin')->user()->log('adjusted_wallet', 'Wallet', $wallet->id, [
            'user_id' => $wallet->user_id,
            'type'    => $request->input('type'),
            'amount'  => $amount,
            'reason'  => $request->input('reason'),
        ]);

        return back()->with('success', 'Wallet balance updated.');
    }

    public function releaseEscrow(Wallet $wallet)
    {
        if ($wallet->escrow_balance <= 0) {
            return back()->with('error', 'No escrow balance to release.');
        }

        $amount = $wallet->escrow_balance;
        $wallet->moveEscrowToCredits($amount);

        auth('admin')->user()->log('released_escrow', 'Wallet', $wallet->id, [
            'user_id' => $wallet->user_id,
            'amount'  => $amount,
        ]);

        return back()->with('success', "£" . number_format($amount, 0) . " released from escrow to credits.");
    }
}
