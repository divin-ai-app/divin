@extends('layouts.marketing')

@section('title', 'How it works — divin.ai')
@section('description', 'How divin.ai auto-generates, claims, and monitors business profiles — and what an AI crawler visit does and does not mean.')

@section('content')
    <section class="bg-ink-950 py-section-y-sm">
        <div class="mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-white sm:text-5xl">How it works</h1>
            <p class="mt-4 text-lg text-ink-300">From an unclaimed public listing to a monitored, verified profile.</p>
        </div>
    </section>

    <section class="bg-white py-section-y">
        <div class="mx-auto max-w-3xl space-y-16 px-4 sm:px-6 lg:px-8">
            <div>
                <span class="text-sm font-bold text-accent">01 — Auto-generate</span>
                <h2 class="mt-2 text-2xl font-bold text-ink-900">A structured profile, built from public data</h2>
                <p class="mt-4 text-ink-600">
                    We assemble a profile from public, non-sensitive sources — business registers,
                    Google-Business-Profile-adjacent data, and OTA listings: name, category, address,
                    hours, and a short description. We never collect or publish personal or sensitive
                    data. The profile is published as a clean, server-rendered page marked up with
                    schema.org — openly crawlable by every major AI engine's bot.
                </p>
                <p class="mt-4 text-sm text-ink-500">
                    Auto-generation only happens in countries that have cleared legal review — see our
                    country clearance status, which currently excludes GDPR-zone countries (EU, UK,
                    Réunion, Mayotte) until that review is complete.
                </p>
            </div>

            <div>
                <span class="text-sm font-bold text-accent">02 — Claim</span>
                <h2 class="mt-2 text-2xl font-bold text-ink-900">Verify you own it, then enrich it</h2>
                <p class="mt-4 text-ink-600">
                    Find your business and tell us it's yours. We verify ownership — typically by
                    sending a one-time code to the business's on-file contact, or via a short manual
                    review for edge cases — before any change goes live. Once verified, you can correct
                    and enrich every field: description, hours, services, pricing, and images.
                </p>
            </div>

            <div>
                <span class="text-sm font-bold text-accent">03 — Monitor</span>
                <h2 class="mt-2 text-2xl font-bold text-ink-900">Stay accurate as your other listings change</h2>
                <p class="mt-4 text-ink-600">
                    On the Managed plan, we periodically re-check your own website, Facebook Page, and
                    OTA listings against your verified profile. When they drift out of agreement, you
                    get an email alert with exactly what changed — so you can confirm or correct it in
                    one place instead of finding out from a confused customer.
                </p>
            </div>

            <div class="rounded-2xl border border-ink-200 bg-ink-50 p-8">
                <h2 class="text-xl font-bold text-ink-900">What an "AI crawler visit" does — and doesn't — mean</h2>
                <p class="mt-4 text-ink-600">
                    Paying customers see a log of AI-engine bot visits to their profile (GPTBot,
                    ClaudeBot, PerplexityBot, and others). This is a <strong>consideration signal</strong> —
                    evidence that AI engines are actively reading your structured data — not proof that
                    any specific AI answer cited or recommended your business. We label it that way
                    everywhere it appears, deliberately, because the distinction matters.
                </p>
            </div>
        </div>
    </section>
@endsection
