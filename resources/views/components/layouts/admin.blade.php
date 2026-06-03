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
<body class="h-dvh flex overflow-hidden w-full" style="background:var(--bg);color:var(--fg);overflow-x:hidden;">

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
            <a href="{{ route('admin.careers.index') }}" class="nav-link {{ request()->routeIs('admin.careers.*') ? 'active' : '' }}">
                <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                Kelola Karir
            </a>
            <a href="{{ route('admin.questions.index') }}" class="nav-link {{ request()->routeIs('admin.questions.*') ? 'active' : '' }}">
                <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Kelola Asesmen
            </a>
            <a href="{{ route('admin.ethics.index') }}" class="nav-link {{ request()->routeIs('admin.ethics.*') ? 'active' : '' }}">
                <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Komite Etika
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Kelola Pengguna
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
    <div class="flex-1 flex flex-col min-w-0 h-dvh relative" style="overflow-x:hidden;">
        <header class="absolute top-0 left-0 w-full flex items-center justify-between px-6 h-14 border-b shrink-0 z-30" style="background:var(--surface);border-color:var(--border);">
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
        
        <div class="flex-1 overflow-y-auto w-full" id="admin-scroll-area">
            {{-- Spacer setinggi header agar konten tidak tertutup saat awal --}}
            <div style="height: 3.5rem; flex-shrink: 0;"></div>
            
            <main class="flex-1 p-6 max-w-7xl w-full mx-auto" id="main-content">
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('modals')
    @livewireScripts
</body>
</html>
