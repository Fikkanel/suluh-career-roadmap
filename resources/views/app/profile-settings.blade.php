<x-layouts.app title="Pengaturan Profil">
    <x-slot:heading>Pengaturan Profil</x-slot:heading>

    <div class="max-w-2xl">
        <div class="mb-6">
            <h2 class="text-xl font-bold mb-2" style="color:var(--fg);">Profil Publik & Eksposur Karir</h2>
            <p style="color:var(--muted);">Atur bagaimana profilmu terlihat oleh publik (termasuk perekrut). Sesuai dengan prinsip Otonomi Pengguna Suluh, kamu memiliki kendali penuh atas privasi datamu.</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-md" style="background:var(--success-subtle);color:var(--success);border:1px solid var(--success);">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-md" style="background:var(--error-subtle);color:var(--error);border:1px solid var(--error);">
                <ul class="list-disc pl-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form Upload Avatar --}}
        <div class="card p-6 mb-6">
            <h3 class="font-semibold mb-4" style="color:var(--fg);">Foto Profil</h3>
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <div style="display: flex; align-items: center; justify-content: center; font-weight: bold; overflow: hidden; width: 80px; height: 80px; min-width: 80px; flex-shrink: 0; border-radius: 50%; background:var(--accent-subtle); color:var(--accent); border:2px solid var(--accent); font-size: 2rem;">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </div>
                <form action="{{ route('profile.avatar.upload') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 0.75rem; flex: 1;">
                    @csrf
                    <div>
                        <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/webp" required style="font-size: 0.875rem; color: var(--fg); max-width: 100%;">
                        <p class="text-xs mt-1" style="color:var(--muted); margin-top: 0.5rem;">Format: JPG, PNG, WebP (Max 2MB)</p>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary btn-sm">Simpan Foto</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card p-6">
            <form action="{{ route('profile.settings.update') }}" method="POST" style="display:flex; flex-direction:column; gap:1.5rem;">
                @csrf
                
                <div class="mb-4">
                    <label for="name" class="block font-medium mb-1" style="color:var(--fg);">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full px-3 py-2" style="background:var(--bg); border:1px solid var(--border); border-radius:0.375rem; color:var(--fg); outline:none;" placeholder="Nama Lengkap Kamu">
                </div>

                <div class="flex items-start gap-4 p-4 rounded-md" style="background:var(--bg-subtle);border:1px solid var(--border);">
                    <div class="pt-1">
                        <input type="hidden" name="is_profile_public" value="0">
                        <input type="checkbox" name="is_profile_public" id="is_profile_public" value="1" class="w-5 h-5 rounded" {{ $user->is_profile_public ? 'checked' : '' }} style="accent-color:var(--accent);">
                    </div>
                    <div>
                        <label for="is_profile_public" class="font-semibold block mb-1" style="color:var(--fg);cursor:pointer;">Tampilkan Profil Publik</label>
                        <p class="text-sm" style="color:var(--muted);">Jika diaktifkan, siapapun yang memiliki link dapat melihat perkembangan skill dan karirmu.</p>
                    </div>
                </div>

                <div>
                    <label for="public_username" class="block font-medium mb-1" style="color:var(--fg);">Username URL</label>
                    <p class="text-xs mb-2" style="color:var(--muted);">Hanya boleh huruf, angka, strip (-), dan underscore (_). Minimal 4 karakter.</p>
                    <div class="flex items-center" style="border: 1px solid var(--border); border-radius: 0.375rem; overflow: hidden; background: var(--bg);">
                        <span class="px-3 py-2 text-sm" style="background:var(--surface); border-right: 1px solid var(--border); color:var(--muted);">suluh.com/u/</span>
                        <input type="text" name="public_username" id="public_username" value="{{ old('public_username', $user->public_username) }}" class="flex-1 px-3 py-2" style="background:transparent; border:none; outline:none; color:var(--fg);" placeholder="nama-kamu">
                    </div>
                </div>

                <div class="pt-4 border-t" style="border-color:var(--border);">
                    <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                    
                    @if($user->is_profile_public && $user->public_username)
                        <a href="{{ route('public.profile', $user->public_username) }}" target="_blank" class="btn btn-secondary ml-3 inline-flex items-center gap-2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                            Lihat Profil Publik
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
