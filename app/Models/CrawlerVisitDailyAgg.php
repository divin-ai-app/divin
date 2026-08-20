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

    /**
     * The only safe way to upsert a (profile, date, bot) row. Eloquent's
     * `date` cast serializes to a full "Y-m-d H:i:s" string on save (see
     * HasAttributes::fromDateTime()/getDateFormat() — this isn't SQLite-
     * specific, every driver's default grammar date format is a full
     * datetime), so a plain 'YYYY-MM-DD' string in a firstOrNew()/
     * updateOrCreate() search array never matches an already-stored row —
     * it always misses and tries to INSERT a duplicate, hitting the unique
     * (profile_id, date, bot_name) constraint. whereDate() compares
     * correctly regardless of how the column actually got stored.
     */
    public static function incrementFor(int $profileId, string $date, BotName $bot, int $by = 1): self
    {
        $agg = self::query()
            ->where('profile_id', $profileId)
            ->where('bot_name', $bot)
            ->whereDate('date', $date)
            ->first() ?? new self(['profile_id' => $profileId, 'date' => $date, 'bot_name' => $bot, 'visit_count' => 0]);

        $agg->visit_count = ($agg->visit_count ?? 0) + $by;
        $agg->save();

        return $agg;
    }
}
