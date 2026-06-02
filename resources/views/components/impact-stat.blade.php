@props([
    'label'       => '',
    'value'       => '—',
    'explanation' => '',
    'updatedAt'   => null,
    'variant'     => 'neutral',
])

@php
$accentColor = match($variant) {
    'positive'  => 'var(--success)',
    'monitored' => 'var(--warning)',
    default     => 'var(--accent)',
};

// Ekstrak angka dari value untuk counter animation
preg_match('/[\d.]+/', $value, $numMatch);
$numericVal = $numMatch[0] ?? null;
$suffix     = $numericVal ? str_replace($numericVal, '', $value) : null;
@endphp

<div class="card card-hover h-full flex flex-col" data-reveal>
    <p class="stat-label mb-3">{{ $label }}</p>
    <p class="stat-number mb-2"
       style="color:{{ $accentColor }};"
       @if($numericVal)
           data-counter="{{ $numericVal }}"
           data-counter-suffix="{{ $suffix }}"
       @endif
    >{{ $value }}</p>
    @if($explanation)
        <p class="text-sm flex-1" style="color:var(--muted);line-height:1.5;">{{ $explanation }}</p>
    @endif
    @if($updatedAt)
        <p class="text-xs mt-3" style="color:var(--muted);opacity:.7;">Diperbarui: {{ $updatedAt }}</p>
    @endif
</div>
