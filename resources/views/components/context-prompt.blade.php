@props([
    'question'    => '',
    'helperText'  => '',
    'sensitivity' => 'optional',
    'options'     => [],
    'fieldName'   => '',
])

@php
$isRequired = $sensitivity === 'required';
$isSensitive = $sensitivity === 'sensitive';
@endphp

<div class="card">
    {{-- Sensitivity label --}}
    <div class="flex items-center gap-2 mb-3">
        @if($isSensitive)
            <span class="badge badge-warm">🔒 Konteks sensitif</span>
        @elseif($isRequired)
            <span class="badge badge-accent">Diperlukan</span>
        @else
            <span class="badge badge-default">Opsional</span>
        @endif
    </div>

    <label class="label" for="{{ $fieldName }}">{{ $question }}</label>
    @if($helperText)
        <p class="helper-text mb-3">{{ $helperText }}</p>
    @endif

    @if(count($options) > 0)
        <div class="flex flex-col gap-2">
            @foreach($options as $value => $label)
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="radio" name="{{ $fieldName }}" value="{{ $value }}" class="accent-green-700">
                    <span class="text-sm">{{ $label }}</span>
                </label>
            @endforeach
        </div>
        @if(!$isRequired)
            <div class="mt-3">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="radio" name="{{ $fieldName }}" value="" class="accent-green-700">
                    <span class="text-sm" style="color:var(--muted);">Lewati pertanyaan ini</span>
                </label>
            </div>
        @endif
    @else
        <input type="text" id="{{ $fieldName }}" name="{{ $fieldName }}" class="input"
               {{ $isRequired ? 'required' : '' }}>
    @endif

    @if($isSensitive)
        <x-privacy-notice
            title="Mengapa kami bertanya ini?"
            body="Informasi ini hanya digunakan untuk menyesuaikan kedalaman panduan, tidak disimpan sebagai data identitas, dan tidak pernah dibagikan."
            variant="compact" />
    @endif
</div>
