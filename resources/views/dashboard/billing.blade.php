@extends('layouts.dashboard')

@section('title', 'Billing — '.$profile->name.' — divin.ai')

@section('content')
    <div class="mx-auto max-w-3xl space-y-8">
        <h1 class="text-2xl font-bold text-ink-900">Plan &amp; billing</h1>

        <div class="rounded-xl border border-ink-200 bg-white p-6">
            @if ($profile->subscription)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-ink-900">{{ str($profile->subscription->tier->value)->title() }} plan</p>
                        <p class="text-sm text-ink-500">
                            {{ str($profile->subscription->status->value)->replace('_', ' ')->title() }}
                            @if ($profile->subscription->renewal_date)
                                &middot; renews {{ $profile->subscription->renewal_date->format('M j, Y') }}
                            @endif
                        </p>
                    </div>
                    <span class="rounded-full bg-accent/10 px-3 py-1 text-xs font-semibold text-accent">
                        {{ str($profile->plan_tier->value)->title() }}
                    </span>
                </div>

                @if ($profile->plan_tier->value === 'registered')
                    <div class="mt-6 rounded-lg bg-ink-50 p-4">
                        <p class="text-sm text-ink-700">
                            Upgrade to <strong>Managed</strong> for ongoing freshness &amp; coherence monitoring
                            with email alerts when your other listings drift out of sync.
                        </p>
                        <a href="{{ lroute('marketing.claim.plan', ['profile' => $profile->slug]) }}"
                           class="mt-3 inline-block rounded-lg bg-accent px-5 py-2 text-sm font-semibold text-white transition hover:bg-accent-600">
                            Upgrade to Managed
                        </a>
                    </div>
                @endif
            @else
                <p class="text-ink-600">No active subscription found for this profile.</p>
            @endif
        </div>

        <div class="rounded-xl border border-ink-200 bg-white p-6">
            <h2 class="font-semibold text-ink-900">Invoices</h2>
            @if ($profile->subscription && $profile->subscription->invoices->isNotEmpty())
                <div class="mt-4 divide-y divide-ink-100">
                    @foreach ($profile->subscription->invoices as $invoice)
                        <div class="flex items-center justify-between py-3 text-sm">
                            <span class="text-ink-600">{{ $invoice->issued_at->format('M j, Y') }}</span>
                            <span class="text-ink-900">${{ number_format($invoice->amount_cents / 100, 2) }}</span>
                            <span class="text-ink-500">{{ str($invoice->status->value)->title() }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="mt-4 text-sm text-ink-500">No invoices yet.</p>
            @endif
        </div>
    </div>
@endsection
