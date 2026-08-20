@extends('layouts.marketing')

@section('title', 'Industries — divin.ai')
@section('description', 'How divin.ai applies to Healthcare, Hospitality, Retail, Food, and Financial Services.')

@section('content')
    <section class="bg-ink-950 py-section-y">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-white sm:text-5xl">Industries</h1>
            <p class="mt-4 text-lg text-ink-300">
                The same AI-discoverability problem shows up differently in every industry. Here's how
                divin.ai applies to yours.
            </p>
        </div>
    </section>

    <section class="bg-white py-section-y">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 sm:grid-cols-2">
                @foreach ($industries as $slug => $industry)
                    <a href="{{ lroute('marketing.industries.show', ['industry' => $slug]) }}"
                       class="rounded-2xl border border-ink-200 p-8 transition hover:border-accent hover:shadow-lg">
                        <h2 class="text-xl font-bold text-ink-900">{{ $industry['name'] }}</h2>
                        <p class="mt-3 text-sm text-ink-600">{{ $industry['tagline'] }}</p>
                        <span class="mt-4 inline-block text-sm font-semibold text-accent-700">Learn more &rarr;</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
