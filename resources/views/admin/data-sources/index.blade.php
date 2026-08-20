@extends('layouts.admin')

@section('title', 'Data sources — divin.ai admin')

@section('content')
    <h1 class="text-2xl font-bold text-ink-900">Data source pipeline status</h1>
    <p class="mt-1 text-sm text-ink-500">
        The external ingestion pipeline is a separate backend project (out of scope for this build) — this table
        will populate once it starts reporting run status here.
    </p>

    <div class="mt-6 overflow-x-auto rounded-xl border border-ink-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-ink-100 bg-ink-50 text-xs font-semibold uppercase tracking-wide text-ink-500">
                <tr>
                    <th class="px-4 py-3">Country</th>
                    <th class="px-4 py-3">Source</th>
                    <th class="px-4 py-3">Last run</th>
                    <th class="px-4 py-3">Result</th>
                    <th class="px-4 py-3">Ingested</th>
                    <th class="px-4 py-3">Failed</th>
                    <th class="px-4 py-3">Next scheduled</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @forelse ($sources as $source)
                    <tr>
                        <td class="px-4 py-3 font-medium text-ink-900">{{ $source->country_code }}</td>
                        <td class="px-4 py-3 text-ink-600">{{ str_replace('_', ' ', $source->source_type->value) }}</td>
                        <td class="px-4 py-3 text-ink-600">{{ $source->last_run_at?->format('d M Y, H:i') ?? '—' }}</td>
                        <td class="px-4 py-3 text-ink-600">{{ $source->last_run_status?->value ?? '—' }}</td>
                        <td class="px-4 py-3 text-ink-600">{{ $source->records_ingested }}</td>
                        <td class="px-4 py-3 text-ink-600">{{ $source->records_failed }}</td>
                        <td class="px-4 py-3 text-ink-600">{{ $source->next_scheduled_run?->format('d M Y, H:i') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-ink-400">No ingestion runs recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
