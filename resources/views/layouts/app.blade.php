<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — Suluh</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen flex" style="background:var(--bg);color:var(--fg);">

    {{-- Sidebar (desktop) --}}
    <aside class="hidden lg:flex flex-col w-56 shrink-0" style="background:var(--surface);border-right:1px solid var(--border);min-height:100vh;">
        <div class="p-5 border-b" style="border-color:var(--border);">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 no-underline" style="color:var(--fg);">
                <span>🔦</span>
                <span style="font-family:var(--font-display);font-weight:700;color:var(--accent);">Suluh</span>
            </a>
        </div>
        <nav class="flex-1 p-3 flex flex-col gap-1">
            <a href="{{ route('dashboard') }}"      class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">📊 Dashboard</a>
            <a href="{{ route('roadmap') }}"         class="nav-link {{ request()->routeIs('roadmap') ? 'active' : '' }}">🗺️ Roadmap</a>
            <a href="{{ route('skill-progress') }}"  class="nav-link {{ request()->routeIs('skill-progress') ? 'active' : '' }}">📈 Progress Skill</a>
            <a href="{{ route('assessment') }}"      class="nav-link {{ request()->routeIs('assessment*') ? 'active' : '' }}">🧭 Asesmen</a>
            <a href="{{ route('archive') }}"         class="nav-link {{ request()->routeIs('archive') ? 'active' : '' }}">📚 Arsip Perjalanan</a>
            <a href="{{ route('export') }}"          class="nav-link {{ request()->routeIs('export') ? 'active' : '' }}">📤 Ekspor Data</a>
            <div class="mt-auto"></div>
            <a href="{{ route('impact') }}"          class="nav-link">🌍 Dashboard Dampak</a>
        </nav>
        <div class="p-3 border-t" style="border-color:var(--border);">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link w-full text-left" style="background:none;border:none;cursor:pointer;">
                    🚪 Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Area --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Top bar --}}
        <header class="flex items-center justify-between px-6 h-14 border-b shrink-0" style="background:var(--surface);border-color:var(--border);">
            <div class="flex items-center gap-3">
                {{-- Mobile menu button --}}
                <button class="lg:hidden btn btn-ghost btn-sm" aria-label="Menu">☰</button>
                @isset($heading)
                    <h1 class="text-base font-semibold" style="margin:0;">{{ $heading }}</h1>
                @endisset
            </div>
            <div class="flex items-center gap-2 text-sm" style="color:var(--muted);">
                <span>{{ auth()->user()?->name ?? 'Pengguna' }}</span>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 page-padding py-8 max-w-5xl w-full mx-auto">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
