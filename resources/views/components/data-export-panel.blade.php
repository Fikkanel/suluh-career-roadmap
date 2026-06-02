@props([
    'availableFormats' => ['pdf', 'json'],
    'lastExportAt'     => null,
    'includedData'     => [],
    'status'           => 'idle',
])

<div class="card" aria-live="polite" aria-atomic="true">
    <h3 class="text-base font-semibold mb-1" style="margin-top:0;">Ekspor Data Kamu</h3>
    <p class="text-sm mb-4" style="color:var(--muted);">
        Semua data milikmu tersedia untuk diunduh kapan saja, tanpa syarat.
    </p>

    {{-- Included data list --}}
    @if(count($includedData) > 0)
        <div class="mb-4">
            <p class="text-xs font-semibold mb-2" style="color:var(--muted);">Data yang akan diekspor:</p>
            <ul class="text-sm flex flex-col gap-1" style="padding-left:1rem;">
                @foreach($includedData as $item)
                    <li>✓ {{ $item }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Status --}}
    @if($status === 'generating')
        <div class="alert alert-neutral mb-4">
            <span class="skeleton inline-block w-4 h-4 mr-2 align-middle"></span>
            Sedang mempersiapkan ekspor... ini biasanya membutuhkan beberapa detik.
        </div>
    @elseif($status === 'ready')
        <div class="alert alert-success mb-4">
            ✓ Ekspor siap diunduh.
        </div>
    @elseif($status === 'failed')
        <div class="alert alert-danger mb-4">
            Ekspor gagal. Silakan coba lagi.
        </div>
    @endif

    {{-- Action buttons --}}
    <div class="flex flex-wrap gap-3">
        @if(in_array('pdf', $availableFormats))
            <form action="{{ route('export.pdf') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn btn-secondary">
                    📄 Unduh PDF
                </button>
            </form>
        @endif
        @if(in_array('json', $availableFormats))
            <form action="{{ route('export.json') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn btn-ghost">
                    📦 Unduh JSON (data mentah)
                </button>
            </form>
        @endif
    </div>

    @if($lastExportAt)
        <p class="text-xs mt-3" style="color:var(--muted);">
            Ekspor terakhir: {{ $lastExportAt }}
        </p>
    @endif

    <x-privacy-notice
        title="Ekspor bersifat privat"
        body="File ekspor hanya diakses oleh kamu. Kami tidak menyimpan salinan dan tidak melacak konten yang kamu unduh."
        variant="compact" />
</div>
