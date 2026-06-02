@props([
    'name'         => '',
    'level'        => 'beginner',
    'status'       => 'not_started',
    'transferable' => false,
])

@php
$baseClass = match($level) {
    'beginner'     => 'badge-beginner',
    'intermediate' => 'badge-intermediate',
    'advanced'     => 'badge-advanced',
    default        => 'badge-default',
};
$icon = match($status) {
    'done'        => '✓',
    'in_progress' => '↻',
    'learning'    => '▶',
    default       => '○',
};
@endphp

<span class="{{ $baseClass }} {{ $transferable ? 'ring-1 ring-offset-1' : '' }}"
      title="{{ $transferable ? 'Skill yang bisa ditransfer ke karir baru' : '' }}"
      style="{{ $transferable ? 'outline: 1px dashed var(--info);' : '' }}">
    <span aria-hidden="true" class="mr-1">{{ $icon }}</span>
    {{ $name }}
    @if($transferable)
        <span class="ml-1 text-xs" style="color:var(--info);" aria-label="dapat ditransfer">⇄</span>
    @endif
</span>
