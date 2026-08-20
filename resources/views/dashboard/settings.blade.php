@extends('layouts.dashboard')

@section('title', 'Settings — divin.ai')

@section('content')
    <div class="mx-auto max-w-md">
        <h1 class="text-2xl font-bold text-ink-900">Account settings</h1>

        <form method="POST" action="{{ lroute('marketing.dashboard.settings.update', ['profile' => $profile->slug]) }}"
              class="mt-6 space-y-4 rounded-xl border border-ink-200 bg-white p-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-ink-700">Your name</label>
                <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" required
                       class="mt-1.5 block w-full rounded-lg border border-ink-200 bg-white px-4 py-2.5 text-ink-900 focus:border-accent focus:ring-accent">
            </div>

            <div>
                <span class="block text-sm font-medium text-ink-700">Email</span>
                <p class="mt-1.5 text-sm text-ink-500">{{ auth()->user()->email }}</p>
            </div>

            <button type="submit"
                    class="rounded-lg bg-accent-700 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-accent-800">
                Save
            </button>
        </form>
    </div>
@endsection
