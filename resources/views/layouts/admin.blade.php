<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="color-scheme" content="light">

    <title>@yield('title', 'Admin — divin.ai')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-ink-50 font-sans text-ink-800 antialiased">
    <div class="flex min-h-screen">
        <aside class="hidden w-64 shrink-0 border-r border-ink-100 bg-ink-950 lg:block">
            <div class="flex h-16 items-center border-b border-ink-800 px-6">
                <a href="{{ lroute('marketing.admin.dashboard') }}" class="text-lg font-bold tracking-tight text-white">
                    divin<span class="text-accent">.ai</span> <span class="text-ink-400">admin</span>
                </a>
            </div>

            <nav class="space-y-1 px-3 py-6">
                @php
                    $navItems = [
                        ['route' => 'marketing.admin.dashboard', 'label' => 'Overview'],
                        ['route' => 'marketing.admin.profiles.index', 'label' => 'Profiles'],
                        ['route' => 'marketing.admin.claims.index', 'label' => 'Claim review'],
                        ['route' => 'marketing.admin.disputes.index', 'label' => 'Disputes'],
                        ['route' => 'marketing.admin.country-clearance.index', 'label' => 'Country clearance'],
                        ['route' => 'marketing.admin.data-sources.index', 'label' => 'Data sources'],
                        ['route' => 'marketing.admin.customers.index', 'label' => 'Customers'],
                    ];
                @endphp
                @foreach ($navItems as $item)
                    <a href="{{ lroute($item['route']) }}"
                       class="block rounded-lg px-3 py-2 text-sm font-medium transition {{ request()->routeIs($item['route'].'*') ? 'bg-white/10 text-white' : 'text-ink-300 hover:bg-white/5 hover:text-white' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </aside>

        <div class="flex-1">
            <header class="flex h-16 items-center justify-between border-b border-ink-100 bg-white px-4 lg:px-8">
                <a href="{{ lroute('marketing.admin.dashboard') }}" class="text-lg font-bold tracking-tight text-ink-900 lg:hidden">
                    divin<span class="text-accent">.ai</span> admin
                </a>
                <span class="hidden text-sm text-ink-500 lg:block">
                    Signed in as {{ auth()->user()->email }} &middot; {{ ucfirst(auth()->user()->role->value) }}
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

                @if ($errors->any())
                    <div class="mb-6 rounded-lg bg-danger/10 px-4 py-3 text-sm font-medium text-danger">
                        <ul class="list-disc space-y-1 pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
