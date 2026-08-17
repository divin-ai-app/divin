@extends('layouts.marketing')

@section('title', 'About — divin.ai')
@section('description', 'Why divin.ai exists, and why it starts in Mauritius.')

@section('content')
    <section class="bg-ink-950 py-section-y">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-white sm:text-5xl">About divin.ai</h1>
        </div>
    </section>

    <section class="bg-white py-section-y">
        <div class="mx-auto max-w-3xl space-y-8 px-4 text-ink-600 sm:px-6 lg:px-8">
            <p>
                Search is changing faster than most small businesses can keep up with. A growing share
                of people now ask ChatGPT, Claude, Gemini, or Perplexity a question instead of typing
                it into Google — and those AI engines answer from whatever they can actually crawl.
            </p>
            <p>
                For a huge number of small businesses, especially outside a handful of large markets,
                that means nothing. Their only public presence is a Facebook Page, which blocks most AI
                crawlers outright, or an OTA listing that shows a thin, generic summary. They're
                effectively invisible to the way people are starting to search.
            </p>
            <p>
                divin.ai exists to fix that: an open, AI-engine-agnostic registry that builds a
                structured, verified profile for a business, publishes it in a form every major AI
                crawler can read, and lets the owner claim and keep it accurate.
            </p>
            <h2 class="pt-4 text-2xl font-bold text-ink-900">Why Mauritius first</h2>
            <p>
                We're launching in Mauritius — hotels, restaurants, clinics, real estate agencies — the
                exact profile of business currently reachable only through Facebook or an OTA listing.
                From there: the wider Indian Ocean, then Africa, then Asia and beyond.
            </p>
        </div>
    </section>
@endsection
