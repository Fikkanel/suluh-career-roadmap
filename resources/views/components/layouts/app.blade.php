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
    @php
        $hasCareer = auth()->check() && auth()->user()->current_career_id;
    @endphp

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
                @php
                    $lockIcon = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="margin-left:auto;opacity:0.6;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>';
                @endphp
                <p class="section-label-muted px-2 pt-2 pb-1">Perjalananku</p>
                
                @if($hasCareer)
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
                @else
                    <a href="{{ route('dashboard') }}" class="nav-link" style="opacity: 0.5; display: flex; align-items: center;" title="Lengkapi kuesioner & pilih karir untuk membuka">
                        <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        <span style="opacity: 0.85;">Dashboard</span>
                        {!! $lockIcon !!}
                    </a>
                    <a href="{{ route('roadmap') }}" class="nav-link" style="opacity: 0.5; display: flex; align-items: center;" title="Lengkapi kuesioner & pilih karir untuk membuka">
                        <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h2l4 12h6l4-12h2"/><path d="M7 12h10"/></svg>
                        <span style="opacity: 0.85;">Roadmap</span>
                        {!! $lockIcon !!}
                    </a>
                    <a href="{{ route('skill-progress') }}" class="nav-link" style="opacity: 0.5; display: flex; align-items: center;" title="Lengkapi kuesioner & pilih karir untuk membuka">
                        <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                        <span style="opacity: 0.85;">Progress Skill</span>
                        {!! $lockIcon !!}
                    </a>
                @endif

                <a href="{{ route('assessment') }}" class="nav-link {{ request()->routeIs('assessment*') ? 'active' : '' }}">
                    <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6m-6 4h4"/></svg>
                    Asesmen Karir
                </a>

                <p class="section-label-muted px-2 pt-3 pb-1">Riwayat &amp; Data</p>
                @if($hasCareer)
                    <a href="{{ route('archive') }}" class="nav-link {{ request()->routeIs('archive') ? 'active' : '' }}">
                        <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5" rx="1"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                        Arsip Perjalanan
                    </a>
                    <a href="{{ route('export') }}" class="nav-link {{ request()->routeIs('export') ? 'active' : '' }}">
                        <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Ekspor Data
                    </a>
                @else
                    <a href="{{ route('archive') }}" class="nav-link" style="opacity: 0.5; display: flex; align-items: center;" title="Lengkapi kuesioner & pilih karir untuk membuka">
                        <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5" rx="1"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                        <span style="opacity: 0.85;">Arsip Perjalanan</span>
                        {!! $lockIcon !!}
                    </a>
                    <a href="{{ route('export') }}" class="nav-link" style="opacity: 0.5; display: flex; align-items: center;" title="Lengkapi kuesioner & pilih karir untuk membuka">
                        <svg aria-hidden="true" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        <span style="opacity: 0.85;">Ekspor Data</span>
                        {!! $lockIcon !!}
                    </a>
                @endif
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
                @if($hasCareer)
                <a href="{{ route('pivot') }}" class="btn btn-ghost btn-sm hidden md:inline-flex gap-1.5" title="Ubah arah karir">
                    <svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                    Pivot
                </a>
                @endif
                
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

    <style>
        #chatbot-toggle-btn {
            box-shadow: 0 8px 24px -4px rgba(61, 122, 82, 0.35);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #chatbot-toggle-btn:hover {
            box-shadow: 0 12px 28px -2px rgba(61, 122, 82, 0.5);
            transform: translateY(-2px) scale(1.05);
        }
        #chatbot-toggle-btn:active {
            transform: translateY(0) scale(0.95);
        }
        .chatbot-pulse-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: var(--success);
            position: relative;
        }
        .chatbot-pulse-dot::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background-color: var(--success);
            border-radius: 50%;
            animation: chatbot-pulse 2s infinite ease-in-out;
        }
        @keyframes chatbot-pulse {
            0% { transform: scale(1); opacity: 0.8; }
            100% { transform: scale(3); opacity: 0; }
        }
        #chatbot-window {
            box-shadow: 0 12px 32px -4px rgba(46, 43, 37, 0.15), 0 4px 12px -2px rgba(46, 43, 37, 0.08);
            background: rgba(253, 252, 250, 0.98);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .chatbot-input-wrapper {
            transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
        }
        .chatbot-input-wrapper:focus-within {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 15%, transparent) !important;
        }
        .chatbot-msg-user {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-deep) 100%);
            color: #ffffff;
            box-shadow: 0 3px 10px -2px rgba(61, 122, 82, 0.25);
        }
        .chatbot-msg-bot {
            background: var(--surface);
            color: var(--fg);
            border: 1px solid var(--border-soft);
            box-shadow: 0 3px 10px -2px rgba(46, 43, 37, 0.04);
        }
        #chatbot-send-btn {
            transition: transform 0.2s, color 0.2s;
        }
        #chatbot-send-btn:hover:not(:disabled) {
            transform: scale(1.15);
            color: var(--accent-deep) !important;
        }
        #chatbot-send-btn:active:not(:disabled) {
            transform: scale(0.9);
        }
    </style>

    <!-- Chatbot Floating Button -->
    <button id="chatbot-toggle-btn" class="transition-all duration-300" style="position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; align-items: center; justify-content: center; width: 56px; height: 56px; background: var(--accent); border: none; border-radius: 50%; cursor: pointer;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="white" style="width: 24px; height: 24px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
        </svg>
    </button>

    <!-- Chatbot Container (Suluh Theme Style) -->
    <div id="chatbot-window" class="transition-all duration-300 transform scale-95 opacity-0 origin-bottom-right" style="position: fixed; bottom: 96px; right: 24px; z-index: 9999; width: 350px; height: 480px; border-radius: 16px; color: var(--fg); font-family: var(--font-body); display: none; flex-direction: column;">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 border-b" style="border-color: var(--border); background: linear-gradient(135deg, var(--accent-soft) 0%, color-mix(in srgb, var(--accent-soft) 90%, var(--surface)) 100%); border-top-left-radius: 16px; border-top-right-radius: 16px;">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center rounded-full" style="width: 36px; height: 36px; background: linear-gradient(135deg, var(--accent) 0%, var(--accent-deep) 100%); flex-shrink: 0;">
                    <span class="text-base" style="filter: drop-shadow(0 1px 1px rgba(0,0,0,0.15));">🤖</span>
                </div>
                <div>
                    <h4 class="text-sm font-semibold m-0" style="line-height: 1.2; color: var(--accent);">Suluh Career Bot</h4>
                    <div class="flex items-center gap-1.5 mt-0.5" style="line-height: 1;">
                        <span class="chatbot-pulse-dot"></span>
                        <span class="text-[10px]" style="color: var(--muted); font-weight: 500;">Aktif sekarang</span>
                    </div>
                </div>
            </div>
            <button id="chatbot-close-btn" class="transition-colors hover:text-[var(--accent)]" style="background: none; border: none; cursor: pointer; padding: 4px; color: var(--muted);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 18px; height: 18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Messages -->
        <div id="chatbot-messages" class="flex-1 overflow-y-auto p-4 space-y-3 flex flex-col" style="background: var(--surface-2); min-height: 0;">
            <div class="flex flex-col max-w-[80%] self-start space-y-1">
                <div class="px-3.5 py-2.5 rounded-2xl text-sm chatbot-msg-bot" style="border-bottom-left-radius: 4px; line-height: 1.45;">
                    Halo! Saya <b>Suluh Career Bot</b>. Ada yang bisa saya bantu hari ini seputar karir, jurusan kuliah, roadmap belajar, atau validasi skill-mu?
                </div>
            </div>
        </div>

        <!-- Typing Indicator -->
        <div id="chatbot-typing" class="hidden px-4 py-2 self-start max-w-[80%]">
            <div class="flex items-center gap-1.5 px-3.5 py-2.5 rounded-2xl chatbot-msg-bot" style="border-bottom-left-radius: 4px;">
                <span class="w-1.5 h-1.5 rounded-full animate-bounce" style="background: var(--accent); animation-delay: 0ms;"></span>
                <span class="w-1.5 h-1.5 rounded-full animate-bounce" style="background: var(--accent); animation-delay: 150ms;"></span>
                <span class="w-1.5 h-1.5 rounded-full animate-bounce" style="background: var(--accent); animation-delay: 300ms;"></span>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-3 border-t" style="border-color: var(--border-soft); background: var(--surface); border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
            <form id="chatbot-form" class="flex items-center gap-2 m-0" onsubmit="event.preventDefault();">
                <div class="flex-1 flex items-center rounded-full px-3.5 py-2 chatbot-input-wrapper" style="background: var(--surface-2); border: 1px solid var(--border);">
                    <input id="chatbot-input" type="text" autocomplete="off" placeholder="Kirim pesan..." class="w-full bg-transparent border-0 focus:ring-0 text-sm focus:outline-none" style="outline: none; padding: 0; margin: 0; box-shadow: none; color: var(--fg); --tw-ring-color: transparent;" />
                </div>
                <button id="chatbot-send-btn" type="submit" class="flex items-center justify-center disabled:opacity-30 disabled:cursor-not-allowed transition-colors" style="background: none; border: none; cursor: pointer; padding: 6px; color: var(--accent);" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 18px; height: 18px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('chatbot-toggle-btn');
            const chatbotWindow = document.getElementById('chatbot-window');
            const closeBtn = document.getElementById('chatbot-close-btn');
            const messagesArea = document.getElementById('chatbot-messages');
            const inputField = document.getElementById('chatbot-input');
            const sendBtn = document.getElementById('chatbot-send-btn');
            const form = document.getElementById('chatbot-form');
            const typingIndicator = document.getElementById('chatbot-typing');

            let chatHistory = [];

            function scrollMessagesToBottom() {
                messagesArea.scrollTop = messagesArea.scrollHeight;
            }

            function openChat() {
                chatbotWindow.style.display = 'flex';
                chatbotWindow.classList.remove('hidden');
                setTimeout(() => {
                    chatbotWindow.classList.remove('scale-95', 'opacity-0');
                    chatbotWindow.classList.add('scale-100', 'opacity-100');
                }, 10);
                scrollMessagesToBottom();
            }

            function closeChat() {
                chatbotWindow.classList.remove('scale-100', 'opacity-100');
                chatbotWindow.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    if (chatbotWindow.classList.contains('opacity-0')) {
                        chatbotWindow.style.display = 'none';
                        chatbotWindow.classList.add('hidden');
                    }
                }, 300);
            }

            toggleBtn.addEventListener('click', () => {
                if (chatbotWindow.classList.contains('hidden') || chatbotWindow.style.display === 'none') {
                    openChat();
                } else {
                    closeChat();
                }
            });

            closeBtn.addEventListener('click', closeChat);

            inputField.addEventListener('input', () => {
                sendBtn.disabled = inputField.value.trim().length === 0;
            });

            function formatMessage(text) {
                const temp = document.createElement('div');
                temp.textContent = text;
                let safeText = temp.innerHTML;
                safeText = safeText.replace(/\n/g, '<br>');
                safeText = safeText.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');
                return safeText;
            }

            function appendMessage(sender, text) {
                const bubble = document.createElement('div');
                bubble.className = 'flex flex-col max-w-[80%] space-y-1';
                
                if (sender === 'user') {
                    bubble.className += ' self-end';
                    bubble.innerHTML = `
                        <div class="px-3.5 py-2.5 rounded-2xl text-sm chatbot-msg-user" style="border-bottom-right-radius: 4px; line-height: 1.45;">
                            ${formatMessage(text)}
                        </div>
                    `;
                } else {
                    bubble.className += ' self-start';
                    bubble.innerHTML = `
                        <div class="px-3.5 py-2.5 rounded-2xl text-sm chatbot-msg-bot" style="border-bottom-left-radius: 4px; line-height: 1.45;">
                            ${formatMessage(text)}
                        </div>
                    `;
                }
                messagesArea.appendChild(bubble);
                scrollMessagesToBottom();
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const text = inputField.value.trim();
                if (!text) return;

                // Append user message
                appendMessage('user', text);
                inputField.value = '';
                sendBtn.disabled = true;

                // Show typing indicator
                typingIndicator.classList.remove('hidden');
                scrollMessagesToBottom();

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const response = await fetch('{{ route("chatbot.message") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            message: text,
                            history: chatHistory
                        })
                    });

                    // Hide typing indicator
                    typingIndicator.classList.add('hidden');

                    if (response.ok) {
                        const data = await response.json();
                        appendMessage('bot', data.reply);
                        
                        // Push to local history
                        chatHistory.push({ sender: 'user', text: text });
                        chatHistory.push({ sender: 'bot', text: data.reply });
                    } else {
                        appendMessage('bot', 'Maaf, terjadi kesalahan saat menghubungi asisten.');
                    }
                } catch (error) {
                    typingIndicator.classList.add('hidden');
                    appendMessage('bot', 'Koneksi gagal. Silakan periksa jaringan Anda.');
                }
            });
        });
    </script>

    @livewireScripts
</body>
</html>
