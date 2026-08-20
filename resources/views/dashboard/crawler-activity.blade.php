@extends('layouts.dashboard')

@section('title', 'AI crawler activity — '.$profile->name.' — divin.ai')

@section('content')
    <div class="mx-auto max-w-3xl">
        <h1 class="text-2xl font-bold text-ink-900">AI crawler activity</h1>
        <div class="mt-2 rounded-lg bg-ink-100 px-4 py-3 text-sm text-ink-600">
            This reflects <strong>consideration, not citation</strong> — bot visits are evidence AI
            engines are reading your data, not proof any specific AI answer recommended your business.
        </div>

        @if (! $unlocked)
            <div class="mt-6 rounded-xl border-2 border-accent bg-white p-8 text-center">
                <p class="text-lg font-semibold text-ink-900">Claim this profile to see crawler activity</p>
                <p class="mt-2 text-sm text-ink-600">
                    Available on both Registered and Managed plans once your profile is verified.
                </p>
            </div>
        @else
            @php
                $max = max($dailyByBot->max() ?: 0, 1);
            @endphp

            <div class="mt-6 rounded-xl border border-ink-200 bg-white p-6">
                <p class="text-sm font-semibold text-ink-900">Last 14 days</p>

                <div class="mt-4 flex items-end gap-2" style="height: 140px;">
                    @foreach ($days as $date)
                        @php $count = $dailyByBot[$date] ?? 0; @endphp
                        <div class="flex flex-1 flex-col items-center justify-end gap-1" title="{{ $date }}: {{ $count }} visit(s)">
                            <span class="text-xs text-ink-400">{{ $count > 0 ? $count : '' }}</span>
                            <div class="w-full rounded-t bg-accent/80" style="height: {{ max((int) round($count / $max * 100), $count > 0 ? 6 : 0) }}%; min-height: {{ $count > 0 ? '4px' : '0' }};"></div>
                            <span class="text-[10px] text-ink-400">{{ \Illuminate\Support\Carbon::parse($date)->format('d/m') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($totalsByBot->isNotEmpty())
                <div class="mt-6 overflow-x-auto rounded-xl border border-ink-200 bg-white">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-ink-100 bg-ink-50 text-xs font-semibold uppercase tracking-wide text-ink-500">
                            <tr>
                                <th class="px-4 py-3">Bot</th>
                                <th class="px-4 py-3">Visits (14 days)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-ink-100">
                            @foreach ($totalsByBot as $bot => $count)
                                <tr>
                                    <td class="px-4 py-3 text-ink-900">{{ str_replace('_', ' ', $bot) }}</td>
                                    <td class="px-4 py-3 text-ink-600">{{ $count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="mt-6 rounded-xl border border-dashed border-ink-200 bg-white p-8 text-center text-sm text-ink-400">
                    No crawler visits recorded yet in the last 14 days.
                </p>
            @endif

            <form method="POST" action="{{ lroute('marketing.dashboard.crawler-activity.simulate', ['profile' => $profile->slug]) }}" class="mt-6">
                @csrf
                <button type="submit" class="rounded-lg border border-ink-300 px-4 py-2 text-sm font-medium text-ink-700 hover:border-accent hover:text-accent">
                    Simulate a bot visit
                </button>
                <span class="ml-2 text-xs text-ink-400">For previewing this chart — real visits are logged automatically.</span>
            </form>
        @endif
    </div>
@endsection
