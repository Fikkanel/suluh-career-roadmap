<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>API Publik Peneliti — Suluh</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body { background: var(--bg); color: var(--fg); font-family: 'Plus Jakarta Sans', sans-serif; }
        .mono { font-family: 'JetBrains Mono', monospace; }
        .method-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: .05em;
            background: rgba(74,124,89,0.15);
            color: var(--accent);
            border: 1px solid var(--accent);
        }
        .endpoint-card {
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 1.25rem;
            transition: box-shadow 0.2s;
        }
        .endpoint-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .endpoint-header {
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
            background: var(--surface);
            user-select: none;
        }
        .endpoint-body { display: none; padding: 1.25rem; border-top: 1px solid var(--border); background: var(--bg); }
        .endpoint-body.open { display: block; }
        .code-block {
            background: #1a1f16;
            color: #b8cfb0;
            border-radius: 8px;
            padding: 1rem 1.25rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.78rem;
            line-height: 1.8;
            overflow-x: auto;
            white-space: pre-wrap;
            margin-top: 0.75rem;
        }
        .code-block .key { color: #9ecfb2; }
        .code-block .string { color: #f0c070; }
        .code-block .number { color: #79b8f0; }
        .try-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 1rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid var(--accent);
            color: var(--accent);
            background: transparent;
            transition: all 0.2s;
        }
        .try-btn:hover { background: var(--accent); color: white; }
        .response-area {
            display: none;
            margin-top: 1rem;
        }
        .response-area.visible { display: block; }
        .spinner { display: inline-block; width: 14px; height: 14px; border: 2px solid var(--accent); border-top-color: transparent; border-radius: 50%; animation: spin 0.6s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .tag { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 0.65rem; font-weight: 600; }
        .tag-green { background: rgba(74,124,89,0.12); color: var(--accent); }
        .tag-orange { background: rgba(210,130,50,0.12); color: var(--accent-warm); }
        .chevron { transition: transform 0.25s; display: inline-block; }
        .chevron.open { transform: rotate(180deg); }
    </style>
</head>
<body>
    <div class="max-w-4xl mx-auto px-4 py-12">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-10">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a4 4 0 0 1 4 4c0 1.5-.5 2.8-1.3 3.8L18 22H6l3.3-12.2A6 6 0 0 1 8 6a4 4 0 0 1 4-4z"/><path d="M9 22h6"/></svg>
                    <span class="font-bold text-lg">Suluh Platform</span>
                    <span class="tag tag-green">Research API</span>
                    <span class="tag tag-orange">v1.0</span>
                </div>
                <h1 class="text-3xl font-bold">Dokumentasi API Publik Peneliti</h1>
                <p class="mt-2 text-sm" style="color:var(--muted); line-height:1.7;">
                    Endpoint read-only untuk riset akademik. Seluruh data merupakan <strong>agregat anonim</strong> — tidak ada PII (Personally Identifiable Information) yang dikembalikan. Sesuai Prinsip P2 Konstitusi Platform Suluh.
                </p>
            </div>
            @auth
                <a href="{{ route('dashboard') }}" style="color:var(--accent); font-size:.85rem; font-weight:600; white-space:nowrap;">← Dashboard</a>
            @endauth
        </div>

        {{-- Auth Box --}}
        <div class="card mb-8" style="border-left: 3px solid var(--accent-warm);">
            <h2 class="font-semibold text-sm mb-3" style="color:var(--accent-warm);">🔑 Autentikasi</h2>
            <p class="text-sm mb-3" style="color:var(--muted);">Semua endpoint membutuhkan API Key pada <em>request header</em>:</p>
            <div class="code-block">X-API-KEY: suluh-api-key-2024</div>
            <p class="text-xs mt-3" style="color:var(--muted);">Untuk mendapatkan API Key resmi, kirim email ke <span style="color:var(--accent);">research@suluh.id</span> dengan menyertakan afiliasi institusi dan tujuan riset Anda.</p>
        </div>

        {{-- Base URL --}}
        <div class="card mb-8">
            <h2 class="font-semibold text-sm mb-2">📍 Base URL</h2>
            <div class="code-block">{{ url('api/v1') }}</div>
        </div>

        {{-- Endpoints --}}
        <h2 class="font-semibold text-base mb-4">Endpoints Tersedia</h2>

        @php
        $endpoints = [
            [
                'method' => 'GET',
                'path' => '/research/summary',
                'title' => 'Ringkasan Statistik Platform',
                'desc' => 'Mengembalikan statistik ringkasan keseluruhan platform: total pengguna, pengguna aktif 30 hari, rata-rata Career Readiness Score, dan pivot rate.',
                'response' => '{
  "success": true,
  "endpoint": "research/summary",
  "note": "Seluruh data merupakan agregat anonim.",
  "data": {
    "total_users": 128,
    "active_users_30d": 74,
    "avg_career_readiness": 42.5,
    "pivot_rate_pct": 18.75,
    "users_with_career": 110
  },
  "cached_at": "2026-05-30T13:00:00+00:00"
}',
            ],
            [
                'method' => 'GET',
                'path' => '/research/career-distribution',
                'title' => 'Distribusi Pilihan Karir',
                'desc' => 'Mengembalikan distribusi jumlah pengguna untuk setiap pilihan karir, beserta kode RIASEC-nya. Berguna untuk analisis tren preferensi karir di Indonesia.',
                'response' => '{
  "success": true,
  "endpoint": "research/career-distribution",
  "data": [
    { "career": "UI/UX Designer", "riasec_code": "ASI", "total_users": 32 },
    { "career": "Software Engineer", "riasec_code": "IRT", "total_users": 28 },
    { "career": "Data Analyst", "riasec_code": "ICR", "total_users": 19 }
  ],
  "cached_at": "2026-05-30T13:00:00+00:00"
}',
            ],
            [
                'method' => 'GET',
                'path' => '/research/crs-trend',
                'title' => 'Tren Career Readiness Score (6 Bulan)',
                'desc' => 'Mengembalikan rata-rata Career Readiness Score (CRS) per bulan selama 6 bulan terakhir. Berguna untuk menganalisis tren pertumbuhan kesiapan karir pengguna dari waktu ke waktu.',
                'response' => '{
  "success": true,
  "endpoint": "research/crs-trend",
  "data": [
    { "month": "2025-12", "avg_crs": 28.4, "active_users": 45 },
    { "month": "2026-01", "avg_crs": 33.1, "active_users": 62 },
    { "month": "2026-02", "avg_crs": 38.7, "active_users": 74 },
    { "month": "2026-03", "avg_crs": 41.2, "active_users": 89 },
    { "month": "2026-04", "avg_crs": 40.8, "active_users": 98 },
    { "month": "2026-05", "avg_crs": 42.5, "active_users": 110 }
  ]
}',
            ],
            [
                'method' => 'GET',
                'path' => '/research/pivot-analysis',
                'title' => 'Analisis Pivot Karir',
                'desc' => 'Mengembalikan data analisis pola perpindahan karir (pivot): total pivot, jumlah pengguna unik yang pernah pivot, dan distribusi jumlah pivot per pengguna.',
                'response' => '{
  "success": true,
  "endpoint": "research/pivot-analysis",
  "data": {
    "total_pivots": 38,
    "unique_pivoters": 29,
    "pivot_rate_pct": 22.66,
    "pivot_distribution": {
      "1": 22,
      "2": 6,
      "3": 1
    }
  }
}',
            ],
        ];
        @endphp

        @foreach($endpoints as $i => $ep)
        <div class="endpoint-card" id="card-{{ $i }}">
            <div class="endpoint-header" onclick="toggleEndpoint({{ $i }})">
                <span class="method-badge">{{ $ep['method'] }}</span>
                <code class="mono text-sm font-medium flex-1">{{ url('api/v1') }}{{ $ep['path'] }}</code>
                <span class="text-sm" style="color:var(--muted);">{{ $ep['title'] }}</span>
                <svg class="chevron" id="chevron-{{ $i }}" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            <div class="endpoint-body" id="body-{{ $i }}">
                <p class="text-sm mb-4" style="color:var(--muted); line-height:1.7;">{{ $ep['desc'] }}</p>

                <p class="text-xs font-semibold mb-1" style="color:var(--muted);">CONTOH REQUEST</p>
                <div class="code-block">curl -X GET "{{ url('api/v1') }}{{ $ep['path'] }}" \
  -H "X-API-KEY: suluh-api-key-2024" \
  -H "Accept: application/json"</div>

                <div class="flex items-center justify-between mt-4 mb-1">
                    <p class="text-xs font-semibold" style="color:var(--muted);">CONTOH RESPONS</p>
                    <button class="try-btn" onclick="tryEndpoint({{ $i }}, '{{ url('api/v1') }}{{ $ep['path'] }}')">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                        Coba Sekarang
                    </button>
                </div>
                <div class="code-block">{{ $ep['response'] }}</div>

                <div class="response-area" id="response-{{ $i }}">
                    <p class="text-xs font-semibold mb-1 mt-4" style="color:var(--accent);">RESPONS LANGSUNG (Live)</p>
                    <div class="code-block" id="response-content-{{ $i }}" style="min-height:80px;"></div>
                </div>
            </div>
        </div>
        @endforeach

        {{-- Privacy Footer --}}
        <div class="card mt-8" style="border-left: 3px solid var(--accent);">
            <h2 class="text-sm font-semibold mb-2" style="color:var(--accent);">🛡️ Komitmen Privasi API</h2>
            <ul class="text-sm space-y-1.5" style="color:var(--muted); line-height:1.7;">
                <li>✓ Tidak ada nama, email, atau atribut unik pengguna yang dikembalikan</li>
                <li>✓ Semua data di-cache selama 1 jam untuk meminimalkan beban server</li>
                <li>✓ API Key dapat dicabut kapan saja jika terjadi penyalahgunaan</li>
                <li>✓ Sesuai dengan Prinsip Privasi P2 dan Anti-Prinsip Platform Suluh</li>
            </ul>
        </div>

        <footer class="text-center pt-10 pb-4 text-xs" style="color:var(--muted);">
            © {{ date('Y') }} Suluh Platform — Research API v1.0 | Dibuat untuk keperluan akademik dan riset sosial terbuka.
        </footer>
    </div>

    <script>
        function toggleEndpoint(i) {
            const body = document.getElementById('body-' + i);
            const chevron = document.getElementById('chevron-' + i);
            body.classList.toggle('open');
            chevron.classList.toggle('open');
        }

        async function tryEndpoint(i, url) {
            const responseArea = document.getElementById('response-' + i);
            const responseContent = document.getElementById('response-content-' + i);

            responseArea.classList.add('visible');
            responseContent.innerHTML = '<span class="spinner"></span>  Mengirim request...';

            try {
                const res = await fetch(url, {
                    headers: {
                        'X-API-KEY': 'suluh-api-key-2024',
                        'Accept': 'application/json',
                    }
                });
                const data = await res.json();
                responseContent.textContent = JSON.stringify(data, null, 2);
            } catch (e) {
                responseContent.textContent = '⚠️ Gagal mengambil data. Pastikan server Laravel sedang berjalan (php artisan serve).';
            }
        }
    </script>
</body>
</html>
