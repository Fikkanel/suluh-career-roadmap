@props([
    'title'         => 'Catatan Privasi',
    'body'          => '',
    'dataUsed'      => [],
    'controlAction' => '',
    'variant'       => 'compact',
])

@if($variant === 'compact')
    <div class="privacy-notice mt-3 flex items-start gap-2">
        <span aria-hidden="true">🔒</span>
        <div>
            <p class="font-medium text-sm">{{ $title }}</p>
            @if($body)
                <p class="text-sm mt-0.5" style="color:var(--muted);">{{ $body }}</p>
            @endif
        </div>
    </div>
@else
    <div class="privacy-notice mt-4" x-data="{ open: false }">
        <button type="button"
                class="flex items-center gap-2 w-full text-left font-medium text-sm"
                @click="open = !open"
                :aria-expanded="open">
            <span aria-hidden="true">🔒</span>
            {{ $title }}
            <span class="ml-auto text-xs" style="color:var(--muted);" x-text="open ? '▲ Tutup' : '▼ Detail'">▼ Detail</span>
        </button>

        <div x-show="open" x-collapse class="mt-3">
            @if($body)
                <p class="text-sm mb-2" style="color:var(--fg);">{{ $body }}</p>
            @endif

            @if(count($dataUsed) > 0)
                <div class="mt-2">
                    <p class="text-xs font-semibold mb-1" style="color:var(--muted);">Data yang digunakan:</p>
                    <ul class="text-sm flex flex-col gap-0.5" style="padding-left:1rem;">
                        @foreach($dataUsed as $item)
                            <li style="color:var(--fg);">{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($controlAction)
                <p class="text-sm mt-3">
                    <strong>Kendalimu:</strong> {{ $controlAction }}
                </p>
            @endif
        </div>
    </div>
@endif
