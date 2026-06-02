<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Masuk' }} — Suluh</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body style="min-height:100vh; display:flex; align-items:center; justify-content:center; padding:3rem 1rem; background:var(--bg);">

    <div style="width:100%; max-width:26rem;">
        {{-- Brand --}}
        <div style="text-align:center; margin-bottom:2rem;">
            <a href="{{ route('landing') }}" style="display:inline-flex; align-items:center; gap:.5rem; text-decoration:none; color:var(--fg);">
                <span style="font-size:1.75rem;">🔦</span>
                <span style="font-family:var(--font-display); font-size:1.5rem; font-weight:700; color:var(--accent);">Suluh</span>
            </a>
            @isset($subtitle)
                <p style="margin-top:.5rem; font-size:.875rem; color:var(--muted);">{{ $subtitle }}</p>
            @endisset
        </div>

        {{-- Card --}}
        <div class="card card-lg">
            {{ $slot }}
        </div>

        {{-- Privacy Note --}}
        <p style="text-align:center; margin-top:1.5rem; font-size:.75rem; color:var(--muted);">
            Dengan mendaftar, kamu tidak menyetujui apa yang tidak kamu baca.
            <a href="{{ route('sunset-policy') }}" style="text-decoration:underline;">Kebijakan Sunset</a> kami transparan.
        </p>
    </div>

    @livewireScripts
</body>
</html>
