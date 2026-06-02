<x-layouts.auth title="Daftar" subtitle="Mulai perjalanan karirmu bersama Suluh">

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mt-2 ml-4 list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.post') }}" class="flex flex-col gap-4 mt-2">
        @csrf
        <div>
            <label class="label" for="name">Nama</label>
            <input type="text" id="name" name="name" class="input @error('name') border-red-500 @enderror" value="{{ old('name') }}" required autofocus>
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="label" for="email">Email</label>
            <input type="email" id="email" name="email" class="input @error('email') border-red-500 @enderror" value="{{ old('email') }}" required>
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="label" for="password">Kata Sandi</label>
            <input type="password" id="password" name="password" class="input @error('password') border-red-500 @enderror" required>
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @else
                <p class="helper-text">Minimal 8 karakter.</p>
            @enderror
        </div>
        <div>
            <label class="label" for="password_confirmation">Konfirmasi Kata Sandi</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="input @error('password') border-red-500 @enderror" required>
        </div>
        <button type="submit" class="btn btn-primary w-full justify-center mt-1">Buat Akun Gratis</button>
    </form>

    {{-- Privacy notice dipindah ke bawah sebagai teks kecil --}}
    <p class="text-xs mt-4 flex items-start gap-1.5" style="color:var(--muted);line-height:1.55;">
        <span>🔒</span>
        <span>Kami hanya meminta nama dan email. Tidak ada data tambahan saat registrasi.</span>
    </p>

    <div class="flex items-center gap-3 mt-4">
        <hr style="flex:1;border:none;border-top:1px solid var(--border);">
        <span class="text-xs" style="color:var(--muted);white-space:nowrap;">atau daftar dengan</span>
        <hr style="flex:1;border:none;border-top:1px solid var(--border);">
    </div>

    <a href="{{ route('auth.google') }}" class="btn btn-secondary w-full justify-center gap-2.5 mt-3">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
        Daftar dengan Google
    </a>

    <p class="text-center mt-4 text-sm" style="color:var(--muted);">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
    </p>

    <div class="mt-5 pt-4 border-t text-center" style="border-color:var(--border);">
        <p class="text-xs" style="color:var(--muted);">Mewakili kampus atau lembaga pelatihan?</p>
        <a href="{{ route('register.institution') }}" class="text-sm font-medium mt-1 inline-block" style="color:var(--accent);">
            🏛️ Daftar sebagai Institusi Mitra →
        </a>
    </div>

</x-layouts.auth>


