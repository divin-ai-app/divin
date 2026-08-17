@extends('layouts.marketing')

@section('title', $industry['name'].' — divin.ai')
@section('description', $industry['tagline'])

@section('content')
    <section class="bg-ink-950 py-section-y">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <p class="text-sm font-semibold tracking-wide text-accent uppercase">{{ $industry['name'] }}</p>
            <h1 class="mt-2 text-4xl font-bold text-white sm:text-5xl">{{ $industry['tagline'] }}</h1>
        </div>
    </section>

    <section class="bg-white py-section-y">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-ink-900">Where it goes wrong today</h2>
            <ul class="mt-6 space-y-4">
                @foreach ($industry['pain_points'] as $point)
                    <li class="flex gap-3 text-ink-600">
                        <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-accent"></span>
                        <span>{{ $point }}</span>
                    </li>
                @endforeach
            </ul>

            <div class="mt-12 rounded-2xl bg-ink-50 p-8">
                <h2 class="text-lg font-bold text-ink-900">A realistic example</h2>
                <p class="mt-3 text-ink-600">{{ $industry['example'] }}</p>
            </div>

            <div class="mt-12 text-center">
                <a href="{{ lroute('marketing.claim') }}"
                   class="inline-block rounded-lg bg-accent px-8 py-3.5 font-semibold text-white transition hover:bg-accent-600">
                    Find your {{ strtolower($industry['name']) }} business
                </a>
            </div>
        </div>
    </section>
@endsection
