@props([
    'career'       => [],
    'matchPercent' => null,
    'reasons'      => [],
    'cautions'     => [],
    'detailUrl'    => '#',
    'variant'      => 'balanced',
])

{{--
  PENTING: Semua kartu rekomendasi ditampilkan setara.
  Tidak ada kartu yang di-preselect, di-spotlight, atau ditampilkan lebih besar.
--}}
<article class="card card-hover flex flex-col"
         style="{{ $variant === 'selected' ? 'border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft);' : '' }}"
         role="article">

    {{-- Header: name + match % --}}
    <div class="mb-3">
        <h3 class="font-semibold mb-2" style="font-size:1rem;line-height:1.35;margin:0;">
            {{ is_array($career) ? ($career['name'] ?? '') : $career->name }}
        </h3>
        @if($matchPercent !== null)
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs" style="color:var(--muted);">Keselarasan</span>
                    <span class="text-xs font-semibold" style="font-family:var(--font-mono);color:var(--accent);"
                          title="Persentase kecocokan berdasarkan pola jawabanmu. Bukan nilai final.">{{ $matchPercent }}%</span>
                </div>
                <div class="match-track"><div class="match-fill" style="width:{{ $matchPercent }}%;"></div></div>
            </div>
        @endif
    </div>

    @php $desc = is_array($career) ? ($career['description'] ?? '') : $career->description; @endphp
    @if($desc)
        <p class="text-sm mb-3" style="color:var(--muted);line-height:1.55;flex:1;">{{ $desc }}</p>
    @endif

    {{-- Reasons --}}
    @if(count($reasons) > 0)
        <div class="mb-3 pt-3" style="border-top:1px solid var(--border);">
            <p class="text-xs font-semibold mb-1.5" style="color:var(--accent);">Mengapa ini mungkin selaras:</p>
            <ul class="flex flex-col gap-1" style="padding-left:.875rem;">
                @foreach($reasons as $reason)
                    <li class="text-sm" style="color:var(--fg);">{{ $reason }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Cautions --}}
    @if(count($cautions) > 0)
        <div class="mb-3">
            <p class="text-xs font-semibold mb-1.5" style="color:var(--accent-warm);">Yang perlu dipertimbangkan:</p>
            <ul class="flex flex-col gap-1" style="padding-left:.875rem;">
                @foreach($cautions as $caution)
                    <li class="text-sm" style="color:var(--muted);">{{ $caution }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-auto pt-3" style="border-top:1px solid var(--border);">
        <p class="text-xs mb-3" style="color:var(--muted);">Kamu tetap memegang keputusan akhirnya.</p>
        <a href="{{ $detailUrl }}" class="btn btn-secondary btn-sm w-full justify-center">
            Pelajari lebih lanjut
        </a>
    </div>
</article>
