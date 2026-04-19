<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAction;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $groups   = Setting::all_grouped();
        $settings = Setting::all()->keyBy('key');

        // Audit log tab
        $actions = AdminAction::with('admin')
            ->when($request->input('admin_filter'), fn($q, $v) => $q->where('admin_id', $v))
            ->when($request->input('action_filter'), fn($q, $v) => $q->where('action', 'like', "%{$v}%"))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $admins = \App\Models\Admin::orderBy('name')->get();

        return view('admin.settings.index', compact('groups', 'settings', 'actions', 'admins'));
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
}
