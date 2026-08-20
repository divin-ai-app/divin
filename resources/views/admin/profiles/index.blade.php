@extends('layouts.admin')

@section('title', 'Profiles — divin.ai admin')

@section('content')
    <h1 class="text-2xl font-bold text-ink-900">Profiles</h1>
    <p class="mt-1 text-sm text-ink-500">{{ $profiles->total() }} matching profiles.</p>

    <form method="GET" class="mt-6 flex flex-wrap items-end gap-4 rounded-xl border border-ink-200 bg-white p-4">
        <div>
            <label for="q" class="block text-xs font-medium text-ink-500">Search name</label>
            <input type="text" name="q" id="q" value="{{ $filters['q'] ?? '' }}"
                   class="mt-1 w-56 rounded-lg border border-ink-200 bg-white px-3 py-2 text-sm text-ink-900 focus:border-accent focus:ring-accent">
        </div>

        <div>
            <label for="status" class="block text-xs font-medium text-ink-500">Status</label>
            <select name="status" id="status" class="mt-1 rounded-lg border border-ink-200 bg-white px-3 py-2 text-sm text-ink-900 focus:border-accent focus:ring-accent">
                <option value="">Any</option>
                @foreach (\App\Enums\ProfileStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(($filters['status'] ?? null) === $status->value)>{{ ucfirst($status->value) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="country" class="block text-xs font-medium text-ink-500">Country</label>
            <select name="country" id="country" class="mt-1 rounded-lg border border-ink-200 bg-white px-3 py-2 text-sm text-ink-900 focus:border-accent focus:ring-accent">
                <option value="">Any</option>
                @foreach ($countries as $country)
                    <option value="{{ $country }}" @selected(($filters['country'] ?? null) === $country)>{{ $country }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="industry" class="block text-xs font-medium text-ink-500">Industry</label>
            <select name="industry" id="industry" class="mt-1 rounded-lg border border-ink-200 bg-white px-3 py-2 text-sm text-ink-900 focus:border-accent focus:ring-accent">
                <option value="">Any</option>
                @foreach (\App\Enums\Industry::cases() as $industry)
                    <option value="{{ $industry->value }}" @selected(($filters['industry'] ?? null) === $industry->value)>{{ $industry->label() }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="rounded-lg bg-accent px-4 py-2 text-sm font-semibold text-white hover:bg-accent-600">
            Filter
        </button>
        @if (array_filter($filters))
            <a href="{{ lroute('marketing.admin.profiles.index') }}" class="text-sm font-medium text-ink-400 hover:text-ink-700">Clear</a>
        @endif
    </form>

    <div class="mt-6 overflow-x-auto rounded-xl border border-ink-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-ink-100 bg-ink-50 text-xs font-semibold uppercase tracking-wide text-ink-500">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Industry</th>
                    <th class="px-4 py-3">Country</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Claim</th>
                    <th class="px-4 py-3">Plan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @forelse ($profiles as $profile)
                    <tr class="hover:bg-ink-50">
                        <td class="px-4 py-3">
                            <a href="{{ lroute('marketing.admin.profiles.show', ['profile' => $profile->slug]) }}" class="font-medium text-ink-900 hover:text-accent">
                                {{ $profile->name }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-ink-600">{{ $profile->industry->label() }}</td>
                        <td class="px-4 py-3 text-ink-600">{{ $profile->country_code }}</td>
                        <td class="px-4 py-3 text-ink-600">{{ ucfirst($profile->status->value) }}</td>
                        <td class="px-4 py-3 text-ink-600">{{ ucfirst(str_replace('_', ' ', $profile->claim_status->value)) }}</td>
                        <td class="px-4 py-3 text-ink-600">{{ ucfirst($profile->plan_tier->value) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-ink-400">No profiles match these filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $profiles->links() }}</div>
@endsection
