<x-layouts.app title="Survei Dampak — {{ $type === '3_months' ? '3 Bulan' : '6 Bulan' }}">
    <x-slot:heading>Survei Dampak {{ $type === '3_months' ? 'Bulan ke-3' : 'Bulan ke-6' }}</x-slot:heading>

    <div class="max-w-xl mx-auto">
        <div class="alert alert-neutral mb-6">
            <strong>Mengapa kami bertanya ini?</strong> Survei ini membantu kami memahami dampak nyata platform terhadap perjalanan karirmu. Semua jawaban bersifat anonim dan tidak akan dijual ke pihak ketiga.
        </div>

        @if($survey->submitted_at)
        <div class="card mb-6 card-warm-left">
            <p class="text-sm font-semibold mb-1">Survei Sudah Diisi</p>
            <p class="text-sm" style="color:var(--muted);">Kamu sudah mengisi survei ini pada {{ $survey->submitted_at->format('d M Y') }}. Kamu bisa mengubah jawabanmu jika mau.</p>
        </div>
        @endif

        <form method="POST" action="{{ route('survey.store', $type) }}" class="flex flex-col gap-5">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-2">Saat ini, apakah kamu sudah bekerja atau sedang mencari pekerjaan?</label>
                <div class="flex flex-col gap-2">
                    @foreach(['yes' => 'Sudah bekerja', 'looking' => 'Sedang mencari pekerjaan', 'no' => 'Belum bekerja'] as $value => $label)
                    <label class="flex items-center gap-3 p-3 rounded-lg cursor-pointer" style="background:var(--bg-subtle);">
                        <input type="radio" name="employed" value="{{ $value }}" class="accent-current"
                            {{ old('employed', $survey->answers['employed'] ?? '') === $value ? 'checked' : '' }} required>
                        <span class="text-sm" style="color:var(--fg);">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                @error('employed') <p class="text-xs mt-1" style="color:var(--error);">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Apakah kamu mengalami perubahan karir setelah menggunakan Suluh?</label>
                <div class="flex gap-3">
                    @foreach(['yes' => 'Ya', 'no' => 'Tidak'] as $value => $label)
                    <label class="flex items-center gap-2 p-3 rounded-lg cursor-pointer flex-1" style="background:var(--bg-subtle);">
                        <input type="radio" name="career_change" value="{{ $value }}" class="accent-current"
                            {{ old('career_change', $survey->answers['career_change'] ?? '') === $value ? 'checked' : '' }} required>
                        <span class="text-sm" style="color:var(--fg);">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                @error('career_change') <p class="text-xs mt-1" style="color:var(--error);">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Apakah kamu mengalami perubahan posisi (naik jabatan, pindah divisi)?</label>
                <div class="flex gap-3">
                    @foreach(['yes' => 'Ya', 'no' => 'Tidak'] as $value => $label)
                    <label class="flex items-center gap-2 p-3 rounded-lg cursor-pointer flex-1" style="background:var(--bg-subtle);">
                        <input type="radio" name="position_change" value="{{ $value }}" class="accent-current"
                            {{ old('position_change', $survey->answers['position_change'] ?? '') === $value ? 'checked' : '' }} required>
                        <span class="text-sm" style="color:var(--fg);">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                @error('position_change') <p class="text-xs mt-1" style="color:var(--error);">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Seberapa puas kamu dengan perjalanan karirmu saat ini? (1 = Sangat Tidak Puas, 5 = Sangat Puas)</label>
                <div class="flex gap-2">
                    @for($i = 1; $i <= 5; $i++)
                    <label class="flex flex-col items-center gap-1 cursor-pointer">
                        <input type="radio" name="satisfaction" value="{{ $i }}" class="sr-only peer"
                            {{ old('satisfaction', $survey->answers['satisfaction'] ?? '') == $i ? 'checked' : '' }}>
                        <span class="w-10 h-10 rounded-lg flex items-center justify-center text-sm font-medium peer-checked:bg-accent peer-checked:text-white"
                              style="background:var(--bg-subtle);color:var(--muted);">
                            {{ $i }}
                        </span>
                    </label>
                    @endfor
                </div>
                @error('satisfaction') <p class="text-xs mt-1" style="color:var(--error);">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5" for="feedback">Ceritakan pengalamanmu (opsional)</label>
                <textarea
                    name="feedback"
                    id="feedback"
                    rows="4"
                    class="w-full rounded-lg border p-3 text-sm"
                    style="background:var(--bg);border-color:var(--border);color:var(--fg);"
                    placeholder="Apa yang paling membantu? Apa yang bisa diperbaiki?"
                >{{ old('feedback', $survey->answers['feedback'] ?? '') }}</textarea>
            </div>

            <div class="flex gap-3 mt-2">
                <button type="submit" class="btn btn-primary">Kirim Survei</button>
                <a href="{{ route('dashboard') }}" class="btn btn-ghost">Kembali ke Dashboard</a>
            </div>
        </form>
    </div>
</x-layouts.app>
