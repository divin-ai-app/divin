@php
    $navLinks = [
        ['route' => 'marketing.how-it-works', 'label' => 'How it works'],
        ['route' => 'marketing.visibility-check', 'label' => 'Free AI Visibility Check'],
        ['route' => 'marketing.industries.index', 'label' => 'Industries'],
        ['route' => 'marketing.pricing', 'label' => 'Pricing'],
        ['route' => 'marketing.about', 'label' => 'About'],
    ];
@endphp

<header class="sticky top-0 z-50 border-b border-ink-100 bg-white/95 backdrop-blur">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8" aria-label="Primary">
        <a href="{{ lroute('marketing.home') }}" class="text-xl font-bold tracking-tight text-ink-900">
            divin<span class="text-accent">.ai</span>
        </a>

        {{-- Desktop links --}}
        <div class="hidden items-center gap-8 lg:flex">
            @foreach ($navLinks as $link)
                <a href="{{ lroute($link['route']) }}"
                   class="text-sm font-medium text-ink-600 transition hover:text-ink-900">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>

        <div class="hidden lg:block">
            <a href="{{ lroute('marketing.claim') }}"
               class="rounded-lg bg-accent px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-accent-600">
                Claim your business
            </a>
        </div>

        {{-- Mobile menu: zero-JS <details>/<summary> disclosure. --}}
        <details class="lg:hidden">
            <summary class="list-none rounded-lg border border-ink-200 px-3 py-2 text-sm font-medium text-ink-700 marker:content-none [&::-webkit-details-marker]:hidden">
                Menu
            </summary>
            <div class="absolute inset-x-0 top-full border-b border-ink-100 bg-white px-4 py-4 shadow-lg">
                <div class="flex flex-col gap-4">
                    @foreach ($navLinks as $link)
                        <a href="{{ lroute($link['route']) }}" class="text-sm font-medium text-ink-700">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                    <a href="{{ lroute('marketing.claim') }}"
                       class="rounded-lg bg-accent px-4 py-2.5 text-center text-sm font-semibold text-white">
                        Claim your business
                    </a>
                </div>
            </div>
        </details>
    </nav>
</header>
