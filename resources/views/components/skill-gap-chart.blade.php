@props([
    'dimensions'  => [],
    'userValues'  => [],
    'targetValues'=> [],
    'variant'     => 'bar',
])

{{-- Accessibility: HTML/CSS bars + table equivalent, no chart library required --}}
<div {{ $attributes }}>
    @if($variant === 'bar')
        {{-- Bar chart view --}}
        <div class="flex flex-col gap-4">
            @foreach($dimensions as $key => $label)
                @php
                    $user   = $userValues[$key]   ?? 0;
                    $target = $targetValues[$key]  ?? 100;
                    $gap    = max(0, $target - $user);
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium">{{ $label }}</span>
                        <span class="text-xs" style="font-family:var(--font-mono);color:var(--muted);">
                            {{ $user }}% / {{ $target }}%
                        </span>
                    </div>
                    <div class="relative" style="height:10px;background:var(--bg);border-radius:999px;border:1px solid var(--border);">
                        {{-- Target bar (background) --}}
                        <div style="position:absolute;left:0;top:0;height:100%;width:{{ $target }}%;background:var(--accent-soft);border-radius:999px;"></div>
                        {{-- User bar (foreground) --}}
                        <div style="position:absolute;left:0;top:0;height:100%;width:{{ $user }}%;background:var(--accent);border-radius:999px;transition:width .4s;"></div>
                    </div>
                    @if($gap > 0)
                        <p class="text-xs mt-0.5" style="color:var(--accent-warm);">Gap: {{ $gap }}% perlu dikerjakan</p>
                    @else
                        <p class="text-xs mt-0.5" style="color:var(--success);">✓ Memenuhi standar</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- Table equivalent for accessibility --}}
    <details class="mt-4">
        <summary class="text-xs cursor-pointer" style="color:var(--muted);">Lihat sebagai tabel</summary>
        <table class="table-suluh mt-2 text-sm">
            <caption class="sr-only">Perbandingan skill kamu dengan standar industri</caption>
            <thead>
                <tr>
                    <th scope="col">Dimensi</th>
                    <th scope="col">Kamu</th>
                    <th scope="col">Standar</th>
                    <th scope="col">Gap</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dimensions as $key => $label)
                    @php
                        $u = $userValues[$key] ?? 0;
                        $t = $targetValues[$key] ?? 100;
                        $g = max(0, $t - $u);
                    @endphp
                    <tr>
                        <td>{{ $label }}</td>
                        <td style="font-family:var(--font-mono);">{{ $u }}%</td>
                        <td style="font-family:var(--font-mono);">{{ $t }}%</td>
                        <td style="font-family:var(--font-mono);color:{{ $g > 0 ? 'var(--accent-warm)' : 'var(--success)' }};">
                            {{ $g > 0 ? $g.'%' : '✓' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </details>
</div>
