<x-layouts.admin title="Manajemen Konten">

    @if(session('success'))
        <div class="alert alert-success mb-5">{{ session('success') }}</div>
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

    {{-- ── Questions Table ────────────────────────────────────────── --}}
    <div class="card">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-semibold" style="font-size:.9375rem;">Pertanyaan Asesmen</h2>
            <button onclick="openAddModal('modal-add-question')" class="btn btn-primary btn-sm gap-1.5">
                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Pertanyaan
            </button>
        </div>
        <div style="overflow-x:auto;">
            <table class="table-suluh">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Pertanyaan</th>
                        <th scope="col">RIASEC</th>
                        <th scope="col">Big Five</th>
                        <th scope="col">Bobot</th>
                        <th scope="col">Tipe</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($questions as $q)
                        <tr>
                            <td style="font-family:var(--font-mono);color:var(--muted);">{{ $q['order'] }}</td>
                            <td class="text-sm" style="max-width:280px;">{{ Str::limit($q['prompt'], 65) }}</td>
                            <td><span class="badge badge-accent">{{ $q['riasec_category'] }}</span></td>
                            <td class="text-sm" style="color:var(--muted);">{{ $q['big_five_trait'] }}</td>
                            <td style="font-family:var(--font-mono);">{{ $q['weight'] }}</td>
                            <td class="text-sm" style="color:var(--muted);">{{ $q['type'] }}</td>
                            <td class="flex gap-1">
                                <button onclick="openEditQuestion({{ json_encode($q) }})" class="btn btn-ghost btn-sm">Edit</button>
                                <form method="POST" action="{{ route('admin.questions.destroy', $q['id']) }}" onsubmit="return confirm('Nonaktifkan pertanyaan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="text-xs mt-3" style="color:var(--muted);">
            Jawaban individual pengguna tidak pernah ditampilkan di panel admin.
        </p>
    </div>

    {{-- ── Ethics Decisions Table ──────────────────────────────── --}}
    <div class="card mt-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="font-semibold" style="font-size:.9375rem;">Komite Etika Data</h2>
                <p class="text-xs mt-0.5" style="color:var(--muted);">Kelola proposal transparansi data yang ditampilkan di halaman publik.</p>
            </div>
            <button onclick="openAddModal('modal-add-ethics')" class="btn btn-primary btn-sm gap-1.5">
                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Proposal
            </button>
        </div>
        <div style="overflow-x:auto;">
            <table class="table-suluh">
                <thead>
                    <tr>
                        <th scope="col">Judul</th>
                        <th scope="col">Status</th>
                        <th scope="col">Setuju</th>
                        <th scope="col">Tolak</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ethicsDecisions as $ed)
                    <tr>
                        <td class="text-sm font-medium" style="max-width:260px;">{{ Str::limit($ed->title, 55) }}</td>
                        <td>
                            @if($ed->status === 'approved')
                                <span class="badge badge-success">Disetujui</span>
                            @elseif($ed->status === 'rejected')
                                <span class="badge" style="background:rgba(200,50,50,.1);color:#c83232;">Ditolak</span>
                            @else
                                <span class="badge badge-accent">Voting</span>
                            @endif
                        </td>
                        <td style="font-family:var(--font-mono);">{{ $ed->votes_for }}</td>
                        <td style="font-family:var(--font-mono);">{{ $ed->votes_against }}</td>
                        <td class="text-sm" style="color:var(--muted);">{{ $ed->created_at->format('d M Y') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.ethics.destroy', $ed->id) }}" onsubmit="return confirm('Hapus proposal ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @if($ethicsDecisions->isEmpty())
                    <tr><td colspan="6" class="text-center text-sm py-6" style="color:var(--muted);">Belum ada proposal. Tambahkan proposal pertama.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Modal: Tambah Karir ────────────────────────────────────── --}}
    <div id="modal-add-career" class="hidden" style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:100;display:none;align-items:center;justify-content:center;" onclick="if(event.target===this)this.classList.add('hidden')">
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
                    <button type="button" onclick="document.getElementById('modal-add-career').classList.add('hidden')" class="btn btn-ghost">Batal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Modal: Edit Karir ──────────────────────────────────────── --}}
    <div id="modal-edit-career" class="hidden" style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:100;display:none;align-items:center;justify-content:center;" onclick="if(event.target===this)this.classList.add('hidden')">
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
                    <button type="button" onclick="document.getElementById('modal-edit-career').classList.add('hidden')" class="btn btn-ghost">Batal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Modal: Tambah Pertanyaan ───────────────────────────────── --}}
    <div id="modal-add-question" class="hidden" style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:100;display:none;align-items:center;justify-content:center;" onclick="if(event.target===this)this.classList.add('hidden')">
        <div class="card" style="width:min(560px,90vw);max-height:85vh;overflow-y:auto;" onclick="event.stopPropagation()">
            <h3 class="font-semibold mb-4">Tambah Pertanyaan Asesmen</h3>
            <form method="POST" action="{{ route('admin.questions.store') }}" class="flex flex-col gap-3">
                @csrf
                <div><label class="label">Pertanyaan</label><textarea name="prompt" class="input" rows="3" required></textarea></div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Tipe</label>
                        <select name="type" class="input">
                            <option value="single_choice">Pilihan Ganda</option>
                            <option value="scale">Skala (1-10)</option>
                            <option value="text_reflection">Refleksi Teks</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Kategori RIASEC</label>
                        <select name="riasec_category" class="input">
                            @foreach(['R','I','A','S','E','C'] as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Big Five Trait</label>
                        <select name="big_five_trait" class="input">
                            @foreach(['Openness','Conscientiousness','Extraversion','Agreeableness','Neuroticism'] as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="label">Bobot</label><input name="weight" type="number" step="0.1" min="0.1" max="3" value="1.0" class="input" required></div>
                    <div><label class="label">Urutan</label><input name="order" type="number" min="1" value="{{ count($questions) + 1 }}" class="input" required></div>
                </div>
                <div class="flex gap-3 mt-2">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" onclick="document.getElementById('modal-add-question').classList.add('hidden')" class="btn btn-ghost">Batal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Modal: Edit Pertanyaan ─────────────────────────────────── --}}
    <div id="modal-edit-question" class="hidden" style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:100;display:none;align-items:center;justify-content:center;" onclick="if(event.target===this)this.classList.add('hidden')">
        <div class="card" style="width:min(560px,90vw);max-height:85vh;overflow-y:auto;" onclick="event.stopPropagation()">
            <h3 class="font-semibold mb-4">Edit Pertanyaan</h3>
            <form id="form-edit-question" method="POST" class="flex flex-col gap-3">
                @csrf @method('PUT')
                <div><label class="label">Pertanyaan</label><textarea id="eq-prompt" name="prompt" class="input" rows="3" required></textarea></div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Tipe</label>
                        <select id="eq-type" name="type" class="input">
                            <option value="single_choice">Pilihan Ganda</option>
                            <option value="scale">Skala (1-10)</option>
                            <option value="text_reflection">Refleksi Teks</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">RIASEC</label>
                        <select id="eq-riasec" name="riasec_category" class="input">
                            @foreach(['R','I','A','S','E','C'] as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Big Five</label>
                        <select id="eq-bigfive" name="big_five_trait" class="input">
                            @foreach(['Openness','Conscientiousness','Extraversion','Agreeableness','Neuroticism'] as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="label">Bobot</label><input id="eq-weight" name="weight" type="number" step="0.1" min="0.1" max="3" class="input" required></div>
                    <div><label class="label">Urutan</label><input id="eq-order" name="order" type="number" min="1" class="input" required></div>
                </div>
                <div class="flex gap-3 mt-2">
                    <button type="submit" class="btn btn-primary">Perbarui</button>
                    <button type="button" onclick="document.getElementById('modal-edit-question').classList.add('hidden')" class="btn btn-ghost">Batal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Modal: Tambah Proposal Etika ───────────────────────── --}}
    <div id="modal-add-ethics" class="hidden" style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:100;display:none;align-items:center;justify-content:center;">
        <div class="card" style="width:min(560px,90vw);max-height:85vh;overflow-y:auto;" onclick="event.stopPropagation()">
            <h3 class="font-semibold mb-4">Tambah Proposal Komite Etika</h3>
            <form method="POST" action="{{ route('admin.ethics.store') }}" class="flex flex-col gap-3">
                @csrf
                <div><label class="label">Judul Proposal</label><input name="title" class="input" placeholder="contoh: Kerjasama dengan Jobstreet" required></div>
                <div><label class="label">Konteks / Latar Belakang</label><textarea name="context" class="input" rows="3" placeholder="Jelaskan latar belakang permintaan data ini..." required></textarea></div>
                <div>
                    <label class="label">Status Awal</label>
                    <select name="status" class="input">
                        <option value="voting">Voting (Sedang Dibahas)</option>
                        <option value="approved">Approved (Disetujui)</option>
                        <option value="rejected">Rejected (Ditolak)</option>
                    </select>
                </div>
                <div><label class="label">Keputusan Komite <span style="color:var(--muted);font-weight:400;">(opsional, isi jika sudah ada keputusan)</span></label><textarea name="decision" class="input" rows="2" placeholder="Keputusan akhir komite..."></textarea></div>
                <div><label class="label">Tanggal Implementasi <span style="color:var(--muted);font-weight:400;">(opsional)</span></label><input name="implementation_date" type="date" class="input"></div>
                <div class="flex gap-3 mt-2">
                    <button type="submit" class="btn btn-primary">Simpan Proposal</button>
                    <button type="button" onclick="document.getElementById('modal-add-ethics').classList.add('hidden')" class="btn btn-ghost">Batal</button>
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
    function openEditQuestion(q) {
        document.getElementById('eq-prompt').value  = q.prompt;
        document.getElementById('eq-type').value    = q.type;
        document.getElementById('eq-riasec').value  = q.riasec_category;
        document.getElementById('eq-bigfive').value = q.big_five_trait;
        document.getElementById('eq-weight').value  = q.weight;
        document.getElementById('eq-order').value   = q.order;
        document.getElementById('form-edit-question').action = '/admin/questions/' + q.id;
        const modal = document.getElementById('modal-edit-question');
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
    }
    document.querySelectorAll('[id^=modal]').forEach(m => {
        m.addEventListener('click', e => { if (e.target === m) { m.classList.add('hidden'); m.style.display = 'none'; } });
    });
    </script>

</x-layouts.admin>
