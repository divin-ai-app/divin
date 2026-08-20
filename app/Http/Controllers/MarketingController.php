<?php

namespace App\Http\Controllers;

use App\Models\BusinessProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

/**
 * Every action here sits inside the {locale} route-group prefix (see
 * routes/web.php). Laravel binds route → controller scalar parameters
 * POSITIONALLY when a method doesn't declare every URI segment by name — so
 * every method below takes `string $locale` first even when unused, to stay
 * aligned with the URI's actual parameter order. Dropping it silently shifts
 * whatever the *next* declared scalar parameter is onto "en" instead of its
 * real value (see git history for the industriesShow bug this caused).
 */
class MarketingController extends Controller
{
    public function home(string $locale): View
    {
        return view('marketing.home');
    }

    public function howItWorks(string $locale): View
    {
        return view('marketing.how-it-works');
    }

    public function visibilityCheck(string $locale, Request $request): View
    {
        $result = null;

        if ($request->filled('q')) {
            $query = $request->string('q')->toString();
            $matches = $this->searchProfiles($query);

            $result = [
                'query' => $query,
                'matches' => $matches,
            ];
        }

        return view('marketing.visibility-check', compact('result'));
    }

    public function industriesIndex(string $locale): View
    {
        return view('marketing.industries.index', [
            'industries' => config('industries'),
        ]);
    }

    public function industriesShow(string $locale, string $industry): View
    {
        return view('marketing.industries.show', [
            'slug' => $industry,
            'industry' => config("industries.$industry"),
        ]);
    }

    public function pricing(string $locale): View
    {
        return view('marketing.pricing', [
            'faqs' => [
                [
                    'q' => 'Why annual billing only?',
                    'a' => 'Both plans are priced low enough that monthly card-processing fees would eat a disproportionate share of the transaction. Annual billing keeps pricing sustainable at $1.99–$4.99/mo equivalent.',
                ],
                [
                    'q' => 'What does "AI crawler activity" actually show?',
                    'a' => 'A log of visits from AI-engine bots (GPTBot, ClaudeBot, PerplexityBot, and others) to your profile. It\'s a consideration signal — evidence AI engines are reading your data — not proof any specific AI answer cited your business.',
                ],
                [
                    'q' => 'Can I upgrade from Registered to Managed later?',
                    'a' => 'Yes — upgrades take effect immediately and the price difference is prorated against your current annual term.',
                ],
                [
                    'q' => 'What happens if I don\'t claim my business?',
                    'a' => 'Your auto-generated profile (where available) stays published using public data only. Claiming lets you verify, correct, and enrich it, and unlocks the dashboard and monitoring features.',
                ],
            ],
        ]);
    }

    public function about(string $locale): View
    {
        return view('marketing.about');
    }

    public function contact(string $locale): View
    {
        return view('marketing.contact');
    }

    public function submitContact(string $locale, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        Mail::raw(
            "From: {$data['name']} <{$data['email']}>\n\n{$data['message']}",
            function ($mail) use ($data) {
                $mail->to(config('mail.contact_inbox'))
                    ->replyTo($data['email'], $data['name'])
                    ->subject('New contact form submission — divin.ai');
            },
        );

        return back()->with('status', 'Thanks — we\'ll get back to you shortly.');
    }

    public function claim(string $locale, Request $request): View
    {
        $result = null;

        if ($request->filled('q')) {
            $query = $request->string('q')->toString();
            $result = [
                'query' => $query,
                'matches' => $this->searchProfiles($query),
            ];
        }

        return view('marketing.claim', compact('result'));
    }

    /** @return Collection<int, BusinessProfile> */
    private function searchProfiles(string $query): Collection
    {
        return BusinessProfile::published()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('city', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get();
    }

    /**
     * Bare `/` has no locale segment — redirect to the configured default
     * locale rather than guessing from Accept-Language (kept simple until a
     * second locale actually ships).
     */
    public function redirectToDefaultLocale(): RedirectResponse
    {
        return redirect()->to('/'.config('locales.default'), Response::HTTP_MOVED_PERMANENTLY);
    }
}
