<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SystemController extends Controller
{
    /**
     * Explicitly allow every major AI-engine crawler — the whole product's
     * pitch is openness to them, so there is no walled garden here (see plan
     * §2, "the divin.ai marketing site itself must practice what it preaches").
     */
    public function robots(): Response
    {
        $bots = [
            'GPTBot',
            'OAI-SearchBot',
            'ChatGPT-User',
            'ClaudeBot',
            'Claude-User',
            'Claude-SearchBot',
            'PerplexityBot',
            'Perplexity-User',
            'Google-Extended',
            'Googlebot',
            'Bingbot',
            'Applebot',
            'Applebot-Extended',
            'CCBot',
            'anthropic-ai',
            'cohere-ai',
        ];

        $lines = ['# divin.ai — open to every AI-engine crawler by design.', ''];

        foreach ($bots as $bot) {
            $lines[] = "User-agent: {$bot}";
            $lines[] = 'Allow: /';
            $lines[] = '';
        }

        $lines[] = 'User-agent: *';
        $lines[] = 'Allow: /';
        $lines[] = '';
        $lines[] = 'Sitemap: '.url('/sitemap.xml');

        return response(implode("\n", $lines), 200)
            ->header('Content-Type', 'text/plain');
    }

    public function sitemap(): Response
    {
        $locales = array_keys(config('locales.available'));

        $staticPaths = [
            '',
            'how-it-works',
            'visibility-check',
            'industries',
            'pricing',
            'about',
            'contact',
            'claim',
        ];

        $industryPaths = array_map(
            fn (string $slug) => "industries/{$slug}",
            array_keys(config('industries')),
        );

        $urls = [];

        foreach ($locales as $locale) {
            foreach ([...$staticPaths, ...$industryPaths] as $path) {
                $urls[] = rtrim(url("/{$locale}/{$path}"), '/');
            }
        }

        // Phase 2 appends published BusinessProfile /p/{slug} URLs here once
        // that table exists — see plan §2 Phase 2.
        $xml = view('sitemap', ['urls' => array_unique($urls)])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function llmsTxt(): Response
    {
        return response(view('llms-txt')->render(), 200)
            ->header('Content-Type', 'text/plain');
    }
}
