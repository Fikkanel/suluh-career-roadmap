<x-layouts.app title="Pivot Karir">
    <x-slot:heading>Mengubah Arah Karir</x-slot:heading>

    <div class="max-w-2xl mx-auto">
        <div class="alert alert-neutral mb-7">
            <div class="flex gap-2.5">
                <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:.125rem;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span><strong>Perjalanan sebelumnya tidak hilang.</strong>
                Roadmap lamamu akan diarsipkan, bukan dihapus. Skill yang sudah selesai tetap diakui di roadmap baru.</span>
            </div>
        </div>

        {{-- Step 1: Reflection --}}
        <div class="card mb-5">
            <div class="flex items-center gap-3 mb-4">
                <span class="step-dot step-dot-active">1</span>
                <h2 class="font-semibold" style="margin:0;font-size:.9375rem;">Refleksi</h2>
                <span class="text-xs" style="color:var(--muted);">Langkah 1 dari 3</span>
            </div>
            <p class="text-sm mb-4" style="color:var(--muted);line-height:1.6;">
                Sebelum berpindah, ada baiknya kamu catat apa yang sudah kamu pelajari.
                Ini bukan syarat — hanya undangan untuk refleksi.
            </p>
            <x-reflection-prompt
                prompt="Apa yang kamu pelajari dari perjalanan karir sejauh ini?"
                helper="Tidak ada jawaban benar atau salah. Refleksimu hanya bisa dilihat oleh kamu sendiri."
                variant="pivot"
                fieldName="reflection" />
        </div>

        {{-- Step 2: Transfer insight (real data from DB) --}}
        <div class="card mb-5 card-success-left">
            <div class="flex items-center gap-3 mb-4">
                <span class="step-dot step-dot-done">2</span>
                <h2 class="font-semibold" style="margin:0;font-size:.9375rem;">Skill yang Kamu Bawa</h2>
                <span class="text-xs" style="color:var(--muted);">Langkah 2 dari 3</span>
            </div>
            @if(count($transferableSkills))
                <p class="text-sm mb-3" style="color:var(--muted);line-height:1.6;">
                    Skill berikut sudah kamu selesaikan dan akan otomatis diakui di roadmap barumu:
                </p>
                <div class="flex flex-wrap gap-2">
                    @foreach($transferableSkills as $skill)
                        <x-skill-badge :name="$skill['name']" :level="$skill['level']" status="done" :transferable="true" />
                    @endforeach
                </div>
            @else
                <p class="text-sm" style="color:var(--muted);line-height:1.6;">
                    Kamu belum menyelesaikan skill apapun di karir ini — tapi itu tidak masalah.
                    Setiap arah baru tetap merupakan pilihan yang valid.
                </p>
            @endif
        </div>

        {{-- Step 3: Confirm --}}
        <div class="card mb-6">
            <div class="flex items-center gap-3 mb-4">
                <span class="step-dot step-dot-pending">3</span>
                <h2 class="font-semibold" style="margin:0;font-size:.9375rem;">Konfirmasi</h2>
                <span class="text-xs" style="color:var(--muted);">Langkah 3 dari 3</span>
            </div>
            <p class="text-sm mb-5" style="color:var(--muted);line-height:1.6;">
                Kamu akan diarahkan kembali ke hasil asesmen untuk memilih karir baru.
                Ini bukan reset — ini kelanjutan perjalananmu.
            </p>
            <form method="POST" action="{{ route('pivot.store') }}">
                @csrf
                <div class="flex gap-3 flex-wrap">
                    <button type="submit" class="btn btn-warm">Lanjutkan ke eksplorasi baru</button>
                    <a href="{{ route('dashboard') }}" class="btn btn-ghost">Belum sekarang</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
