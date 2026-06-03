<x-layouts.admin title="Kelola Asesmen">

    @if(session('success'))
        <div class="alert alert-success mb-5">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger mb-5">{{ session('error') }}</div>
    @endif

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

    @push('modals')
    {{-- ── Modal: Tambah Pertanyaan ───────────────────────────────── --}}
    <div id="modal-add-question" class="hidden" style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:100;display:none;align-items:center;justify-content:center;" onclick="if(event.target===this)closeModal('modal-add-question')">
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
                    <button type="button" onclick="closeModal('modal-add-question')" class="btn btn-ghost">Batal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Modal: Edit Pertanyaan ─────────────────────────────────── --}}
    <div id="modal-edit-question" class="hidden" style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:100;display:none;align-items:center;justify-content:center;" onclick="if(event.target===this)closeModal('modal-edit-question')">
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
                    <button type="button" onclick="closeModal('modal-edit-question')" class="btn btn-ghost">Batal</button>
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
        m.addEventListener('click', e => { if (e.target === m) { closeModal(m.id); } });
    });
    </script>
    @endpush

</x-layouts.admin>
