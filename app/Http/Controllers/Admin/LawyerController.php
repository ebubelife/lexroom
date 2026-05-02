<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lawyer;
use Illuminate\Http\Request;

class LawyerController extends Controller
{
    public function index(Request $request)
    {
        $lawyers = Lawyer::query()
            ->when($request->search, fn($q, $v) => $q->where('name', 'like', "%{$v}%")
                ->orWhere('email', 'like', "%{$v}%"))
            ->when($request->jurisdiction, fn($q, $v) => $q->where('jurisdiction', $v))
            ->when($request->speciality, fn($q, $v) => $q->where('speciality', $v))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $jurisdictions = ['Nigeria', 'UK', 'South Africa', 'Ghana'];
        $specialities  = ['Tenancy', 'Freelance', 'Business', 'E-commerce', 'Debt', 'Employment', 'Marriage'];

        return view('admin.lawyers.index', compact('lawyers', 'jurisdictions', 'specialities'));
    }

    public function create()
    {
        $jurisdictions = ['Nigeria', 'UK', 'South Africa', 'Ghana'];
        $specialities  = ['Tenancy', 'Freelance', 'Business', 'E-commerce', 'Debt', 'Employment', 'Marriage'];
        return view('admin.lawyers.create', compact('jurisdictions', 'specialities'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'email'            => 'required|email|unique:lawyers,email',
            'phone'            => 'required|string|max:20',
            'jurisdiction'     => 'required|string',
            'speciality'       => 'required|string',
            'bio'              => 'nullable|string|max:500',
            'bar_number'       => 'nullable|string|max:50',
            'years_experience' => 'required|integer|min:0',
            'commission_rate'  => 'required|numeric|min:0|max:100',
            'verified'         => 'boolean',
            'active'           => 'boolean',
        ]);

        $data['verified'] = $request->boolean('verified');
        $data['active']   = $request->boolean('active', true);

        $lawyer = Lawyer::create($data);

        auth('admin')->user()->log('created_lawyer', 'Lawyer', $lawyer->id);

        return redirect()->route('admin.lawyers.index')->with('success', "Lawyer '{$lawyer->name}' added.");
    }

    public function edit(Lawyer $lawyer)
    {
        $jurisdictions = ['Nigeria', 'UK', 'South Africa', 'Ghana'];
        $specialities  = ['Tenancy', 'Freelance', 'Business', 'E-commerce', 'Debt', 'Employment', 'Marriage'];
        return view('admin.lawyers.edit', compact('lawyer', 'jurisdictions', 'specialities'));
    }

    public function update(Request $request, Lawyer $lawyer)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'email'            => 'required|email|unique:lawyers,email,' . $lawyer->id,
            'phone'            => 'required|string|max:20',
            'jurisdiction'     => 'required|string',
            'speciality'       => 'required|string',
            'bio'              => 'nullable|string|max:500',
            'bar_number'       => 'nullable|string|max:50',
            'years_experience' => 'required|integer|min:0',
            'commission_rate'  => 'required|numeric|min:0|max:100',
            'verified'         => 'boolean',
            'active'           => 'boolean',
        ]);

        $data['verified'] = $request->boolean('verified');
        $data['active']   = $request->boolean('active');

        $lawyer->update($data);

        auth('admin')->user()->log('updated_lawyer', 'Lawyer', $lawyer->id);

        return redirect()->route('admin.lawyers.index')->with('success', "Lawyer '{$lawyer->name}' updated.");
    }

    public function toggle(Lawyer $lawyer)
    {
        $lawyer->update(['active' => !$lawyer->active]);
        auth('admin')->user()->log('toggled_lawyer', 'Lawyer', $lawyer->id);
        return back()->with('success', "Lawyer " . ($lawyer->active ? 'activated' : 'deactivated') . ".");
    }

    public function destroy(Lawyer $lawyer)
    {
        $name = $lawyer->name;
        $lawyer->delete();
        auth('admin')->user()->log('deleted_lawyer', 'Lawyer', $lawyer->id);
        return redirect()->route('admin.lawyers.index')->with('success', "Lawyer '{$name}' removed.");
    }
}
