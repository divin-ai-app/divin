@extends('layouts.dashboard')

@section('title', 'Freshness report — '.$profile->name.' — divin.ai')

@section('content')
    <div class="mx-auto max-w-3xl">
        <h1 class="text-2xl font-bold text-ink-900">Freshness &amp; coherence report</h1>

        @if (! $unlocked)
            <div class="mt-6 rounded-xl border-2 border-accent bg-white p-8 text-center">
                <p class="text-lg font-semibold text-ink-900">Managed plan required</p>
                <p class="mt-2 text-sm text-ink-600">
                    Freshness &amp; coherence monitoring — checking your profile against your own
                    website, Facebook Page, and OTA listings, with email alerts when they drift —
                    is included on the Managed plan.
                </p>
                <a href="{{ lroute('marketing.claim.plan', ['profile' => $profile->slug]) }}"
                   class="mt-5 inline-block rounded-lg bg-accent-700 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-accent-800">
                    Upgrade to Managed
                </a>
            </div>
        @else
            <p class="mt-2 text-sm text-ink-500">
                We periodically compare this listing against your other data sources and flag anything
                that's drifted out of sync.
            </p>

            @if ($profile->dataSources->isNotEmpty())
                <div class="mt-6 overflow-x-auto rounded-xl border border-ink-200 bg-white">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-ink-100 bg-ink-50 text-xs font-semibold uppercase tracking-wide text-ink-500">
                            <tr>
                                <th class="px-4 py-3">Source</th>
                                <th class="px-4 py-3">Coherence</th>
                                <th class="px-4 py-3">Last checked</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-ink-100">
                            @foreach ($profile->dataSources as $source)
                                @php
                                    $badge = match ($source->coherence_status->value) {
                                        'aligned' => 'bg-success/10 text-success',
                                        'minor_drift' => 'bg-warning/10 text-warning',
                                        'major_drift' => 'bg-danger/10 text-danger',
                                        default => 'bg-ink-100 text-ink-500',
                                    };
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 text-ink-900">{{ str_replace('_', ' ', $source->source_type->value) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $badge }}">
                                            {{ str_replace('_', ' ', $source->coherence_status->value) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-ink-600">{{ $source->last_checked_at?->format('d M Y, H:i') ?? 'Not checked yet' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <h2 class="mt-8 font-semibold text-ink-900">Needs your review</h2>
            <div class="mt-3 space-y-4">
                @forelse ($openLogs as $log)
                    @php
                        $severityBadge = match ($log->severity->value) {
                            'high' => 'bg-danger/10 text-danger',
                            'medium' => 'bg-warning/10 text-warning',
                            default => 'bg-ink-100 text-ink-500',
                        };
                    @endphp
                    <div id="log-{{ $log->id }}" class="rounded-xl border border-ink-200 bg-white p-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-ink-500">
                                From {{ $log->dataSource ? str_replace('_', ' ', $log->dataSource->source_type->value) : 'a data source' }}
                                &middot; {{ $log->checked_at->format('d M Y') }}
                            </p>
                            <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $severityBadge }}">
                                {{ ucfirst($log->severity->value) }} severity
                            </span>
                        </div>

                        <div class="mt-4 space-y-3">
                            @foreach ($log->discrepancies as $d)
                                <div class="rounded-lg bg-ink-50 px-4 py-3 text-sm">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <p class="font-medium text-ink-700">{{ $d['label'] }}</p>
                                            <p class="mt-1 text-ink-600">
                                                <span class="text-ink-500 line-through">{{ $d['current_value'] ?? '(empty)' }}</span>
                                                &rarr;
                                                <span class="font-medium text-ink-900">{{ $d['source_value'] }}</span>
                                            </p>
                                        </div>

                                        @if (empty($d['resolution'] ?? null))
                                            <div class="flex shrink-0 gap-2">
                                                <form method="POST" action="{{ lroute('marketing.dashboard.freshness.resolve', ['profile' => $profile->slug, 'freshnessCheckLog' => $log->id]) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="field" value="{{ $d['field'] }}">
                                                    <input type="hidden" name="action" value="accepted_new_value">
                                                    <button type="submit" class="rounded-lg bg-accent-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-accent-800">
                                                        Accept
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ lroute('marketing.dashboard.freshness.resolve', ['profile' => $profile->slug, 'freshnessCheckLog' => $log->id]) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="field" value="{{ $d['field'] }}">
                                                    <input type="hidden" name="action" value="kept_current_value">
                                                    <button type="submit" class="rounded-lg border border-ink-300 px-3 py-1.5 text-xs font-semibold text-ink-700 hover:border-accent hover:text-accent">
                                                        Keep
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="shrink-0 text-xs font-medium text-ink-500">
                                                {{ $d['resolution'] === 'accepted_new_value' ? 'Accepted' : 'Kept current' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="rounded-xl border border-dashed border-ink-200 bg-white p-8 text-center text-sm text-ink-500">
                        Nothing needs your attention right now.
                    </p>
                @endforelse
            </div>

            @if ($resolvedLogs->isNotEmpty())
                <h2 class="mt-8 font-semibold text-ink-900">Recently resolved</h2>
                <div class="mt-3 space-y-2">
                    @foreach ($resolvedLogs as $log)
                        @php
                            $accepted = collect($log->discrepancies)->where('resolution', 'accepted_new_value')->count();
                            $kept = collect($log->discrepancies)->where('resolution', 'kept_current_value')->count();
                            $summary = match (true) {
                                $accepted > 0 && $kept > 0 => "{$accepted} accepted, {$kept} kept",
                                $accepted > 0 => 'Accepted',
                                default => 'Kept current',
                            };
                        @endphp
                        <div class="flex items-center justify-between rounded-lg border border-ink-100 px-4 py-3 text-sm">
                            <span class="text-ink-600">
                                {{ count($log->discrepancies) }} field(s) from {{ $log->dataSource ? str_replace('_', ' ', $log->dataSource->source_type->value) : 'a data source' }}
                            </span>
                            <span class="text-ink-500">
                                {{ $summary }} &middot; {{ $log->resolved_at->format('d M Y') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
@endsection
