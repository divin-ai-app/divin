<?php

namespace App\Models;

use App\Enums\ClaimRequestStatus;
use App\Enums\VerificationMethod;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'profile_id', 'requested_by_user_id', 'verification_method', 'contact_value',
    'status', 'otp_hash', 'otp_expires_at', 'document_url',
    'reviewed_by_staff_id', 'review_notes',
])]
class ClaimRequest extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'verification_method' => VerificationMethod::class,
            'status' => ClaimRequestStatus::class,
            'otp_expires_at' => 'datetime',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class, 'profile_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_staff_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(ClaimAuditEvent::class);
    }
}
