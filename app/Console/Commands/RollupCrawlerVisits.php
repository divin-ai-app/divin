<?php

namespace App\Console\Commands;

use App\Models\CrawlerVisitDailyAgg;
use App\Models\CrawlerVisitLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Rolls raw crawler_visit_logs (written by App\Http\Middleware\LogCrawlerVisit
 * on every bot hit to a public profile page) into the daily aggregate the
 * dashboard actually queries, then prunes the raw rows — see plan §5's note
 * that the raw table is "high-volume, short retention". Only rolls up rows
 * from before today, so a same-day re-run never double-counts today's
 * still-accumulating visits. Scheduled — see routes/console.php — same
 * cPanel Cron Job story as CheckFreshness.
 */
class RollupCrawlerVisits extends Command
{
    protected $signature = 'crawler:rollup';

    protected $description = 'Roll up raw crawler visit logs into daily aggregates and prune the raw rows';

    public function handle(): int
    {
        $cutoff = now()->startOfDay();

        $groups = CrawlerVisitLog::query()
            ->where('timestamp', '<', $cutoff)
            ->select('profile_id', 'bot_name', DB::raw('DATE(timestamp) as visit_date'), DB::raw('COUNT(*) as visit_count'))
            ->groupBy('profile_id', 'bot_name', 'visit_date')
            ->get();

        foreach ($groups as $group) {
            CrawlerVisitDailyAgg::incrementFor($group->profile_id, $group->visit_date, $group->bot_name, $group->visit_count);
        }

        $pruned = CrawlerVisitLog::query()->where('timestamp', '<', $cutoff)->delete();

        $this->info("Rolled up {$groups->count()} group(s), pruned {$pruned} raw log row(s).");

        return self::SUCCESS;
    }
}
