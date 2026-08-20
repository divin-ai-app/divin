<?php

namespace App\Models;

use App\Enums\ClaimStatus;
use App\Enums\Industry;
use App\Enums\PlanTier;
use App\Enums\ProfileStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'canonical_id', 'slug', 'name', 'legal_name', 'industry', 'category',
    'country_code', 'city', 'address_line1', 'address_line2', 'postal_code',
    'lat', 'lng', 'phone', 'public_email', 'website',
    'description_short', 'description_long', 'hours', 'price_range',
    'status', 'claim_status', 'plan_tier', 'last_verified_at', 'published_at',
])]
class BusinessProfile extends Model
{
    use HasFactory;

    /** Route model binding uses the public slug, not the numeric id — see routes/web.php. */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'industry' => Industry::class,
            'status' => ProfileStatus::class,
            'claim_status' => ClaimStatus::class,
            'plan_tier' => PlanTier::class,
            'hours' => 'array',
            'lat' => 'float',
            'lng' => 'float',
            'last_verified_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function countryClearance(): BelongsTo
    {
        return $this->belongsTo(CountryClearance::class, 'country_code', 'country_code');
    }

    public function services(): HasMany
    {
        return $this->hasMany(ProfileService::class, 'profile_id')->orderBy('sort_order');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProfileImage::class, 'profile_id')->orderBy('sort_order');
    }

    public function dataSources(): HasMany
    {
        return $this->hasMany(DataSource::class, 'profile_id');
    }

    public function owners(): HasMany
    {
        return $this->hasMany(ProfileOwnership::class, 'profile_id');
    }

    public function claimRequests(): HasMany
    {
        return $this->hasMany(ClaimRequest::class, 'profile_id');
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'profile_id');
    }

    public function freshnessLogs(): HasMany
    {
        return $this->hasMany(FreshnessCheckLog::class, 'profile_id');
    }

    public function crawlerVisitsRaw(): HasMany
    {
        return $this->hasMany(CrawlerVisitLog::class, 'profile_id');
    }

    public function crawlerVisitsDaily(): HasMany
    {
        return $this->hasMany(CrawlerVisitDailyAgg::class, 'profile_id');
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(Dispute::class, 'profile_id');
    }

    /** Only PUBLISHED profiles should ever be rendered/linked publicly. */
    public function scopePublished($query)
    {
        return $query->where('status', ProfileStatus::Published);
    }
}
