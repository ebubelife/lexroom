<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $query = Billing::with(['user', 'room'])
            ->latest('created_at');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('stripe_payment_intent_id', 'like', "%{$search}%")
                  ->orWhere('stripe_session_id', 'like', "%{$search}%")
                  ->orWhereHas('room', fn($r) => $r->where('case_id', 'like', "%{$search}%"))
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")
                                                    ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($party = $request->input('party')) {
            $query->where('party', $party);
        }

        $billings = $query->paginate(30)->withQueryString();

        $totals = [
            'paid'     => Billing::where('status', 'paid')->sum('amount'),
            'pending'  => Billing::where('status', 'pending')->count(),
            'refunded' => Billing::where('status', 'refunded')->sum('amount'),
        ];

        return view('admin.billing.index', compact('billings', 'totals'));
    }

    public function refunds(Request $request)
    {
        $query = Billing::with(['user', 'room'])
            ->where('status', 'paid')
            ->whereNotNull('stripe_payment_intent_id')
            ->latest('paid_at');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('room', fn($r) => $r->where('case_id', 'like', "%{$search}%"))
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")
                                                    ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $billings = $query->paginate(30)->withQueryString();

        return view('admin.billing.refunds', compact('billings'));
    }

    public function issueRefund(Request $request, Billing $billing)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        if ($billing->status !== 'paid') {
            return back()->with('error', 'Only paid transactions can be refunded.');
        }

        if (!$billing->stripe_payment_intent_id) {
            return back()->with('error', 'No Stripe payment intent found for this transaction.');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            \Stripe\Refund::create([
                'payment_intent' => $billing->stripe_payment_intent_id,
                'reason'         => 'requested_by_customer',
            ]);

            $billing->update(['status' => 'refunded']);

            auth('admin')->user()->log('issued_refund', 'Billing', $billing->id, [
                'amount'                   => $billing->amount,
                'stripe_payment_intent_id' => $billing->stripe_payment_intent_id,
                'reason'                   => $request->input('reason'),
            ]);

            return back()->with('success', "Refund of £{$billing->amount} issued successfully.");

        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe refund failed: ' . $e->getMessage());
            return back()->with('error', 'Stripe error: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $query = Billing::with(['user', 'room'])->latest('created_at');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $billings = $query->get();

        $filename = 'billing-export-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($billings) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID', 'Case ID', 'User', 'Email', 'Party', 'Plan',
                'Amount', 'Status', 'Stripe Intent ID', 'Paid At', 'Created At',
            ]);

            foreach ($billings as $b) {
                fputcsv($handle, [
                    $b->id,
                    $b->room?->case_id ?? '—',
                    $b->user?->name ?? '—',
                    $b->user?->email ?? '—',
                    $b->party,
                    $b->plan ?? '—',
                    $b->amount,
                    $b->status,
                    $b->stripe_payment_intent_id ?? '—',
                    $b->paid_at?->format('Y-m-d H:i:s') ?? '—',
                    $b->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        auth('admin')->user()->log('exported_billing_csv', null, null, ['status_filter' => $request->input('status', 'all')]);

        return response()->stream($callback, 200, $headers);
    }
}
