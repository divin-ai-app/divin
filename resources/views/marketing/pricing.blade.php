@extends('layouts.marketing')

@section('title', 'Pricing — divin.ai')
@section('description', 'Registered vs Managed — simple annual pricing for a verified, monitored AI-crawlable business profile.')

@push('schema')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => collect($faqs)->map(fn ($faq) => [
                '@type' => 'Question',
                'name' => $faq['q'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a']],
            ])->all(),
        ], JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush

@section('content')
    <section class="bg-ink-950 py-section-y">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-white sm:text-5xl">Simple, annual pricing</h1>
            <p class="mt-4 text-lg text-ink-300">Billed annually only — no card-fee drag on small transactions.</p>
        </div>
    </section>

    <section class="bg-white py-section-y">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-2">
                <div class="rounded-2xl border border-ink-200 p-10">
                    <h2 class="text-xl font-bold text-ink-900">Registered</h2>
                    <p class="mt-2 text-4xl font-bold text-ink-900">
                        US$1.99<span class="text-base font-normal text-ink-500">/mo equivalent</span>
                    </p>
                    <p class="mt-1 text-sm text-ink-500">Billed annually — US$23.88/year</p>
                    <ul class="mt-8 space-y-3 text-sm text-ink-600">
                        @foreach (['Claim &amp; verify your business profile', 'Full editor: description, hours, services, images', 'Published on your public divin.ai page', 'AI crawler visit activity dashboard'] as $feature)
                            <li class="flex gap-2"><span class="text-success">&check;</span> {!! $feature !!}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="rounded-2xl border-2 border-accent p-10">
                    <span class="rounded-full bg-accent/10 px-3 py-1 text-xs font-semibold text-accent">Most complete</span>
                    <h2 class="mt-3 text-xl font-bold text-ink-900">Managed</h2>
                    <p class="mt-2 text-4xl font-bold text-ink-900">
                        US$4.99<span class="text-base font-normal text-ink-500">/mo equivalent</span>
                    </p>
                    <p class="mt-1 text-sm text-ink-500">Billed annually — US$59.88/year</p>
                    <ul class="mt-8 space-y-3 text-sm text-ink-600">
                        @foreach (['Everything in Registered', 'Ongoing freshness checks against your other listings', 'Cross-source coherence checks (website, Facebook, OTA)', 'Email alerts the moment sources drift out of agreement'] as $feature)
                            <li class="flex gap-2"><span class="text-success">&check;</span> {{ $feature }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="mt-12 text-center">
                <a href="{{ lroute('marketing.claim') }}"
                   class="inline-block rounded-lg bg-accent px-8 py-3.5 font-semibold text-white transition hover:bg-accent-600">
                    Find your business to get started
                </a>
            </div>
        </div>
    </section>

    <section class="bg-ink-50 py-section-y">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-center text-2xl font-bold text-ink-900">Frequently asked questions</h2>
            <div class="mt-10 space-y-6">
                @foreach ($faqs as $faq)
                    <div class="rounded-xl bg-white p-6 shadow-md">
                        <h3 class="font-semibold text-ink-900">{{ $faq['q'] }}</h3>
                        <p class="mt-2 text-sm text-ink-600">{{ $faq['a'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
