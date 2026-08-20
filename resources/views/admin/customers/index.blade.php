@extends('layouts.admin')

@section('title', 'Customers — divin.ai admin')

@section('content')
    <h1 class="text-2xl font-bold text-ink-900">Customers</h1>
    <p class="mt-1 text-sm text-ink-500">
        An internal approximation — Stripe remains the source of truth for actual billing.
    </p>

    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-ink-200 bg-white p-6">
            <p class="text-sm font-medium text-ink-500">Estimated MRR</p>
            <p class="mt-2 text-3xl font-bold text-ink-900">${{ number_format($mrrCents / 100, 2) }}</p>
        </div>
        <div class="rounded-2xl border border-ink-200 bg-white p-6">
            <p class="text-sm font-medium text-ink-500">Active subscriptions</p>
            <p class="mt-2 text-3xl font-bold text-ink-900">{{ $activeCount }}</p>
        </div>
        <div class="rounded-2xl border border-ink-200 bg-white p-6">
            <p class="text-sm font-medium text-ink-500">Plan mix</p>
            <p class="mt-2 text-sm text-ink-700">
                Registered: <span class="font-semibold text-ink-900">{{ $planMix['registered'] }}</span>
                &middot;
                Managed: <span class="font-semibold text-ink-900">{{ $planMix['managed'] }}</span>
            </p>
        </div>
    </div>

    <div class="mt-8 overflow-x-auto rounded-xl border border-ink-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-ink-100 bg-ink-50 text-xs font-semibold uppercase tracking-wide text-ink-500">
                <tr>
                    <th class="px-4 py-3">Business</th>
                    <th class="px-4 py-3">Owner</th>
                    <th class="px-4 py-3">Tier</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Renews</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @forelse ($subscriptions as $subscription)
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ lroute('marketing.admin.profiles.show', ['profile' => $subscription->profile->slug]) }}" class="font-medium text-ink-900 hover:text-accent">
                                {{ $subscription->profile->name }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-ink-600">{{ $subscription->profile->owners->first()?->user?->email ?? '—' }}</td>
                        <td class="px-4 py-3 text-ink-600">{{ ucfirst($subscription->tier->value) }}</td>
                        <td class="px-4 py-3 text-ink-600">{{ ucfirst(str_replace('_', ' ', $subscription->status->value)) }}</td>
                        <td class="px-4 py-3 text-ink-600">{{ $subscription->renewal_date?->format('d M Y') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-ink-400">No subscriptions yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $subscriptions->links() }}</div>
@endsection
