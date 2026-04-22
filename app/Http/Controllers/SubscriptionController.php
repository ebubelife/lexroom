<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\TopupPackage;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class SubscriptionController extends Controller
{
    public function __construct(private SubscriptionService $service) {}

    public function pricing()
    {
        $plans   = SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();
        $topups  = TopupPackage::where('is_active', true)->orderBy('sort_order')->get();
        $current = auth()->check() ? $this->service->getActiveSub(auth()->user()) : null;

        return view('pricing', compact('plans', 'topups', 'current'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'plan_slug' => 'required|string',
            'cycle'     => 'required|in:monthly,quarterly,yearly',
        ]);

        $plan = SubscriptionPlan::where('slug', $request->plan_slug)
            ->where('is_active', true)
            ->firstOrFail();

        $priceId = $plan->stripePriceIdFor($request->cycle);

        if (!$priceId || str_starts_with($priceId, 'STRIPE_')) {
            return back()->with('error', 'Stripe price not configured yet. Please contact support.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'mode'                 => 'subscription',
            'line_items'           => [[
                'price'    => $priceId,
                'quantity' => 1,
            ]],
            'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('pricing'),
            'metadata'    => [
                'type'          => 'subscription',
                'user_id'       => auth()->id(),
                'plan_id'       => $plan->id,
                'billing_cycle' => $request->cycle,
            ],
        ]);

        return redirect($session->url);
    }

    public function topup(Request $request)
    {
        $request->validate(['package_id' => 'required|integer']);

        $package = TopupPackage::where('id', $request->package_id)
            ->where('is_active', true)
            ->firstOrFail();

        if (!$package->stripe_price_id || str_starts_with($package->stripe_price_id, 'STRIPE_')) {
            return back()->with('error', 'Top-up not configured yet. Please contact support.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'mode'                 => 'payment',
            'line_items'           => [[
                'price'    => $package->stripe_price_id,
                'quantity' => 1,
            ]],
            'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}&type=topup',
            'cancel_url'  => route('settings.index') . '?tab=subscription',
            'metadata'    => [
                'type'       => 'topup',
                'user_id'    => auth()->id(),
                'package_id' => $package->id,
            ],
        ]);

        return redirect($session->url);
    }

    public function cancel(Request $request)
    {
        $user = auth()->user();

        if ($this->service->cancelSubscription($user)) {
            return back()->with('success', 'Your subscription will cancel at the end of the current billing period.');
        }

        return back()->with('error', 'Could not cancel subscription. Please try again or contact support.');
    }

    public function success(Request $request)
    {
        $type = $request->input('type', 'subscription');
        return view('subscription.success', compact('type'));
    }
}
