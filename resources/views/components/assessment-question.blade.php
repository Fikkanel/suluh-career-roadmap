@props([
    'prompt'         => '',
    'context'        => '',
    'options'        => [],
    'selectedOption' => null,
    'step'           => 1,
    'total'          => 10,
    'questionId'     => null,
    'type'           => 'single_choice',
])

<div class="card" role="group" aria-labelledby="q-{{ $questionId }}-label">
    {{-- Progress indicator --}}
    <div class="flex items-center justify-between mb-4">
        <span class="section-label">Pertanyaan {{ $step }} dari {{ $total }}</span>
        <x-progress-bar :value="$step - 1" :max="$total" class="w-32" tone="default" />
    </div>

    {{-- Question --}}
    <fieldset>
        <legend id="q-{{ $questionId }}-label" class="text-base font-semibold mb-2" style="font-size:1.0625rem;line-height:1.5;">
            {{ $prompt }}
        </legend>
        @if($context)
            <p class="text-sm mb-4" style="color:var(--muted);">{{ $context }}</p>
        @endif

        @if($type === 'single_choice')
            <div class="flex flex-col gap-2">
                @foreach($options as $key => $label)
                    <label class="flex items-start gap-3 p-3 rounded-card cursor-pointer transition-colors"
                           style="border:1px solid var(--border);background:var(--surface);"
                           onmouseover="this.style.borderColor='var(--accent)'"
                           onmouseout="this.style.borderColor='{{ $selectedOption == $key ? 'var(--accent)' : 'var(--border)' }}'">
                        <input type="radio"
                               name="answers[{{ $questionId }}]"
                               value="{{ $key }}"
                               {{ $selectedOption == $key ? 'checked' : '' }}
                               required
                               class="mt-0.5 accent-green-700"
                               wire:model.live="answers.{{ $questionId }}">
                        <span class="text-sm">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        @elseif($type === 'scale')
            <div class="flex items-center gap-3 mt-2">
                <span class="text-xs" style="color:var(--muted);">Tidak sesuai</span>
                <div class="flex gap-2 flex-1 justify-center">
                    @foreach(range(1, 10) as $i)
                        <label class="flex flex-col items-center gap-1 cursor-pointer">
                            <input type="radio"
                                   name="answers[{{ $questionId }}]"
                                   value="{{ $i }}"
                                   {{ $selectedOption == $i ? 'checked' : '' }}
                                   required
                                   wire:model.live="answers.{{ $questionId }}">
                            <span class="text-sm font-mono">{{ $i }}</span>
                        </label>
                    @endforeach
                </div>
                <span class="text-xs" style="color:var(--muted);">Sangat sesuai</span>
            </div>
        @elseif($type === 'text_reflection')
            <div class="mt-2">
<textarea name="answers[{{ $questionId }}]"
                          rows="4"
                          class="input"
                          placeholder="Tuliskan refleksimu di sini..."
                          required
                          wire:model.debounce.500ms="answers.{{ $questionId }}">{{ $selectedOption }}</textarea>
                <p class="helper-text">Tidak ada jawaban benar atau salah. Tuliskan apa yang terlintas.</p>
            </div>
        @endif
    </fieldset>
</div>
