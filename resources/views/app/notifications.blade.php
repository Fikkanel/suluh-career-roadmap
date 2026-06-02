<x-layouts.app title="Notifikasi">
    <x-slot:heading>Notifikasi</x-slot:heading>

    <div class="mb-6 max-w-3xl">
        <h2 class="text-xl font-bold mb-2" style="color:var(--fg);">Pesan Kontekstual</h2>
        <p style="color:var(--muted);">Pengingat dan apresiasi otomatis berdasarkan perkembangan Career Readiness Score (CRS) milikmu.</p>
    </div>

    <div class="grid gap-4 max-w-3xl">
        @forelse($notifications as $notif)
            @php
                $isUnread = is_null($notif->read_at);
                $type = $notif->data['type'] ?? 'encouragement';
                $toneColors = [
                    'encouragement' => 'var(--accent)',
                    'milestone' => 'var(--accent-warm)',
                    'stagnant' => 'var(--muted)'
                ];
                $color = $toneColors[$type] ?? 'var(--accent)';
            @endphp
            
            <div class="card p-4 flex gap-4 {{ $isUnread ? 'ring-1' : '' }}" style="{{ $isUnread ? 'ring-color:var(--accent);background:var(--bg-subtle);' : '' }}">
                <div class="flex-shrink-0 mt-1">
                    <div style="width:2.5rem;height:2.5rem;border-radius:50%;background:{{ $isUnread ? 'var(--bg)' : 'transparent' }};border:1px solid {{ $color }};display:flex;align-items:center;justify-content:center;color:{{ $color }};">
                        @if($type === 'milestone')
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        @elseif($type === 'encouragement')
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        @else
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        @endif
                    </div>
                </div>
                
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start mb-1 gap-2">
                        <p class="font-semibold text-sm" style="color:var(--fg);">{{ ucfirst($type) }}</p>
                        <p class="text-xs whitespace-nowrap" style="color:var(--muted);">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>
                    
                    <p class="text-sm mb-3" style="color:var(--muted);line-height:1.6;">{{ $notif->data['message'] ?? 'Tidak ada pesan.' }}</p>
                    
                    @if($isUnread)
                        <form action="{{ route('notifications.read', $notif->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs font-semibold hover:underline" style="color:var(--accent);background:none;border:none;padding:0;cursor:pointer;">Tandai sudah dibaca</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="card text-center py-10">
                <svg class="mx-auto mb-3" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
                <p class="font-medium mb-1" style="color:var(--fg);">Belum ada notifikasi</p>
                <p class="text-sm" style="color:var(--muted);">Terus pelajari skill-mu, notifikasi akan muncul secara otomatis.</p>
            </div>
        @endforelse
        
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </div>
</x-layouts.app>
