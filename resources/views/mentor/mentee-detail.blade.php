<x-layouts.app title="Profil Mentee — {{ $mentee->name }}">
    <x-slot:heading>Profil Mentee</x-slot:heading>

    {{-- Header / Back --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('mentor.dashboard') }}" class="btn btn-ghost btn-sm" style="padding:.4rem .8rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali
        </a>
        <h2 class="font-bold text-lg" style="font-family:var(--font-display);color:var(--fg);">{{ $mentee->name }}</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif
    @error('content')
        <div class="alert alert-error mb-6">{{ $message }}</div>
    @enderror

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── LEFT (2/3): Identitas + Progress ── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Identity Card --}}
            <div class="card card-info-left">
                <div class="flex items-center gap-4 mb-4">
                    <div style="width:3.5rem;height:3.5rem;border-radius:50%;background:var(--accent-soft);border:2px solid var(--accent);display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:700;color:var(--accent);">
                        {{ strtoupper(substr($mentee->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-lg" style="color:var(--fg);">{{ $mentee->name }}</h3>
                        <p class="text-sm" style="color:var(--muted);">
                            Karir: <span class="badge badge-accent">{{ $mentee->currentCareer->name ?? 'Belum memilih' }}</span>
                        </p>
                    </div>
                </div>

                {{-- CRS Score Besar --}}
                @php
                    $total = $progresses->count();
                    $done  = $progresses->where('status', 'done')->count();
                    $crs   = $total > 0 ? round($done / $total * 100) : 0;
                @endphp
                <div class="mb-1">
                    <div class="flex justify-between items-center mb-2">
                        <p class="text-sm font-semibold" style="color:var(--fg);">Career Readiness Score (CRS)</p>
                        <p class="text-xl font-bold" style="color:var(--accent);">{{ $crs }}%</p>
                    </div>
                    <x-progress-bar :value="$crs" :max="100" />
                    <p class="text-xs mt-2" style="color:var(--muted);">{{ $done }} dari {{ $total }} skill telah diselesaikan</p>
                </div>

                {{-- Context info --}}
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-4">
                    @foreach([
                        ['Usia', $mentee->age_range ?? '—'],
                        ['Pendidikan', $mentee->education_level ?? '—'],
                        ['Pengalaman', $mentee->work_experience ?? '—'],
                    ] as [$label, $value])
                    <div class="p-3 rounded-lg" style="background:var(--bg-subtle);border:1px solid var(--border);">
                        <p class="text-xs" style="color:var(--muted);">{{ $label }}</p>
                        <p class="text-sm font-semibold mt-0.5" style="color:var(--fg);">{{ $value }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Roadmap Progress --}}
            <div class="card">
                <h3 class="font-semibold mb-4" style="color:var(--fg);">Progress Roadmap Skill</h3>

                @php
                    $grouped = $progresses->groupBy(fn($p) => $p->skill->level ?? 'Lainnya');
                    $levelOrder = ['beginner', 'intermediate', 'advanced', 'Lainnya'];
                @endphp

                @forelse($grouped->sortBy(fn($_, $k) => array_search($k, $levelOrder)) as $level => $items)
                    <div class="mb-5">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="badge badge-default" style="text-transform:capitalize;">{{ $level }}</span>
                            <span class="text-xs" style="color:var(--muted);">{{ $items->where('status','done')->count() }}/{{ $items->count() }} selesai</span>
                        </div>
                        <div class="flex flex-col gap-2">
                            @foreach($items as $progress)
                            <div class="flex items-center justify-between p-3 rounded-lg" style="background:var(--bg-subtle);border:1px solid var(--border);">
                                <div class="flex items-center gap-3">
                                    {{-- status icon --}}
                                    @if($progress->status === 'done')
                                        <div style="width:1.4rem;height:1.4rem;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        </div>
                                    @elseif($progress->status === 'learning' || $progress->status === 'in_progress')
                                        <div style="width:1.4rem;height:1.4rem;border-radius:50%;border:2px solid var(--accent-warm);background:transparent;display:flex;align-items:center;justify-content:center;">
                                            <div style="width:.5rem;height:.5rem;border-radius:50%;background:var(--accent-warm);"></div>
                                        </div>
                                    @else
                                        <div style="width:1.4rem;height:1.4rem;border-radius:50%;border:2px solid var(--border);background:transparent;"></div>
                                    @endif
                                    <p class="text-sm font-medium" style="color:var(--fg);">{{ $progress->skill->name ?? 'Skill Terhapus' }}</p>
                                </div>
                                <div>
                                    @if($progress->status === 'done')
                                        <span class="badge badge-success" style="font-size:.65rem;">Selesai</span>
                                    @elseif($progress->status === 'in_progress')
                                        <span class="badge badge-accent" style="font-size:.65rem;">Sedang dikerjakan</span>
                                    @elseif($progress->status === 'learning')
                                        <span class="badge badge-accent" style="font-size:.65rem;">Belajar</span>
                                    @else
                                        <span class="badge badge-default" style="font-size:.65rem;">Belum mulai</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-center py-6" style="color:var(--muted);">Mentee ini belum memulai skill apapun dalam roadmap mereka.</p>
                @endforelse
            </div>

        </div>

        {{-- ── RIGHT (1/3): Feedback Panel ── --}}
        <div class="space-y-6">

            {{-- Form Feedback --}}
            <div class="card card-warm-left">
                <h3 class="font-semibold mb-1" style="color:var(--fg);">Beri Masukan Roadmap</h3>
                <p class="text-xs mb-4" style="color:var(--muted);line-height:1.5;">Tulis masukan yang mendukung otonomi pengguna. Hindari bahasa yang menghakimi pilihan karir mereka.</p>

                <form action="{{ route('mentor.feedback.store', $mentee->id) }}" method="POST" id="feedback-form-{{ $mentee->id }}">
                    @csrf
                    <textarea
                        name="content"
                        class="w-full rounded-lg border p-3 text-sm mb-3"
                        style="background:var(--bg);border-color:var(--border);color:var(--fg);resize:vertical;min-height:100px;"
                        placeholder="Contoh: Skill X yang kamu pilih sudah bagus. Mungkin kamu bisa mempertimbangkan untuk menambahkan Y karena industri ini sedang bergerak ke arah itu..."
                        required></textarea>

                    <button type="submit" class="btn btn-primary w-full" id="submit-feedback-{{ $mentee->id }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        Kirim Feedback
                    </button>
                </form>
            </div>

            {{-- Feedback History --}}
            <div class="card">
                <h3 class="font-semibold mb-4" style="color:var(--fg);">Riwayat Feedback</h3>

                @forelse($feedbacks as $fb)
                    <div class="mb-4 pb-4 last:mb-0 last:pb-0" style="border-bottom:1px solid var(--border);">
                        <div class="flex items-start gap-2 mb-2">
                            <div style="width:1.6rem;height:1.6rem;border-radius:50%;background:var(--bg-subtle);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:700;color:var(--accent);flex-shrink:0;margin-top:2px;">
                                {{ strtoupper(substr($fb->mentor->name ?? 'M', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-xs font-semibold" style="color:var(--fg);">{{ $fb->mentor->name ?? 'Mentor' }}</p>
                                <p class="text-xs" style="color:var(--muted);">{{ $fb->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <p class="text-sm" style="color:var(--muted);line-height:1.6;padding-left:1.9rem;">"{{ $fb->content }}"</p>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <svg class="mx-auto mb-2" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        <p class="text-xs" style="color:var(--muted);">Belum ada feedback yang diberikan ke mentee ini.</p>
                    </div>
                @endforelse
            </div>

            {{-- Mentor Ethics Note --}}
            <div class="p-4 rounded-lg" style="background:var(--bg-subtle);border:1px solid var(--border);">
                <p class="text-xs font-semibold mb-1" style="color:var(--fg);">🔒 Prinsip Privasi</p>
                <p class="text-xs" style="color:var(--muted);line-height:1.6;">Data profil ini hanya boleh digunakan untuk keperluan bimbingan. Riwayat pivot mentee bersifat privat dan tidak ditampilkan.</p>
            </div>

        </div>
    </div>

</x-layouts.app>
