<x-layouts.admin title="Dashboard">
    {{-- Stat cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-3 mb-7">
        @foreach([
            ['Total Pengguna',   $stats['total_users'],        'accent'],
            ['Aktif Bulan Ini',  $stats['active_this_month'],  'info'],
            ['Rata-rata CRS',    $stats['avg_crs'].'%',        'accent'],
            ['Asesmen Selesai',  $stats['assessments_done'],   'success'],
            ['Pivot Dilakukan',  $stats['pivot_count'],        'warm'],
        ] as [$label, $val, $tone])
        <div class="card" style="padding:1.25rem;">
            <p class="stat-label mb-2">{{ $label }}</p>
            <p class="stat-number" style="font-size:2rem;color:var(--{{ $tone === 'warm' ? 'accent-warm' : $tone }});">{{ $val }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        {{-- Career distribution --}}
        <div class="card">
            <h2 class="font-semibold mb-4" style="font-size:.9375rem;">Distribusi Karir <span class="section-label-muted ml-2">Agregat</span></h2>
            @php $maxCount = max($stats['careers_distribution']); @endphp
            <div class="flex flex-col gap-3">
                @foreach($stats['careers_distribution'] as $career => $count)
                    <div>
                        <div class="flex justify-between text-xs mb-1.5">
                            <span style="color:var(--fg);">{{ $career }}</span>
                            <span style="font-family:var(--font-mono);color:var(--muted);">{{ $count }}</span>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill" style="width:{{ $maxCount > 0 ? round($count / $maxCount * 100) : 0 }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            <p class="text-xs mt-3" style="color:var(--muted);">Data agregat anonim. Tidak ada identifikasi individual.</p>
        </div>

        {{-- Quick links --}}
        <div class="card">
            <h2 class="font-semibold mb-4" style="font-size:.9375rem;">Aksi Cepat</h2>
            <div class="flex flex-col gap-2.5">
                <a href="{{ route('admin.careers.index') }}" class="btn btn-secondary btn-sm justify-start gap-2">
                    <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    Kelola Karir
                </a>
                <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary btn-sm justify-start gap-2">
                    <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    Kelola Asesmen
                </a>
                <a href="{{ route('admin.ethics.index') }}" class="btn btn-secondary btn-sm justify-start gap-2">
                    <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Komite Etika
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm justify-start gap-2">
                    <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    Kelola Pengguna
                </a>
                <a href="{{ route('impact') }}" class="btn btn-ghost btn-sm justify-start gap-2 mt-1" style="border-top:1px dashed var(--border);padding-top:0.75rem;">
                    <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    Lihat Dashboard Publik
                </a>
            </div>
            <div class="mt-4 pt-4" style="border-top:1px solid var(--border);">
                <p class="text-xs" style="color:var(--muted);line-height:1.55;">Jawaban individual pengguna tidak pernah tersedia di panel admin, bahkan untuk super admin.</p>
            </div>
        </div>
    </div>
</x-layouts.admin>
