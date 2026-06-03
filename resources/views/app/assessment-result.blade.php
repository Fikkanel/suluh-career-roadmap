<x-layouts.app title="Hasil Asesmen">
    <x-slot:heading>Rekomendasi Karir Terkunci Untukmu</x-slot:heading>

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
                    <div class="text-sm narrative-container" style="line-height:1.8;color:var(--fg);">
                        {!! nl2br(preg_replace('/^\*\s*(.*?)$/m', '&bull; $1', preg_replace('/\*\*(.*?)\*\*/', '<b>$1</b>', e($narrative)))) !!}
                    </div>
                </div>
            @endif

            @php
                $hasTransitionPath = collect($recommendations)->contains(fn($r) => !($r['is_major_match'] ?? false));
            @endphp

            @if($hasTransitionPath)
                {{-- Pivot / Transition Path Notice --}}
                <div class="card mb-5" style="background:var(--warning-soft, #fef9c3);border-left:3px solid var(--warning, #ca8a04);padding:1rem 1.25rem;">
                    <p class="text-sm font-semibold mb-1" style="color:var(--warning, #ca8a04);">🔀 Jalur Transisi Karir Tersedia</p>
                    <p class="text-sm" style="color:var(--fg);line-height:1.65;">
                        Beberapa rekomendasi di bawah mengarah ke bidang yang berbeda dari jurusan kuliahmu (<strong>{{ auth()->user()->major }}</strong>).
                        Rekomendasi ini didasarkan pada <strong>pola minat psikologis dan kepribadianmu</strong>. Keterampilan yang telah kamu peroleh dari jurusanmu tetap bernilai tinggi dan dapat ditransfer ke bidang baru tersebut!
                    </p>
                </div>
            @else
                <p class="text-sm mb-4" style="color:var(--muted);line-height:1.65;max-width:40rem;">
                    Berdasarkan isian kuesioner asesmen dan jurusan kuliahmu (<strong>{{ auth()->user()->major }}</strong>), sistem telah menentukan beberapa rekomendasi karir berikut yang paling selaras untukmu.
                </p>
            @endif

            <div class="card card-accent-left mb-6" style="background:var(--accent-soft);border-left-color:var(--accent);">
                <p class="text-sm font-semibold mb-1" style="color:var(--accent);">💡 Langkah selanjutnya:</p>
                <p class="text-sm" style="color:var(--fg);line-height:1.6;">
                    Klik <strong>"Pelajari lebih lanjut"</strong> pada salah satu kartu karir di bawah untuk melihat detail kurikulum skill yang dibutuhkan. 
                    Setelah yakin, pilih karir tersebut untuk mengaktifkan roadmap pembelajaran personalisasimu.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            @forelse($recommendations as $rec)
                <x-career-card
                    :career="$rec"
                    :matchPercent="$rec['matchPercent'] ?? null"
                    :reasons="$rec['reasons'] ?? []"
                    :cautions="$rec['cautions'] ?? []"
                    :isMajorMatch="$rec['is_major_match'] ?? false"
                    :detailUrl="route('career.detail', $rec['id'])" />
            @empty
                <div class="col-span-3 text-center py-6">
                    <p class="text-sm" style="color:var(--muted);">Tidak ada rekomendasi karir yang ditemukan.</p>
                </div>
            @endforelse
        </div>

        <div class="card py-8" style="text-align:center;">
            <div style="width:2.5rem;height:2.5rem;background:var(--bg);border:1px solid var(--border);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <h3 class="font-semibold mb-2">Butuh waktu untuk mempertimbangkan?</h3>
            <p class="text-sm mb-5" style="color:var(--muted);max-width:28rem;margin-left:auto;margin-right:auto;line-height:1.6;">
                Hasil ini tersimpan. Kamu bisa kembali kapan saja, baca detail rekomendasi karirmu,
                dan memutuskan saat kamu siap.
            </p>
            <a href="{{ route('dashboard') }}" class="btn btn-ghost">Kembali ke Dashboard</a>
        </div>
    </div>
</x-layouts.app>
