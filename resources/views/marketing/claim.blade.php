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

            <form method="GET" action="{{ lroute('marketing.visibility-check') }}"
                  class="mx-auto mt-10 flex max-w-xl flex-col gap-3 sm:flex-row" role="search">
                <label for="q" class="sr-only">Business name or location</label>
                <input type="text" name="q" id="q"
                       placeholder="Business name and location"
                       class="w-full rounded-lg border-0 px-5 py-4 text-ink-900 placeholder:text-ink-400 focus:ring-2 focus:ring-accent focus:outline-none">
                <button type="submit"
                        class="shrink-0 rounded-lg bg-accent px-8 py-4 font-semibold text-white transition hover:bg-accent-600">
                    Search
                </button>
            </form>
        </div>
    </section>

    <section class="bg-white py-section-y">
        <div class="mx-auto max-w-2xl px-4 text-center sm:px-6 lg:px-8">
            <p class="text-ink-600">
                Full search against published profiles, and the ownership-verification claim wizard,
                land once the registry has real profile data to search — the auto-generated business
                registry itself is being built next. In the meantime,
                <a href="{{ lroute('marketing.contact') }}" class="font-semibold text-accent hover:text-accent-600">
                    contact us
                </a>
                and we'll help directly.
            </p>
        </div>
    </section>
@endsection
