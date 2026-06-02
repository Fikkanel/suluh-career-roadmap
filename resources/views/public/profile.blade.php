@php
    $layout = auth()->check() ? 'layouts.app' : 'layouts.public';
@endphp

<x-dynamic-component :component="$layout" title="Profil {{ $user->name }} — Suluh">
    @if(auth()->check())
        <x-slot:heading>Profil {{ $user->name }}</x-slot:heading>
    @endif

    <div class="max-w-4xl mx-auto space-y-8 py-10">
        <!-- Profile Hero -->
        <div class="card p-8 text-center flex flex-col items-center justify-center">
            <div class="w-24 h-24 rounded-full mb-4 flex items-center justify-center text-3xl font-bold overflow-hidden" style="background:var(--accent-subtle);color:var(--accent);border:2px solid var(--accent);">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                @endif
            </div>
            <h1 class="text-3xl font-bold mb-2">{{ $user->name }}</h1>
            <p class="text-lg mb-4" style="color:var(--muted);">Calon <strong style="color:var(--accent);">{{ $user->currentCareer->name ?? 'Talenta Profesional' }}</strong></p>
            
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold" style="background:var(--surface);border:1px solid var(--border);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                {{ $doneProgress->count() }} Skill Terselesaikan
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <!-- Skills Section -->
            <div class="space-y-4">
                <h2 class="text-xl font-bold">Portofolio Skill</h2>
                @if($doneProgress->count() > 0)
                    <div class="card p-0 overflow-hidden">
                        <ul class="divide-y" style="border-color:var(--border);">
                            @foreach($doneProgress as $progress)
                                <li class="p-4 flex items-start gap-3">
                                    <svg class="mt-0.5 text-green-500 shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                    <div>
                                        <p class="font-semibold">{{ $progress->skill->name }}</p>
                                        <p class="text-xs mt-1 uppercase tracking-wider" style="color:var(--muted);">Tingkat: {{ $progress->skill->level }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="card p-6 text-center text-sm" style="color:var(--muted);">
                        Belum ada skill yang dipublikasikan.
                    </div>
                @endif
            </div>

            <!-- Job Opportunities (Mock Data) -->
            <div class="space-y-4">
                <h2 class="text-xl font-bold flex items-center gap-2">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                    Peluang Karir Cocok
                </h2>
                <p class="text-sm" style="color:var(--muted);">Berdasarkan capaian skill {{ $user->name }}, sistem mendeteksi kesesuaian dengan posisi berikut (Mock API Jobstreet/Glints):</p>
                
                <div class="space-y-3">
                    @forelse($jobs as $job)
                        <div class="card p-4 hover:shadow-md transition-shadow relative overflow-hidden" style="border-left:4px solid var(--accent);">
                            <div class="absolute top-0 right-0 px-3 py-1 text-xs font-bold rounded-bl-lg" style="background:var(--accent-subtle);color:var(--accent);">
                                Kesesuaian {{ $job['match_score'] }}%
                            </div>
                            <h3 class="font-bold text-lg pr-20">{{ $job['title'] }}</h3>
                            <p class="text-sm font-medium mt-1">{{ $job['company'] }}</p>
                            <div class="flex items-center gap-4 mt-3 text-xs" style="color:var(--muted);">
                                <span class="flex items-center gap-1">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                    {{ $job['location'] }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                                    {{ $job['type'] }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="card p-6 text-center text-sm" style="color:var(--muted);">
                            Sistem belum menemukan lowongan yang cocok dengan roadmap ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <footer class="text-center pt-8 border-t mt-12 text-sm" style="border-color:var(--border);color:var(--muted);">
            &copy; 2026 Suluh Platform. Ditenagai oleh AI dan Komunitas.
        </footer>
    </div>
</x-dynamic-component>

