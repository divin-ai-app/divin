@extends('layouts.marketing')

@section('title', $profile->name.' — '.$profile->city.' — divin.ai')
@section('description', $profile->description_short)

@push('schema')
    <script type="application/ld+json">
        {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endpush

@php
    $days = [
        'monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday',
        'thursday' => 'Thursday', 'friday' => 'Friday', 'saturday' => 'Saturday', 'sunday' => 'Sunday',
    ];
@endphp

@section('content')
    <section class="bg-ink-950 py-section-y-sm">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold tracking-wide text-accent uppercase">
                {{ $profile->industry->label() }} &middot; {{ $profile->category }}
            </p>
            <h1 class="mt-2 text-4xl font-bold text-white sm:text-5xl">{{ $profile->name }}</h1>
            <p class="mt-3 text-ink-300">{{ $profile->city }}, Mauritius</p>

            @if ($profile->claim_status->value === 'unclaimed')
                <div class="mt-8 rounded-xl border border-ink-700 bg-ink-900 p-6">
                    <p class="text-white">
                        <strong>Is this your business?</strong> Claim this profile to verify and enrich it.
                    </p>
                    <a href="{{ lroute('marketing.claim.show', ['profile' => $profile->slug]) }}"
                       class="mt-4 inline-block rounded-lg bg-accent px-6 py-3 text-sm font-semibold text-white transition hover:bg-accent-600">
                        Claim this business
                    </a>
                </div>
            @endif
        </div>
    </section>

    <section class="bg-white py-section-y">
        <div class="mx-auto grid max-w-4xl gap-10 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">
            @if (session('status'))
                <div class="lg:col-span-3 rounded-lg bg-success/10 px-4 py-3 text-sm font-medium text-success">
                    {{ session('status') }}
                </div>
            @endif

            <div class="lg:col-span-2">
                <h2 class="text-xl font-bold text-ink-900">About</h2>
                <p class="mt-3 text-ink-600">{{ $profile->description_short }}</p>

                @if ($profile->services->isNotEmpty())
                    <h2 class="mt-10 text-xl font-bold text-ink-900">Services</h2>
                    <ul class="mt-4 space-y-3">
                        @foreach ($profile->services as $service)
                            <li class="flex items-baseline justify-between border-b border-ink-100 pb-3">
                                <div>
                                    <span class="font-medium text-ink-900">{{ $service->name }}</span>
                                    @if ($service->description)
                                        <p class="text-sm text-ink-500">{{ $service->description }}</p>
                                    @endif
                                </div>
                                @if ($service->price)
                                    <span class="shrink-0 text-sm text-ink-600">{{ $service->price }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div>
                <div class="rounded-2xl border border-ink-200 p-6">
                    <dl class="space-y-4 text-sm">
                        <div>
                            <dt class="font-semibold text-ink-900">Address</dt>
                            <dd class="text-ink-600">
                                {{ $profile->address_line1 }}@if ($profile->address_line2), {{ $profile->address_line2 }}@endif<br>
                                {{ $profile->city }}, Mauritius
                            </dd>
                        </div>

                        @if ($profile->phone)
                            <div>
                                <dt class="font-semibold text-ink-900">Phone</dt>
                                <dd class="text-ink-600">{{ $profile->phone }}</dd>
                            </div>
                        @endif

                        @if ($profile->price_range)
                            <div>
                                <dt class="font-semibold text-ink-900">Price range</dt>
                                <dd class="text-ink-600">{{ $profile->price_range }}</dd>
                            </div>
                        @endif

                        @if ($profile->hours)
                            <div>
                                <dt class="font-semibold text-ink-900">Hours</dt>
                                <dd class="mt-1 space-y-1 text-ink-600">
                                    @foreach ($days as $key => $label)
                                        @php $entry = $profile->hours[$key] ?? null; @endphp
                                        <div class="flex justify-between gap-4">
                                            <span>{{ $label }}</span>
                                            <span>
                                                @if (is_array($entry) && ! empty($entry['open']))
                                                    {{ $entry['open'] }}&ndash;{{ $entry['close'] }}
                                                @else
                                                    Closed
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <p class="mt-4 text-center text-xs text-ink-400">
                    Sourced from public data.
                    <a href="{{ lroute('marketing.profile.report', ['profile' => $profile->slug]) }}" class="underline">Report an issue</a>.
                </p>
            </div>
        </div>
    </section>
@endsection
