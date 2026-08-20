@extends('layouts.dashboard')

@section('title', $profile->name.' — divin.ai')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-ink-900">{{ $profile->name }}</h1>
                <a href="{{ lroute('marketing.profile.show', ['profile' => $profile->slug]) }}"
                   class="text-sm text-accent-700 hover:text-accent-800" target="_blank" rel="noopener">
                    View public profile &rarr;
                </a>
            </div>
            <div class="flex gap-2">
                <span class="rounded-full bg-ink-100 px-3 py-1 text-xs font-semibold text-ink-700">
                    {{ str($profile->claim_status->value)->title() }}
                </span>
                <span class="rounded-full bg-accent/10 px-3 py-1 text-xs font-semibold text-accent-700">
                    {{ str($profile->plan_tier->value)->title() }}
                </span>
            </div>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-ink-200 bg-white p-6">
                <p class="text-2xl font-bold text-ink-900">{{ $profile->services->count() }}</p>
                <p class="text-sm text-ink-500">Services listed</p>
            </div>
            <div class="rounded-xl border border-ink-200 bg-white p-6">
                <p class="text-2xl font-bold text-ink-900">{{ $profile->images->count() }}</p>
                <p class="text-sm text-ink-500">Images</p>
            </div>
            <div class="rounded-xl border border-ink-200 bg-white p-6">
                <p class="text-2xl font-bold text-ink-900">
                    {{ $profile->last_verified_at?->format('M j, Y') ?? '—' }}
                </p>
                <p class="text-sm text-ink-500">Last verified</p>
            </div>
        </div>

        <div class="mt-8 rounded-xl border border-ink-200 bg-white p-6">
            <h2 class="font-semibold text-ink-900">Current profile</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-ink-500">Category</dt>
                    <dd class="text-right text-ink-900">{{ $profile->category }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-ink-500">Address</dt>
                    <dd class="text-right text-ink-900">{{ $profile->address_line1 }}, {{ $profile->city }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-ink-500">Description</dt>
                    <dd class="max-w-sm text-right text-ink-900">{{ $profile->description_short }}</dd>
                </div>
            </dl>
            <a href="{{ lroute('marketing.dashboard.edit', ['profile' => $profile->slug]) }}"
               class="mt-6 inline-block rounded-lg bg-accent-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-accent-800">
                Edit profile
            </a>
        </div>
    </div>
@endsection
