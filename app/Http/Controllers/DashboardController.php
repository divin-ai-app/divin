<?php

namespace App\Http\Controllers;

use App\Enums\BotName;
use App\Enums\PlanTier;
use App\Enums\ResolutionAction;
use App\Models\BusinessProfile;
use App\Models\CrawlerVisitDailyAgg;
use App\Models\FreshnessCheckLog;
use App\Models\ProfileImage;
use App\Models\ProfileService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * The owner-facing dashboard (Phase 4 — see plan §7). Every {profile}-scoped
 * action is additionally gated by BusinessProfilePolicy@manage via the
 * `can:manage,profile` route middleware (routes/web.php), not just `auth` —
 * login alone only proves who you are, not that you own this business.
 *
 * Every method takes `$locale` first for the same positional-route-binding
 * reason documented on MarketingController.
 */
class DashboardController extends Controller
{
    private const DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    public function index(string $locale): View|RedirectResponse
    {
        $profiles = Auth::user()->ownerships()->with('profile')->get()->pluck('profile');

        // Skip the picker entirely when there's only one business to manage.
        if ($profiles->count() === 1) {
            return redirect()->route('marketing.dashboard.overview', ['locale' => $locale, 'profile' => $profiles->first()->slug]);
        }

        return view('dashboard.index', compact('profiles'));
    }

    public function overview(string $locale, BusinessProfile $profile): View
    {
        $profile->load(['services', 'images', 'subscription']);

        return view('dashboard.overview', compact('profile'));
    }

    public function edit(string $locale, BusinessProfile $profile): View
    {
        $profile->load(['services', 'images']);

        return view('dashboard.edit', ['profile' => $profile, 'days' => self::DAYS]);
    }

