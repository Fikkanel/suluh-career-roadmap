@props([
    'prompt'     => '',
    'helper'     => '',
    'savedValue' => '',
    'variant'    => 'pivot',
    'fieldName'  => 'reflection',
])

@php
$borderColor = match($variant) {
    'pivot'      => 'var(--accent-warm)',
    'validation' => 'var(--accent)',
    'progress'   => 'var(--success)',
    default      => 'var(--border)',
};
$icon = match($variant) {
    'pivot'      => '🔀',
    'validation' => '💡',
    'progress'   => '📝',
    default      => '💬',
};
@endphp

<div class="card" style="border-left: 3px solid {{ $borderColor }};">
    <div class="flex items-start gap-2 mb-3">
        <span aria-hidden="true" class="text-lg">{{ $icon }}</span>
        <label class="label" for="{{ $fieldName }}" style="margin:0;">{{ $prompt }}</label>
    </div>

    @if($helper)
        <p class="helper-text mb-3">{{ $helper }}</p>
    @endif

    <textarea
        id="{{ $fieldName }}"
        name="{{ $fieldName }}"
        rows="4"
        class="input"
        placeholder="Tuliskan refleksimu di sini... tidak ada jawaban benar atau salah."
        wire:model.debounce.800ms="{{ $fieldName }}">{{ $savedValue }}</textarea>

    <p class="text-xs mt-2" style="color:var(--muted);">
        Refleksimu tersimpan otomatis. Tidak akan dikirim sebelum kamu konfirmasi.
    </p>
</div>
