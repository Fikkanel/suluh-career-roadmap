<x-layouts.public title="Kebijakan Sunset">
    <div class="pt-8 px-6 md:px-10 lg:px-14 w-full" data-reveal>
        <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-sm font-medium no-underline" style="color:var(--muted);transition:color 0.2s;" onmouseover="this.style.color='var(--fg)'" onmouseout="this.style.color='var(--muted)'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
            Kembali ke Halaman Utama
        </a>
    </div>

    <div class="max-w-3xl mx-auto page-padding pt-6 pb-14">
        <p class="section-label mb-4" data-reveal>Kebijakan Sunset</p>
        <h1 class="mb-4" style="font-family:var(--font-display);font-size:clamp(1.625rem,4vw,2.25rem);font-weight:700;line-height:1.2;">Apa yang Terjadi Jika Platform Ini Berhenti?</h1>
        <p class="mb-10 text-base" style="color:var(--muted);line-height:1.7;max-width:38rem;">
            Kebijakan ini ditulis sejak hari pertama. Kami percaya pengguna berhak tahu apa yang akan terjadi pada data mereka, bahkan di skenario terburuk.
        </p>

        <div class="flex flex-col gap-5">
            <div class="card">
                <div class="flex items-center gap-3 mb-4">
                    <div style="width:2rem;height:2rem;background:var(--accent-soft);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </div>
                    <h2 class="font-semibold" style="font-size:.9375rem;margin:0;">Hak Ekspor Pengguna</h2>
                </div>
                <ul class="text-sm flex flex-col gap-2.5">
                    @foreach(['Kamu bisa mengekspor seluruh data milikmu kapan saja dalam format PDF dan JSON','Ekspor tersedia tanpa syarat dan tanpa batas frekuensi','Tidak ada dark pattern yang menghalangi akses ekspor'] as $item)
                    <li class="flex items-start gap-2.5">
                        <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:.175rem;"><polyline points="20 6 9 17 4 12"/></svg>
                        <span style="color:var(--fg);">{{ $item }}</span>
                    </li>
                    @endforeach
                </ul>
                <div class="mt-5">
                    <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">Ekspor datamu sekarang</a>
                </div>
            </div>

            <div class="card">
                <div class="flex items-center gap-3 mb-4">
                    <div style="width:2rem;height:2rem;background:#f5e8da;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--accent-warm)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <h2 class="font-semibold" style="font-size:.9375rem;margin:0;">Prosedur Jika Platform Berhenti</h2>
                </div>
                <ol class="flex flex-col gap-4 list-none">
                    @foreach([
                        ['Hari 0',             'Pengumuman resmi via email, notifikasi in-app, dan media sosial.'],
                        ['Hari 1–90',          'Platform tetap aktif dalam mode read-only. Fitur ekspor berjalan penuh. Pengingat ekspor dikirim di hari ke-30, 60, dan 80.'],
                        ['Hari 90',            'Seluruh data pengguna yang tidak diekspor dihapus permanen.'],
                        ['Pasca hari 90',      'Penghapusan divalidasi melalui konfirmasi berbasis hash yang dipublikasikan sebagai bukti audit.'],
                    ] as [$step, $desc])
                    <li class="flex gap-3 items-start">
                        <span class="badge badge-warm shrink-0 mt-0.5" style="min-width:fit-content;">{{ $step }}</span>
                        <span class="text-sm" style="color:var(--fg);line-height:1.6;">{{ $desc }}</span>
                    </li>
                    @endforeach
                </ol>
            </div>

            <div class="card">
                <div class="flex items-center gap-3 mb-4">
                    <div style="width:2rem;height:2rem;background:var(--accent-soft);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h2 class="font-semibold" style="font-size:.9375rem;margin:0;">Perlindungan Terhadap Akuisisi</h2>
                </div>
                <ul class="text-sm flex flex-col gap-2.5" style="color:var(--muted);">
                    <li>Pengguna diberitahu minimal 60 hari sebelum akuisisi efektif</li>
                    <li>Pengguna punya hak untuk menghapus akun dan seluruh data sebelum akuisisi</li>
                    <li>Acquirer tidak mewarisi data pengguna yang sudah melakukan penghapusan</li>
                </ul>
            </div>
        </div>

    </div>
</x-layouts.public>
