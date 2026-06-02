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
                <a href="{{ route('admin.management') }}" class="btn btn-secondary btn-sm justify-start gap-2">
                    <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2z"/><path d="M7 7h.01"/></svg>
                    Manajemen Konten
                </a>
                <a href="{{ route('impact') }}" class="btn btn-ghost btn-sm justify-start gap-2">
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
