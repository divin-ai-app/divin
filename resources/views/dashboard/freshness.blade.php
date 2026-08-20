@extends('layouts.dashboard')

@section('title', 'Freshness report — '.$profile->name.' — divin.ai')

@section('content')
    <div class="mx-auto max-w-3xl">
        <h1 class="text-2xl font-bold text-ink-900">Freshness &amp; coherence report</h1>

        @if (! $unlocked)
            <div class="mt-6 rounded-xl border-2 border-accent bg-white p-8 text-center">
                <p class="text-lg font-semibold text-ink-900">Managed plan required</p>
                <p class="mt-2 text-sm text-ink-600">
                    Freshness &amp; coherence monitoring — checking your profile against your own
                    website, Facebook Page, and OTA listings, with email alerts when they drift —
                    is included on the Managed plan.
                </p>
                <a href="{{ lroute('marketing.claim.plan', ['profile' => $profile->slug]) }}"
                   class="mt-5 inline-block rounded-lg bg-accent px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-accent-600">
                    Upgrade to Managed
                </a>
            </div>
        @else
            <div class="mt-6 rounded-xl border border-ink-200 bg-white p-8 text-center">
                <p class="text-ink-600">
                    Your Managed plan is active. Freshness checks run periodically — full reporting
                    is coming in a future update.
                </p>
            </div>
        @endif
    </div>
@endsection
