<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAction;
use App\Models\SessionPackage;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $groups   = Setting::all_grouped();
        $settings = Setting::all()->keyBy('key');
        $packages = SessionPackage::orderBy('sort_order')->get();

        // Audit log tab
        $actions = AdminAction::with('admin')
            ->when($request->input('admin_filter'), fn($q, $v) => $q->where('admin_id', $v))
            ->when($request->input('action_filter'), fn($q, $v) => $q->where('action', 'like', "%{$v}%"))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $admins = \App\Models\Admin::orderBy('name')->get();

        return view('admin.settings.index', compact('groups', 'settings', 'actions', 'admins', 'packages'));
    }

    public function update(Request $request)
    {
        $groups  = Setting::all_grouped();
        $changed = [];

        foreach ($groups as $group => $keys) {
            foreach ($keys as $key => $meta) {
                if ($meta['type'] === 'boolean') {
                    // Checkboxes only appear in POST when checked
                    $value = $request->has($key) ? 'true' : 'false';
                } else {
                    if (!$request->has($key)) continue;
                    $value = $request->input($key);
                }

                $old = Setting::get($key);
                Setting::set($key, $value);

                if ((string) $old !== (string) $value) {
                    $changed[$key] = ['from' => $old, 'to' => $value];
                }
            }
        }

        if (!empty($changed)) {
            auth('admin')->user()->log('updated_settings', null, null, ['changes' => $changed]);
        }

        return back()->with('success', 'Settings saved successfully.');
    }

    public function updatePackage(Request $request, SessionPackage $package)
    {
        $request->validate([
            'name'              => 'required|string|max:50',
            'duration_minutes'  => 'required|integer|min:1',
            'full_price_pence'  => 'required|integer|min:1',
            'split_price_pence' => 'required|integer|min:1',
        ]);

        $package->update($request->only(['name', 'duration_minutes', 'full_price_pence', 'split_price_pence']));

        auth('admin')->user()->log('updated_session_package', 'SessionPackage', $package->id);

        return back()->with('success', "Package '{$package->name}' updated.");
    }

    public function storePackage(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:50',
            'duration_minutes'  => 'required|integer|min:1',
            'full_price_pence'  => 'required|integer|min:1',
            'split_price_pence' => 'required|integer|min:1',
        ]);

        $package = SessionPackage::create([
            'name'              => $request->name,
            'duration_minutes'  => $request->duration_minutes,
            'full_price_pence'  => $request->full_price_pence,
            'split_price_pence' => $request->split_price_pence,
            'is_active'         => true,
            'sort_order'        => SessionPackage::max('sort_order') + 1,
        ]);

        auth('admin')->user()->log('created_session_package', 'SessionPackage', $package->id);

        return back()->with('success', "Package '{$package->name}' created.");
    }

    public function destroyPackage(SessionPackage $package)
    {
        $name = $package->name;
        $package->delete();

        auth('admin')->user()->log('deleted_session_package', 'SessionPackage', $package->id);

        return back()->with('success', "Package '{$name}' deleted.");
    }
}
