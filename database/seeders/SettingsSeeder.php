<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Platform
            ['key' => 'app_maintenance_mode',            'value' => 'false',                       'type' => 'boolean', 'description' => 'Take the platform offline for all users'],
            ['key' => 'support_email',                   'value' => 'hello@firstmediator.com',      'type' => 'string',  'description' => 'Email shown to users for support'],
            ['key' => 'max_file_upload_mb',              'value' => '20',                           'type' => 'integer', 'description' => 'Maximum evidence file size in megabytes'],
            ['key' => 'otp_expiry_minutes',              'value' => '10',                           'type' => 'integer', 'description' => 'How long OTP codes remain valid'],

            // Payments (in USD cents)
            ['key' => 'price_30min',                     'value' => '450',                          'type' => 'integer', 'description' => 'Full session price in USD cents'],
            ['key' => 'price_60min',                     'value' => '750',                          'type' => 'integer', 'description' => 'Full session price in USD cents'],
            ['key' => 'price_90min',                     'value' => '1000',                         'type' => 'integer', 'description' => 'Full session price in USD cents'],
            ['key' => 'extension_price_30min',           'value' => '250',                          'type' => 'integer', 'description' => 'Extension price in USD cents'],
            ['key' => 'extension_price_60min',           'value' => '450',                          'type' => 'integer', 'description' => 'Extension price in USD cents'],

            // Referrals
            ['key' => 'referral_minutes_per_reward',     'value' => '10',                           'type' => 'integer', 'description' => 'Minutes awarded per successful referral'],
            ['key' => 'referral_pending_expiry_days',    'value' => '90',                           'type' => 'integer', 'description' => 'Days before a pending reward expires'],
            ['key' => 'referral_minutes_expiry_months',  'value' => '12',                           'type' => 'integer', 'description' => 'Months before earned minutes expire'],
            ['key' => 'referral_max_per_day',            'value' => '5',                            'type' => 'integer', 'description' => 'Max rewards a user can earn per day'],

            // Features
            ['key' => 'feature_referrals',               'value' => 'true',                         'type' => 'boolean', 'description' => 'Allow users to earn referral rewards'],
            ['key' => 'feature_fmrefer',                 'value' => 'true',                         'type' => 'boolean', 'description' => 'Show the FM Refer lawyer directory'],
            ['key' => 'feature_split_payment',           'value' => 'true',                         'type' => 'boolean', 'description' => 'Allow rooms to use split payment mode'],
            ['key' => 'feature_session_extend',          'value' => 'true',                         'type' => 'boolean', 'description' => 'Allow parties to extend sessions'],
        ];

        foreach ($defaults as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
