<?php

namespace App\Models;

use App\Enums\BotName;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['profile_id', 'bot_name', 'path', 'user_agent', 'ip_hash', 'timestamp'])]
class CrawlerVisitLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'bot_name' => BotName::class,
            'timestamp' => 'datetime',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class, 'profile_id');
    }
}
