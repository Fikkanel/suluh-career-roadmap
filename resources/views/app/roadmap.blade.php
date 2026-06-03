<x-layouts.app title="Roadmap">
    <x-slot:heading>Roadmap — {{ $career }}</x-slot:heading>

    <div class="max-w-3xl mx-auto">
        {{-- Skill Gap Chart --}}
        <div class="card mb-8">
            <h2 class="font-semibold mb-4">Analisis Skill Gap</h2>
            <x-skill-gap-chart
                :dimensions="$dimensions"
                :userValues="$userValues"
                :targetValues="$targetValues" />
        </div>

        @if(!empty($guidance))
            <div class="card card-accent-left mb-8" style="background:var(--accent-soft);border-left-color:var(--accent);">
                <p class="text-sm font-semibold mb-1" style="color:var(--accent);">🧭 Arahan Pembimbing AI:</p>
                <p class="text-sm" style="color:var(--fg);line-height:1.65;">
                    {{ $guidance }}
                </p>
            </div>
        @endif

        {{-- Roadmap Stages --}}
        <h2 class="font-semibold mb-4">Tahapan Roadmap</h2>
        <div class="flex flex-col gap-4">
            @foreach($stages as $stage)
                <x-roadmap-card
                    :title="$stage['title']"
                    :level="$stage['level']"
                    :duration="$stage['duration']"
                    :status="$stage['status']"
                    :skillCount="$stage['skillCount']"
                    :description="$stage['description']"
                    :nextAction="$stage['nextAction']" />
            @endforeach
        </div>

        <div class="mt-6 flex gap-3">
            <a href="{{ route('skill-progress') }}" class="btn btn-primary">Update Progress Skill →</a>
            <a href="{{ route('pivot') }}" class="btn btn-ghost">Ingin mengubah arah?</a>
        </div>
    </div>
</x-layouts.app>
