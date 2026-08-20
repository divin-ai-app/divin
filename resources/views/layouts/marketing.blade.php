<!doctype html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index, follow">
    {{-- No dark theme exists yet — without this, browsers with OS/browser
         dark mode enabled render native form controls (inputs, textareas)
         with a dark background by default, regardless of any text-color
         utility class, making typed text invisible on unstyled fields. --}}
    <meta name="color-scheme" content="light">

    <title>@yield('title', 'divin.ai — Be discoverable when AI answers the question')</title>
    <meta name="description" content="@yield('description', 'divin.ai auto-generates and verifies structured business profiles so AI engines like ChatGPT, Claude, Gemini and Perplexity can find and cite your business.')">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- OpenGraph --}}
    <meta property="og:site_name" content="divin.ai">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'divin.ai — Be discoverable when AI answers the question')">
    <meta property="og:description" content="@yield('description', 'divin.ai auto-generates and verifies structured business profiles so AI engines can find and cite your business.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">

    {{-- Organization schema.org — on every page, per plan §2's
         "practice what it preaches" requirement. Built in
         App\Support\SchemaOrgBuilder, not inline — see that class's docblock
         for why (Blade corrupts literal '@context'/'@type' keys). --}}
    <script type="application/ld+json">
        {!! json_encode(\App\Support\SchemaOrgBuilder::organization(), JSON_UNESCAPED_SLASHES) !!}
    </script>

    @stack('schema')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white font-sans text-ink-800 antialiased">
    @include('partials.nav')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')
</body>
</html>
