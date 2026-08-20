<?php

namespace App\Models;

use App\Enums\FreshnessSeverity;
use App\Enums\ResolutionAction;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'profile_id', 'data_source_id', 'checked_at', 'discrepancies', 'severity',
    'alert_sent', 'alert_sent_at', 'resolved_at', 'resolution_action',
])]
class FreshnessCheckLog extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'discrepancies' => 'array',
            'severity' => FreshnessSeverity::class,
            'resolution_action' => ResolutionAction::class,
            'alert_sent' => 'boolean',
            'checked_at' => 'datetime',
            'alert_sent_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class, 'profile_id');
    }

    public function dataSource(): BelongsTo
    {
        return $this->belongsTo(DataSource::class);
    }
}
