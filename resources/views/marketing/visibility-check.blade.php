@extends('layouts.marketing')

@section('title', 'Free AI Visibility Check — divin.ai')
@section('description', 'See what AI engines currently do — and don\'t — know about your business, in seconds.')

@section('content')
    <section class="bg-ink-950 py-section-y">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-white sm:text-5xl">Free AI Visibility Check</h1>
            <p class="mt-4 text-lg text-ink-300">
                Search your business name — we'll show you what's currently public, and what's
                missing, from the sources AI engines actually read.
            </p>

            <form method="GET" action="{{ lroute('marketing.visibility-check') }}"
                  class="mx-auto mt-10 flex max-w-xl flex-col gap-3 sm:flex-row" role="search">
                <label for="q" class="sr-only">Business name or location</label>
                <input type="text" name="q" id="q" value="{{ $result['query'] ?? '' }}"
                       placeholder="e.g. &quot;Coral Bay Guesthouse, Grand Baie&quot;"
                       class="w-full rounded-lg border-0 px-5 py-4 text-ink-900 placeholder:text-ink-400 focus:ring-2 focus:ring-accent focus:outline-none">
                <button type="submit"
                        class="shrink-0 rounded-lg bg-accent px-8 py-4 font-semibold text-white transition hover:bg-accent-600">
                    Check visibility
                </button>
            </form>
        </div>
    </section>

    @if ($result)
        <section class="bg-white py-section-y">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                @if ($result['matches']->isNotEmpty())
                    <h2 class="text-xl font-bold text-ink-900">
                        We found {{ $result['matches']->count() }} match(es) for &ldquo;{{ $result['query'] }}&rdquo;
                    </h2>
                    <div class="mt-6 space-y-4">
                        @foreach ($result['matches'] as $match)
                            <a href="{{ lroute('marketing.profile.show', ['profile' => $match->slug]) }}"
                               class="block rounded-xl border border-ink-200 p-6 transition hover:border-accent hover:shadow-md">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-ink-900">{{ $match->name }}</p>
                                        <p class="text-sm text-ink-500">{{ $match->industry->label() }} &middot; {{ $match->city }}</p>
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $match->claim_status->value === 'unclaimed' ? 'bg-warning/10 text-warning' : 'bg-success/10 text-success' }}">
                                        {{ str($match->claim_status->value)->replace('_', ' ')->title() }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-2xl border border-ink-200 p-8">
                        <h2 class="text-xl font-bold text-ink-900">
                            Results for &ldquo;{{ $result['query'] }}&rdquo;
                        </h2>

                        <p class="mt-2 text-sm text-ink-500">
                            divin.ai's registry doesn't have a public profile matching this business yet
                            — which is itself the finding: nothing structured and AI-crawlable exists for
                            it right now.
                        </p>

                        <dl class="mt-8 divide-y divide-ink-100">
                            @foreach ([
                                'Business name & category' => 'Not found',
                                'Address & hours' => 'Not found',
                                'Description AI engines can read' => 'Not found',
                                'Services / products' => 'Not found',
                                'Structured, crawlable source (schema.org)' => 'Not found',
                            ] as $field => $status)
                                <div class="flex items-center justify-between py-3">
                                    <dt class="text-sm text-ink-700">{{ $field }}</dt>
                                    <dd class="inline-flex items-center gap-1.5 text-sm font-medium text-danger">
                                        <span class="h-1.5 w-1.5 rounded-full bg-danger"></span>
                                        {{ $status }}
                                    </dd>
                                </div>
                            @endforeach
                        </dl>

                        <div class="mt-8 rounded-xl bg-ink-50 p-6 text-center">
                            <p class="text-ink-700">
                                Give AI engines something to actually cite. Create a verified, structured
                                profile in minutes.
                            </p>
                            <a href="{{ lroute('marketing.claim') }}"
                               class="mt-4 inline-block rounded-lg bg-accent px-8 py-3.5 font-semibold text-white transition hover:bg-accent-600">
                                Create your profile
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif
@endsection
