@php
    $layout = auth()->check() ? 'layouts.app' : 'layouts.public';
@endphp

<x-dynamic-component :component="$layout" title="Komite Etika Data">
    @if(auth()->check())
        <x-slot:heading>Komite Etika Data</x-slot:heading>
    @endif

    <div class="max-w-4xl mx-auto {{ auth()->check() ? '' : 'page-padding py-14' }}">
        
        <!-- Hero -->
        <div class="text-center pb-8 {{ auth()->check() ? '' : 'pt-4' }}">
            <h1 class="text-3xl font-bold mb-4">Log Transparansi Etika Data</h1>
            <p class="text-lg max-w-2xl mx-auto" style="color:var(--muted);">
                Sesuai Prinsip Konstitusi P1 dan P2, setiap keputusan yang melibatkan pembagian data pengguna ke pihak ketiga atau penggunaan algoritma (AI) diputuskan secara terbuka melalui voting komite independen.
            </p>
        </div>

        @if(session('success'))
            <div class="p-4 mb-6 rounded-md text-center font-medium" style="background:var(--success-subtle);color:var(--success);border:1px solid var(--success);">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 mb-6 rounded-md text-center font-medium" style="background:var(--error-subtle);color:var(--error);border:1px solid var(--error);">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid gap-6">
            @foreach($decisions as $log)
                <div class="card p-6 border-l-4" style="border-left-color: 
                    {{ $log->status === 'approved' ? 'var(--success)' : ($log->status === 'rejected' ? 'var(--error)' : 'var(--accent-warm)') }}">
                    
                    <div class="flex justify-between items-start mb-4">
                        <h2 class="text-xl font-bold">{{ $log->title }}</h2>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider" 
                              style="background:var(--surface); border:1px solid var(--border); 
                                     color: {{ $log->status === 'approved' ? 'var(--success)' : ($log->status === 'rejected' ? 'var(--error)' : 'var(--accent-warm)') }}">
                            {{ $log->status }}
                        </span>
                    </div>
                    
                    <p class="mb-4 text-sm" style="color:var(--muted); line-height:1.6;"><strong>Konteks:</strong> {{ $log->context }}</p>
                    
                    @if($log->decision)
                        <div class="p-4 rounded mb-4 text-sm" style="background:var(--bg-subtle); border:1px solid var(--border);">
                            <strong style="color:var(--fg);">Keputusan Komite:</strong> <br>
                            <span style="color:var(--muted);">{{ $log->decision }}</span>
                        </div>
                    @endif

                    <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t" style="border-color:var(--border);">
                        <div class="flex items-center gap-6 text-sm font-medium">
                            <span class="flex items-center gap-2" style="color:var(--success);">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
                                {{ $log->votes_for }} Setuju
                            </span>
                            <span class="flex items-center gap-2" style="color:var(--error);">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zm7-13h2.67A2.31 2.31 0 0 1 22 4v7a2.31 2.31 0 0 1-2.33 2H17"></path></svg>
                                {{ $log->votes_against }} Menolak
                            </span>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <span class="text-xs" style="color:var(--muted);">{{ $log->created_at->format('d M Y') }}</span>
                            
                            @if($log->status === 'voting' && auth()->check())
                                <form action="{{ route('ethics.vote', $log->id) }}" method="POST" class="flex gap-2">
                                    @csrf
                                    <button type="submit" name="vote" value="for" class="btn btn-secondary btn-sm !px-3" style="color:var(--success);border-color:var(--success);">Setuju</button>
                                    <button type="submit" name="vote" value="against" class="btn btn-secondary btn-sm !px-3" style="color:var(--error);border-color:var(--error);">Tolak</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <footer class="text-center pt-8 border-t mt-12 text-sm" style="border-color:var(--border);color:var(--muted);">
            &copy; {{ date('Y') }} Suluh Platform. Data Etika adalah Hak Asasi Pengguna.
        </footer>
    </div>
</x-dynamic-component>
