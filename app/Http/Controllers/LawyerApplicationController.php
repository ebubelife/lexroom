<?php

namespace App\Http\Controllers;

use App\Models\Lawyer;
use Illuminate\Http\Request;

class LawyerApplicationController extends Controller
{
    public function create()
    {
        $jurisdictions = ['Nigeria', 'UK', 'South Africa', 'Ghana'];
        $specialities  = ['Tenancy', 'Freelance', 'Business', 'E-commerce', 'Debt', 'Employment', 'Marriage'];

        return view('lawyers.apply', compact('jurisdictions', 'specialities'));
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
        ]);

        // Self-submitted applications always start unverified and inactive —
        // only an admin can verify/activate a lawyer from the admin panel.
        $data['verified'] = false;
        $data['active']   = false;

        Lawyer::create($data);

        return redirect()->route('lawyers.apply')->with('success', "Thanks — your application has been received. Our team will review it and get back to you.");
    }
}
