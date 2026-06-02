<x-layouts.app title="Ekspor Data">
    <x-slot:heading>Ekspor Data</x-slot:heading>

    <div class="max-w-2xl mx-auto">

        {{-- Intro --}}
        <p class="text-sm mb-8" style="color:var(--muted); line-height:1.65;">
            Semua data perjalanan karirmu bisa diunduh kapan saja, tanpa syarat, tanpa batas frekuensi.
            Ini adalah <strong style="color:var(--fg);">hakmu</strong>, bukan fitur berbayar.
        </p>

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="card mb-5 flex items-center gap-3" style="border-left:4px solid var(--accent); padding:1rem 1.25rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            <p class="text-sm font-medium" style="color:var(--fg);">{{ session('success') }}</p>
        </div>
        @endif

        {{-- Data yang akan diekspor --}}
        <div class="card mb-5">
            <p class="text-xs font-semibold mb-4" style="color:var(--muted); letter-spacing:.07em; text-transform:uppercase;">Data yang disertakan dalam ekspor</p>
            <ul style="display:flex; flex-direction:column; gap:.875rem; padding:0; margin:0; list-style:none;">
                @foreach([
                    ['path' => 'M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2', 'extra' => '<circle cx="12" cy="7" r="4"/>',   'label' => 'Profil pengguna',                   'sub' => 'Nama, email, domisili, riwayat pendidikan & pengalaman kerja'],
                    ['path' => 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2',  'extra' => '<rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6m-6 4h4"/>',   'label' => 'Hasil asesmen RIASEC & Big Five',   'sub' => 'Skor per dimensi dan rekomendasi karir teratas'],
                    ['path' => 'M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5',                       'extra' => '',                                              'label' => 'Roadmap aktif & progress skill',    'sub' => 'Status tiap skill beserta waktu mulai dan selesai'],
                    ['path' => 'M4 6h16M4 12h16M4 18h7',                                                         'extra' => '',                                              'label' => 'Riwayat pivot & arsip perjalanan',  'sub' => 'Roadmap lama yang diarsipkan beserta refleksinya'],
                    ['path' => 'M9 19v-6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2zm0 0V9a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v10m-6 0a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2m0 0V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v14a2 2 0 0 0-2 2h-2a2 2 0 0 0-2-2z', 'extra' => '', 'label' => 'Hasil survei dampak', 'sub' => 'Survei 3 dan 6 bulan jika sudah diisi'],
                ] as $item)
                <li class="flex items-start gap-3">
                    <div style="width:1.875rem; height:1.875rem; border-radius:7px; background:var(--accent-soft); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:.125rem;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="{{ $item['path'] }}"/>{!! $item['extra'] !!}
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold" style="color:var(--fg); line-height:1.3;">{{ $item['label'] }}</p>
                        <p class="text-xs mt-0.5" style="color:var(--muted); line-height:1.5;">{{ $item['sub'] }}</p>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>

        {{-- Tombol Download — form POST --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.25rem;">

            {{-- PDF --}}
            <form method="POST" action="{{ route('export.pdf') }}">
                @csrf
                <button type="submit" id="btn-export-pdf"
                        class="card card-hover"
                        style="width:100%; text-align:left; display:flex; flex-direction:column; align-items:flex-start; gap:.875rem; padding:1.25rem; cursor:pointer; background:var(--bg-subtle); border:1px solid var(--border); transition:border-color .2s, box-shadow .2s;"
                        onmouseover="this.style.borderColor='var(--accent)'"
                        onmouseout="this.style.borderColor='var(--border)'">
                    <div style="width:2.5rem; height:2.5rem; border-radius:10px; background:var(--accent-soft); border:1px solid var(--border); display:flex; align-items:center; justify-content:center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </div>
                    <div style="flex:1;">
                        <p class="font-semibold text-sm mb-1" style="color:var(--fg);">Unduh PDF</p>
                        <p class="text-xs" style="color:var(--muted); line-height:1.5;">Ringkasan perjalanan karir yang siap dibaca dan dibagikan</p>
                    </div>
                </button>
            </form>

            {{-- JSON --}}
            <form method="POST" action="{{ route('export.json') }}">
                @csrf
                <button type="submit" id="btn-export-json"
                        class="card card-hover"
                        style="width:100%; text-align:left; display:flex; flex-direction:column; align-items:flex-start; gap:.875rem; padding:1.25rem; cursor:pointer; background:var(--bg-subtle); border:1px solid var(--border); transition:border-color .2s;"
                        onmouseover="this.style.borderColor='var(--accent-warm)'"
                        onmouseout="this.style.borderColor='var(--border)'">
                    <div style="width:2.5rem; height:2.5rem; border-radius:10px; background:#f5e8da; border:1px solid var(--border); display:flex; align-items:center; justify-content:center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--accent-warm)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <polyline points="16 18 22 12 16 6"/>
                            <polyline points="8 6 2 12 8 18"/>
                        </svg>
                    </div>
                    <div style="flex:1;">
                        <p class="font-semibold text-sm mb-1" style="color:var(--fg);">Unduh JSON</p>
                        <p class="text-xs" style="color:var(--muted); line-height:1.5;">Semua data mentah terstruktur untuk diproses atau dipindahkan</p>
                    </div>
                </button>
            </form>

        </div>

        {{-- Privacy notice --}}
        <x-privacy-notice
            title="File yang diunduh tidak mengandung password atau token akses"
            body="Data hanya tersimpan di perangkat kamu setelah diunduh — server tidak menyimpan salinan file ekspor. Kamu bisa menghapus akun beserta seluruh data kapan saja melalui halaman Pengaturan Profil."
            variant="compact" />

    </div>
</x-layouts.app>
