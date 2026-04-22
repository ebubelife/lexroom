<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'credits_per_cycle',
        'price_monthly', 'price_quarterly', 'price_yearly',
        'stripe_monthly_price_id', 'stripe_quarterly_price_id', 'stripe_yearly_price_id',
        'features', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'features'          => 'array',
        'is_active'         => 'boolean',
        'credits_per_cycle' => 'decimal:2',
        'price_monthly'     => 'decimal:2',
        'price_quarterly'   => 'decimal:2',
        'price_yearly'      => 'decimal:2',
    ];

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class, 'plan_id');
    }

    public function priceFor(string $cycle): float
    {
        return match ($cycle) {
            'quarterly' => (float) $this->price_quarterly,
            'yearly'    => (float) $this->price_yearly,
            default     => (float) $this->price_monthly,
        };
    }

    public function stripePriceIdFor(string $cycle): ?string
    {
        return match ($cycle) {
            'quarterly' => $this->stripe_quarterly_price_id,
            'yearly'    => $this->stripe_yearly_price_id,
            default     => $this->stripe_monthly_price_id,
        };
    }

    public function savingsPercent(string $cycle): int
    {
        if ($cycle === 'quarterly') {
            $monthly = $this->price_monthly * 3;
            return $monthly > 0 ? (int) round((1 - $this->price_quarterly / $monthly) * 100) : 0;
        }
        if ($cycle === 'yearly') {
            $monthly = $this->price_monthly * 12;
            return $monthly > 0 ? (int) round((1 - $this->price_yearly / $monthly) * 100) : 0;
        }
        return 0;
    }
}
