@extends('layouts.admin')

@section('title', 'Country clearance — divin.ai admin')

@section('content')
    <h1 class="text-2xl font-bold text-ink-900">Country clearance</h1>
    <p class="mt-1 text-sm text-ink-500">
        Gates auto-generation per country — the ingestion endpoint rejects any country whose status isn't "Cleared".
    </p>

    <div class="mt-6 overflow-x-auto rounded-xl border border-ink-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-ink-100 bg-ink-50 text-xs font-semibold uppercase tracking-wide text-ink-500">
                <tr>
                    <th class="px-4 py-3">Country</th>
                    <th class="px-4 py-3">Profiles</th>
                    <th class="px-4 py-3">Legal status</th>
                    <th class="px-4 py-3">GDPR-excluded</th>
                    <th class="px-4 py-3">Notes</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @foreach ($clearances as $clearance)
                    @php $formId = "clearance-{$clearance->id}"; @endphp
                    <tr>
                        <td class="px-4 py-3 font-medium text-ink-900">
                            {{ $clearance->country_name }}
                            <span class="text-ink-400">({{ $clearance->country_code }})</span>
                        </td>
                        <td class="px-4 py-3 text-ink-600">{{ $clearance->profiles_count }}</td>
                        <td class="px-4 py-3">
                            <select name="legal_status" form="{{ $formId }}" class="rounded-lg border-ink-200 bg-white px-2 py-1.5 text-sm text-ink-900 focus:border-accent focus:ring-accent">
                                @foreach (\App\Enums\LegalStatus::cases() as $status)
                                    <option value="{{ $status->value }}" @selected($clearance->legal_status === $status)>{{ ucfirst(str_replace('_', ' ', $status->value)) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" name="gdpr_excluded" value="1" form="{{ $formId }}" @checked($clearance->gdpr_excluded)
                                   class="rounded border-ink-300 text-accent focus:ring-accent">
                        </td>
                        <td class="px-4 py-3">
                            <input type="text" name="notes" value="{{ $clearance->notes }}" placeholder="Optional notes" form="{{ $formId }}"
                                   class="w-48 rounded-lg border-ink-200 bg-white px-2 py-1.5 text-sm text-ink-900 focus:border-accent focus:ring-accent">
                        </td>
                        <td class="px-4 py-3">
                            <button type="submit" form="{{ $formId }}" class="rounded-lg bg-accent px-3 py-1.5 text-sm font-semibold text-white hover:bg-accent-600">
                                Save
                            </button>
                            <form id="{{ $formId }}" method="POST" action="{{ lroute('marketing.admin.country-clearance.update', ['clearance' => $clearance->id]) }}">
                                @csrf
                                @method('PUT')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
