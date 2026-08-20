@extends('layouts.dashboard')

@section('title', 'Edit — '.$profile->name.' — divin.ai')

@section('content')
    <div class="mx-auto max-w-3xl space-y-8">
        <h1 class="text-2xl font-bold text-ink-900">Edit {{ $profile->name }}</h1>

        @if ($errors->any())
            <div class="rounded-lg bg-danger/10 px-4 py-3 text-sm text-danger">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Basic details + hours --}}
        <form method="POST" action="{{ lroute('marketing.dashboard.update', ['profile' => $profile->slug]) }}"
              class="rounded-xl border border-ink-200 bg-white p-6">
            @csrf
            @method('PUT')

            <h2 class="font-semibold text-ink-900">Details</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-medium text-ink-700">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $profile->name) }}" required
                           class="mt-1.5 block w-full rounded-lg border-ink-200 bg-white px-4 py-2.5 text-ink-900 focus:border-accent focus:ring-accent">
                </div>
                <div>
                    <label for="category" class="block text-sm font-medium text-ink-700">Category</label>
                    <input type="text" name="category" id="category" value="{{ old('category', $profile->category) }}" required
                           class="mt-1.5 block w-full rounded-lg border-ink-200 bg-white px-4 py-2.5 text-ink-900 focus:border-accent focus:ring-accent">
                </div>
            </div>

            <div class="mt-4">
                <label for="description_short" class="block text-sm font-medium text-ink-700">Short description</label>
                <textarea name="description_short" id="description_short" rows="2" required
                          class="mt-1.5 block w-full rounded-lg border-ink-200 bg-white px-4 py-2.5 text-ink-900 focus:border-accent focus:ring-accent">{{ old('description_short', $profile->description_short) }}</textarea>
            </div>

            <div class="mt-4">
                <label for="description_long" class="block text-sm font-medium text-ink-700">Full description (optional)</label>
                <textarea name="description_long" id="description_long" rows="4"
                          class="mt-1.5 block w-full rounded-lg border-ink-200 bg-white px-4 py-2.5 text-ink-900 focus:border-accent focus:ring-accent">{{ old('description_long', $profile->description_long) }}</textarea>
            </div>

            <div class="mt-4 grid gap-4 sm:grid-cols-3">
                <div>
                    <label for="phone" class="block text-sm font-medium text-ink-700">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $profile->phone) }}"
                           class="mt-1.5 block w-full rounded-lg border-ink-200 bg-white px-4 py-2.5 text-ink-900 focus:border-accent focus:ring-accent">
                </div>
                <div>
                    <label for="public_email" class="block text-sm font-medium text-ink-700">Public email</label>
                    <input type="email" name="public_email" id="public_email" value="{{ old('public_email', $profile->public_email) }}"
                           class="mt-1.5 block w-full rounded-lg border-ink-200 bg-white px-4 py-2.5 text-ink-900 focus:border-accent focus:ring-accent">
                </div>
                <div>
                    <label for="price_range" class="block text-sm font-medium text-ink-700">Price range</label>
                    <input type="text" name="price_range" id="price_range" value="{{ old('price_range', $profile->price_range) }}" placeholder="$$"
                           class="mt-1.5 block w-full rounded-lg border-ink-200 bg-white px-4 py-2.5 text-ink-900 focus:border-accent focus:ring-accent">
                </div>
            </div>

            <div class="mt-4">
                <label for="website" class="block text-sm font-medium text-ink-700">Website</label>
                <input type="url" name="website" id="website" value="{{ old('website', $profile->website) }}" placeholder="https://"
                       class="mt-1.5 block w-full rounded-lg border-ink-200 bg-white px-4 py-2.5 text-ink-900 focus:border-accent focus:ring-accent">
            </div>

            <h2 class="mt-8 font-semibold text-ink-900">Hours</h2>
            <div class="mt-4 space-y-3">
                @foreach ($days as $day)
                    @php $entry = $profile->hours[$day] ?? null; $isClosed = ! is_array($entry); @endphp
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="w-24 shrink-0 text-sm font-medium text-ink-700 capitalize">{{ $day }}</span>
                        <label class="flex items-center gap-1.5 text-sm text-ink-600">
                            <input type="checkbox" name="hours[{{ $day }}][closed]" value="1" {{ $isClosed ? 'checked' : '' }}
                                   class="rounded border-ink-300 text-accent focus:ring-accent">
                            Closed
                        </label>
                        <input type="time" name="hours[{{ $day }}][open]" value="{{ $isClosed ? '' : $entry['open'] }}"
                               class="rounded-lg border-ink-200 bg-white px-3 py-1.5 text-sm text-ink-900 focus:border-accent focus:ring-accent">
                        <span class="text-ink-400">to</span>
                        <input type="time" name="hours[{{ $day }}][close]" value="{{ $isClosed ? '' : $entry['close'] }}"
                               class="rounded-lg border-ink-200 bg-white px-3 py-1.5 text-sm text-ink-900 focus:border-accent focus:ring-accent">
                    </div>
                @endforeach
            </div>

            <button type="submit"
                    class="mt-6 rounded-lg bg-accent px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-accent-600">
                Save changes
            </button>
        </form>

        {{-- Services --}}
        <div class="rounded-xl border border-ink-200 bg-white p-6">
            <h2 class="font-semibold text-ink-900">Services</h2>

            @if ($profile->services->isNotEmpty())
                <div class="mt-4 space-y-3">
                    @foreach ($profile->services as $service)
                        <div class="flex items-center justify-between rounded-lg border border-ink-100 p-3">
                            <div>
                                <p class="text-sm font-medium text-ink-900">{{ $service->name }}</p>
                                @if ($service->description)
                                    <p class="text-xs text-ink-500">{{ $service->description }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-3">
                                @if ($service->price)
                                    <span class="text-sm text-ink-600">{{ $service->price }}</span>
                                @endif
                                <form method="POST" action="{{ lroute('marketing.dashboard.services.destroy', ['profile' => $profile->slug, 'service' => $service->id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-danger hover:underline">Remove</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ lroute('marketing.dashboard.services.store', ['profile' => $profile->slug]) }}"
                  class="mt-4 grid gap-3 sm:grid-cols-4">
                @csrf
                <input type="text" name="name" placeholder="Service name" required
                       class="rounded-lg border-ink-200 bg-white px-3 py-2 text-sm text-ink-900 focus:border-accent focus:ring-accent sm:col-span-2">
                <input type="text" name="price" placeholder="Price (optional)"
                       class="rounded-lg border-ink-200 bg-white px-3 py-2 text-sm text-ink-900 focus:border-accent focus:ring-accent">
                <button type="submit" class="rounded-lg border border-ink-300 px-3 py-2 text-sm font-semibold text-ink-700 hover:border-accent hover:text-accent">
                    Add service
                </button>
            </form>
        </div>

        {{-- Images --}}
        <div class="rounded-xl border border-ink-200 bg-white p-6">
            <h2 class="font-semibold text-ink-900">Images</h2>

            @if ($profile->images->isNotEmpty())
                <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-4">
                    @foreach ($profile->images as $image)
                        <div class="relative">
                            <img src="{{ $image->url }}" alt="{{ $image->alt_text }}" class="aspect-square w-full rounded-lg object-cover">
                            <form method="POST" action="{{ lroute('marketing.dashboard.images.destroy', ['profile' => $profile->slug, 'image' => $image->id]) }}" class="mt-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full text-xs font-medium text-danger hover:underline">Remove</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ lroute('marketing.dashboard.images.store', ['profile' => $profile->slug]) }}"
                  enctype="multipart/form-data" class="mt-4 flex flex-wrap items-center gap-3">
                @csrf
                <input type="file" name="image" accept="image/*" required
                       class="text-sm text-ink-600 file:mr-3 file:rounded-lg file:border-0 file:bg-ink-100 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-ink-700">
                <input type="text" name="alt_text" placeholder="Description (optional)"
                       class="rounded-lg border-ink-200 bg-white px-3 py-2 text-sm text-ink-900 focus:border-accent focus:ring-accent">
                <button type="submit" class="rounded-lg border border-ink-300 px-3 py-2 text-sm font-semibold text-ink-700 hover:border-accent hover:text-accent">
                    Upload
                </button>
            </form>
        </div>
    </div>
@endsection
