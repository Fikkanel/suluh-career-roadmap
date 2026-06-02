<x-layouts.app title="{{ $career['name'] }}">
    <x-slot:heading>{{ $career['name'] }}</x-slot:heading>

    <div class="max-w-3xl mx-auto">
        <div class="flex flex-wrap items-center gap-2 mb-5">
            <span class="badge badge-accent">{{ $career['industry_standard'] }}</span>
            <span class="badge badge-default" title="Kode RIASEC Holland">RIASEC: {{ $career['riasec_code'] }}</span>
        </div>

        <p class="mb-7 text-base" style="color:var(--muted);line-height:1.7;max-width:48rem;">{{ $career['description'] }}</p>

        <div class="card mb-5">
            <h3 class="font-semibold mb-4" style="font-size:.9375rem;">Skill yang dibutuhkan</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($career['skills'] as $skill)
                    <x-skill-badge :name="$skill['name']" :level="$skill['level']" status="not_started" />
                @endforeach
            </div>
        </div>

        <div class="card mb-6 card-warm-left">
            <div class="flex gap-3">
                <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent-warm)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:.125rem;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <div>
                    <h3 class="font-semibold mb-1.5" style="font-size:.9375rem;">Sebelum kamu memutuskan</h3>
                    <p class="text-sm" style="color:var(--muted);line-height:1.6;">
                        Luangkan waktu untuk mempertimbangkan apakah karir ini selaras dengan keseharianmu.
                        Kamu bisa kembali dan membandingkan opsi lain kapan saja.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-3 mb-4">
            <form method="POST" action="{{ route('career.choose', $career['id']) }}">
                @csrf
                <button type="submit" class="btn btn-primary">Pilih karir ini</button>
            </form>
            <a href="{{ route('assessment.result') }}" class="btn btn-ghost">Bandingkan opsi lain</a>
        </div>

        <p class="text-sm" style="color:var(--muted);">
            Kamu bisa mengubah pilihanmu kapan saja melalui fitur Pivot di dashboard.
        </p>
    </div>
</x-layouts.app>
