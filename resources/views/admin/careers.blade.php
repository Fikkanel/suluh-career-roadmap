<x-layouts.admin title="Kelola Karir">

    @if(session('success'))
        <div class="alert alert-success mb-5">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger mb-5">{{ session('error') }}</div>
    @endif

    {{-- ── Careers Table ──────────────────────────────────────────── --}}
    <div class="card mb-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-semibold" style="font-size:.9375rem;">Karir</h2>
            <button onclick="openAddModal('modal-add-career')" class="btn btn-primary btn-sm gap-1.5">
                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Karir
            </button>
        </div>
        <div style="overflow-x:auto;">
            <table class="table-suluh">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nama Karir</th>
                        <th scope="col">RIASEC</th>
                        <th scope="col">Skill</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($careers as $career)
                        <tr>
                            <td style="font-family:var(--font-mono);color:var(--muted);">{{ $career['id'] }}</td>
                            <td class="font-medium">{{ $career['name'] }}</td>
                            <td><span class="badge badge-accent">{{ $career['riasec_code'] }}</span></td>
                            <td style="font-family:var(--font-mono);">{{ $career['skills_count'] }}</td>
                            <td>
                                @if($career['is_active'])
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-default">Nonaktif</span>
                                @endif
                            </td>
                            <td class="flex gap-1">
                                <button onclick="openEditCareer({{ json_encode($career) }})" class="btn btn-ghost btn-sm">Edit</button>
                                <form method="POST" action="{{ route('admin.careers.destroy', $career['id']) }}" onsubmit="return confirm('Nonaktifkan karir ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);">Nonaktifkan</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('modals')
    {{-- ── Modal: Tambah Karir ────────────────────────────────────── --}}
    <div id="modal-add-career" class="hidden" style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:100;display:none;align-items:center;justify-content:center;" onclick="if(event.target===this)closeModal('modal-add-career')">
        <div class="card" style="width:min(520px,90vw);max-height:85vh;overflow-y:auto;" onclick="event.stopPropagation()">
            <h3 class="font-semibold mb-4">Tambah Karir Baru</h3>
            <form method="POST" action="{{ route('admin.careers.store') }}" class="flex flex-col gap-3">
                @csrf
                <div><label class="label">Nama Karir</label><input name="name" class="input" required></div>
                <div><label class="label">Deskripsi</label><textarea name="description" class="input" rows="3" required></textarea></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="label">Kode RIASEC</label><input name="riasec_code" class="input" maxlength="6" placeholder="contoh: IRA" required></div>
                    <div><label class="label">Industri</label><input name="industry_standard" class="input" required></div>
                </div>
                <div class="flex gap-3 mt-2">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" onclick="closeModal('modal-add-career')" class="btn btn-ghost">Batal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Modal: Edit Karir ──────────────────────────────────────── --}}
    <div id="modal-edit-career" class="hidden" style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:100;display:none;align-items:center;justify-content:center;" onclick="if(event.target===this)closeModal('modal-edit-career')">
        <div class="card" style="width:min(520px,90vw);max-height:85vh;overflow-y:auto;" onclick="event.stopPropagation()">
            <h3 class="font-semibold mb-4">Edit Karir</h3>
            <form id="form-edit-career" method="POST" class="flex flex-col gap-3">
                @csrf @method('PUT')
                <div><label class="label">Nama Karir</label><input id="ec-name" name="name" class="input" required></div>
                <div><label class="label">Deskripsi</label><textarea id="ec-description" name="description" class="input" rows="3" required></textarea></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="label">Kode RIASEC</label><input id="ec-riasec" name="riasec_code" class="input" maxlength="6" required></div>
                    <div><label class="label">Industri</label><input id="ec-industry" name="industry_standard" class="input" required></div>
                </div>
                <div class="flex gap-3 mt-2">
                    <button type="submit" class="btn btn-primary">Perbarui</button>
                    <button type="button" onclick="closeModal('modal-edit-career')" class="btn btn-ghost">Batal</button>
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
    function openEditCareer(career) {
        document.getElementById('ec-name').value        = career.name;
        document.getElementById('ec-description').value = career.description || '';
        document.getElementById('ec-riasec').value      = career.riasec_code;
        document.getElementById('ec-industry').value    = career.industry_standard || '';
        document.getElementById('form-edit-career').action = '/admin/careers/' + career.id;
        const modal = document.getElementById('modal-edit-career');
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
    }
    document.querySelectorAll('[id^=modal]').forEach(m => {
        m.addEventListener('click', e => { if (e.target === m) { closeModal(m.id); } });
    });
    </script>
    @endpush

</x-layouts.admin>
