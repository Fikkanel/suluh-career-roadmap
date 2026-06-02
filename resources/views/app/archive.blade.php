<x-layouts.app title="Arsip Perjalanan">
    <x-slot:heading>Arsip Perjalanan</x-slot:heading>

    <div class="max-w-3xl mx-auto">
        <p class="text-sm mb-6" style="color:var(--muted);">
            Setiap perjalanan yang kamu mulai tersimpan di sini. Bukan sebagai bukti kegagalan,
            tapi sebagai rekam jejak pertumbuhan yang hanya bisa kamu lihat sendiri.
        </p>

        @if(count($archives) === 0)
            <div class="card text-center py-14">
                <div style="width:3rem;height:3rem;background:var(--bg);border:1px solid var(--border);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5" rx="1"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                </div>
                <p class="font-semibold mb-2">Arsip masih kosong</p>
                <p class="text-sm" style="color:var(--muted);">Roadmap yang kamu arsipkan saat pivot akan muncul di sini.</p>
            </div>
        @else
            <div class="flex flex-col gap-4">
                @foreach($archives as $archive)
                    <div class="card">
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div>
                                <h3 class="font-semibold mb-0.5" style="font-size:.9375rem;">{{ $archive['career'] }}</h3>
                                <p class="text-xs" style="color:var(--muted);">Diarsipkan {{ $archive['archived_at'] }}</p>
                            </div>
                            <span class="badge badge-default">Arsip</span>
                        </div>
                        <x-progress-bar
                            :value="$archive['completed_skills']"
                            :max="$archive['total_skills']"
                            :label="$archive['completed_skills'] . ' dari ' . $archive['total_skills'] . ' skill selesai'" />
                        @if($archive['reflection'])
                            <div class="mt-4 p-3.5 text-sm" style="background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-card);border-left:3px solid var(--muted);">
                                <p class="section-label-muted mb-1.5">Refleksimu saat itu</p>
                                <p style="color:var(--muted);line-height:1.6;">{{ $archive['reflection'] }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
