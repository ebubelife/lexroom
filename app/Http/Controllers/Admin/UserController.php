<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount(['referrals'])
            ->with('wallet')
            ->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($filter = $request->input('filter')) {
            match ($filter) {
                'verified'    => $query->whereNotNull('email_verified_at'),
                'unverified'  => $query->whereNull('email_verified_at'),
                'suspended'   => $query->whereNotNull('suspended_at'),
                'new_7d'      => $query->where('created_at', '>=', now()->subDays(7)),
                default       => null,
            };
        }

        $users = $query->paginate(25)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('wallet', 'referrals', 'referredBy', 'referralRewards.referredUser');

        $rooms = Room::with(['partyA', 'partyB'])
            ->where('party_a_id', $user->id)
            ->orWhere('party_b_id', $user->id)
            ->latest()
            ->get();

        $billings = \App\Models\Billing::where('user_id', $user->id)
            ->with('room')
            ->latest()
            ->get();

        return view('admin.users.show', compact('user', 'rooms', 'billings'));
    }

    public function verifyEmail(User $user)
    {
        $user->update(['email_verified_at' => now()]);
        auth('admin')->user()->log('verified_email', 'User', $user->id);

        return back()->with('success', "{$user->name}'s email verified.");
    }

    public function verifyPhone(User $user)
    {
        $user->update(['phone_verified_at' => now()]);
        auth('admin')->user()->log('verified_phone', 'User', $user->id);

        return back()->with('success', "{$user->name}'s phone verified.");
    }

    public function suspend(Request $request, User $user)
    {
        $request->validate(['reason' => 'nullable|string|max:255']);

        $user->update(['suspended_at' => now()]);
        auth('admin')->user()->log('suspended_user', 'User', $user->id, [
            'reason' => $request->input('reason'),
        ]);

        return back()->with('success', "{$user->name} has been suspended.");
    }

    public function unsuspend(User $user)
    {
        $user->update(['suspended_at' => null]);
        auth('admin')->user()->log('unsuspended_user', 'User', $user->id);

        return back()->with('success', "{$user->name} has been unsuspended.");
    }

    public function adjustWallet(Request $request, User $user)
    {
        $request->validate([
            'type'   => 'required|in:add,deduct',
            'amount' => 'required|numeric|min:1',
            'reason' => 'required|string|max:255',
        ]);

        $wallet = $user->wallet;
        if (!$wallet) {
            return back()->with('error', 'User has no wallet.');
        }

        $amount = (float) $request->input('amount');

        if ($request->input('type') === 'add') {
            $wallet->increment('credits_balance', $amount);
        } else {
            if ($wallet->credits_balance < $amount) {
                return back()->with('error', 'Insufficient wallet balance.');
            }
            $wallet->decrement('credits_balance', $amount);
        }

        auth('admin')->user()->log('adjusted_wallet', 'User', $user->id, [
            'type'   => $request->input('type'),
            'amount' => $amount,
            'reason' => $request->input('reason'),
        ]);

        return back()->with('success', 'Wallet balance updated.');
    }
}
