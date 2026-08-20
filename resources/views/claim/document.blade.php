@extends('layouts.marketing')

@section('title', 'Verify — '.$profile->name.' — divin.ai')

@section('content')
    <section class="bg-white py-section-y">
        <div class="mx-auto max-w-md px-4 sm:px-6 lg:px-8">
            <h1 class="text-center text-3xl font-bold text-ink-900">Verify {{ $profile->name }}</h1>
            <p class="mt-3 text-center text-sm text-ink-500">
                We don't have a public contact on file for this business yet, so a member of our
                team will review your claim manually. Tell us how you're connected to
                {{ $profile->name }} — your role, and anything that helps us confirm it (a phone
                number we can reach you on, a link to your own listing elsewhere, etc.).
            </p>

            <form method="POST" action="{{ lroute('marketing.claim.document.submit', ['profile' => $profile->slug]) }}" class="mt-8 space-y-4">
                @csrf
                <div>
                    <label for="message" class="block text-sm font-medium text-ink-700">Tell us about your connection to this business</label>
                    <textarea name="message" id="message" rows="6" required
                              class="mt-1.5 block w-full rounded-lg border border-ink-200 px-4 py-3 text-ink-900 focus:border-accent focus:ring-accent"></textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                        class="w-full rounded-lg bg-accent-700 px-6 py-3.5 font-semibold text-white transition hover:bg-accent-800">
                    Submit for review
                </button>
            </form>
        </div>
    </section>
@endsection
