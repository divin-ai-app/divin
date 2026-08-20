<?php

namespace App\Models;

use App\Enums\LegalStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['country_code', 'country_name', 'legal_status', 'gdpr_excluded', 'notes', 'cleared_at', 'reviewed_by_staff_id'])]
class CountryClearance extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'legal_status' => LegalStatus::class,
            'gdpr_excluded' => 'boolean',
            'cleared_at' => 'datetime',
        ];
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_staff_id');
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(BusinessProfile::class, 'country_code', 'country_code');
    }
}
