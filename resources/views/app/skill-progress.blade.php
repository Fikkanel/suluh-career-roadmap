<x-layouts.app title="Progress Skill">
    <x-slot:heading>Progress Skill</x-slot:heading>

    <div class="max-w-3xl mx-auto">
        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        <p class="text-sm mb-6" style="color:var(--muted);">
            Tandai skill yang sudah kamu pelajari. Status yang kamu pilih bersifat reflektif —
            tidak ada penilaian benar/salah. Yang sudah kamu pelajari tetap bernilai.
        </p>

        <div class="flex flex-col gap-3">
            @foreach($skills as $skill)
                <div class="card flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3 flex-1">
                        <x-skill-badge
                            :name="$skill['name']"
                            :level="$skill['level']"
                            :status="$skill['status']"
                            :transferable="$skill['transferable']" />
                    </div>
                    <form method="POST" action="{{ route('skill-progress.update', $skill['id']) }}" class="flex items-center gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="input" style="width:auto;padding:.375rem .75rem;font-size:.875rem;"
                                onchange="this.form.submit()">
                            @foreach(['not_started' => 'Belum Mulai', 'learning' => 'Sedang Belajar', 'in_progress' => 'Dalam Proses', 'done' => 'Selesai'] as $val => $lbl)
                                <option value="{{ $val }}" {{ $skill['status'] === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @endforeach
        </div>

        <x-reflection-prompt
            prompt="Ada yang ingin kamu catat tentang progres belajarmu?"
            helper="Refleksi opsional. Tidak dikirim ke siapa pun."
            variant="progress"
            fieldName="progress_reflection"
            class="mt-6" />
    </div>
</x-layouts.app>
