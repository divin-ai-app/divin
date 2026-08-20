@extends('layouts.marketing')

@section('title', 'Sign in — divin.ai')
@section('description', 'Sign in to your divin.ai account.')

@section('content')
    <section class="bg-white py-section-y">
        <div class="mx-auto max-w-md px-4 sm:px-6 lg:px-8">
            <h1 class="text-center text-3xl font-bold text-ink-900">Sign in</h1>
            <p class="mt-2 text-center text-sm text-ink-500">
                We'll email you a one-time sign-in link — no password needed.
            </p>

            @error('email')
                <div class="mt-6 rounded-lg bg-danger/10 px-4 py-3 text-sm font-medium text-danger">
                    {{ $message }}
                </div>
            @enderror

            <form method="POST" action="{{ lroute('marketing.login.send') }}" class="mt-8 space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-ink-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           class="mt-1.5 block w-full rounded-lg border border-ink-200 px-4 py-3 text-ink-900 focus:border-accent focus:ring-accent">
                </div>
                <button type="submit"
                        class="w-full rounded-lg bg-accent px-6 py-3.5 font-semibold text-white transition hover:bg-accent-600">
                    Send sign-in link
                </button>
            </form>
        </div>
    </section>
@endsection
