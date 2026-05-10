<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'description'];

    // Cache TTL in seconds
    const CACHE_TTL = 3600;

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = Cache::remember("setting:{$key}", self::CACHE_TTL, function () use ($key) {
            return static::where('key', $key)->first();
        });

        if (!$setting) return $default;

        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $setting->value,
            'json'    => json_decode($setting->value, true),
            default   => $setting->value,
        };
    }

    public static function set(string $key, mixed $value): void
    {
        $setting = static::where('key', $key)->first();

        if ($setting) {
            $setting->update(['value' => is_array($value) ? json_encode($value) : (string) $value]);
        } else {
            static::create(['key' => $key, 'value' => (string) $value]);
        }

        Cache::forget("setting:{$key}");
    }

    public static function all_grouped(): array
    {
        $settings = static::all()->keyBy('key');

        return [
            'Platform' => [
                'app_maintenance_mode'   => ['label' => 'Maintenance Mode',        'type' => 'boolean', 'description' => 'Take the platform offline for all users'],
                'support_email'          => ['label' => 'Support Email',            'type' => 'string',  'description' => 'Email shown to users for support'],
                'max_file_upload_mb'     => ['label' => 'Max File Upload (MB)',     'type' => 'integer', 'description' => 'Maximum evidence file size in megabytes'],
                'otp_expiry_minutes'     => ['label' => 'OTP Expiry (minutes)',     'type' => 'integer', 'description' => 'How long OTP codes remain valid'],
            ],
            'Payments' => [
                // Session prices are managed in Admin → Settings → Session Packages tab
                // (session_packages table) — not here
                'default_currency' => ['label' => 'Default Currency', 'type' => 'select', 'description' => 'Platform currency code', 'options' => [
                    'gbp' => 'GBP (£) - British Pound',
                    'usd' => 'USD ($) - US Dollar', 
                    'eur' => 'EUR (€) - Euro',
                    'cad' => 'CAD (CA$) - Canadian Dollar',
                    'aud' => 'AUD (A$) - Australian Dollar'
                ]],
            ],
            'Referrals' => [
                'referral_minutes_per_reward'   => ['label' => 'Minutes Per Referral',       'type' => 'integer', 'description' => 'Minutes awarded per successful referral'],
                'referral_pending_expiry_days'  => ['label' => 'Pending Expiry (days)',       'type' => 'integer', 'description' => 'Days before a pending reward expires'],
                'referral_minutes_expiry_months'=> ['label' => 'Minutes Expiry (months)',     'type' => 'integer', 'description' => 'Months before earned minutes expire'],
                'referral_max_per_day'          => ['label' => 'Max Referrals Per Day',       'type' => 'integer', 'description' => 'Max rewards a user can earn per day'],
            ],
            'Features' => [
                'feature_referrals'      => ['label' => 'Enable Referrals',         'type' => 'boolean', 'description' => 'Allow users to earn referral rewards'],
                'feature_fmrefer'        => ['label' => 'Enable FM Refer',          'type' => 'boolean', 'description' => 'Show the FM Refer lawyer directory'],
                'feature_split_payment'  => ['label' => 'Enable Split Payment',     'type' => 'boolean', 'description' => 'Allow rooms to use split payment mode'],
                'feature_session_extend' => ['label' => 'Enable Session Extension', 'type' => 'boolean', 'description' => 'Allow parties to extend sessions'],
            ],
        ];
    }
}
