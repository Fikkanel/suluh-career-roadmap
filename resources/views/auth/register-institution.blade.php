<x-layouts.auth title="Daftar sebagai Institusi Mitra" subtitle="Bergabunglah sebagai mitra kampus atau lembaga pelatihan">

    @if(session('info'))
        <div class="alert alert-info mb-4">{{ session('info') }}</div>
    @endif

    {{-- Notice Box --}}
    <div class="mb-5 p-4 rounded-lg text-sm" style="background:var(--accent-soft);border:1px solid var(--accent);line-height:1.6;">
        <p class="font-semibold mb-1" style="color:var(--accent);">🏛️ Khusus Institusi Mitra Resmi</p>
        <p style="color:var(--muted);">Halaman ini hanya untuk kampus, lembaga pelatihan, atau institusi riset yang telah mendapatkan <strong>Kode Akses Resmi</strong> dari tim Suluh. Untuk pengguna individual, silakan <a href="{{ route('register') }}" style="color:var(--accent);">daftar di sini</a>.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="ml-4 list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.institution.post') }}" class="flex flex-col gap-4">
        @csrf

        <div>
            <label class="label" for="inst-name">Nama Institusi</label>
            <input type="text" id="inst-name" name="name" class="input @error('name') border-red-500 @enderror"
                   value="{{ old('name') }}" placeholder="contoh: Universitas Nusantara Bangsa" required autofocus>
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="label" for="inst-type">Jenis Institusi</label>
            <select id="inst-type" name="institution_type" class="input @error('institution_type') border-red-500 @enderror">
                <option value="university" {{ old('institution_type') === 'university' ? 'selected' : '' }}>Universitas / Institut / Sekolah Tinggi</option>
                <option value="vocational" {{ old('institution_type') === 'vocational' ? 'selected' : '' }}>Politeknik / SMK</option>
                <option value="training_center" {{ old('institution_type') === 'training_center' ? 'selected' : '' }}>Lembaga Pelatihan / Bootcamp</option>
                <option value="research" {{ old('institution_type') === 'research' ? 'selected' : '' }}>Lembaga Riset / NGO</option>
            </select>
            @error('institution_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="label" for="inst-email">Email Institusi (Resmi)</label>
            <input type="email" id="inst-email" name="email" class="input @error('email') border-red-500 @enderror"
                   value="{{ old('email') }}" placeholder="contoh: it@universitasnusantara.ac.id" required>
            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="label" for="inst-password">Kata Sandi Akun</label>
            <input type="password" id="inst-password" name="password" class="input @error('password') border-red-500 @enderror" required>
            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @else<p class="helper-text">Minimal 8 karakter.</p>@enderror
        </div>

        <div>
            <label class="label" for="inst-password-confirm">Konfirmasi Kata Sandi</label>
            <input type="password" id="inst-password-confirm" name="password_confirmation" class="input" required>
        </div>

        <div>
            <label class="label" for="inst-code">Kode Akses Institusi Mitra</label>
            <input type="text" id="inst-code" name="institution_code"
                   class="input @error('institution_code') border-red-500 @enderror"
                   value="{{ old('institution_code') }}"
                   placeholder="Kode diberikan oleh tim Suluh" required>
            @error('institution_code')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @else
                <p class="helper-text">Belum punya kode? Hubungi <span style="color:var(--accent);">partnership@suluh.id</span> (Kode demo: <strong>SULUH-MITRA-2026</strong>)</p>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-full justify-center mt-1">
            Daftarkan Institusi
        </button>
    </form>

    <p class="text-center mt-5 text-sm" style="color:var(--muted);">
        Sudah punya akun institusi? <a href="{{ route('login') }}">Masuk</a>
    </p>

</x-layouts.auth>
