<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — {{ $title ?? 'Dashboard' }} — Suluh</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen flex" style="background:var(--bg);color:var(--fg);">

    {{-- Admin Sidebar — konsisten dengan sidebar role lain --}}
    <aside class="hidden lg:flex flex-col w-56 shrink-0" style="background:var(--surface);border-right:1px solid var(--border);min-height:100vh;">
        <div class="p-5 border-b" style="border-color:var(--border);">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 no-underline" style="color:var(--fg);">
                <span>🔦</span>
                <span style="font-family:var(--font-display);font-weight:700;color:var(--accent);">Suluh</span>
            </a>
            <span class="text-xs mt-1 block" style="color:var(--muted);padding-left:1.625rem;">Admin Panel</span>
        </div>
        <nav class="flex-1 p-3 flex flex-col gap-1">
            <a href="{{ route('admin.dashboard') }}"  class="nav-link {{ request()->routeIs('admin.dashboard')  ? 'active' : '' }}">📊 Dashboard</a>
            <a href="{{ route('admin.management') }}" class="nav-link {{ request()->routeIs('admin.management') ? 'active' : '' }}">⚙️ Manajemen</a>
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

    {{-- Admin Content Area --}}
    <div class="flex-1 flex flex-col min-w-0">
        <header class="flex items-center justify-between px-6 h-14 border-b shrink-0" style="background:var(--surface);border-color:var(--border);">
            <div class="flex items-center gap-3">
                <button class="lg:hidden btn btn-ghost btn-sm" aria-label="Menu">☰</button>
                @isset($heading)
                    <h1 class="text-base font-semibold" style="margin:0;">{{ $heading }}</h1>
                @else
                    <h1 class="text-base font-semibold" style="margin:0;">{{ $title ?? 'Admin Dashboard' }}</h1>
                @endisset
            </div>
            <span class="text-sm" style="color:var(--muted);">{{ auth()->user()?->name ?? 'Admin' }}</span>
        </header>
        <main class="flex-1 page-padding py-8 max-w-5xl w-full mx-auto">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
