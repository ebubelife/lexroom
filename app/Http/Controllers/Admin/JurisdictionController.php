<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurisdiction;
use Illuminate\Http\Request;

class JurisdictionController extends Controller
{
    public function index(Request $request)
    {
        $jurisdictions = Jurisdiction::orderBy('sort_order')
            ->orderBy('region')
            ->orderBy('name')
            ->get()
            ->groupBy('region');

        return view('admin.jurisdictions.index', compact('jurisdictions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:100|unique:jurisdictions,name',
            'region' => 'required|string|max:100',
            'region_flag' => 'nullable|string|max:10',
        ]);

        Jurisdiction::create([
            'name'        => $request->name,
            'region'      => $request->region,
            'region_flag' => $request->region_flag ?? '🌐',
            'is_active'   => true,
            'sort_order'  => Jurisdiction::where('region', $request->region)->max('sort_order') ?? 99,
        ]);

        auth('admin')->user()->log('added_jurisdiction', 'Jurisdiction', null, [
            'name'   => $request->name,
            'region' => $request->region,
        ]);

        return back()->with('success', "\"{$request->name}\" added.");
    }

    public function toggle(Jurisdiction $jurisdiction)
    {
        $jurisdiction->update(['is_active' => !$jurisdiction->is_active]);

        auth('admin')->user()->log(
            $jurisdiction->is_active ? 'enabled_jurisdiction' : 'disabled_jurisdiction',
            'Jurisdiction',
            $jurisdiction->id,
            ['name' => $jurisdiction->name]
        );

        return back()->with('success', "\"{$jurisdiction->name}\" " . ($jurisdiction->is_active ? 'enabled' : 'disabled') . '.');
    }

    public function destroy(Jurisdiction $jurisdiction)
    {
        $name = $jurisdiction->name;
        auth('admin')->user()->log('deleted_jurisdiction', 'Jurisdiction', $jurisdiction->id, ['name' => $name]);
        $jurisdiction->delete();

        return back()->with('success', "\"{$name}\" deleted.");
    }
}
