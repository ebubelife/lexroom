<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PlatformSetting extends Model
{
    protected $fillable = ['key', 'value', 'label', 'group'];

    public static function get(string $key, string $default = ''): string
    {
        return Cache::remember("platform_setting_{$key}", 3600, function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    public static function set(string $key, string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("platform_setting_{$key}");
    }

    // Currency helpers
    public static function currencySymbol(): string
    {
        $symbols = [
            'gbp' => '£',
            'usd' => '$',
            'eur' => '€',
            'cad' => 'CA$',
            'aud' => 'A$',
        ];

        $currency = \App\Models\Setting::get('default_currency', 'gbp');
        return $symbols[$currency] ?? '£';
    }

    public static function currencyCode(): string
    {
        return \App\Models\Setting::get('default_currency', 'gbp');
    }
}
