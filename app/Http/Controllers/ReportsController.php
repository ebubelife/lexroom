<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index()
    {
        $reports = [
            [
                'category' => 'Tenancy',
                'badge_color' => '#1D4ED8',
                'date' => 'Dec 15, 2024',
                'title' => 'Security Deposit Dispute Resolution',
                'summary' => 'Mediation between tenant and landlord regarding security deposit refund. Lex AI facilitated agreement on partial refund.',
                'duration' => '60',
                'outcome' => 'Resolved',
            ],
            [
                'category' => 'Freelance',
                'badge_color' => '#15803D',
                'date' => 'Dec 10, 2024',
                'title' => 'Web Development Payment Dispute',
                'summary' => 'Client disputed final payment for completed project. Agreement reached on payment plan.',
                'duration' => '45',
                'outcome' => 'Partially Resolved',
            ],
        ];

        return view('reports.index', compact('reports'));
    }
}