    public function update(string $locale, BusinessProfile $profile, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'category' => ['required', 'string', 'max:190'],
            'description_short' => ['required', 'string', 'max:500'],
            'description_long' => ['nullable', 'string', 'max:5000'],
            'phone' => ['nullable', 'string', 'max:50'],
            'public_email' => ['nullable', 'email', 'max:190'],
            'website' => ['nullable', 'url', 'max:255'],
            'price_range' => ['nullable', 'string', 'max:10'],
            'hours' => ['nullable', 'array'],
        ]);

        $data['hours'] = $this->normalizeHours($request);

        $profile->update($data);

        return back()->with('status', 'Profile updated.');
    }

    public function storeService(string $locale, BusinessProfile $profile, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['nullable', 'string', 'max:50'],
        ]);

        $profile->services()->create([
            ...$data,
            'sort_order' => $profile->services()->count(),
        ]);

        return back()->with('status', 'Service added.');
    }

    public function destroyService(string $locale, BusinessProfile $profile, ProfileService $service): RedirectResponse
    {
        abort_unless($service->profile_id === $profile->id, 404);

        $service->delete();

        return back()->with('status', 'Service removed.');
    }

    public function storeImage(string $locale, BusinessProfile $profile, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'image' => ['required', 'image', 'max:4096'],
            'alt_text' => ['nullable', 'string', 'max:190'],
        ]);

        $path = $request->file('image')->store("profile-images/{$profile->id}", 'public');

        $profile->images()->create([
            'url' => Storage::disk('public')->url($path),
            'alt_text' => $data['alt_text'] ?? null,
            'sort_order' => $profile->images()->count(),
        ]);

        return back()->with('status', 'Image uploaded.');
    }

    public function destroyImage(string $locale, BusinessProfile $profile, ProfileImage $image): RedirectResponse
    {
        abort_unless($image->profile_id === $profile->id, 404);

        $path = Str::after($image->url, Storage::disk('public')->url(''));
        Storage::disk('public')->delete($path);
        $image->delete();

        return back()->with('status', 'Image removed.');
    }

    public function billing(string $locale, BusinessProfile $profile): View
    {
        $profile->load('subscription.invoices');

        return view('dashboard.billing', compact('profile'));
    }

    public function settings(string $locale, BusinessProfile $profile): View
    {
        return view('dashboard.settings', compact('profile'));
    }

    public function updateSettings(string $locale, BusinessProfile $profile, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
        ]);

        Auth::user()->update($data);

        return back()->with('status', 'Settings updated.');
    }

    /** Managed-tier only — compares the profile against its DataSources; see App\Console\Commands\CheckFreshness. */
    public function freshness(string $locale, BusinessProfile $profile): View
    {
        $unlocked = $profile->plan_tier === PlanTier::Managed;

        $openLogs = collect();
        $resolvedLogs = collect();

        if ($unlocked) {
            $profile->load('dataSources');

            $openLogs = $profile->freshnessLogs()->whereNull('resolved_at')->with('dataSource')->latest('checked_at')->get();
            $resolvedLogs = $profile->freshnessLogs()->whereNotNull('resolved_at')->with('dataSource')->latest('resolved_at')->take(5)->get();
        }

        return view('dashboard.freshness', [
            'profile' => $profile,
            'unlocked' => $unlocked,
            'openLogs' => $openLogs,
            'resolvedLogs' => $resolvedLogs,
        ]);
    }

    /**
     * Each discrepancy inside a log is resolved independently — a single
     * check can flag several fields at once (e.g. phone AND website), and
     * an owner may want to accept one while keeping another. Only once
     * every discrepancy in the log has its own resolution does the log
     * itself get marked resolved (drives the "Needs your review" list).
     */
    public function resolveFreshness(string $locale, BusinessProfile $profile, FreshnessCheckLog $freshnessCheckLog, Request $request): RedirectResponse
    {
        abort_unless($freshnessCheckLog->profile_id === $profile->id, 404);
        abort_if($freshnessCheckLog->resolved_at !== null, 404);

        $data = $request->validate([
            'field' => ['required', 'string'],
            'action' => ['required', Rule::enum(ResolutionAction::class)],
        ]);
        $action = ResolutionAction::from($data['action']);

        $discrepancies = $freshnessCheckLog->discrepancies;
        $matched = false;

        foreach ($discrepancies as &$discrepancy) {
            if ($discrepancy['field'] !== $data['field'] || ! empty($discrepancy['resolution'] ?? null)) {
                continue;
            }

            $matched = true;
            $discrepancy['resolution'] = $action->value;

            if ($action === ResolutionAction::AcceptedNewValue) {
                $profile->update([$discrepancy['field'] => $discrepancy['source_value']]);
            }

            break;
        }
        unset($discrepancy);

        abort_unless($matched, 404);

        $freshnessCheckLog->discrepancies = $discrepancies;

        $resolutions = collect($discrepancies)->pluck('resolution');
        if ($resolutions->every(fn ($r) => ! empty($r))) {
            $freshnessCheckLog->resolved_at = now();
            $freshnessCheckLog->resolution_action = $resolutions->unique()->count() === 1 ? $resolutions->first() : null;
        }

        $freshnessCheckLog->save();

        return back()->with('status', $action === ResolutionAction::AcceptedNewValue
            ? 'Updated — the new value is now live on your profile.'
            : 'Kept your current value — no changes made.');
    }

    /** Available on both paid tiers once verified — chart built from CrawlerVisitDailyAgg. */
    public function crawlerActivity(string $locale, BusinessProfile $profile): View
    {
        $unlocked = $profile->plan_tier !== PlanTier::None;

        $dailyByBot = collect();
        $totalsByBot = collect();
        $days = collect();

        if ($unlocked) {
            $since = now()->subDays(13)->startOfDay();
            $days = collect(range(0, 13))->map(fn ($i) => now()->subDays(13 - $i)->toDateString());

            $aggs = $profile->crawlerVisitsDaily()->where('date', '>=', $since)->get();

            // Group by an explicit toDateString(), not a bare ->where('date',
            // $string) collection filter: $agg->date is a Carbon instance
            // (the 'date' cast), and comparing it against a plain string
            // never matches, so every bucket would silently read zero.
            $byDate = $aggs->groupBy(fn ($agg) => $agg->date->toDateString());

            $dailyByBot = $days->mapWithKeys(fn ($date) => [
                $date => ($byDate->get($date) ?? collect())->sum('visit_count'),
            ]);

            $totalsByBot = $aggs->groupBy(fn ($agg) => $agg->bot_name->value)
                ->map(fn ($group) => $group->sum('visit_count'))
                ->sortDesc();
        }

        return view('dashboard.crawler-activity', [
            'profile' => $profile,
            'unlocked' => $unlocked,
            'days' => $days,
            'dailyByBot' => $dailyByBot,
            'totalsByBot' => $totalsByBot,
        ]);
    }

    /**
     * Demo/testing helper, clearly labeled as such in the UI — a brand-new
     * site has no real bot traffic yet to show off the chart with. Real
     * visits are logged for real by App\Http\Middleware\LogCrawlerVisit.
     */
    public function simulateCrawlerVisit(string $locale, BusinessProfile $profile): RedirectResponse
    {
        $bot = collect(BotName::cases())->random();

        CrawlerVisitDailyAgg::incrementFor($profile->id, now()->toDateString(), $bot);

        return back()->with('status', "Simulated a visit from {$bot->value}.");
    }

    private function normalizeHours(Request $request): array
    {
        $hours = [];

        foreach (self::DAYS as $day) {
            $closed = $request->boolean("hours.{$day}.closed");
            $open = $request->input("hours.{$day}.open");
            $close = $request->input("hours.{$day}.close");

            $hours[$day] = ($closed || ! $open || ! $close)
                ? 'closed'
                : ['open' => $open, 'close' => $close];
        }

        return $hours;
    }
}
