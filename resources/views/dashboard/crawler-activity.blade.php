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
            <div class="mt-6 rounded-xl border border-ink-200 bg-white p-8 text-center">
                <p class="text-ink-600">
                    No crawler visits recorded yet. Bot-visit logging and the activity chart are
                    coming in a future update.
                </p>
            </div>
        @endif
    </div>
@endsection
