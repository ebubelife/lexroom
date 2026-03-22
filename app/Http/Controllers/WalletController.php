<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        $balance = 15000;
        
        $transactions = [
            [
                'date' => 'Dec 18, 2024',
                'description' => 'Wallet Top-up',
                'type' => 'Credit',
                'type_color' => '#15803D',
                'amount' => '+₦10,000',
                'amount_color' => '#15803D',
            ],
            [
                'date' => 'Dec 15, 2024',
                'description' => 'Tenancy Dispute Session',
                'type' => 'Debit',
                'type_color' => '#DC2626',
                'amount' => '-₦7,500',
                'amount_color' => '#DC2626',
            ],
            [
                'date' => 'Dec 12, 2024',
                'description' => 'Referral Bonus - John Doe',
                'type' => 'Credit',
                'type_color' => '#15803D',
                'amount' => '+₦1,000',
                'amount_color' => '#15803D',
            ],
            [
                'date' => 'Dec 10, 2024',
                'description' => 'Freelance Dispute Session',
                'type' => 'Debit',
                'type_color' => '#DC2626',
                'amount' => '-₦3,750',
                'amount_color' => '#DC2626',
            ],
            [
                'date' => 'Dec 8, 2024',
                'description' => 'Wallet Top-up',
                'type' => 'Credit',
                'type_color' => '#15803D',
                'amount' => '+₦20,000',
                'amount_color' => '#15803D',
            ],
        ];

        return view('wallet.index', compact('balance', 'transactions'));
    }
}
