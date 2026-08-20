<?php

namespace App\Http\Controllers;

use App\Enums\PlanTier;
use App\Models\BusinessProfile;
use App\Models\ProfileImage;
use App\Models\ProfileService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    /** Managed-tier only — real content lands in Phase 6; this enforces the gate now. */
    public function freshness(string $locale, BusinessProfile $profile): View
    {
        return view('dashboard.freshness', [
            'profile' => $profile,
            'unlocked' => $profile->plan_tier === PlanTier::Managed,
        ]);
    }

    /** Available on both paid tiers — real data lands in Phase 6; placeholder for now. */
    public function crawlerActivity(string $locale, BusinessProfile $profile): View
    {
        return view('dashboard.crawler-activity', [
            'profile' => $profile,
            'unlocked' => $profile->plan_tier !== PlanTier::None,
        ]);
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
