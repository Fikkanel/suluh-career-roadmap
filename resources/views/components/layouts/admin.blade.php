<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — {{ $title ?? 'Dashboard' }} — Suluh</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;450;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-dvh flex" style="background:var(--bg);color:var(--fg);">

    {{-- Admin Sidebar — konsisten dengan sidebar role lain --}}
    <aside class="hidden lg:flex flex-col w-60 shrink-0 sidebar-noscroll" style="background:var(--surface);border-right:1px solid var(--border);height:100dvh;overflow-y:auto;">
        <div class="px-4 py-3 border-b" style="border-color:var(--border);">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 no-underline" style="color:var(--fg);">
                <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2a4 4 0 0 1 4 4c0 1.5-.5 2.8-1.3 3.8L18 22H6l3.3-12.2A6 6 0 0 1 8 6a4 4 0 0 1 4-4z"/>
                    <path d="M9 22h6"/>
                </svg>
                <div>
                    <div style="font-family:var(--font-display);font-weight:700;font-size:1rem;letter-spacing:-.01em;">Suluh</div>
                    <div style="font-size:.6875rem;color:var(--muted);letter-spacing:.04em;text-transform:uppercase;line-height:1;">Admin Panel</div>
                </div>
            </a>
        </div>

        <nav class="flex-1 px-2 py-1 flex flex-col gap-0" aria-label="Navigasi admin">
            <p class="section-label-muted px-2 pt-2 pb-1">Manajemen</p>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.management') }}" class="nav-link {{ request()->routeIs('admin.management') ? 'active' : '' }}">
                <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2z"/><path d="M7 7h.01"/></svg>
                Manajemen Konten
            </a>
        </nav>

        <div class="px-2 py-2 border-t" style="border-color:var(--border);">
            <div class="px-2 py-1 mb-1 text-xs font-medium truncate" style="color:var(--muted);">{{ auth()->user()?->name ?? 'Admin' }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link w-full justify-start" style="background:none;border:none;cursor:pointer;">
                    <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Admin Content Area --}}
    <div class="flex-1 flex flex-col min-w-0">
        <header class="flex items-center justify-between px-6 h-14 border-b shrink-0 sticky top-0 z-30" style="background:var(--surface);border-color:var(--border);">
            <div class="flex items-center gap-3">
                <button class="lg:hidden btn btn-ghost btn-sm" aria-label="Buka menu navigasi">
                    <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <h1 class="font-semibold text-base" style="margin:0;color:var(--fg);">{{ $title ?? 'Admin Dashboard' }}</h1>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge badge-warm text-xs">Admin</span>
                <span class="text-sm" style="color:var(--muted);">{{ auth()->user()?->name ?? 'Admin' }}</span>
            </div>
        </header>
        <main class="flex-1 p-6 max-w-7xl w-full" id="main-content">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
