@php
    $footerColumns = [
        'Industries' => collect(config('industries'))->map(fn ($industry, $slug) => [
            'label' => $industry['name'],
            'route' => 'marketing.industries.show',
            'params' => ['industry' => $slug],
        ])->values()->all(),
        'Product' => [
            ['label' => 'How it works', 'route' => 'marketing.how-it-works'],
            ['label' => 'Free AI Visibility Check', 'route' => 'marketing.visibility-check'],
            ['label' => 'Pricing', 'route' => 'marketing.pricing'],
            ['label' => 'Claim your business', 'route' => 'marketing.claim'],
        ],
        'Resources' => [
            ['label' => 'Industries overview', 'route' => 'marketing.industries.index'],
            ['label' => 'How AI crawling works', 'route' => 'marketing.how-it-works'],
        ],
        'Company' => [
            ['label' => 'About', 'route' => 'marketing.about'],
            ['label' => 'Contact', 'route' => 'marketing.contact'],
        ],
    ];
@endphp

<footer class="border-t border-ink-800 bg-ink-950 text-ink-200">
    {{-- Logo/trust strip placeholder — reserved for future customer logos. --}}
    <div class="border-b border-ink-800/60">
        <div class="mx-auto max-w-7xl px-4 py-8 text-center sm:px-6 lg:px-8">
            <p class="text-xs font-semibold tracking-wide text-ink-400 uppercase">
                Trusted by businesses across Mauritius &amp; the Indian Ocean
            </p>
            <div class="mt-4 flex items-center justify-center gap-8 opacity-40" aria-hidden="true">
                <div class="h-8 w-24 rounded bg-ink-700"></div>
                <div class="h-8 w-24 rounded bg-ink-700"></div>
                <div class="h-8 w-24 rounded bg-ink-700"></div>
                <div class="h-8 w-24 rounded bg-ink-700"></div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 gap-8 lg:grid-cols-5">
            <div class="col-span-2 lg:col-span-1">
                <a href="{{ lroute('marketing.home') }}" class="text-xl font-bold tracking-tight text-white">
                    divin<span class="text-accent">.ai</span>
                </a>
                <p class="mt-3 text-sm text-ink-400">
                    The open, AI-engine-agnostic business registry.
                </p>
            </div>

            @foreach ($footerColumns as $heading => $links)
                <div>
                    <h3 class="text-sm font-semibold text-white">{{ $heading }}</h3>
                    <ul class="mt-4 space-y-3">
                        @foreach ($links as $link)
                            <li>
                                <a href="{{ lroute($link['route'], $link['params'] ?? []) }}"
                                   class="text-sm text-ink-400 transition hover:text-white">
                                    {{ $link['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        <div class="mt-16 flex flex-col items-center justify-between gap-4 border-t border-ink-800 pt-8 sm:flex-row">
            <p class="text-xs text-ink-500">
                &copy; {{ now()->year }} divin.ai. All rights reserved.
            </p>
            <p class="text-xs text-ink-500">
                Auto-generated profiles use public, non-sensitive business data only.
            </p>
        </div>
    </div>
</footer>
