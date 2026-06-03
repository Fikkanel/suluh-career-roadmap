<x-layouts.app title="Dashboard Institusi Mitra">
    <x-slot:heading>Dashboard Institusi Mitra</x-slot:heading>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <div class="max-w-5xl mx-auto">

        {{-- Header Notice --}}
        <div class="card mb-6" style="border-left: 3px solid var(--accent); background: var(--accent-soft);">
            <div class="flex items-start gap-3">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <div>
                    <p class="text-sm font-semibold mb-1" style="color:var(--accent);">Data Agregat Anonim</p>
                    <p class="text-xs" style="color:var(--muted); line-height:1.6;">
                        Seluruh data di halaman ini adalah statistik agregat. Tidak ada identitas pengguna individual (nama, email, atau atribut unik lainnya) yang ditampilkan — sesuai Prinsip Privasi P2 Platform Suluh.
                    </p>
                </div>
            </div>
        </div>

        {{-- API Key Management Card --}}
        <div class="card mb-6">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-sm font-semibold m-0">Kunci API Riset &amp; Integrasi (API Key)</h2>
                @if(auth()->user()->api_key)
                    <span class="badge badge-success">Aktif</span>
                @else
                    <span class="badge badge-default">Belum Dibuat</span>
                @endif
            </div>
            <p class="text-xs mb-4" style="color:var(--muted); line-height:1.6;">
                Gunakan Kunci API ini untuk mengintegrasikan data agregat riset alumni/mahasiswa Anda dengan sistem akademik eksternal atau portal analitik kampus melalui header <code>X-API-KEY</code>.
            </p>

            @if(auth()->user()->api_key)
                <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3 p-3 rounded-lg mb-4" style="background: var(--surface-2); border: 1px solid var(--border);">
                    <code class="font-mono text-sm break-all flex-1 select-all" id="api-key-text" style="color: var(--accent-warm);">{{ auth()->user()->api_key }}</code>
                    <button class="btn btn-ghost btn-sm" onclick="copyApiKey()" id="copy-btn">📋 Salin Kunci</button>
                </div>
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
                    <p class="text-[11px] m-0" style="color: var(--danger); font-weight: 500;">
                        ⚠️ PENTING: Jaga kerahasiaan kunci ini. Jangan pernah membagikannya ke publik.
                    </p>
                    <form action="{{ route('institution.api-key.revoke') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan kunci API ini? Seluruh integrasi aktif akan terputus.')">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">🔒 Revoke Key</button>
                    </form>
                </div>
            @else
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 p-4 rounded-lg" style="background: var(--accent-soft); border: 1px dashed var(--accent);">
                    <div class="flex-1">
                        <p class="text-xs font-semibold mb-1" style="color: var(--accent);">Siap Melakukan Integrasi?</p>
                        <p class="text-[11px] m-0 text-gray-700">Buat kunci API baru untuk mulai mengakses endpoint riset akademik publik.</p>
                    </div>
                    <form action="{{ route('institution.api-key.generate') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">🔑 Generate API Key</button>
                    </form>
                </div>
            @endif
        </div>

        <script>
            function copyApiKey() {
                const keyText = document.getElementById('api-key-text').innerText;
                navigator.clipboard.writeText(keyText).then(() => {
                    const btn = document.getElementById('copy-btn');
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '✅ Tersalin!';
                    btn.style.color = 'var(--success)';
                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.style.color = '';
                    }, 2000);
                });
            }
        </script>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="card text-center">
                <p class="text-3xl font-bold mb-1" style="color:var(--accent);">{{ number_format($totalUsers) }}</p>
                <p class="text-xs" style="color:var(--muted);">Total Pengguna Terdaftar</p>
            </div>
            <div class="card text-center">
                <p class="text-3xl font-bold mb-1" style="color:var(--accent);">{{ number_format($activeUsers) }}</p>
                <p class="text-xs" style="color:var(--muted);">Aktif 30 Hari Terakhir</p>
            </div>
            <div class="card text-center">
                <p class="text-3xl font-bold mb-1" style="color:var(--accent);">{{ $avgCrs }}%</p>
                <p class="text-xs" style="color:var(--muted);">Rata-rata CRS Pengguna</p>
            </div>
            <div class="card text-center">
                <p class="text-3xl font-bold mb-1" style="color:var(--muted);">{{ $pivotRate }}%</p>
                <p class="text-xs" style="color:var(--muted);">Pivot Rate</p>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

            {{-- Donut: Sebaran Karir --}}
            <div class="card">
                <h2 class="text-sm font-semibold mb-1">Sebaran Pilihan Karir</h2>
                <p class="text-xs mb-4" style="color:var(--muted);">Karir yang paling banyak dipilih pengguna (agregat anonim).</p>
                @if($careerDistribution->isNotEmpty())
                    <div style="position:relative;height:220px;">
                        <canvas id="careerDonutChart"></canvas>
                    </div>
                @else
                    <p class="text-sm" style="color:var(--muted);">Belum ada data karir.</p>
                @endif
            </div>

            {{-- Bar: Distribusi Progres CRS --}}
            <div class="card">
                <h2 class="text-sm font-semibold mb-1">Distribusi Progres Pengguna</h2>
                <p class="text-xs mb-4" style="color:var(--muted);">Sebaran persentase Career Readiness Score (CRS) pengguna aktif.</p>
                <div style="position:relative;height:220px;">
                    <canvas id="progressBarChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Line Chart: Pertumbuhan Pengguna --}}
        <div class="card mb-6">
            <h2 class="text-sm font-semibold mb-1">Tren Pertumbuhan Pengguna (6 Bulan)</h2>
            <p class="text-xs mb-4" style="color:var(--muted);">Jumlah pengguna baru yang bergabung setiap bulan.</p>
            <div style="position:relative;height:200px;">
                <canvas id="growthLineChart"></canvas>
            </div>
        </div>

        {{-- Tabel Sebaran Karir --}}
        @if($careerDistribution->isNotEmpty())
        <div class="card mb-6">
            <h2 class="text-sm font-semibold mb-4">Detail Sebaran Karir</h2>
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:.8rem;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border);">
                            <th style="text-align:left;padding:.5rem .75rem;color:var(--muted);font-weight:500;">Karir</th>
                            <th style="text-align:center;padding:.5rem .75rem;color:var(--muted);font-weight:500;">Kode RIASEC</th>
                            <th style="text-align:right;padding:.5rem .75rem;color:var(--muted);font-weight:500;">Jumlah Pengguna</th>
                            <th style="text-align:right;padding:.5rem .75rem;color:var(--muted);font-weight:500;">Proporsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = $careerDistribution->sum('total'); @endphp
                        @foreach($careerDistribution as $item)
                        <tr style="border-bottom:1px solid var(--border);">
                            <td style="padding:.5rem .75rem;color:var(--fg);">{{ $item->career_name }}</td>
                            <td style="padding:.5rem .75rem;text-align:center;">
                                <span class="badge badge-default">{{ $item->riasec_code ?? '-' }}</span>
                            </td>
                            <td style="padding:.5rem .75rem;text-align:right;color:var(--fg);">{{ $item->total }}</td>
                            <td style="padding:.5rem .75rem;text-align:right;color:var(--accent);">
                                {{ $total > 0 ? round($item->total / $total * 100, 1) : 0 }}%
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Privacy Footer --}}
        <x-privacy-notice
            title="Komitmen Privasi Institusi"
            body="Data yang Anda lihat tidak dapat digunakan untuk mengidentifikasi pengguna secara individual. Sesuai perjanjian Institusi Mitra, penggunaan data hanya diperbolehkan untuk keperluan perencanaan kurikulum dan riset dampak pendidikan."
            variant="compact"
            class="mt-2" />
    </div>

    <script>
        const ACCENT = '#4a7c59';
        const MUTED  = '#8a9080';
        const FG     = '#2c3227';
        const palette = ['#4a7c59','#6b9e7a','#8cbf9b','#aed4b5','#cee8d0','#dff0e4'];

        const baseOpts = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { labels: { color: FG, font: { size: 11 } } } }
        };

        // 1. Donut — Sebaran Karir
        @if($careerDistribution->isNotEmpty())
        new Chart(document.getElementById('careerDonutChart'), {
            type: 'doughnut',
            data: {
                labels: @json($careerDistribution->pluck('career_name')),
                datasets: [{
                    data: @json($careerDistribution->pluck('total')),
                    backgroundColor: palette,
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: { ...baseOpts, cutout: '62%' }
        });
        @endif

        // 2. Bar — Distribusi CRS
        new Chart(document.getElementById('progressBarChart'), {
            type: 'bar',
            data: {
                labels: @json(array_keys($progressBuckets)),
                datasets: [{
                    label: 'Jumlah Pengguna',
                    data: @json(array_values($progressBuckets)),
                    backgroundColor: palette,
                    borderRadius: 5,
                    borderWidth: 0,
                }]
            },
            options: {
                ...baseOpts,
                plugins: { legend: { display: false } },
                scales: {
                    y: { ticks: { color: MUTED, font:{size:10}, stepSize:1 }, grid: { color:'rgba(138,144,128,.2)' } },
                    x: { ticks: { color: MUTED, font:{size:10} }, grid: { display:false } }
                }
            }
        });

        // 3. Line — Pertumbuhan Pengguna
        new Chart(document.getElementById('growthLineChart'), {
            type: 'line',
            data: {
                labels: @json($monthlyGrowth->keys()),
                datasets: [{
                    label: 'Pengguna Baru',
                    data: @json($monthlyGrowth->values()),
                    borderColor: ACCENT,
                    backgroundColor: 'rgba(74,124,89,0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: ACCENT,
                    pointRadius: 4,
                }]
            },
            options: {
                ...baseOpts,
                scales: {
                    y: { ticks: { color:MUTED, font:{size:10}, stepSize:1 }, grid: { color:'rgba(138,144,128,.2)' } },
                    x: { ticks: { color:MUTED, font:{size:10} }, grid: { display:false } }
                }
            }
        });
    </script>
</x-layouts.app>
