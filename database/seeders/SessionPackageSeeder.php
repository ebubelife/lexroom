<?php

namespace Database\Seeders;

use App\Models\SessionPackage;
use Illuminate\Database\Seeder;

class SessionPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name'               => 'Starter',
                'duration_minutes'   => 30,
                'full_price_pence'   => 4500,  // £45.00
                'split_price_pence'  => 2250,  // £22.50
                'is_active'          => true,
                'sort_order'         => 1,
            ],
            [
                'name'               => 'Standard',
                'duration_minutes'   => 60,
                'full_price_pence'   => 8000,  // £80.00
                'split_price_pence'  => 4000,  // £40.00
                'is_active'          => true,
                'sort_order'         => 2,
            ],
            [
                'name'               => 'Extended',
                'duration_minutes'   => 90,
                'full_price_pence'   => 10000, // £100.00
                'split_price_pence'  => 5000,  // £50.00
                'is_active'          => true,
                'sort_order'         => 3,
            ],
        ];

        foreach ($packages as $package) {
            SessionPackage::updateOrCreate(
                ['duration_minutes' => $package['duration_minutes']],
                $package
            );
        }
    }
}
