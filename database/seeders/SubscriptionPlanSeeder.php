<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'               => 'Starter',
                'slug'               => 'starter',
                'description'        => 'Perfect for occasional disputes',
                'credits_per_cycle'  => 12.00,
                'price_monthly'      => 9.00,
                'price_quarterly'    => 24.00,
                'price_yearly'       => 86.00,
                'stripe_monthly_price_id'    => 'STRIPE_STARTER_MONTHLY',
                'stripe_quarterly_price_id'  => 'STRIPE_STARTER_QUARTERLY',
                'stripe_yearly_price_id'     => 'STRIPE_STARTER_YEARLY',
                'features'           => [
                    '£12 session credits per month',
                    'Up to 2-3 sessions/month',
                    'AI mediation with Lex',
                    'PDF mediation report',
                    'Evidence vault',
                ],
                'sort_order'         => 0,
            ],
            [
                'name'               => 'Standard',
                'slug'               => 'standard',
                'description'        => 'For regular mediation needs',
                'credits_per_cycle'  => 27.00,
                'price_monthly'      => 19.00,
                'price_quarterly'    => 51.00,
                'price_yearly'       => 182.00,
                'stripe_monthly_price_id'    => 'STRIPE_STANDARD_MONTHLY',
                'stripe_quarterly_price_id'  => 'STRIPE_STANDARD_QUARTERLY',
                'stripe_yearly_price_id'     => 'STRIPE_STANDARD_YEARLY',
                'features'           => [
                    '£27 session credits per month',
                    'Up to 5-6 sessions/month',
                    'Everything in Starter',
                    'Priority Lex responses',
                    'FM Refer access',
                ],
                'sort_order'         => 1,
            ],
            [
                'name'               => 'Pro',
                'slug'               => 'pro',
                'description'        => 'Unlimited mediation power',
                'credits_per_cycle'  => 60.00,
                'price_monthly'      => 39.00,
                'price_quarterly'    => 105.00,
                'price_yearly'       => 374.00,
                'stripe_monthly_price_id'    => 'STRIPE_PRO_MONTHLY',
                'stripe_quarterly_price_id'  => 'STRIPE_PRO_QUARTERLY',
                'stripe_yearly_price_id'     => 'STRIPE_PRO_YEARLY',
                'features'           => [
                    '£60 session credits per month',
                    'Unlimited sessions',
                    'Everything in Standard',
                    'Dedicated support',
                    'Custom dispute categories',
                ],
                'sort_order'         => 2,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::firstOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
