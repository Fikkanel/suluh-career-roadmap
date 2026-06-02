<x-layouts.public title="Dashboard Dampak & Analitik">
    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <div class="pt-8 px-6 md:px-10 lg:px-14 w-full" data-reveal>
        <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-sm font-medium no-underline" style="color:var(--muted);transition:color 0.2s;" onmouseover="this.style.color='var(--fg)'" onmouseout="this.style.color='var(--muted)'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
            Kembali ke Halaman Utama
        </a>
    </div>

    <div class="max-w-5xl mx-auto page-padding pt-6 pb-14">
        <div class="text-center mb-10" data-reveal>
            <p class="section-label mb-3">Dashboard Dampak & Analitik</p>
            <h1 class="text-3xl font-bold mb-3">Data Nyata, Bukan Klaim</h1>
            <p class="max-w-xl mx-auto" style="color:var(--muted);">
                Semua angka di bawah ini adalah data agregat anonim. Tidak ada data individual yang ditampilkan.
                Diperbarui otomatis setiap jam.
            </p>
        </div>

        {{-- Stats grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-10 items-stretch">
            @foreach($stats as $i => $stat)
                <div class="h-full" data-reveal data-reveal-delay="{{ $i * 80 }}">
                    <x-impact-stat
                        :label="$stat['label']"
                        :value="$stat['value']"
                        :explanation="$stat['explanation']"
                        :variant="$stat['variant']"
                        :updatedAt="$stat['updatedAt']" />
                </div>
            @endforeach
        </div>

        {{-- ─── GRAFIK SECTION ─── --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

            {{-- Donut Chart: Sebaran Karir --}}
            <div class="card" data-reveal data-reveal-delay="0">
                <h2 class="text-base font-semibold mb-1">Sebaran Pilihan Karir</h2>
                <p class="text-xs mb-4" style="color:var(--muted);">Karir yang paling banyak dipilih pengguna aktif.</p>
                @if($careerDistribution->isNotEmpty())
                    <div style="position:relative;height:220px;">
                        <canvas id="careerDonutChart"></canvas>
                    </div>
                @else
                    <p class="text-sm" style="color:var(--muted);">Belum ada data karir yang dipilih.</p>
                @endif
            </div>

            {{-- Line Chart: Rata-rata CRS --}}
            <div class="card" data-reveal data-reveal-delay="120">
                <h2 class="text-base font-semibold mb-1">Tren Rata-rata CRS (6 Bulan)</h2>
                <p class="text-xs mb-4" style="color:var(--muted);">Career Readiness Score rata-rata pengguna dari waktu ke waktu.</p>
                <div style="position:relative;height:220px;">
                    <canvas id="crsLineChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Bar Chart: Tren Pengguna Baru --}}
        <div class="card mb-6" data-reveal>
            <h2 class="text-base font-semibold mb-1">Pertumbuhan Pengguna (6 Bulan Terakhir)</h2>
            <p class="text-xs mb-4" style="color:var(--muted);">Jumlah pengguna baru yang bergabung setiap bulan.</p>
            <div style="position:relative;height:200px;">
                <canvas id="growthBarChart"></canvas>
            </div>
        </div>

        {{-- Province Distribution --}}
        @if(!empty($provinceDistribution))
        <div class="card mb-6" data-reveal>
            <h2 class="text-base font-semibold mb-4">Sebaran Pengguna per Provinsi</h2>
            <p class="text-sm mb-4" style="color:var(--muted);">Data agregat anonim pengguna yang mengisi informasi provinsi.</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                @foreach($provinceDistribution as $province => $count)
                <div class="flex items-center gap-2 p-2 rounded" style="background:var(--bg-subtle);">
                    <span class="text-xs font-medium" style="color:var(--fg);">{{ $province }}</span>
                    <span class="badge badge-default ml-auto">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- API Peneliti Section --}}
        <div class="card mb-6" data-reveal style="border-left:3px solid var(--accent);">
            <div class="flex items-start gap-3">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px;"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                <div>
                    <h2 class="text-base font-semibold mb-1">API Publik untuk Peneliti</h2>
                    <p class="text-sm mb-3" style="color:var(--muted);">
                        Data agregat anonim ini tersedia via REST API untuk keperluan riset akademik. Gratis untuk peneliti dan mahasiswa.
                    </p>
                    <div style="background:var(--bg-subtle);border-radius:6px;padding:.75rem 1rem;" class="mb-3">
                        <code class="text-xs" style="color:var(--accent);">GET /api/v1/research/summary</code><br>
                        <code class="text-xs" style="color:var(--accent);">GET /api/v1/research/career-distribution</code><br>
                        <code class="text-xs" style="color:var(--accent);">GET /api/v1/research/crs-trend</code>
                    </div>
                    <p class="text-xs" style="color:var(--muted);">
                        Autentikasi menggunakan header <code>X-Research-Key: [API_KEY_ANDA]</code>.
                        Hubungi tim Suluh untuk mendapatkan kunci penelitian.
                    </p>
                </div>
            </div>
        </div>

        {{-- Methodology note --}}
        <div class="card" data-reveal>
            <h2 class="text-base font-semibold mb-2">Catatan Metodologi</h2>
            <div class="text-sm flex flex-col gap-2" style="color:var(--muted);">
                <p>• <strong>CRS (Career Readiness Score):</strong> persentase skill yang sudah diselesaikan dari total roadmap.</p>
                <p>• <strong>Perubahan positif:</strong> dilaporkan secara sukarela melalui survei dampak bulan ke-3 dan ke-6.</p>
                <p>• <strong>Pivot Rate:</strong> persentase pengguna yang memulai roadmap baru. Dimonitor sebagai data, bukan masalah.</p>
                <p>• Semua data telah melalui proses anonimisasi teknis. Tidak ada kombinasi atribut yang bisa mengidentifikasi individu.</p>
            </div>
        </div>

        <x-privacy-notice
            title="Tentang data di halaman ini"
            body="Halaman ini hanya menampilkan agregat anonim. Tidak ada akun individu yang teridentifikasi dari data ini."
            variant="compact"
            class="mt-4" />
    </div>

    {{-- Chart.js Scripts --}}
    <script>
        const ACCENT    = '#4a7c59';
        const MUTED     = '#8a9080';
        const FG_COLOR  = '#2c3227';
        const BG_SUBTLE = '#f0ede6';

        // Animasi Chart.js yang halus
        const chartAnimation = {
            duration: 900,
            easing: 'easeInOutQuart',
        };

        const chartDefaults = {
            responsive: true,
            maintainAspectRatio: false,
            animation: chartAnimation,
            plugins: {
                legend: { labels: { color: FG_COLOR, font: { size: 11, family: 'inherit' } } }
            }
        };

        // 1. Donut Chart — Sebaran Karir
        @if($careerDistribution->isNotEmpty())
        const careerLabels = @json($careerDistribution->pluck('label'));
        const careerValues = @json($careerDistribution->pluck('value'));
        const palette = ['#4a7c59','#6b9e7a','#8cbf9b','#aed4b5','#cee8d0','#e8f5ec'];
        new Chart(document.getElementById('careerDonutChart'), {
            type: 'doughnut',
            data: {
                labels: careerLabels,
                datasets: [{ data: careerValues, backgroundColor: palette, borderWidth: 0, hoverOffset: 8 }]
            },
            options: {
                ...chartDefaults,
                cutout: '65%',
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1000,
                    easing: 'easeOutQuart',
                }
            }
        });
        @endif

        // 2. Line Chart — Tren CRS
        const crsLabels = @json($avgCrsByMonth->keys());
        const crsValues = @json($avgCrsByMonth->values());
        new Chart(document.getElementById('crsLineChart'), {
            type: 'line',
            data: {
                labels: crsLabels,
                datasets: [{
                    label: 'Rata-rata CRS (%)',
                    data: crsValues,
                    borderColor: ACCENT,
                    backgroundColor: 'rgba(74,124,89,0.12)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: ACCENT,
                    pointRadius: 4,
                }]
            },
            options: {
                ...chartDefaults,
                scales: {
                    y: { min: 0, max: 100, ticks: { color: MUTED, font: { size: 10 } }, grid: { color: 'rgba(138,144,128,0.2)' } },
                    x: { ticks: { color: MUTED, font: { size: 10 } }, grid: { display: false } }
                }
            }
        });

        // 3. Bar Chart — Pertumbuhan Pengguna
        const growthLabels = @json($monthlyGrowth->keys());
        const growthValues = @json($monthlyGrowth->values());
        new Chart(document.getElementById('growthBarChart'), {
            type: 'bar',
            data: {
                labels: growthLabels,
                datasets: [{
                    label: 'Pengguna Baru',
                    data: growthValues,
                    backgroundColor: 'rgba(74,124,89,0.75)',
                    borderColor: ACCENT,
                    borderWidth: 1,
                    borderRadius: 5,
                }]
            },
            options: {
                ...chartDefaults,
                plugins: { legend: { display: false } },
                scales: {
                    y: { ticks: { color: MUTED, font: { size: 10 }, stepSize: 1 }, grid: { color: 'rgba(138,144,128,0.2)' } },
                    x: { ticks: { color: MUTED, font: { size: 10 } }, grid: { display: false } }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutBounce',
                    delay: (ctx) => ctx.dataIndex * 80,
                }
            }
        });

        // ─── COUNTER ANIMATION ───
        // Berjalan saat elemen [data-counter] terlihat di layar
        function animateCounter(el) {
            const target   = parseFloat(el.dataset.counter);
            const suffix   = el.dataset.counterSuffix || '';
            const isFloat  = target % 1 !== 0;
            const duration = 1200; // ms
            const steps    = 60;
            const stepTime = duration / steps;
            let current    = 0;
            const increment = target / steps;

            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                el.textContent = isFloat
                    ? current.toFixed(1) + suffix
                    : Math.floor(current) + suffix;
            }, stepTime);
        }

        // Gunakan IntersectionObserver untuk trigger counter saat kartu terlihat
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counterEl = entry.target.querySelector('[data-counter]');
                    if (counterEl) animateCounter(counterEl);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });

        document.querySelectorAll('.card.card-hover[data-reveal]').forEach(el => {
            counterObserver.observe(el);
        });
    </script>
</x-layouts.public>


