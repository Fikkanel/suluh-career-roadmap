<x-layouts.admin title="Kelola Pengguna">

    @if(session('success'))
        <div class="alert alert-success mb-5">
            {{ session('success') }}
            @if(session('new_user_email') && session('new_user_password'))
                <div class="mt-3 p-3 rounded" style="background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.2);">
                    <p class="text-xs mb-1" style="opacity:0.85;">Kredensial Akun Baru (Silakan salin sebelum berpindah halaman):</p>
                    <div style="font-family:var(--font-mono); font-size:0.875rem; user-select:all;">
                        <strong>Email:</strong> {{ session('new_user_email') }}<br>
                        <strong>Password:</strong> {{ session('new_user_password') }}
                    </div>
                </div>
            @endif
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger mb-5">{{ session('error') }}</div>
    @endif

    {{-- ── Users Table ────────────────────────────────────────── --}}
    <div class="card">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-semibold" style="font-size:.9375rem; margin:0;">Manajemen Peran Pengguna &amp; Moderasi Mentor</h2>
            <button onclick="openAddModal('modal-add-user')" class="btn btn-primary btn-sm gap-1.5">
                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Pengguna Baru
            </button>
        </div>
        <div style="overflow-x:auto;">
            <table class="table-suluh">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Email</th>
                        <th scope="col">Peran (Role)</th>
                        <th scope="col">Admin Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td style="font-family:var(--font-mono);color:var(--muted);">{{ $user['id'] }}</td>
                        <td class="font-medium">{{ $user['name'] }}</td>
                        <td class="text-sm" style="color:var(--muted);">{{ $user['email'] }}</td>
                        <td>
                            @if($user['role'] === 'mentor')
                                <span class="badge badge-accent">Mentor</span>
                            @elseif($user['role'] === 'institution')
                                <span class="badge badge-warm">Mitra/Instansi</span>
                            @else
                                <span class="badge badge-default">Mentee (User)</span>
                            @endif
                        </td>
                        <td>
                            @if($user['is_admin'])
                                <span class="badge badge-danger">Admin</span>
                            @else
                                <span style="color:var(--muted); font-size: 0.8rem;">Bukan Admin</span>
                            @endif
                        </td>
                        <td class="flex gap-1">
                            @if($user['is_admin'])
                                <span class="text-xs" style="color:var(--muted); padding: 0.25rem 0.5rem;">Tidak dapat diubah</span>
                            @else
                                <button onclick="openEditUser({{ json_encode($user) }})" class="btn btn-ghost btn-sm">Ubah Peran</button>
                            @endif
                            
                            @if($user['id'] !== auth()->id() && !$user['is_admin'])
                            <form method="POST" action="{{ route('admin.users.destroy', $user['id']) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna ini secara permanen?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);">Hapus</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('modals')
    {{-- ── Modal: Edit User ──────────────────────────────────────── --}}
    <div id="modal-edit-user" class="hidden" style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:100;display:none;align-items:center;justify-content:center;" onclick="if(event.target===this)closeModal('modal-edit-user')">
        <div class="card" style="width:min(440px,90vw);max-height:85vh;overflow-y:auto;" onclick="event.stopPropagation()">
            <h3 class="font-semibold mb-4">Ubah Peran Pengguna / Mentor</h3>
            <form id="form-edit-user" method="POST" class="flex flex-col gap-3">
                @csrf @method('PUT')
                <div>
                    <label class="label">Peran Pengguna</label>
                    <select id="eu-role" name="role" class="input">
                        <option value="user">Mentee (Regular User)</option>
                        <option value="mentor">Mentor (Ahli Pembimbing)</option>
                        <option value="institution">Mitra/Instansi (Kampus/Lembaga)</option>
                    </select>
                </div>
                <div>
                    <label class="label">Status Administrator</label>
                    <select id="eu-admin" name="is_admin" class="input">
                        <option value="0">Bukan Admin</option>
                        <option value="1">Admin (Akses Panel Pengelola)</option>
                    </select>
                </div>
                <div class="flex gap-3 mt-2">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" onclick="closeModal('modal-edit-user')" class="btn btn-ghost">Batal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Modal: Tambah User ────────────────────────────────────── --}}
    <div id="modal-add-user" class="hidden" style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:100;display:none;align-items:center;justify-content:center;" onclick="if(event.target===this)closeModal('modal-add-user')">
        <div class="card" style="width:min(480px,90vw);max-height:85vh;overflow-y:auto;" onclick="event.stopPropagation()">
            <h3 class="font-semibold mb-4">Tambah Pengguna Baru</h3>
            <form method="POST" action="{{ route('admin.users.store') }}" class="flex flex-col gap-3">
                @csrf
                <div>
                    <label class="label">Nama Lengkap</label>
                    <input id="au-name" name="name" class="input" placeholder="Nama Lengkap" required>
                </div>
                <div>
                    <label class="label">Alamat Email</label>
                    <input id="au-email" name="email" type="email" class="input" placeholder="contoh: budi@suluh.id" required>
                </div>
                <div>
                    <label class="label flex justify-between items-center">
                        <span>Kata Sandi</span>
                        <button type="button" onclick="generateRandomData()" class="btn btn-ghost btn-xs" style="color:var(--accent); font-weight:600; padding:0; background:none; border:none; cursor:pointer;">
                            ✨ Generate Data Acak
                        </button>
                    </label>
                    <input id="au-password" name="password" type="text" class="input" placeholder="Minimal 8 karakter" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Peran Pengguna</label>
                        <select name="role" class="input">
                            <option value="user">Mentee (Regular User)</option>
                            <option value="mentor">Mentor (Ahli Pembimbing)</option>
                            <option value="institution">Mitra/Instansi (Kampus/Lembaga)</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Status Administrator</label>
                        <select name="is_admin" class="input">
                            <option value="0">Bukan Admin</option>
                            <option value="1">Admin (Akses Panel)</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-3 mt-3">
                    <button type="submit" class="btn btn-primary">Simpan Akun</button>
                    <button type="button" onclick="closeModal('modal-add-user')" class="btn btn-ghost">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openAddModal(id) {
        const m = document.getElementById(id);
        m.classList.remove('hidden');
        m.style.display = 'flex';
    }
    function closeModal(id) {
        const m = document.getElementById(id);
        m.classList.add('hidden');
        m.style.display = 'none';
    }
    function openEditUser(user) {
        document.getElementById('eu-role').value = user.role;
        document.getElementById('eu-admin').value = user.is_admin ? "1" : "0";
        document.getElementById('form-edit-user').action = '/admin/users/' + user.id;
        const modal = document.getElementById('modal-edit-user');
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
    }
    function generateRandomData() {
        const firstNames = ['Budi', 'Joko', 'Siti', 'Dewi', 'Ahmad', 'Rian', 'Putri', 'Agus', 'Wati', 'Tono', 'Hendra', 'Mega', 'Rudi', 'Lina', 'Eko', 'Sari', 'Dani', 'Fikri', 'Farhan', 'Rina'];
        const lastNames = ['Santoso', 'Pratama', 'Hidayat', 'Kusuma', 'Lestari', 'Wibowo', 'Siregar', 'Ginting', 'Wijaya', 'Situmorang', 'Gunawan', 'Setiawan', 'Nugroho', 'Saputra', 'Putra'];
        
        const first = firstNames[Math.floor(Math.random() * firstNames.length)];
        const last = lastNames[Math.floor(Math.random() * lastNames.length)];
        const name = first + ' ' + last;
        const randomNumber = Math.floor(10 + Math.random() * 90);
        const email = first.toLowerCase() + '.' + last.toLowerCase() + randomNumber + '@suluh.id';
        
        // Secure random password generation
        const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$';
        let password = '';
        for (let i = 0; i < 12; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        
        document.getElementById('au-name').value = name;
        document.getElementById('au-email').value = email;
        document.getElementById('au-password').value = password;
    }
    document.querySelectorAll('[id^=modal]').forEach(m => {
        m.addEventListener('click', e => { if (e.target === m) { closeModal(m.id); } });
    });
    </script>
    @endpush

</x-layouts.admin>
