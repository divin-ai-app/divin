<?php

namespace App\Models;

use App\Enums\BotName;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['profile_id', 'date', 'bot_name', 'visit_count'])]
class CrawlerVisitDailyAgg extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'bot_name' => BotName::class,
            'date' => 'date',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class, 'profile_id');
    }
}
