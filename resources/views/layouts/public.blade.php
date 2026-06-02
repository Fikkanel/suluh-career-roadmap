<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $description ?? 'Suluh — Peta perjalanan karir personal untuk Indonesia.' }}">
    <title>{{ $title ?? 'Suluh' }} — Platform Roadmap Karir Personal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen flex flex-col" style="background:var(--bg);color:var(--fg);">

    {{-- Public Navigation --}}
    <header style="background:var(--surface);border-bottom:1px solid var(--border);">
        <nav class="max-w-5xl mx-auto page-padding flex items-center justify-between h-16">
            <a href="{{ route('landing') }}" class="flex items-center gap-2 no-underline font-semibold text-lg" style="color:var(--fg);">
                <span style="color:var(--accent);">🔦</span>
                <span style="font-family:var(--font-display);">Suluh</span>
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('impact') }}" class="nav-link text-sm">Dampak</a>
                <a href="{{ route('sunset-policy') }}" class="nav-link text-sm">Kebijakan Sunset</a>
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Mulai</a>
            </div>
        </nav>
    </header>

    {{-- Main Content --}}
    <main class="flex-1">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer style="border-top:1px solid var(--border);background:var(--surface);">
        <div class="max-w-5xl mx-auto page-padding py-8 flex flex-col md:flex-row items-center justify-between gap-4 text-sm" style="color:var(--muted);">
            <p>© {{ date('Y') }} Suluh. Platform gratis, selamanya.</p>
            <div class="flex gap-4">
                <a href="{{ route('impact') }}" class="nav-link text-sm">Dashboard Dampak</a>
                <a href="{{ route('sunset-policy') }}" class="nav-link text-sm">Kebijakan Sunset</a>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
