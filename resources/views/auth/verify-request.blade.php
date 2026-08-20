@extends('layouts.marketing')

@section('title', 'Check your email — divin.ai')
@section('description', 'A sign-in link is on its way to your inbox.')

@section('content')
    <section class="bg-white py-section-y">
        <div class="mx-auto max-w-md px-4 text-center sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-ink-900">Check your email</h1>
            <p class="mt-4 text-ink-600">
                We've sent a sign-in link to your inbox. It expires in 15 minutes and can only be
                used once.
            </p>
        </div>
    </section>
@endsection
