<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::orderBy('sort_order')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                       => 'required|string|max:100',
            'slug'                       => 'required|string|max:100|unique:subscription_plans,slug',
            'description'                => 'nullable|string',
            'credits_per_cycle'          => 'required|numeric|min:0',
            'price_monthly'              => 'required|numeric|min:0',
            'price_quarterly'            => 'required|numeric|min:0',
            'price_yearly'               => 'required|numeric|min:0',
            'stripe_monthly_price_id'    => 'nullable|string',
            'stripe_quarterly_price_id'  => 'nullable|string',
            'stripe_yearly_price_id'     => 'nullable|string',
            'features'                   => 'nullable|string',
        ]);

        $data['features']    = array_filter(array_map('trim', explode("\n", $data['features'] ?? '')));
        $data['sort_order']  = SubscriptionPlan::max('sort_order') + 1;

        SubscriptionPlan::create($data);
        auth('admin')->user()->log('created_plan', null, null, ['name' => $data['name']]);

        return back()->with('success', "Plan \"{$data['name']}\" created.");
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $data = $request->validate([
            'name'                       => 'required|string|max:100',
            'description'                => 'nullable|string',
            'credits_per_cycle'          => 'required|numeric|min:0',
            'price_monthly'              => 'required|numeric|min:0',
            'price_quarterly'            => 'required|numeric|min:0',
            'price_yearly'               => 'required|numeric|min:0',
            'stripe_monthly_price_id'    => 'nullable|string',
            'stripe_quarterly_price_id'  => 'nullable|string',
            'stripe_yearly_price_id'     => 'nullable|string',
            'features'                   => 'nullable|string',
        ]);

        $data['features'] = array_filter(array_map('trim', explode("\n", $data['features'] ?? '')));

        $plan->update($data);
        auth('admin')->user()->log('updated_plan', 'SubscriptionPlan', $plan->id, ['name' => $plan->name]);

        return back()->with('success', "Plan \"{$plan->name}\" updated.");
    }

    public function toggle(SubscriptionPlan $plan)
    {
        $plan->update(['is_active' => !$plan->is_active]);
        auth('admin')->user()->log(
            $plan->is_active ? 'enabled_plan' : 'disabled_plan',
            'SubscriptionPlan', $plan->id
        );

        return back()->with('success', "\"{$plan->name}\" " . ($plan->is_active ? 'enabled' : 'disabled') . '.');
    }
}
