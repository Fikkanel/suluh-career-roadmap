<x-layouts.admin title="Komite Etika">

    @if(session('success'))
        <div class="alert alert-success mb-5">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger mb-5">{{ session('error') }}</div>
    @endif

    {{-- ── Ethics Decisions Table ──────────────────────────────── --}}
    <div class="card">
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

    @push('modals')
    {{-- ── Modal: Tambah Proposal Etika ───────────────────────── --}}
    <div id="modal-add-ethics" class="hidden" style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:100;display:none;align-items:center;justify-content:center;" onclick="if(event.target===this)closeModal('modal-add-ethics')">
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
                    <button type="button" onclick="closeModal('modal-add-ethics')" class="btn btn-ghost">Batal</button>
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
    document.querySelectorAll('[id^=modal]').forEach(m => {
        m.addEventListener('click', e => { if (e.target === m) { closeModal(m.id); } });
    });
    </script>
    @endpush

</x-layouts.admin>
