@extends('layouts.admin')

@section('title', 'Claim review — divin.ai admin')

@section('content')
    <h1 class="text-2xl font-bold text-ink-900">Claim review</h1>
    <p class="mt-1 text-sm text-ink-500">
        Claims that need manual review — mostly the document-upload fallback, for businesses without an on-file public email to OTP against.
    </p>

    <div class="mt-6 space-y-4">
        @forelse ($claimRequests as $claimRequest)
            <div class="rounded-xl border border-ink-200 bg-white p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <a href="{{ lroute('marketing.admin.profiles.show', ['profile' => $claimRequest->profile->slug]) }}" class="font-semibold text-ink-900 hover:text-accent">
                            {{ $claimRequest->profile->name }}
                        </a>
                        <p class="mt-1 text-sm text-ink-500">
                            Requested by {{ $claimRequest->requester->email }} &middot; {{ $claimRequest->created_at->format('d M Y, H:i') }}
                        </p>
                        <p class="mt-1 text-xs font-medium uppercase tracking-wide text-ink-400">
                            {{ str_replace('_', ' ', $claimRequest->verification_method->value) }} &middot; {{ str_replace('_', ' ', $claimRequest->status->value) }}
                        </p>
                        @if ($claimRequest->review_notes)
                            <p class="mt-3 rounded-lg bg-ink-50 px-3 py-2 text-sm text-ink-700">{{ $claimRequest->review_notes }}</p>
                        @endif
                    </div>

                    <div class="flex shrink-0 gap-2">
                        <form method="POST" action="{{ lroute('marketing.admin.claims.approve', ['claimRequest' => $claimRequest->id]) }}">
                            @csrf
                            <button type="submit" class="rounded-lg bg-success px-4 py-2 text-sm font-semibold text-white hover:opacity-90">
                                Approve
                            </button>
                        </form>

                        <details class="relative">
                            <summary class="cursor-pointer list-none rounded-lg border border-danger px-4 py-2 text-sm font-semibold text-danger hover:bg-danger/5">
                                Reject
                            </summary>
                            <form method="POST" action="{{ lroute('marketing.admin.claims.reject', ['claimRequest' => $claimRequest->id]) }}"
                                  class="absolute right-0 z-10 mt-2 w-72 space-y-2 rounded-lg border border-ink-200 bg-white p-4 shadow-lg">
                                @csrf
                                <label for="review_notes_{{ $claimRequest->id }}" class="block text-xs font-medium text-ink-500">Reason (required)</label>
                                <textarea name="review_notes" id="review_notes_{{ $claimRequest->id }}" rows="3" required
                                          class="block w-full rounded-lg border border-ink-200 bg-white px-3 py-2 text-sm text-ink-900 focus:border-accent focus:ring-accent"></textarea>
                                <button type="submit" class="w-full rounded-lg bg-danger px-3 py-2 text-sm font-semibold text-white hover:opacity-90">
                                    Confirm reject
                                </button>
                            </form>
                        </details>
                    </div>
                </div>
            </div>
        @empty
            <p class="rounded-xl border border-dashed border-ink-200 bg-white p-8 text-center text-sm text-ink-400">
                Nothing pending review.
            </p>
        @endforelse
    </div>

    <div class="mt-6">{{ $claimRequests->links() }}</div>
@endsection
