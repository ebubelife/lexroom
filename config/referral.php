<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Referral Reward Settings
    |--------------------------------------------------------------------------
    | These values will be editable from the admin dashboard.
    | For now they are set here as defaults.
    */

    // Minutes awarded per successful referral
    'minutes_per_referral' => env('REFERRAL_MINUTES_PER_REWARD', 10),

    // How long referral reward stays pending before expiring (days)
    'pending_expiry_days' => env('REFERRAL_PENDING_EXPIRY_DAYS', 90),

    // How long earned minutes last before expiring (months)
    'minutes_expiry_months' => env('REFERRAL_MINUTES_EXPIRY_MONTHS', 12),

    // Max referrals rewarded per user per month
    'max_per_month' => env('REFERRAL_MAX_PER_MONTH', 50),

    // Max referrals rewarded per user per day
    'max_per_day' => env('REFERRAL_MAX_PER_DAY', 5),
];
