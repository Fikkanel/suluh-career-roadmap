@props([
    'title'       => '',
    'level'       => 'beginner',
    'duration'    => '',
    'status'      => 'not_started',
    'description' => '',
    'skillCount'  => 0,
    'nextAction'  => '',
])

@php
$statusLabel = [
    'not_started' => 'Belum Dimulai',
    'learning'    => 'Sedang Belajar',
    'in_progress' => 'Dalam Proses',
    'done'        => 'Selesai',
    'archived'    => 'Diarsipkan',
][$status] ?? $status;

$statusClass = [
    'not_started' => 'badge-not-started',
    'learning'    => 'badge-learning',
    'in_progress' => 'badge-in-progress',
    'done'        => 'badge-done',
    'archived'    => 'badge-archived',
][$status] ?? 'badge-default';

$levelLabel = [
    'beginner'     => 'Pemula',
    'intermediate' => 'Menengah',
    'advanced'     => 'Lanjutan',
][$level] ?? $level;

$levelClass = [
    'beginner'     => 'badge-beginner',
    'intermediate' => 'badge-intermediate',
    'advanced'     => 'badge-advanced',
][$level] ?? 'badge-default';
@endphp

@php
$stripClass = [
    'not_started' => 'strip-not-started',
    'learning'    => 'strip-learning',
    'in_progress' => 'strip-in-progress',
    'done'        => 'strip-done',
    'archived'    => 'strip-archived',
][$status] ?? 'strip-not-started';
@endphp

<article class="card{{ $status !== 'archived' ? ' card-hover' : '' }} flex gap-4" role="article"
         style="{{ $status === 'archived' ? 'opacity:.65;' : '' }}padding:0;overflow:hidden;">
    {{-- Status strip --}}
    <div class="roadmap-status-strip {{ $stripClass }}" style="min-height:100%;width:4px;flex-shrink:0;"></div>

    {{-- Content --}}
    <div style="padding:1.25rem 1.25rem 1.25rem 0;flex:1;min-width:0;">
        <div class="flex items-start justify-between gap-3 flex-wrap mb-2">
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="{{ $levelClass }}">{{ $levelLabel }}</span>
                <span class="{{ $statusClass }}">{{ $statusLabel }}</span>
            </div>
            @if($duration)
                <span class="text-xs" style="color:var(--muted);font-family:var(--font-mono);">{{ $duration }}</span>
            @endif
        </div>

        <h3 class="font-semibold mb-1" style="font-size:.9375rem;margin-top:0;line-height:1.4;">{{ $title }}</h3>

        @if($description)
            <p class="text-sm mb-3" style="color:var(--muted);line-height:1.55;">{{ $description }}</p>
        @endif

        <div class="flex items-center justify-between flex-wrap gap-2 mt-2">
            <span class="text-xs" style="color:var(--muted);">{{ $skillCount }} skill</span>
            @if($status === 'done')
                <span class="text-xs flex items-center gap-1" style="color:var(--success);">
                    <svg aria-hidden="true" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Selesai
                </span>
            @elseif($nextAction && $status !== 'archived')
                <span class="text-xs" style="color:var(--accent);">{{ $nextAction }}</span>
            @endif
        </div>
    </div>
</article>
