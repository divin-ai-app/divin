<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="color-scheme" content="light">

    <title>@yield('title', 'Dashboard — divin.ai')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-ink-50 font-sans text-ink-800 antialiased">
    <div class="flex min-h-screen">
        <aside class="hidden w-64 shrink-0 border-r border-ink-100 bg-white lg:block">
            <div class="flex h-16 items-center border-b border-ink-100 px-6">
                <a href="{{ lroute('marketing.dashboard.index') }}" class="text-lg font-bold tracking-tight text-ink-900">
                    divin<span class="text-accent">.ai</span>
                </a>
            </div>

            @isset($profile)
                <nav class="space-y-1 px-3 py-6">
                    @php
                        $navItems = [
                            ['route' => 'marketing.dashboard.overview', 'label' => 'Overview'],
                            ['route' => 'marketing.dashboard.edit', 'label' => 'Edit profile'],
                            ['route' => 'marketing.dashboard.freshness', 'label' => 'Freshness report'],
                            ['route' => 'marketing.dashboard.crawler-activity', 'label' => 'AI crawler activity'],
                            ['route' => 'marketing.dashboard.billing', 'label' => 'Plan & billing'],
                            ['route' => 'marketing.dashboard.settings', 'label' => 'Settings'],
                        ];
                    @endphp
                    @foreach ($navItems as $item)
                        <a href="{{ lroute($item['route'], ['profile' => $profile->slug]) }}"
                           class="block rounded-lg px-3 py-2 text-sm font-medium transition {{ request()->routeIs($item['route']) ? 'bg-accent/10 text-accent-700' : 'text-ink-600 hover:bg-ink-50 hover:text-ink-900' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="border-t border-ink-100 px-6 py-4">
                    <p class="truncate text-sm font-medium text-ink-900">{{ $profile->name }}</p>
                    <a href="{{ lroute('marketing.dashboard.index') }}" class="text-xs font-medium text-ink-500 hover:text-ink-700">
                        Switch business
                    </a>
                </div>
            @endisset
        </aside>

        <div class="flex-1">
            <header class="flex h-16 items-center justify-between border-b border-ink-100 bg-white px-4 lg:px-8">
                <a href="{{ lroute('marketing.dashboard.index') }}" class="text-lg font-bold tracking-tight text-ink-900 lg:hidden">
                    divin<span class="text-accent">.ai</span>
                </a>
                <span class="hidden text-sm text-ink-500 lg:block">
                    Signed in as {{ auth()->user()->email }}
                </span>
                <div class="flex items-center gap-4">
                    <a href="{{ lroute('marketing.home') }}" class="text-sm font-medium text-ink-500 hover:text-ink-900">
                        View site
                    </a>
                    <form method="POST" action="{{ lroute('marketing.logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-ink-500 hover:text-ink-900">
                            Sign out
                        </button>
                    </form>
                </div>
            </header>

            <main class="px-4 py-8 lg:px-8">
                @if (session('status'))
                    <div class="mb-6 rounded-lg bg-success/10 px-4 py-3 text-sm font-medium text-success">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
