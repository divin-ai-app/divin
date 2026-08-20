@extends('layouts.admin')

@section('title', 'Admin overview — divin.ai')

@section('content')
    <h1 class="text-2xl font-bold text-ink-900">Overview</h1>
    <p class="mt-1 text-sm text-ink-500">Registry-wide status at a glance.</p>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @php
            $cards = [
                ['label' => 'Published profiles', 'value' => $stats['profiles_published'], 'sub' => $stats['profiles_total'].' total', 'route' => 'marketing.admin.profiles.index'],
                ['label' => 'Pending claims', 'value' => $stats['claims_pending'], 'sub' => 'awaiting review', 'route' => 'marketing.admin.claims.index'],
                ['label' => 'Open disputes', 'value' => $stats['disputes_open'], 'sub' => 'need resolution', 'route' => 'marketing.admin.disputes.index'],
                ['label' => 'Countries in review', 'value' => $stats['countries_in_review'], 'sub' => 'legal clearance pending', 'route' => 'marketing.admin.country-clearance.index'],
                ['label' => 'Active subscriptions', 'value' => $stats['active_subscriptions'], 'sub' => 'Registered + Managed', 'route' => 'marketing.admin.customers.index'],
            ];
        @endphp

        @foreach ($cards as $card)
            <a href="{{ lroute($card['route']) }}" class="rounded-2xl border border-ink-200 bg-white p-6 transition hover:border-accent">
                <p class="text-sm font-medium text-ink-500">{{ $card['label'] }}</p>
                <p class="mt-2 text-3xl font-bold text-ink-900">{{ $card['value'] }}</p>
                <p class="mt-1 text-xs text-ink-400">{{ $card['sub'] }}</p>
            </a>
        @endforeach
    </div>
@endsection
