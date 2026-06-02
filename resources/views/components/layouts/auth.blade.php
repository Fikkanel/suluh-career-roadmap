<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Masuk' }} — Suluh</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;450;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex w-full" style="background:var(--bg);height:100dvh;overflow:hidden;overflow-x:hidden;">

    {{-- Left brand panel — desktop only --}}
    <div class="hidden lg:flex flex-col justify-between w-96 shrink-0 p-10" style="background:var(--surface);border-right:1px solid var(--border);height:100dvh;overflow:hidden;">
        <a href="{{ route('landing') }}" class="flex items-center gap-2.5 no-underline" style="color:var(--fg);">
            <svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2a4 4 0 0 1 4 4c0 1.5-.5 2.8-1.3 3.8L18 22H6l3.3-12.2A6 6 0 0 1 8 6a4 4 0 0 1 4-4z"/>
                <path d="M9 22h6"/>
            </svg>
            <span style="font-family:var(--font-display);font-weight:700;font-size:1.0625rem;">Suluh</span>
        </a>
        <div>
            <p style="font-family:var(--font-display);font-size:1.375rem;font-weight:600;line-height:1.4;color:var(--fg);margin-bottom:1.25rem;">
                "Suluh tidak memilihkan jalan.<br>Ia hanya menerangi."
            </p>
            <p class="text-sm" style="color:var(--muted);line-height:1.65;">
                Platform roadmap karir personal untuk Indonesia. Gratis, jujur, dan berpihak pada pengguna.
            </p>
        </div>
        <p class="text-xs" style="color:var(--muted);">
            <a href="{{ route('sunset-policy') }}" style="color:var(--muted);" class="underline decoration-dotted">Kebijakan Sunset</a> tersedia sejak hari pertama.
        </p>
    </div>

    {{-- Right form area --}}
    <div class="flex-1 flex flex-col items-center px-4" style="padding-top:5rem;padding-bottom:3rem;height:100dvh;overflow-y:auto;">
        {{-- Mobile brand logo only --}}
        <div class="lg:hidden text-center mb-6">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 no-underline" style="color:var(--fg);">
                <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2a4 4 0 0 1 4 4c0 1.5-.5 2.8-1.3 3.8L18 22H6l3.3-12.2A6 6 0 0 1 8 6a4 4 0 0 1 4-4z"/>
                </svg>
                <span style="font-family:var(--font-display);font-weight:700;font-size:1.125rem;color:var(--accent);">Suluh</span>
            </a>
        </div>

        <div class="w-full max-w-sm">

            <div class="card card-lg">
                {{-- Page title inside card --}}
                <div class="mb-5">
                    <h1 style="font-family:var(--font-display);font-size:1.375rem;font-weight:700;color:var(--fg);margin:0 0 .25rem;">{{ $title ?? 'Masuk' }}</h1>
                    @isset($subtitle)
                        <p class="text-sm" style="color:var(--muted);">{{ $subtitle }}</p>
                    @endisset
                </div>

                {{ $slot }}
            </div>

            <p class="text-center mt-5 text-xs" style="color:var(--muted);line-height:1.55;">
                Dengan mendaftar, kamu tidak menyetujui apa yang tidak kamu baca.
                <a href="{{ route('sunset-policy') }}" class="underline decoration-dotted" style="color:var(--muted);">Kebijakan Sunset</a> kami transparan.
            </p>
        </div>
    </div>

    @livewireScripts
</body>
</html>
