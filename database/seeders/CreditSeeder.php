<?php

namespace Database\Seeders;

use App\Models\CreditSetting;
use App\Models\TopupPackage;
use Illuminate\Database\Seeder;

class CreditSeeder extends Seeder
{
    public function run(): void
    {
        // ── Top-up packages ──────────────────────────────────────────
        $packages = [
            ['label' => 'Small',  'credits' => 5.00,  'price' => 5.00,  'bonus_credits' => 0.00,  'stripe_price_id' => 'STRIPE_TOPUP_SMALL',  'sort_order' => 0],
            ['label' => 'Medium', 'credits' => 10.00, 'price' => 10.00, 'bonus_credits' => 1.00,  'stripe_price_id' => 'STRIPE_TOPUP_MEDIUM', 'sort_order' => 1],
            ['label' => 'Large',  'credits' => 25.00, 'price' => 25.00, 'bonus_credits' => 5.00,  'stripe_price_id' => 'STRIPE_TOPUP_LARGE',  'sort_order' => 2],
            ['label' => 'XL',     'credits' => 50.00, 'price' => 50.00, 'bonus_credits' => 15.00, 'stripe_price_id' => 'STRIPE_TOPUP_XL',     'sort_order' => 3],
        ];

        foreach ($packages as $pkg) {
            TopupPackage::firstOrCreate(['label' => $pkg['label']], $pkg);
        }

        // ── Credit settings ──────────────────────────────────────────
        $settings = [
            ['key' => 'credits_expire_on_renewal', 'value' => 'true',  'label' => 'Expire unused credits on renewal',  'group' => 'credits'],
            ['key' => 'credits_to_minutes_rate',   'value' => '4',     'label' => 'Session minutes per £1 credit',      'group' => 'credits'],
            ['key' => 'referral_reward_credits',   'value' => '2.00',  'label' => 'Referral reward (£ credits)',         'group' => 'credits'],
            ['key' => 'gbp_to_usd_rate',           'value' => '1.27',  'label' => 'GBP → USD exchange rate',            'group' => 'currency'],
            ['key' => 'gbp_to_eur_rate',           'value' => '1.17',  'label' => 'GBP → EUR exchange rate',            'group' => 'currency'],
        ];

        foreach ($settings as $s) {
            CreditSetting::firstOrCreate(['key' => $s['key']], $s);
        }
    }
}
