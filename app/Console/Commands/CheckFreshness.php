<?php

namespace App\Console\Commands;

use App\Enums\CoherenceStatus;
use App\Enums\PlanTier;
use App\Mail\FreshnessAlertMail;
use App\Models\BusinessProfile;
use App\Support\FreshnessChecker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Managed-tier freshness/coherence check (plan §4 flow 3). Meant to run on
 * a schedule — see routes/console.php — triggered in production by a
 * cPanel Cron Job (no long-running daemon available on this hosting plan,
 * see the divin.ai HostGator deploy notes). Compares each DataSource's
 * current_snapshot against the live profile via FreshnessChecker; a new
 * FreshnessCheckLog + owner alert email is only created once per drift
 * (an existing unresolved log for the same DataSource is left alone, so
 * re-running this daily doesn't re-alert on the same unresolved issue).
 */
class CheckFreshness extends Command
{
    protected $signature = 'freshness:check';

    protected $description = 'Compare Managed profiles against their data sources and alert owners of drift';

    public function handle(): int
    {
        $profiles = BusinessProfile::query()
            ->where('plan_tier', PlanTier::Managed)
            ->with(['dataSources', 'owners.user'])
            ->get();

        $checked = 0;
        $alerted = 0;

        foreach ($profiles as $profile) {
            foreach ($profile->dataSources as $dataSource) {
                $checked++;
                $discrepancies = FreshnessChecker::compare($profile, $dataSource);

                if ($discrepancies === []) {
                    $dataSource->update(['last_checked_at' => now(), 'coherence_status' => CoherenceStatus::Aligned]);

                    continue;
                }

                $severity = FreshnessChecker::severityFor($discrepancies);

                $dataSource->update([
                    'last_checked_at' => now(),
                    'coherence_status' => FreshnessChecker::coherenceStatusFor($severity),
                ]);

                $alreadyOpen = $dataSource->freshnessLogs()->whereNull('resolved_at')->exists();

                if ($alreadyOpen) {
                    continue;
                }

                $log = $profile->freshnessLogs()->create([
                    'data_source_id' => $dataSource->id,
                    'checked_at' => now(),
                    'discrepancies' => $discrepancies,
                    'severity' => $severity,
                ]);

                $recipients = $profile->owners->pluck('user.email')->filter()->unique();

                if ($recipients->isNotEmpty()) {
                    $reportUrl = route('marketing.dashboard.freshness', ['locale' => config('locales.default'), 'profile' => $profile]).'#log-'.$log->id;

                    foreach ($recipients as $email) {
                        Mail::to($email)->send(new FreshnessAlertMail($log, $profile->name, $reportUrl));
                    }

                    $log->update(['alert_sent' => true, 'alert_sent_at' => now()]);
                    $alerted++;
                }
            }
        }

        $this->info("Checked {$checked} data source(s), sent {$alerted} new alert(s).");

        return self::SUCCESS;
    }
}
