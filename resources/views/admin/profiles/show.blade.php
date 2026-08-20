@extends('layouts.admin')

@section('title', $profile->name.' — divin.ai admin')

@section('content')
    <div class="flex items-start justify-between">
        <div>
            <a href="{{ lroute('marketing.admin.profiles.index') }}" class="text-sm font-medium text-ink-400 hover:text-ink-700">&larr; All profiles</a>
            <h1 class="mt-2 text-2xl font-bold text-ink-900">{{ $profile->name }}</h1>
            <p class="mt-1 text-sm text-ink-500">
                {{ $profile->industry->label() }} &middot; {{ $profile->city }}, {{ $profile->country_code }}
            </p>
        </div>

        <div class="flex gap-3">
            <a href="{{ lroute('marketing.profile.show', ['profile' => $profile->slug]) }}" target="_blank"
               class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-700 hover:bg-ink-50">
                View public page
            </a>
            <a href="{{ lroute('marketing.dashboard.edit', ['profile' => $profile->slug]) }}"
               class="rounded-lg bg-accent px-4 py-2 text-sm font-semibold text-white hover:bg-accent-600">
                Edit profile
            </a>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-ink-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-ink-900">Status</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-ink-500">Profile status</dt><dd class="font-medium text-ink-900">{{ ucfirst($profile->status->value) }}</dd></div>
                <div class="flex justify-between"><dt class="text-ink-500">Claim status</dt><dd class="font-medium text-ink-900">{{ ucfirst(str_replace('_', ' ', $profile->claim_status->value)) }}</dd></div>
                <div class="flex justify-between"><dt class="text-ink-500">Plan tier</dt><dd class="font-medium text-ink-900">{{ ucfirst($profile->plan_tier->value) }}</dd></div>
                <div class="flex justify-between"><dt class="text-ink-500">Country clearance</dt><dd class="font-medium text-ink-900">{{ $profile->countryClearance ? ucfirst(str_replace('_', ' ', $profile->countryClearance->legal_status->value)) : '—' }}</dd></div>
            </dl>
        </div>

        <div class="rounded-xl border border-ink-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-ink-900">Subscription</h2>
            @if ($profile->subscription)
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-ink-500">Tier</dt><dd class="font-medium text-ink-900">{{ ucfirst($profile->subscription->tier->value) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-500">Status</dt><dd class="font-medium text-ink-900">{{ ucfirst(str_replace('_', ' ', $profile->subscription->status->value)) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-500">Renews</dt><dd class="font-medium text-ink-900">{{ $profile->subscription->renewal_date?->format('d M Y') ?? '—' }}</dd></div>
                </dl>
            @else
                <p class="mt-4 text-sm text-ink-400">No active subscription.</p>
            @endif
        </div>

        <div class="rounded-xl border border-ink-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-ink-900">Contact</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-ink-500">Email</dt><dd class="font-medium text-ink-900">{{ $profile->public_email ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-ink-500">Phone</dt><dd class="font-medium text-ink-900">{{ $profile->phone ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-ink-500">Website</dt><dd class="font-medium text-ink-900">{{ $profile->website ?? '—' }}</dd></div>
            </dl>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-ink-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-ink-900">Claim requests</h2>
            @forelse ($profile->claimRequests as $claimRequest)
                <div class="mt-3 border-t border-ink-100 pt-3 text-sm first:mt-0 first:border-0 first:pt-0">
                    <p class="font-medium text-ink-900">{{ $claimRequest->requester->email }}</p>
                    <p class="text-ink-500">{{ ucfirst(str_replace('_', ' ', $claimRequest->status->value)) }} &middot; {{ $claimRequest->created_at->format('d M Y') }}</p>
                </div>
            @empty
                <p class="mt-3 text-sm text-ink-400">No claim requests yet.</p>
            @endforelse
        </div>

        <div class="rounded-xl border border-ink-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-ink-900">Disputes</h2>
            @forelse ($profile->disputes as $dispute)
                <div class="mt-3 border-t border-ink-100 pt-3 text-sm first:mt-0 first:border-0 first:pt-0">
                    <p class="font-medium text-ink-900">{{ ucfirst(str_replace('_', ' ', $dispute->type->value)) }}</p>
                    <p class="text-ink-500">{{ ucfirst(str_replace('_', ' ', $dispute->status->value)) }} &middot; {{ $dispute->created_at->format('d M Y') }}</p>
                </div>
            @empty
                <p class="mt-3 text-sm text-ink-400">No disputes filed.</p>
            @endforelse
        </div>
    </div>
@endsection
