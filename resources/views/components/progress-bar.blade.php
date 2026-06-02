@props([
    'value' => 0,
    'max'   => 100,
    'label' => '',
    'tone'  => 'default',
])

@php
$pct        = $max > 0 ? min(100, round(($value / $max) * 100)) : 0;
$fillClass  = match($tone) {
    'warm'    => 'progress-fill-warm',
    'success' => 'progress-fill-success',
    'info'    => 'progress-fill-info',
    default   => '',
};
@endphp

<div {{ $attributes }}>
    @if($label)
        <div class="flex items-center justify-between mb-1">
            <span class="text-sm font-medium" style="color:var(--fg);">{{ $label }}</span>
            <span class="text-sm" style="font-family:var(--font-mono);color:var(--muted);">{{ $pct }}%</span>
        </div>
    @endif
    <div class="progress-track"
         role="progressbar"
         aria-valuenow="{{ $value }}"
         aria-valuemin="0"
         aria-valuemax="{{ $max }}"
         aria-label="{{ $label ?: 'Kemajuan' }}: {{ $pct }}%">
        <div class="progress-fill {{ $fillClass }}" style="width:{{ $pct }}%;"></div>
    </div>
</div>
