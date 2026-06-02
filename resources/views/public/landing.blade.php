<x-layouts.public title="Suluh">

    {{-- Hero — editorial left-aligned on desktop --}}
    <section class="max-w-6xl mx-auto page-padding pt-16 pb-14 md:pt-20 md:pb-20">
        <div class="max-w-2xl">
            <p class="section-label mb-4" data-reveal data-reveal-delay="0">Platform Roadmap Karir Personal Indonesia</p>
            <h1 class="mb-5" data-reveal data-reveal-delay="80" style="font-family:var(--font-display);font-size:clamp(2rem,5vw,3.25rem);font-weight:700;line-height:1.18;letter-spacing:-.02em;color:var(--fg);">
                Suluh tidak memilihkan jalan.<br>
                <span style="color:var(--accent);">Ia hanya menerangi.</span>
            </h1>
            <p class="mb-8 text-lg" data-reveal data-reveal-delay="160" style="color:var(--muted);line-height:1.7;max-width:32rem;">
                Temukan arah karir yang selaras dengan dirimu, bukan yang paling populer.
                Buat roadmap personal, pantau progres, dan pegang kendali penuh atas perjalananmu.
            </p>
            <div class="flex flex-wrap gap-3" data-reveal data-reveal-delay="240">
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Mulai Eksplorasi</a>
                <a href="{{ route('impact') }}" class="btn btn-ghost btn-lg">Lihat Dampak Platform</a>
            </div>
            <p class="mt-5 text-sm" data-reveal data-reveal-delay="300" style="color:var(--muted);">Gratis selamanya. Tanpa iklan. Datamu adalah milikmu.</p>
        </div>
    </section>

    {{-- Privacy proof --}}
    <section style="background:var(--accent-soft);border-top:1px solid var(--border);border-bottom:1px solid var(--border);">
        <div class="max-w-6xl mx-auto page-padding py-12">
            <p class="section-label-muted mb-7" data-reveal>Komitmen Privasi</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach([
                    ['icon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>', 'title' => 'Privasi adalah Arsitektur', 'desc' => 'Data sensitifmu dienkripsi sejak hari pertama. Bukan sekadar janji dalam kebijakan privasi.'],
                    ['icon' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>', 'title' => 'Ekspor Kapan Saja', 'desc' => 'Unduh seluruh datamu dalam PDF atau JSON tanpa syarat, tanpa batas frekuensi.'],
                    ['icon' => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>', 'title' => 'Kebijakan Sunset Terbuka', 'desc' => 'Prosedur penutupan platform sudah ditulis sejak hari pertama.'],
                ] as $i => $item)
                <div class="card card-hover flex gap-4" data-reveal data-reveal-delay="{{ $i * 100 }}">
                    <div class="shrink-0 mt-0.5">
                        <div style="width:2.25rem;height:2.25rem;background:var(--accent-soft);border:1.5px solid var(--accent);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                            <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $item['icon'] !!}</svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-sm mb-1.5">{{ $item['title'] }}</h3>
                        <p class="text-sm" style="color:var(--muted);line-height:1.55;">{{ $item['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- How it works — numbered steps --}}
    <section class="max-w-6xl mx-auto page-padding py-16">
        <p class="section-label-muted mb-2" data-reveal>Cara Kerja Suluh</p>
        <h2 class="mb-10" data-reveal data-reveal-delay="80" style="font-family:var(--font-display);font-size:1.5rem;font-weight:700;color:var(--fg);">Empat langkah, satu perjalanan.</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            @foreach([
                ['n'=>'1','title'=>'Asesmen','desc'=>'Jawab pertanyaan berbasis skenario sehari-hari. Tidak ada jawaban benar atau salah.'],
                ['n'=>'2','title'=>'Eksplorasi','desc'=>'Lihat 3 kemungkinan karir yang selaras — setara, tanpa ranking, tanpa paksaan.'],
                ['n'=>'3','title'=>'Roadmap','desc'=>'Kamu pilih sendiri. Lalu kami bantu susun langkah konkret dari skillmu hari ini.'],
                ['n'=>'4','title'=>'Pivot','desc'=>'Berubah arah kapan saja. Perjalanan sebelumnya diarsipkan, bukan dihapus.'],
            ] as $i => $step)
            <div data-reveal data-reveal-delay="{{ $i * 100 }}">
                <div class="how-step-number">{{ $step['n'] }}</div>
                <h3 class="font-semibold mb-2 text-base">{{ $step['title'] }}</h3>
                <p class="text-sm" style="color:var(--muted);line-height:1.6;">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section style="background:var(--surface);border-top:1px solid var(--border);">
        <div class="max-w-6xl mx-auto page-padding py-14">
            <div class="max-w-lg">
                <h2 class="mb-3" data-reveal style="font-family:var(--font-display);font-size:1.625rem;font-weight:700;">Mulai perjalananmu hari ini.</h2>
                <p class="mb-6 text-base" data-reveal data-reveal-delay="80" style="color:var(--muted);">Kamu tetap memegang keputusan akhirnya.</p>
                <div class="flex gap-3 flex-wrap" data-reveal data-reveal-delay="160">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Daftar Gratis</a>
                    <a href="{{ route('impact') }}" class="btn btn-ghost btn-lg">Lihat Dampak →</a>
                </div>
            </div>
        </div>
    </section>

</x-layouts.public>
