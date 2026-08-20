@extends('layouts.marketing')

@section('title', $profile->name.' is already claimed — divin.ai')

@section('content')
    <section class="bg-white py-section-y">
        <div class="mx-auto max-w-md px-4 text-center sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-ink-900">This profile is already claimed</h1>
            <p class="mt-4 text-ink-600">
                <strong>{{ $profile->name }}</strong> has already been verified by its owner. If
                that's a mistake, let us know.
            </p>
            <a href="{{ lroute('marketing.contact') }}" class="mt-8 inline-block font-semibold text-accent hover:text-accent-600">
                Contact us &rarr;
            </a>
        </div>
    </section>
@endsection
