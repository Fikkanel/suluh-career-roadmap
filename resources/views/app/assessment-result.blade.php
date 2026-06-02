<x-layouts.app title="Hasil Asesmen">
    <x-slot:heading>Kemungkinan yang Layak Kamu Eksplorasi</x-slot:heading>

    <div class="max-w-4xl mx-auto">
        @if(session('pivot'))
            <div class="alert alert-neutral mb-6">
                <div class="flex gap-2.5">
                    <svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:.1rem;"><path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                    <span><strong>Perjalanan sebelumnya tidak hilang.</strong>
                    Arsip roadmap lamamu tetap tersimpan di <a href="{{ route('archive') }}">Arsip Perjalanan</a>.</span>
                </div>
            </div>
        @endif

        <div class="mb-7">
            @if(!empty($narrative))
                <div class="card mb-5 card-accent-left">
                    <p class="text-sm" style="line-height:1.7;color:var(--fg);">{{ $narrative }}</p>
                </div>
            @endif
            <p class="text-sm mb-3" style="color:var(--muted);line-height:1.65;max-width:40rem;">
                Tiga arah di bawah ini selaras dengan pola jawabanmu. Bukan ranking — bukan rekomendasi final.
                Kamu tetap memegang keputusan akhirnya.
            </p>
            <div class="card card-accent-left" style="background:var(--accent-soft);border-left-color:var(--accent);">
                <p class="text-sm font-semibold mb-1" style="color:var(--accent);">💡 Langkah selanjutnya:</p>
                <p class="text-sm" style="color:var(--fg);line-height:1.6;">
                    Klik <strong>"Pelajari lebih lanjut"</strong> pada karir yang menarik perhatianmu untuk melihat detail skill yang dibutuhkan. 
                    Setelah yakin, klik <strong>"Pilih karir ini"</strong> untuk memulai roadmap pembelajaran.
                </p>
            </div>
        </div>

        {{-- PENTING: Semua kartu ditampilkan setara. Tidak ada yang di-spotlight atau auto-select. --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            @foreach($recommendations as $rec)
                <x-career-card
                    :career="$rec"
                    :matchPercent="$rec['matchPercent']"
                    :reasons="$rec['reasons']"
                    :cautions="$rec['cautions']"
                    :detailUrl="route('career.detail', $rec['id'])" />
            @endforeach
        </div>

        <div class="card py-8" style="text-align:center;">
            <div style="width:2.5rem;height:2.5rem;background:var(--bg);border:1px solid var(--border);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <h3 class="font-semibold mb-2">Butuh waktu untuk mempertimbangkan?</h3>
            <p class="text-sm mb-5" style="color:var(--muted);max-width:28rem;margin-left:auto;margin-right:auto;line-height:1.6;">
                Hasil ini tersimpan. Kamu bisa kembali kapan saja, baca detail setiap opsi,
                dan memutuskan saat kamu siap.
            </p>
            <a href="{{ route('dashboard') }}" class="btn btn-ghost">Kembali ke Dashboard</a>
        </div>
    </div>
</x-layouts.app>
