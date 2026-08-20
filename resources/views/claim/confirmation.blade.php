@extends('layouts.marketing')

@section('title', 'Thanks — '.$profile->name.' — divin.ai')

@section('content')
    <section class="bg-white py-section-y">
        <div class="mx-auto max-w-md px-4 text-center sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-ink-900">Thanks!</h1>
            <p class="mt-4 text-ink-600">
                We're finishing setting up your subscription for <strong>{{ $profile->name }}</strong>.
                This usually takes a few seconds — refresh your profile page shortly to see it reflected.
            </p>
            <a href="{{ lroute('marketing.profile.show', ['profile' => $profile->slug]) }}"
               class="mt-8 inline-block rounded-lg bg-accent px-8 py-3.5 font-semibold text-white transition hover:bg-accent-600">
                View your profile
            </a>
        </div>
    </section>
@endsection
