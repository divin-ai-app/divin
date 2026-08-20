@extends('layouts.admin')

@section('title', 'Disputes — divin.ai admin')

@section('content')
    <h1 class="text-2xl font-bold text-ink-900">Disputes</h1>
    <p class="mt-1 text-sm text-ink-500">Issues reported from public profile pages — no login required to file one.</p>

    <div class="mt-6 space-y-4">
        @forelse ($disputes as $dispute)
            <div class="rounded-xl border border-ink-200 bg-white p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <a href="{{ lroute('marketing.admin.profiles.show', ['profile' => $dispute->profile->slug]) }}" class="font-semibold text-ink-900 hover:text-accent">
                            {{ $dispute->profile->name }}
                        </a>
                        <p class="mt-1 text-sm text-ink-500">
                            {{ $dispute->submitter_email }} &middot; {{ $dispute->created_at->format('d M Y, H:i') }}
                        </p>
                        <p class="mt-1 text-xs font-medium uppercase tracking-wide text-ink-500">
                            {{ str_replace('_', ' ', $dispute->type->value) }}
                        </p>
                        <p class="mt-3 max-w-xl text-sm text-ink-700">{{ $dispute->description }}</p>
                    </div>

                    <details class="relative shrink-0">
                        <summary class="cursor-pointer list-none rounded-lg bg-accent px-4 py-2 text-sm font-semibold text-white hover:bg-accent-600">
                            Resolve
                        </summary>
                        <form method="POST" action="{{ lroute('marketing.admin.disputes.resolve', ['dispute' => $dispute->id]) }}"
                              class="absolute right-0 z-10 mt-2 w-80 space-y-3 rounded-lg border border-ink-200 bg-white p-4 shadow-lg">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="status_{{ $dispute->id }}" class="block text-xs font-medium text-ink-500">Resolution</label>
                                <select name="status" id="status_{{ $dispute->id }}" required
                                        class="mt-1 block w-full rounded-lg border border-ink-200 bg-white px-3 py-2 text-sm text-ink-900 focus:border-accent focus:ring-accent">
                                    <option value="corrected">Corrected — I've fixed the listing</option>
                                    <option value="removed">Removed — unpublish this profile</option>
                                    <option value="rejected">Rejected — report isn't valid</option>
                                </select>
                            </div>

                            <div>
                                <label for="resolution_notes_{{ $dispute->id }}" class="block text-xs font-medium text-ink-500">Notes (required)</label>
                                <textarea name="resolution_notes" id="resolution_notes_{{ $dispute->id }}" rows="3" required
                                          class="mt-1 block w-full rounded-lg border border-ink-200 bg-white px-3 py-2 text-sm text-ink-900 focus:border-accent focus:ring-accent"></textarea>
                            </div>

                            <button type="submit" class="w-full rounded-lg bg-accent px-3 py-2 text-sm font-semibold text-white hover:bg-accent-600">
                                Submit resolution
                            </button>
                        </form>
                    </details>
                </div>
            </div>
        @empty
            <p class="rounded-xl border border-dashed border-ink-200 bg-white p-8 text-center text-sm text-ink-500">
                No open disputes.
            </p>
        @endforelse
    </div>

    <div class="mt-6">{{ $disputes->links() }}</div>
@endsection
