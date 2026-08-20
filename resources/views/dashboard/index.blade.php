@extends('layouts.dashboard')

@section('title', 'Your businesses — divin.ai')

@section('content')
    <div class="mx-auto max-w-3xl">
        <h1 class="text-2xl font-bold text-ink-900">Your businesses</h1>

        @if ($profiles->isEmpty())
            <p class="mt-4 text-ink-600">
                You haven't claimed any businesses yet.
                <a href="{{ lroute('marketing.claim') }}" class="font-semibold text-accent-700 hover:text-accent-800">
                    Find your business &rarr;
                </a>
            </p>
        @else
            <div class="mt-6 space-y-4">
                @foreach ($profiles as $profile)
                    <a href="{{ lroute('marketing.dashboard.overview', ['profile' => $profile->slug]) }}"
                       class="block rounded-xl border border-ink-200 bg-white p-6 transition hover:border-accent hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-ink-900">{{ $profile->name }}</p>
                                <p class="text-sm text-ink-500">{{ $profile->industry->label() }} &middot; {{ $profile->city }}</p>
                            </div>
                            <span class="rounded-full bg-ink-100 px-3 py-1 text-xs font-semibold text-ink-700">
                                {{ str($profile->plan_tier->value)->title() }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
