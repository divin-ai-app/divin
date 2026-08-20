@extends('layouts.marketing')

@section('title', 'Dashboard — divin.ai')

@section('content')
    <section class="bg-white py-section-y">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-ink-900">Welcome, {{ auth()->user()->name }}</h1>
                <form method="POST" action="{{ lroute('marketing.logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-semibold text-ink-500 hover:text-ink-900">
                        Sign out
                    </button>
                </form>
            </div>

            <h2 class="mt-10 text-lg font-bold text-ink-900">Your businesses</h2>

            @if ($profiles->isEmpty())
                <p class="mt-4 text-ink-600">
                    You haven't claimed any businesses yet.
                    <a href="{{ lroute('marketing.claim') }}" class="font-semibold text-accent hover:text-accent-600">
                        Find your business &rarr;
                    </a>
                </p>
            @else
                <div class="mt-4 space-y-4">
                    @foreach ($profiles as $profile)
                        <a href="{{ lroute('marketing.profile.show', ['profile' => $profile->slug]) }}"
                           class="block rounded-xl border border-ink-200 p-6 transition hover:border-accent hover:shadow-md">
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
    </section>
@endsection
