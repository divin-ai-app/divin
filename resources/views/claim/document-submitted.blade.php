@extends('layouts.marketing')

@section('title', 'Claim submitted — divin.ai')

@section('content')
    <section class="bg-white py-section-y">
        <div class="mx-auto max-w-md px-4 text-center sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-ink-900">Thanks — we're on it</h1>
            <p class="mt-4 text-ink-600">
                Your claim for <strong>{{ $profile->name }}</strong> has been submitted for manual
                review. We'll be in touch once it's confirmed, usually within a couple of business
                days.
            </p>
            <a href="{{ lroute('marketing.home') }}" class="mt-8 inline-block font-semibold text-accent hover:text-accent-600">
                Back to divin.ai &rarr;
            </a>
        </div>
    </section>
@endsection
