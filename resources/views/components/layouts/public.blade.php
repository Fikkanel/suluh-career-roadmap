<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $description ?? 'Suluh — Peta perjalanan karir personal untuk Indonesia.' }}">
    <title>{{ $title ?? 'Suluh' }} — Platform Roadmap Karir Personal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;450;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-dvh flex flex-col overflow-hidden" style="background:var(--bg);color:var(--fg);">

    {{-- Public Navigation (Fixed, Non-bouncing) --}}
    <header class="shrink-0" style="background:var(--surface);border-bottom:1px solid var(--border);z-index:40;">
        <nav class="max-w-6xl mx-auto page-padding flex items-center justify-between h-16">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5 no-underline" style="color:var(--fg);">
                <svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2a4 4 0 0 1 4 4c0 1.5-.5 2.8-1.3 3.8L18 22H6l3.3-12.2A6 6 0 0 1 8 6a4 4 0 0 1 4-4z"/>
                    <path d="M9 22h6"/>
                </svg>
                <span style="font-family:var(--font-display);font-weight:700;font-size:1.0625rem;letter-spacing:-.01em;">Suluh</span>
            </a>
            <div class="flex items-center gap-1">
                <a href="{{ route('impact') }}" class="nav-link text-sm px-3 hidden sm:flex" style="{{ request()->routeIs('impact') ? 'font-weight:600;color:var(--accent);' : '' }}">Dampak</a>
                <a href="{{ route('sunset-policy') }}" class="nav-link text-sm px-3 hidden sm:flex" style="{{ request()->routeIs('sunset-policy') ? 'font-weight:600;color:var(--accent);' : '' }}">Kebijakan Sunset</a>
                <a href="{{ route('api-docs') }}" class="nav-link text-sm px-3 hidden sm:flex" style="{{ request()->routeIs('api-docs') ? 'font-weight:600;color:var(--accent);' : '' }}">API Docs</a>
                <div class="w-px h-5 mx-1 hidden sm:block" style="background:var(--border);"></div>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm flex items-center gap-2">
                        @if(auth()->user()->avatar)
                            <div style="width: 24px; height: 24px; min-width: 24px; border-radius: 50%; overflow: hidden; border: 1px solid var(--border); flex-shrink: 0;">
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @endif
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Mulai</a>
                @endauth
            </div>
        </nav>

    </header>

    {{-- Scrollable Area (Main Content + Footer) --}}
    <div class="flex-1 overflow-y-auto flex flex-col">
        {{-- Main Content --}}
        <main class="flex-1" id="main-content">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="shrink-0" style="border-top:1px solid var(--border);background:var(--surface);">
            <div class="max-w-6xl mx-auto page-padding py-10">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2a4 4 0 0 1 4 4c0 1.5-.5 2.8-1.3 3.8L18 22H6l3.3-12.2A6 6 0 0 1 8 6a4 4 0 0 1 4-4z"/>
                            </svg>
                            <span style="font-family:var(--font-display);font-weight:700;font-size:.9375rem;color:var(--fg);">Suluh</span>
                        </div>
                        <p class="text-sm" style="color:var(--muted);max-width:24rem;line-height:1.6;">Platform roadmap karir personal untuk Indonesia. Gratis selamanya. Datamu adalah milikmu.</p>
                    </div>
                    <div class="flex flex-col gap-2 text-sm" style="color:var(--muted);">
                        <a href="{{ route('impact') }}" class="hover:underline" style="color:var(--muted);">Dashboard Dampak</a>
                        <a href="{{ route('sunset-policy') }}" class="hover:underline" style="color:var(--muted);">Kebijakan Sunset</a>
                        <a href="{{ route('api-docs') }}" class="hover:underline" style="color:var(--muted);">API Publik Peneliti</a>
                    </div>
                </div>
                <div class="mt-8 pt-6 text-xs flex items-center justify-between" style="border-top:1px solid var(--border);color:var(--muted);">
                    <span>© {{ date('Y') }} Suluh</span>
                    <span>Data agregat publik diperbarui setiap 24 jam</span>
                </div>
            </div>
        </footer>
    </div>

    @livewireScripts
</body>
</html>
