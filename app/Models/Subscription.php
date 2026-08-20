<?php

namespace App\Models;

use App\Enums\BillingCycle;
use App\Enums\PlanTier;
use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'profile_id', 'tier', 'billing_cycle', 'status',
    'stripe_customer_id', 'stripe_subscription_id',
    'current_period_start', 'current_period_end', 'renewal_date', 'canceled_at',
])]
class Subscription extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'tier' => PlanTier::class,
            'billing_cycle' => BillingCycle::class,
            'status' => SubscriptionStatus::class,
            'current_period_start' => 'datetime',
            'current_period_end' => 'datetime',
            'renewal_date' => 'datetime',
            'canceled_at' => 'datetime',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class, 'profile_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
