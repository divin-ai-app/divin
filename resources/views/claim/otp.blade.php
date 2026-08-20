@extends('layouts.marketing')

@section('title', 'Verify — '.$profile->name.' — divin.ai')

@section('content')
    <section class="bg-white py-section-y">
        <div class="mx-auto max-w-md px-4 sm:px-6 lg:px-8">
            <h1 class="text-center text-3xl font-bold text-ink-900">Verify {{ $profile->name }}</h1>
            <p class="mt-3 text-center text-sm text-ink-500">
                We emailed a 6-digit code to <strong>{{ $contact }}</strong>, the business's on-file
                contact. Enter it below to confirm you control this business.
            </p>

            @if (session('status'))
                <div class="mt-6 rounded-lg bg-success/10 px-4 py-3 text-center text-sm font-medium text-success">
                    {{ session('status') }}
                </div>
            @endif

            @error('code')
                <div class="mt-6 rounded-lg bg-danger/10 px-4 py-3 text-center text-sm font-medium text-danger">
                    {{ $message }}
                </div>
            @enderror

            <form method="POST" action="{{ lroute('marketing.claim.otp.verify', ['profile' => $profile->slug]) }}" class="mt-8 space-y-4">
                @csrf
                <div>
                    <label for="code" class="block text-sm font-medium text-ink-700">6-digit code</label>
                    <input type="text" name="code" id="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autofocus
                           class="mt-1.5 block w-full rounded-lg border border-ink-200 px-4 py-3 text-center text-2xl tracking-[0.5em] text-ink-900 focus:border-accent focus:ring-accent">
                </div>
                <button type="submit"
                        class="w-full rounded-lg bg-accent-700 px-6 py-3.5 font-semibold text-white transition hover:bg-accent-800">
                    Verify
                </button>
            </form>

            <form method="POST" action="{{ lroute('marketing.claim.otp.resend', ['profile' => $profile->slug]) }}" class="mt-4 text-center">
                @csrf
                <button type="submit" class="text-sm font-semibold text-accent-700 hover:text-accent-800">
                    Resend code
                </button>
            </form>
        </div>
    </section>
@endsection
