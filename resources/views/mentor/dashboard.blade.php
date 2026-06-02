<x-layouts.app title="Dashboard Mentor — Suluh">
    <x-slot:heading>Dashboard Mentor</x-slot:heading>

    {{-- Mentor Welcome Banner --}}
    <div class="card card-accent-left mb-8">
        <div class="flex items-start gap-4">
            <div style="width:3rem;height:3rem;border-radius:50%;background:var(--accent-soft);border:2px solid var(--accent);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div>
                <p class="stat-label mb-1">Selamat datang kembali</p>
                <h2 class="font-bold text-xl mb-1" style="font-family:var(--font-display);color:var(--fg);">{{ auth()->user()->name }}</h2>
                <p class="text-sm" style="color:var(--muted);line-height:1.6;">Peranmu sebagai mentor membantu pengguna lain menerangi jalan karir mereka. Berikan masukan yang jujur dan tidak menghakimi.</p>
            </div>
        </div>
    </div>

    {{-- Summary Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
        <div class="card-metric card-accent-left">
            <p class="stat-label mb-2">Total Mentee</p>
            <p class="stat-number stat-number-lg">{{ $mentees->count() }}</p>
            <p class="text-xs mt-2" style="color:var(--muted);">Pengguna aktif dengan karir terpilih</p>
        </div>
        <div class="card-metric card-info-left">
            <p class="stat-label mb-2">Rata-rata CRS Mentee</p>
            <p class="stat-number stat-number-lg">{{ $mentees->count() > 0 ? round($mentees->avg('crs_score')) : 0 }}<span style="font-size:1.2rem;font-weight:500;">%</span></p>
            <p class="text-xs mt-2" style="color:var(--muted);">Career Readiness Score rata-rata</p>
        </div>
        <div class="card-metric card-warm-left">
            <p class="stat-label mb-2">Butuh Perhatian</p>
            <p class="stat-number stat-number-lg">{{ $mentees->where('crs_score', '<', 25)->count() }}</p>
            <p class="text-xs mt-2" style="color:var(--muted);">Mentee dengan CRS di bawah 25%</p>
        </div>
    </div>

    {{-- Mentee List --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold" style="color:var(--muted);">Daftar Mentee</h2>
        <span class="badge badge-default">{{ $mentees->count() }} orang</span>
    </div>

    <div class="flex flex-col gap-3">
        @forelse($mentees->sortBy('crs_score') as $mentee)
        <div class="card card-hover" style="padding:1rem 1.25rem;">
            <div class="flex items-center justify-between gap-4 flex-wrap">

                {{-- Identity --}}
                <div class="flex items-center gap-3 min-w-0">
                    <div style="width:2.5rem;height:2.5rem;border-radius:50%;background:var(--bg-subtle);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-weight:700;font-size:.9rem;color:var(--accent);">
                        {{ strtoupper(substr($mentee->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold truncate" style="color:var(--fg);max-width:200px;">{{ $mentee->name }}</p>
                        <p class="text-xs" style="color:var(--muted);">
                            <span class="badge badge-accent" style="font-size:0.65rem;">{{ $mentee->currentCareer->name ?? 'Belum memilih karir' }}</span>
                        </p>
                    </div>
                </div>

                {{-- CRS Progress --}}
                <div class="flex-1 min-w-[140px] max-w-xs">
                    <div class="flex justify-between items-center mb-1.5">
                        <p class="text-xs" style="color:var(--muted);">Career Readiness Score</p>
                        <p class="text-xs font-bold" style="color:{{ $mentee->crs_score >= 50 ? 'var(--accent)' : ($mentee->crs_score >= 25 ? 'var(--accent-warm)' : 'var(--error)') }};">
                            {{ $mentee->crs_score }}%
                        </p>
                    </div>
                    <div class="h-1.5 w-full rounded-full overflow-hidden" style="background:var(--border);">
                        <div class="h-full rounded-full transition-all duration-700"
                             style="width:{{ $mentee->crs_score }}%;background:{{ $mentee->crs_score >= 50 ? 'var(--accent)' : ($mentee->crs_score >= 25 ? 'var(--accent-warm)' : '#ef4444') }};"></div>
                    </div>
                </div>

                {{-- Status badge --}}
                <div class="flex-shrink-0">
                    @if($mentee->crs_score >= 75)
                        <span class="badge badge-success">Sangat Baik</span>
                    @elseif($mentee->crs_score >= 50)
                        <span class="badge badge-accent">Progres Bagus</span>
                    @elseif($mentee->crs_score >= 25)
                        <span class="badge badge-default">Perlu Dorongan</span>
                    @else
                        <span style="display:inline-flex;align-items:center;gap:4px;padding:.2rem .6rem;border-radius:999px;font-size:.65rem;font-weight:600;background:#fef2f2;color:#ef4444;border:1px solid #fca5a5;">
                            ⚠ Perlu Perhatian
                        </span>
                    @endif
                </div>

                {{-- Action --}}
                <div class="flex-shrink-0">
                    <a href="{{ route('mentor.mentee.show', $mentee->id) }}"
                       class="btn btn-primary btn-sm"
                       id="mentee-detail-{{ $mentee->id }}">
                        Lihat Profil & Beri Feedback
                    </a>
                </div>

            </div>
        </div>
        @empty
        <div class="card text-center py-12">
            <svg class="mx-auto mb-4" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            <p class="font-semibold mb-1" style="color:var(--fg);">Belum ada mentee saat ini</p>
            <p class="text-sm" style="color:var(--muted);">Mentee akan muncul di sini saat pengguna menyelesaikan asesmen dan memilih jalur karir mereka.</p>
        </div>
        @endforelse
    </div>

    {{-- Mentor Ethics Reminder (sesuai PRD P1 & P2) --}}
    <div class="mt-8">
        <x-privacy-notice
            title="Panduan etika sebagai mentor"
            body="Sebagai mentor, kamu diberi kepercayaan untuk mengakses profil perjalanan pengguna lain. Gunakan akses ini untuk mendukung, bukan menghakimi. Setiap data yang kamu lihat bersifat rahasia dan tidak boleh disebarluaskan."
            controlAction="Baca lebih lanjut tentang tanggung jawab mentor di halaman kebijakan kami."
            variant="detailed"
            :dataUsed="['Nama mentee', 'Karir yang dipilih', 'Progress skill', 'Riwayat feedback']" />
    </div>

</x-layouts.app>
