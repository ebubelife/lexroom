<?php

namespace App\Http\Controllers;

use App\Models\Lawyer;
use Illuminate\Http\Request;

class FmReferController extends Controller
{
    public function index(Request $request)
    {
        $query = Lawyer::where('active', true)->where('verified', true);

        if ($request->filled('jurisdiction')) {
            $query->where('jurisdiction', $request->jurisdiction);
        }

        if ($request->filled('speciality')) {
            $query->where('speciality', $request->speciality);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $lawyers      = $query->orderBy('years_experience', 'desc')->paginate(12);
        $jurisdictions = ['Nigeria', 'UK', 'South Africa', 'Ghana'];
        $specialities  = ['Tenancy', 'Freelance', 'Business', 'E-commerce', 'Debt', 'Employment', 'Marriage'];

        return view('fmrefer.index', compact('lawyers', 'jurisdictions', 'specialities'));
    }

    public function show(Lawyer $lawyer)
    {
        abort_if(!$lawyer->active || !$lawyer->verified, 404);
        return view('fmrefer.show', compact('lawyer'));
    }

    public function contact(Request $request, Lawyer $lawyer)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'message' => 'required|string|max:1000',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your request has been sent. The lawyer will contact you within 48-72 hours.',
        ]);
    }
}
