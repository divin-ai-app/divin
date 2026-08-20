@extends('layouts.marketing')

@section('title', 'Report an issue — '.$profile->name.' — divin.ai')

@section('content')
    <section class="bg-white py-section-y">
        <div class="mx-auto max-w-md px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-ink-900">Report an issue</h1>
            <p class="mt-2 text-sm text-ink-500">Flagging something about <strong>{{ $profile->name }}</strong>.</p>

            <form method="POST" action="{{ lroute('marketing.profile.report.submit', ['profile' => $profile->slug]) }}"
                  class="mt-6 space-y-4">
                @csrf

                <div>
                    <label for="type" class="block text-sm font-medium text-ink-700">What's wrong?</label>
                    <select name="type" id="type" required
                            class="mt-1.5 block w-full rounded-lg border border-ink-200 bg-white px-4 py-2.5 text-ink-900 focus:border-accent focus:ring-accent">
                        <option value="not_my_business">This isn't my business</option>
                        <option value="incorrect_data">Some information is incorrect</option>
                        <option value="duplicate">This is a duplicate listing</option>
                        <option value="unwanted_listing">I don't want this listing published</option>
                        <option value="other">Something else</option>
                    </select>
                    @error('type') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="submitter_email" class="block text-sm font-medium text-ink-700">Your email</label>
                    <input type="email" name="submitter_email" id="submitter_email" value="{{ old('submitter_email') }}" required
                           class="mt-1.5 block w-full rounded-lg border border-ink-200 bg-white px-4 py-2.5 text-ink-900 focus:border-accent focus:ring-accent">
                    <p class="mt-1 text-xs text-ink-500">So we can follow up if we need more details.</p>
                    @error('submitter_email') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-ink-700">Details</label>
                    <textarea name="description" id="description" rows="5" required
                              class="mt-1.5 block w-full rounded-lg border border-ink-200 bg-white px-4 py-2.5 text-ink-900 focus:border-accent focus:ring-accent">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                        class="w-full rounded-lg bg-accent px-6 py-3 text-sm font-semibold text-white transition hover:bg-accent-600">
                    Submit report
                </button>
            </form>
        </div>
    </section>
@endsection
