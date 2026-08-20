<?php

namespace App\Models;

use App\Enums\CoherenceStatus;
use App\Enums\SourceType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['profile_id', 'source_type', 'source_url', 'contact_email', 'contact_phone', 'last_checked_at', 'current_snapshot', 'coherence_status'])]
class DataSource extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'source_type' => SourceType::class,
            'coherence_status' => CoherenceStatus::class,
            'current_snapshot' => 'array',
            'last_checked_at' => 'datetime',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class, 'profile_id');
    }

    public function freshnessLogs(): HasMany
    {
        return $this->hasMany(FreshnessCheckLog::class);
    }
}
