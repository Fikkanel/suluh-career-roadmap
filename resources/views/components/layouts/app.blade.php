<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — Suluh</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;450;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-dvh flex overflow-hidden w-full" style="background:var(--bg);color:var(--fg);overflow-x:hidden;">

    {{-- Sidebar (desktop) --}}
    <aside class="hidden lg:flex flex-col w-60 shrink-0 sidebar-noscroll" style="background:var(--surface);border-right:1px solid var(--border);height:100dvh;overflow-y:auto;">
        <div class="px-4 py-3 border-b" style="border-color:var(--border);">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 no-underline" style="color:var(--fg);">
                <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2a4 4 0 0 1 4 4c0 1.5-.5 2.8-1.3 3.8L18 22H6l3.3-12.2A6 6 0 0 1 8 6a4 4 0 0 1 4-4z"/>
                    <path d="M9 22h6"/>
                </svg>
                <span style="font-family:var(--font-display);font-weight:700;font-size:1rem;letter-spacing:-.01em;">Suluh</span>
            </a>
        </div>

        <nav class="flex-1 px-2 py-1 flex flex-col gap-0" aria-label="Navigasi utama">
            @if(auth()->check() && auth()->user()->role === 'mentor')
                <p class="section-label-muted px-2 pt-2 pb-1">Ruang Mentor</p>
                <a href="{{ route('mentor.dashboard') }}" class="nav-link {{ request()->routeIs('mentor.*') ? 'active' : '' }}">
                    <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                    Dashboard Mentor
                </a>
            @elseif(auth()->check() && auth()->user()->role === 'institution')
                <p class="section-label-muted px-2 pt-2 pb-1">Mitra Institusi</p>
                <a href="{{ route('institution.dashboard') }}" class="nav-link {{ request()->routeIs('institution.*') ? 'active' : '' }}">
                    <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                    Dashboard Analitik
                </a>
            @else
                <p class="section-label-muted px-2 pt-2 pb-1">Perjalananku</p>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('roadmap') }}" class="nav-link {{ request()->routeIs('roadmap') ? 'active' : '' }}">
                    <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h2l4 12h6l4-12h2"/><path d="M7 12h10"/></svg>
                    Roadmap
                </a>
                <a href="{{ route('skill-progress') }}" class="nav-link {{ request()->routeIs('skill-progress') ? 'active' : '' }}">
                    <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    Progress Skill
                </a>
                <a href="{{ route('assessment') }}" class="nav-link {{ request()->routeIs('assessment*') ? 'active' : '' }}">
                    <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6m-6 4h4"/></svg>
                    Asesmen Karir
                </a>

                <p class="section-label-muted px-2 pt-3 pb-1">Riwayat &amp; Data</p>
                <a href="{{ route('archive') }}" class="nav-link {{ request()->routeIs('archive') ? 'active' : '' }}">
                    <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5" rx="1"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                    Arsip Perjalanan
                </a>
                <a href="{{ route('export') }}" class="nav-link {{ request()->routeIs('export') ? 'active' : '' }}">
                    <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Ekspor Data
                </a>
            @endif

            <div class="flex-1"></div>
            <div class="mt-2 pt-2" style="border-top:1px solid var(--border);">
                <a href="{{ route('impact') }}" class="nav-link">
                    <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    Dashboard Dampak
                </a>
                <a href="{{ route('ethics') }}" class="nav-link {{ request()->routeIs('ethics') ? 'active' : '' }}">
                    <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    Komite Etika Data
                </a>
            </div>
        </nav>

        <div class="px-2 py-2 border-t" style="border-color:var(--border);">
            <div class="px-2 py-1 mb-1 text-xs font-medium truncate" style="color:var(--muted);">{{ auth()->user()?->name ?? 'Pengguna' }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link w-full justify-start" style="background:none;border:none;cursor:pointer;">
                    <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Area --}}
    <div class="flex-1 flex flex-col min-w-0 h-dvh relative" style="overflow-x:hidden;">

        {{-- Top bar --}}
        <header id="app-header" class="absolute top-0 left-0 w-full flex items-center justify-between px-6 h-14 border-b shrink-0 z-30 transition-transform duration-300 ease-in-out" style="background:var(--surface);border-color:var(--border);">
            <div class="flex items-center gap-3">
                <button class="lg:hidden btn btn-ghost btn-sm" aria-label="Buka menu navigasi">
                    <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                @isset($heading)
                    <h1 class="text-base font-semibold" style="margin:0;color:var(--fg);">{{ $heading }}</h1>
                @endisset
            </div>
            <div class="flex items-center gap-4">
                @if(!auth()->check() || auth()->user()->role !== 'mentor')
                <a href="{{ route('pivot') }}" class="btn btn-ghost btn-sm hidden md:inline-flex gap-1.5" title="Ubah arah karir">
                    <svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                    Pivot
                </a>
                
                {{-- Notification Bell --}}
                <a href="{{ route('notifications') }}" class="relative inline-flex items-center text-gray-500 hover:text-gray-900" style="color:var(--muted);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full" style="font-size:0.6rem;background:var(--error);">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </a>
                @endif
                
                <a href="{{ route('profile.settings') }}" class="text-sm font-medium hover:underline flex items-center gap-2" style="color:var(--fg);border-left:1px solid var(--border);padding-left:1rem; max-width: 250px;" title="Pengaturan Profil Publik">
                    @if(auth()->check() && auth()->user()->avatar)
                        <div style="width: 28px; height: 28px; min-width: 28px; border-radius: 50%; overflow: hidden; border: 1px solid var(--border); flex-shrink: 0;">
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    @endif
                    <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ auth()->user()?->name ?? 'Pengguna' }}</span>
                    <svg class="shrink-0" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                </a>
            </div>

        </header>

        {{-- Scrollable Area --}}
        <div class="flex-1 overflow-y-auto w-full" id="app-scroll-area">
            {{-- Spacer setinggi header agar konten tidak tertutup saat awal --}}
            <div style="height: 3.5rem; flex-shrink: 0;"></div>
            
            {{-- Page Content --}}
            <main class="page-padding py-8 max-w-5xl w-full mx-auto" id="main-content">
            @if(session('info'))
                <div class="mb-6 p-4 rounded-card" style="background:var(--surface); border-left:4px solid var(--accent); color:var(--fg);">
                    {{ session('info') }}
                </div>
            @endif
            @if(session('success'))
                <div class="mb-6 p-4 rounded-card" style="background:var(--surface); border-left:4px solid var(--success); color:var(--fg);">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 rounded-card" style="background:var(--surface); border-left:4px solid var(--error); color:var(--fg);">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scrollArea = document.getElementById('app-scroll-area');
            const header = document.getElementById('app-header');
            
            if (scrollArea && header) {
                let lastScrollY = scrollArea.scrollTop;
                
                scrollArea.addEventListener('scroll', () => {
                    const currentScrollY = scrollArea.scrollTop;
                    
                    // Kalau scroll ke bawah (melewati 60px) -> sembunyikan header
                    if (currentScrollY > lastScrollY && currentScrollY > 60) {
                        header.style.transform = 'translateY(-100%)';
                    } 
                    // Kalau scroll ke atas -> tampilkan header kembali
                    else if (currentScrollY < lastScrollY) {
                        header.style.transform = 'translateY(0)';
                    }
                    
                    lastScrollY = currentScrollY;
                });
            }
        });
    </script>

    @livewireScripts
</body>
</html>
