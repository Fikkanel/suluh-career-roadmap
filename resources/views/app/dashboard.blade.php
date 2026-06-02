<x-layouts.app title="Dashboard">
    <x-slot:heading>Dashboard</x-slot:heading>

    {{-- Metric cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

        {{-- CRS — primary metric --}}
        <div class="card-metric card-accent-left">
            <p class="stat-label mb-3">Career Readiness Score</p>
            <p class="stat-number stat-number-lg mb-3">{{ $data['crs'] }}<span style="font-size:1.5rem;font-weight:500;">%</span></p>
            <x-progress-bar :value="$data['crs']" :max="100" />
            <p class="text-xs mt-2.5" style="color:var(--muted);">{{ $data['completed_skills'] }} dari {{ $data['total_skills'] }} skill selesai</p>
        </div>

        {{-- Current career --}}
        <div class="card-metric card-info-left">
            <p class="stat-label mb-3">Karir yang Kamu Pilih</p>
            <p class="font-semibold text-lg mb-1" style="font-family:var(--font-display);line-height:1.3;">{{ $data['career'] }}</p>
            <p class="text-sm mb-4" style="color:var(--muted);">Tahap aktif: {{ $data['roadmap_stage'] }}</p>
            <a href="{{ route('roadmap') }}" class="btn btn-secondary btn-sm">Lihat Roadmap</a>
        </div>

        {{-- Next step --}}
        <div class="card-metric card-warm-left">
            <p class="stat-label mb-3">Langkah Berikutnya</p>
            <p class="font-semibold mb-1" style="line-height:1.35;">{{ $data['next_skill'] }}</p>
            <p class="text-xs mb-4" style="color:var(--muted);">Aktivitas terakhir: {{ $data['last_activity'] }}</p>
            <a href="{{ route('skill-progress') }}" class="btn btn-primary btn-sm">Tandai Progress</a>
        </div>
    </div>

    {{-- Milestone narrative from LLM --}}
    @if(!empty($data['milestone_narrative']))
    <div class="card mb-6 card-accent-left">
        <p class="text-xs stat-label mb-1.5">Catatan perjalanan</p>
        <p class="text-sm" style="line-height:1.7;">{{ $data['milestone_narrative'] }}</p>
    </div>
    @endif
    
    {{-- Mentor Feedbacks --}}
    @if(isset($data['mentor_feedbacks']) && $data['mentor_feedbacks']->count() > 0)
    <div class="mb-8">
        <h2 class="text-sm font-semibold mb-3" style="color:var(--muted);">Umpan Balik Mentor</h2>
        <div class="grid grid-cols-1 gap-3">
            @foreach($data['mentor_feedbacks'] as $feedback)
            <div class="card p-4" style="border-left: 4px solid var(--accent); background: var(--bg-subtle);">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden shrink-0">
                        @if($feedback->mentor->avatar)
                            <img src="{{ asset('storage/' . $feedback->mentor->avatar) }}" alt="{{ $feedback->mentor->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-xs font-bold" style="color: var(--accent);">{{ strtoupper(substr($feedback->mentor->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <p class="text-sm font-semibold" style="color:var(--fg);">{{ $feedback->mentor->name }} <span class="text-xs font-normal" style="color:var(--muted);">(Mentor)</span></p>
                    <span class="text-xs ml-auto" style="color:var(--muted);">{{ $feedback->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm" style="color:var(--fg); line-height: 1.6;">"{{ $feedback->content }}"</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Job Recommendations (MVP) --}}
    @if(isset($data['job_recommendations']) && count($data['job_recommendations']) > 0)
    <div class="mb-8">
        <h2 class="text-sm font-semibold mb-3" style="color:var(--muted);">Rekomendasi Pekerjaan (<span style="color:var(--accent);">Auto-Match</span>)</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($data['job_recommendations'] as $job)
            <div class="card card-hover flex flex-col justify-between" style="border: 1px solid var(--border);">
                <div>
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-base" style="color:var(--fg);">{{ $job['title'] }}</h3>
                        <span class="badge badge-accent text-xs">{{ $job['match'] }} Match</span>
                    </div>
                    <p class="text-sm font-medium mb-1" style="color:var(--accent);">{{ $job['company'] }}</p>
                    <p class="text-xs mb-3 flex items-center gap-1" style="color:var(--muted);">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        {{ $job['location'] }}
                    </p>
                </div>
                <div class="flex justify-between items-end pt-3 border-t" style="border-color:var(--border);">
                    <div>
                        <p class="text-xs" style="color:var(--muted);">Estimasi Gaji</p>
                        <p class="text-sm font-semibold" style="color:var(--fg);">{{ $job['salary'] }}</p>
                    </div>
                    <button class="btn btn-secondary btn-sm" onclick="alert('Ini adalah simulasi fitur agregasi pekerjaan (MVP). Data ditarik dari {{ $job['source'] }}.')">
                        Lamar
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Quick links with SVG icons --}}
    <h2 class="text-sm font-semibold mb-3" style="color:var(--muted);">Aksi Cepat</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8">
        @foreach([
            [route('assessment'), '<path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6m-6 4h4"/>', 'Ulangi Asesmen', 'accent'],
            [route('pivot'), '<path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/>', 'Pivot Karir', 'warm'],
            [route('archive'), '<polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5" rx="1"/><line x1="10" y1="12" x2="14" y2="12"/>', 'Arsip Perjalanan', 'default'],
            [route('export'), '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>', 'Ekspor Data', 'default'],
        ] as [$url, $icon, $label, $tone])
        <a href="{{ $url }}" class="card card-hover flex flex-col items-center justify-center gap-2 py-5 text-center no-underline" style="color:var(--fg);">
            <div style="width:2.25rem;height:2.25rem;border-radius:8px;display:flex;align-items:center;justify-content:center;background:{{ $tone === 'accent' ? 'var(--accent-soft)' : ($tone === 'warm' ? '#f5e8da' : 'var(--bg)') }};border:1px solid var(--border);">
                <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="{{ $tone === 'accent' ? 'var(--accent)' : ($tone === 'warm' ? 'var(--accent-warm)' : 'var(--muted)') }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $icon !!}</svg>
            </div>
            <p class="text-xs font-medium" style="color:var(--fg);">{{ $label }}</p>
        </a>
        @endforeach
    </div>

    {{-- Data rights reminder --}}
    <x-privacy-notice
        title="Datamu, kendalimu"
        body="Semua data perjalanan karirmu bisa diekspor kapan saja dalam format PDF atau JSON."
        controlAction="Pergi ke halaman Ekspor Data untuk mengunduh seluruh datamu."
        variant="detailed"
        :dataUsed="['Profil', 'Hasil asesmen', 'Roadmap & progress', 'Riwayat pivot']" />

</x-layouts.app>
