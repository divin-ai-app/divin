@extends('layouts.marketing')

@section('title', 'Choose a plan — '.$profile->name.' — divin.ai')

@section('content')
    <section class="bg-ink-950 py-section-y-sm">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <p class="text-sm font-semibold tracking-wide text-accent uppercase">{{ $profile->name }} is verified</p>
            <h1 class="mt-2 text-4xl font-bold text-white">Choose a plan</h1>
            <p class="mt-3 text-ink-300">Billed annually only. Cancel anytime before renewal.</p>
        </div>
    </section>

    <section class="bg-white py-section-y">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            @error('tier')
                <div class="mb-8 rounded-lg bg-danger/10 px-4 py-3 text-center text-sm font-medium text-danger">
                    {{ $message }}
                </div>
            @enderror

            <div class="grid gap-8 lg:grid-cols-2">
                <form method="POST" action="{{ lroute('marketing.claim.checkout', ['profile' => $profile->slug]) }}"
                      class="rounded-2xl border border-ink-200 p-10">
                    @csrf
                    <input type="hidden" name="tier" value="registered">
                    <h2 class="text-xl font-bold text-ink-900">Registered</h2>
                    <p class="mt-2 text-4xl font-bold text-ink-900">US$1.99<span class="text-base font-normal text-ink-500">/mo equiv.</span></p>
                    <p class="mt-1 text-sm text-ink-500">Billed annually — US$23.88/year</p>
                    <ul class="mt-8 space-y-3 text-sm text-ink-600">
                        <li class="flex gap-2"><span class="text-success">&check;</span> Full editor: description, hours, services, images</li>
                        <li class="flex gap-2"><span class="text-success">&check;</span> Published on your public divin.ai page</li>
                        <li class="flex gap-2"><span class="text-success">&check;</span> AI crawler visit activity dashboard</li>
                    </ul>
                    <button type="submit"
                            class="mt-8 w-full rounded-lg border border-ink-300 px-6 py-3 font-semibold text-ink-900 transition hover:border-accent hover:text-accent">
                        Choose Registered
                    </button>
                </form>

                <form method="POST" action="{{ lroute('marketing.claim.checkout', ['profile' => $profile->slug]) }}"
                      class="rounded-2xl border-2 border-accent p-10">
                    @csrf
                    <input type="hidden" name="tier" value="managed">
                    <span class="rounded-full bg-accent/10 px-3 py-1 text-xs font-semibold text-accent">Most complete</span>
                    <h2 class="mt-3 text-xl font-bold text-ink-900">Managed</h2>
                    <p class="mt-2 text-4xl font-bold text-ink-900">US$4.99<span class="text-base font-normal text-ink-500">/mo equiv.</span></p>
                    <p class="mt-1 text-sm text-ink-500">Billed annually — US$59.88/year</p>
                    <ul class="mt-8 space-y-3 text-sm text-ink-600">
                        <li class="flex gap-2"><span class="text-success">&check;</span> Everything in Registered</li>
                        <li class="flex gap-2"><span class="text-success">&check;</span> Ongoing freshness &amp; coherence monitoring</li>
                        <li class="flex gap-2"><span class="text-success">&check;</span> Email alerts when sources drift</li>
                    </ul>
                    <button type="submit"
                            class="mt-8 w-full rounded-lg bg-accent px-6 py-3 font-semibold text-white transition hover:bg-accent-600">
                        Choose Managed
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection
