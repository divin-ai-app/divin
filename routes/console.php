<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Phase 6 — see App\Console\Commands\CheckFreshness / RollupCrawlerVisits
// docblocks. This schedule only fires if something actually invokes
// `php artisan schedule:run` periodically, which this hosting plan has no
// daemon for — production instead points a cPanel Cron Job directly at
// each command (see README's deploy notes). Kept here anyway so `schedule:run`
// does the right thing for anyone who *does* have a scheduler running it.
Schedule::command('freshness:check')->daily();
Schedule::command('crawler:rollup')->daily();
