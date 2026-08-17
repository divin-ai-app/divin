@extends('layouts.marketing')

@section('title', 'divin.ai — Be discoverable when AI answers the question')
@section('description', 'Search is moving from Google to AI. divin.ai auto-generates, verifies, and monitors structured business profiles so ChatGPT, Claude, Gemini and Perplexity can actually find and cite your business.')

@section('content')
    {{-- Hero --}}
    <section class="bg-ink-950">
        <div class="mx-auto max-w-5xl px-4 py-section-y-lg text-center sm:px-6 lg:px-8">
            <h1 class="text-5xl font-bold tracking-tight text-white sm:text-6xl lg:text-7xl">
                Be discoverable when
                <span class="text-accent">AI answers the question.</span>
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-lg text-ink-300">
                Search is shifting from Google to ChatGPT, Claude, Gemini and Perplexity. Most small
                businesses are invisible to it — no crawlable website, just a Facebook Page most AI
                bots can't read. divin.ai fixes that.
            </p>
            <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                <a href="{{ lroute('marketing.claim') }}"
                   class="rounded-lg bg-accent px-8 py-4 text-base font-semibold text-white shadow-lg transition hover:bg-accent-600">
                    Find your business
                </a>
                <a href="{{ lroute('marketing.visibility-check') }}"
                   class="rounded-lg border border-ink-700 px-8 py-4 text-base font-semibold text-white transition hover:border-ink-500">
                    Run a free AI visibility check
                </a>
            </div>
        </div>
    </section>

    {{-- Problem framing --}}
    <section class="bg-white py-section-y">
        <div class="mx-auto max-w-5xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-ink-900 sm:text-4xl">
                Your only public presence is a Facebook Page. AI can't read it.
            </h2>
            <p class="mx-auto mt-4 max-w-2xl text-ink-600">
                Most AI crawlers — GPTBot, ClaudeBot, PerplexityBot — can't index Facebook Pages, and
                OTA listings only show a thin, generic summary. If an AI engine can't crawl a clean
                source about your business, it can't cite you when someone asks.
            </p>
        </div>
    </section>

    {{-- How it works (3-step) --}}
    <section class="bg-ink-50 py-section-y">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-center text-3xl font-bold text-ink-900 sm:text-4xl">How divin.ai works</h2>
            <div class="mt-12 grid gap-8 sm:grid-cols-3">
                @foreach ([
                    ['step' => '01', 'title' => 'Auto-generate', 'body' => 'We build a structured, verified profile from public business-register, directory, and listing data — name, category, address, hours, description. No personal or sensitive data, ever.'],
                    ['step' => '02', 'title' => 'Claim', 'body' => 'Find your business, verify you own it, and correct or enrich the details — services, pricing, images, and more.'],
                    ['step' => '03', 'title' => 'Monitor', 'body' => 'On the Managed plan, we continuously check your profile against your own website, Facebook Page, and OTA listings — and alert you the moment they drift apart.'],
                ] as $item)
                    <div class="rounded-2xl bg-white p-8 shadow-md">
                        <span class="text-sm font-bold text-accent">{{ $item['step'] }}</span>
                        <h3 class="mt-2 text-xl font-semibold text-ink-900">{{ $item['title'] }}</h3>
                        <p class="mt-3 text-sm text-ink-600">{{ $item['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Pricing teaser --}}
    <section class="bg-white py-section-y">
        <div class="mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-ink-900 sm:text-4xl">Simple, annual pricing</h2>
            <p class="mt-4 text-ink-600">Two tiers. Billed annually only — no card-fee drag on small transactions.</p>
            <div class="mt-10 grid gap-6 sm:grid-cols-2">
                <div class="rounded-2xl border border-ink-200 p-8 text-left">
                    <h3 class="text-lg font-semibold text-ink-900">Registered</h3>
                    <p class="mt-1 text-3xl font-bold text-ink-900">US$1.99<span class="text-base font-normal text-ink-500">/mo equiv.</span></p>
                    <p class="mt-3 text-sm text-ink-600">Claim, verify, and enrich your profile.</p>
                </div>
                <div class="rounded-2xl border-2 border-accent p-8 text-left">
                    <h3 class="text-lg font-semibold text-ink-900">Managed</h3>
                    <p class="mt-1 text-3xl font-bold text-ink-900">US$4.99<span class="text-base font-normal text-ink-500">/mo equiv.</span></p>
                    <p class="mt-3 text-sm text-ink-600">Everything in Registered, plus ongoing freshness &amp; coherence monitoring with email alerts.</p>
                </div>
            </div>
            <a href="{{ lroute('marketing.pricing') }}" class="mt-8 inline-block text-sm font-semibold text-accent hover:text-accent-600">
                See full pricing comparison &rarr;
            </a>
        </div>
    </section>

    {{-- Industries teaser --}}
    <section class="bg-ink-50 py-section-y">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-center text-3xl font-bold text-ink-900 sm:text-4xl">Built for the businesses AI engines overlook</h2>
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-5">
                @foreach (config('industries') as $slug => $industry)
                    <a href="{{ lroute('marketing.industries.show', ['industry' => $slug]) }}"
                       class="rounded-xl bg-white p-6 text-center shadow-md transition hover:shadow-lg">
                        <span class="text-sm font-semibold text-ink-900">{{ $industry['name'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
