@extends('layouts.marketing')

@section('title', 'Contact — divin.ai')
@section('description', 'Get in touch with the divin.ai team.')

@section('content')
    <section class="bg-ink-950 py-section-y">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-white sm:text-5xl">Contact us</h1>
            <p class="mt-4 text-lg text-ink-300">Questions about claiming a profile, pricing, or anything else.</p>
        </div>
    </section>

    <section class="bg-white py-section-y">
        <div class="mx-auto max-w-xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-lg bg-success/10 px-4 py-3 text-sm font-medium text-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ lroute('marketing.contact.submit') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-ink-700">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="mt-1.5 block w-full rounded-lg border border-ink-200 px-4 py-3 text-ink-900 focus:border-accent focus:ring-accent">
                    @error('name') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-ink-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="mt-1.5 block w-full rounded-lg border border-ink-200 px-4 py-3 text-ink-900 focus:border-accent focus:ring-accent">
                    @error('email') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-ink-700">Message</label>
                    <textarea name="message" id="message" rows="5" required
                              class="mt-1.5 block w-full rounded-lg border border-ink-200 px-4 py-3 text-ink-900 focus:border-accent focus:ring-accent">{{ old('message') }}</textarea>
                    @error('message') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                        class="w-full rounded-lg bg-accent-700 px-6 py-3.5 font-semibold text-white transition hover:bg-accent-800">
                    Send message
                </button>
            </form>
        </div>
    </section>
@endsection
