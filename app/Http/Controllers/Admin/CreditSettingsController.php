<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreditSetting;
use App\Models\TopupPackage;
use Illuminate\Http\Request;

class CreditSettingsController extends Controller
{
    public function index()
    {
        $settings = CreditSetting::all()->keyBy('key');
        $topups   = TopupPackage::orderBy('sort_order')->get();
        return view('admin.credits.index', compact('settings', 'topups'));
    }

    public function update(Request $request)
    {
        $keys = ['credits_expire_on_renewal', 'credits_to_minutes_rate', 'referral_reward_credits', 'gbp_to_usd_rate', 'gbp_to_eur_rate'];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                CreditSetting::set($key, $request->input($key));
            }
        }

        // Handle boolean checkbox
        CreditSetting::set('credits_expire_on_renewal', $request->has('credits_expire_on_renewal') ? 'true' : 'false');

        auth('admin')->user()->log('updated_credit_settings');
        return back()->with('success', 'Credit settings saved.');
    }

    public function storeTopup(Request $request)
    {
        $data = $request->validate([
            'label'          => 'required|string|max:50',
            'credits'        => 'required|numeric|min:0.01',
            'price'          => 'required|numeric|min:0.01',
            'bonus_credits'  => 'nullable|numeric|min:0',
            'stripe_price_id'=> 'nullable|string',
        ]);

        $data['bonus_credits'] = $data['bonus_credits'] ?? 0;
        $data['sort_order']    = TopupPackage::max('sort_order') + 1;

        TopupPackage::create($data);
        auth('admin')->user()->log('created_topup_package', null, null, ['label' => $data['label']]);

        return back()->with('success', "Top-up \"{$data['label']}\" created.");
    }

    public function updateTopup(Request $request, TopupPackage $package)
    {
        $data = $request->validate([
            'label'          => 'required|string|max:50',
            'credits'        => 'required|numeric|min:0.01',
            'price'          => 'required|numeric|min:0.01',
            'bonus_credits'  => 'nullable|numeric|min:0',
            'stripe_price_id'=> 'nullable|string',
        ]);

        $data['bonus_credits'] = $data['bonus_credits'] ?? 0;
        $package->update($data);
        auth('admin')->user()->log('updated_topup_package', 'TopupPackage', $package->id);

        return back()->with('success', "Top-up \"{$package->label}\" updated.");
    }

    public function toggleTopup(TopupPackage $package)
    {
        $package->update(['is_active' => !$package->is_active]);
        return back()->with('success', "\"{$package->label}\" " . ($package->is_active ? 'enabled' : 'disabled') . '.');
    }

    public function destroyTopup(TopupPackage $package)
    {
        $label = $package->label;
        $package->delete();
        auth('admin')->user()->log('deleted_topup_package', null, null, ['label' => $label]);
        return back()->with('success', "\"{$label}\" deleted.");
    }
}
