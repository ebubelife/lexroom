<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LexReferController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => 8,
            'successful' => 5,
            'earned' => 5000,
        ];

        $referralLink = url('/register?ref=' . auth()->user()->id);

        $referrals = [
            [
                'date' => 'Dec 18, 2024',
                'name' => 'John Doe',
                'status' => 'Completed',
                'status_color' => '#15803D',
                'reward' => '+₦1,000',
            ],
            [
                'date' => 'Dec 15, 2024',
                'name' => 'Jane Smith',
                'status' => 'Completed',
                'status_color' => '#15803D',
                'reward' => '+₦1,000',
            ],
            [
                'date' => 'Dec 12, 2024',
                'name' => 'Mike Johnson',
                'status' => 'Pending',
                'status_color' => '#F59E0B',
                'reward' => '₦0',
            ],
            [
                'date' => 'Dec 10, 2024',
                'name' => 'Sarah Williams',
                'status' => 'Completed',
                'status_color' => '#15803D',
                'reward' => '+₦1,000',
            ],
            [
                'date' => 'Dec 8, 2024',
                'name' => 'David Brown',
                'status' => 'Completed',
                'status_color' => '#15803D',
                'reward' => '+₦1,000',
            ],
            [
                'date' => 'Dec 5, 2024',
                'name' => 'Emma Davis',
                'status' => 'Pending',
                'status_color' => '#F59E0B',
                'reward' => '₦0',
            ],
            [
                'date' => 'Dec 3, 2024',
                'name' => 'Chris Wilson',
                'status' => 'Completed',
                'status_color' => '#15803D',
                'reward' => '+₦1,000',
            ],
            [
                'date' => 'Dec 1, 2024',
                'name' => 'Lisa Anderson',
                'status' => 'Pending',
                'status_color' => '#F59E0B',
                'reward' => '₦0',
            ],
        ];

        return view('lexrefer.index', compact('stats', 'referralLink', 'referrals'));
    }
}
