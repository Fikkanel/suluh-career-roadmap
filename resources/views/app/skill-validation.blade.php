<x-layouts.app title="Validasi Skill — {{ $skill->name }}">
    <x-slot:heading>Refleksi: {{ $skill->name }}</x-slot:heading>

    <div class="max-w-xl mx-auto">
        <div class="card mb-6">
            <div class="flex items-center gap-2 mb-3">
                <span class="badge badge-default">{{ $skill->level }}</span>
                <span class="badge badge-accent">{{ $skill->career->name ?? '' }}</span>
            </div>

            @if($skill->scenario_question)
            <div class="p-3 rounded mb-4" style="background:var(--bg-subtle);border-left:3px solid var(--accent);">
                <p class="text-sm font-semibold mb-1" style="color:var(--fg);">Skenario</p>
                <p class="text-sm" style="color:var(--muted);">{{ $skill->scenario_question }}</p>
            </div>
            @endif

            <p class="text-sm" style="color:var(--muted);line-height:1.6;">
                Ceritakan pengalamanmu dengan skill ini. Tidak ada jawaban benar atau salah — kami ingin memahami perjalanan belajarmu.
            </p>
        </div>

        @if($existingValidation)
        <div class="card mb-6 card-warm-left">
            <p class="text-sm font-semibold mb-2">Refleksi Sebelumnya</p>
            <p class="text-sm" style="color:var(--muted);">{{ $existingValidation->response }}</p>
            <p class="text-xs mt-2" style="color:var(--muted);">Dikirim: {{ $existingValidation->validated_at?->diffForHumans() ?? '-' }}</p>
        </div>
        @endif

        <form method="POST" action="{{ route('skill-validation.store', $skill->id) }}" class="flex flex-col gap-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1.5" for="response">Ceritakan pengalamanmu</label>
                <textarea
                    name="response"
                    id="response"
                    rows="5"
                    class="w-full rounded-lg border p-3 text-sm"
                    style="background:var(--bg);border-color:var(--border);color:var(--fg);"
                    placeholder="Contoh: Saya baru mulai belajar ini minggu lalu. Tantangan terbesarnya adalah..."
                    required
                >{{ old('response', $existingValidation->response ?? '') }}</textarea>
                @error('response')
                    <p class="text-xs mt-1" style="color:var(--error);">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">Seberapa paham kamu dengan skill ini?</label>
                <div class="flex gap-2">
                    @for($i = 1; $i <= 5; $i++)
                    <label class="flex flex-col items-center gap-1 cursor-pointer">
                        <input type="radio" name="self_assessed_level" value="{{ $i }}" class="sr-only peer"
                            {{ old('self_assessed_level', $existingValidation?->self_assessed_level) == $i ? 'checked' : '' }}>
                        <span class="w-10 h-10 rounded-lg flex items-center justify-center text-sm font-medium peer-checked:bg-accent peer-checked:text-white"
                              style="background:var(--bg-subtle);color:var(--muted);">
                            {{ $i }}
                        </span>
                        <span class="text-xs" style="color:var(--muted);">
                            @if($i == 1) Baru @elseif($i == 2) Dasar @elseif($i == 3) Menengah @elseif($i == 4) Mahir @else Ahli @endif
                        </span>
                    </label>
                    @endfor
                </div>
            </div>

            <input type="hidden" name="type" value="{{ $skill->validation_type !== 'none' ? $skill->validation_type : 'reflection' }}">

            <div class="flex gap-3 mt-2">
                <button type="submit" class="btn btn-primary">Simpan Refleksi</button>
                <a href="{{ route('skill-progress') }}" class="btn btn-ghost">Kembali</a>
            </div>
        </form>
    </div>
</x-layouts.app>
