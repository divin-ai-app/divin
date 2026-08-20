@extends('layouts.marketing')

@section('title', 'Find your business — divin.ai')
@section('description', 'Find and claim your business profile on divin.ai.')

@section('content')
    <section class="bg-ink-950 py-section-y">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-white sm:text-5xl">Find your business</h1>
            <p class="mt-4 text-lg text-ink-300">
                Search by name to see if a profile already exists — or start a new one.
            </p>

            <form method="GET" action="{{ lroute('marketing.claim') }}"
                  class="mx-auto mt-10 flex max-w-xl flex-col gap-3 sm:flex-row" role="search">
                <label for="q" class="sr-only">Business name or location</label>
                <input type="text" name="q" id="q" value="{{ $result['query'] ?? '' }}"
                       placeholder="Business name and location"
                       class="w-full rounded-lg border-0 bg-white px-5 py-4 text-ink-900 placeholder:text-ink-400 focus:ring-2 focus:ring-accent focus:outline-none">
                <button type="submit"
                        class="shrink-0 rounded-lg bg-accent px-8 py-4 font-semibold text-white transition hover:bg-accent-600">
                    Search
                </button>
            </form>
        </div>
    </section>

    <section class="bg-white py-section-y">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            @if ($result)
                @if ($result['matches']->isNotEmpty())
                    <h2 class="text-lg font-bold text-ink-900">
                        {{ $result['matches']->count() }} result(s) for &ldquo;{{ $result['query'] }}&rdquo;
                    </h2>
                    <div class="mt-6 space-y-4">
                        @foreach ($result['matches'] as $match)
                            <a href="{{ lroute('marketing.profile.show', ['profile' => $match->slug]) }}"
                               class="block rounded-xl border border-ink-200 p-6 transition hover:border-accent hover:shadow-md">
                                <p class="font-semibold text-ink-900">{{ $match->name }}</p>
                                <p class="text-sm text-ink-500">{{ $match->industry->label() }} &middot; {{ $match->city }}</p>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-ink-600">
                        No profile found for &ldquo;{{ $result['query'] }}&rdquo; yet.
                        <a href="{{ lroute('marketing.contact') }}" class="font-semibold text-accent hover:text-accent-600">
                            Contact us
                        </a>
                        to get one started.
                    </p>
                @endif
            @else
                <p class="text-center text-ink-600">
                    Search above, or
                    <a href="{{ lroute('marketing.contact') }}" class="font-semibold text-accent hover:text-accent-600">
                        contact us
                    </a>
                    if you'd rather we help directly.
                </p>
            @endif
        </div>
    </section>
@endsection
