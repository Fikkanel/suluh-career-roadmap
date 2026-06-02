<x-layouts.app title="Onboarding">
    <x-slot:heading>Sebelum memulai, kenali konteksmu</x-slot:heading>

    <div class="max-w-xl mx-auto">
        <div class="alert alert-neutral mb-6">
            <strong>Mengapa kami bertanya ini?</strong> Informasi ini membantu kami menyesuaikan kedalaman panduan.
            Semua pertanyaan opsional, dan kamu bisa melewatinya kapan saja.
        </div>

        <form method="POST" action="{{ route('onboarding.store') }}" class="flex flex-col gap-6">
            @csrf

            <x-context-prompt
                question="Berapa kisaran usiamu?"
                helperText="Kami tidak menyimpan ini sebagai data demografis — hanya untuk menentukan konteks narasi."
                sensitivity="optional"
                fieldName="age_range"
                :options="['< 18' => 'Di bawah 18 tahun', '18-22' => '18–22 tahun (SMA/Awal Kuliah)', '23-27' => '23–27 tahun (Akhir Kuliah/Fresh Graduate)', '28+' => '28 tahun ke atas']" />

            <x-context-prompt
                question="Pendidikan terakhirmu?"
                helperText=""
                sensitivity="optional"
                fieldName="education_level"
                :options="['sma' => 'SMA/SMK', 's1_ongoing' => 'Sedang kuliah S1', 's1_done' => 'Lulus S1', 'other' => 'Lainnya']" />

            <x-context-prompt
                question="Apakah kamu sudah punya pengalaman kerja?"
                helperText=""
                sensitivity="optional"
                fieldName="work_experience"
                :options="['none' => 'Belum ada pengalaman kerja', 'internship' => 'Hanya magang/freelance', '1-2y' => '1–2 tahun', '3+y' => 'Lebih dari 3 tahun']" />

            <x-context-prompt
                question="Provinsi tempat tinggalmu?"
                helperText="Digunakan untuk menampilkan sebaran pengguna per wilayah di dashboard dampak. Data hanya ditampilkan sebagai agregat anonim."
                sensitivity="optional"
                fieldName="province"
                :options="[
                    'Aceh' => 'Aceh',
                    'Sumatera Utara' => 'Sumatera Utara',
                    'Sumatera Barat' => 'Sumatera Barat',
                    'Riau' => 'Riau',
                    'Jambi' => 'Jambi',
                    'Sumatera Selatan' => 'Sumatera Selatan',
                    'Bengkulu' => 'Bengkulu',
                    'Lampung' => 'Lampung',
                    'Kep. Bangka Belitung' => 'Kep. Bangka Belitung',
                    'Kep. Riau' => 'Kep. Riau',
                    'DKI Jakarta' => 'DKI Jakarta',
                    'Jawa Barat' => 'Jawa Barat',
                    'Jawa Tengah' => 'Jawa Tengah',
                    'DI Yogyakarta' => 'DI Yogyakarta',
                    'Jawa Timur' => 'Jawa Timur',
                    'Banten' => 'Banten',
                    'Bali' => 'Bali',
                    'NTB' => 'Nusa Tenggara Barat',
                    'NTT' => 'Nusa Tenggara Timur',
                    'Kalimantan Barat' => 'Kalimantan Barat',
                    'Kalimantan Tengah' => 'Kalimantan Tengah',
                    'Kalimantan Selatan' => 'Kalimantan Selatan',
                    'Kalimantan Timur' => 'Kalimantan Timur',
                    'Kalimantan Utara' => 'Kalimantan Utara',
                    'Sulawesi Utara' => 'Sulawesi Utara',
                    'Sulawesi Tengah' => 'Sulawesi Tengah',
                    'Sulawesi Selatan' => 'Sulawesi Selatan',
                    'Sulawesi Tenggara' => 'Sulawesi Tenggara',
                    'Gorontalo' => 'Gorontalo',
                    'Sulawesi Barat' => 'Sulawesi Barat',
                    'Maluku' => 'Maluku',
                    'Maluku Utara' => 'Maluku Utara',
                    'Papua Barat' => 'Papua Barat',
                    'Papua' => 'Papua',
                ]" />

            <div class="card mb-2" style="border-left:3px solid var(--accent);">
                <p class="text-sm font-semibold" style="color:var(--fg);">Pertanyaan Kesiapan Eksplorasi</p>
                <p class="text-xs mt-1" style="color:var(--muted);">Membantu menyesuaikan kedalaman narasi panduan karirmu.</p>
            </div>

            <x-context-prompt
                question="Seberapa nyaman kamu menjelajahi karir yang belum kamu ketahui sebelumnya?"
                helperText="Tidak ada jawaban benar atau salah. Ini membantu kami memahami seberapa terbuka eksplorasimu."
                sensitivity="optional"
                fieldName="exploration_readiness"
                :options="['very_comfortable' => 'Sangat nyaman — saya suka hal baru', 'comfortable' => 'Cukup nyaman, tapi butuh panduan', 'cautious' => 'Agak ragu, tapi mau coba', 'prefer_known' => 'Lebih suka sesuatu yang sudah dikenal']" />

            <x-context-prompt
                question="Seberapa besar dukungan yang kamu rasakan dari sekitarmu untuk mengeksplorasi karir baru?"
                helperText="Dukungan keluarga, teman, atau komunitas. Membantu kami memahami konteksmu."
                sensitivity="optional"
                fieldName="support_level"
                :options="['strong' => 'Sangat didukung — ada yang membimbing', 'moderate' => 'Didukung tapi saya harus cari sendiri', 'limited' => 'Kurang dukungan, harus mandiri', 'none' => 'Belum ada dukungan khusus']" />

            <div class="flex gap-3 mt-2">
                <button type="submit" class="btn btn-primary">Lanjut ke Asesmen →</button>
                <a href="{{ route('assessment') }}" class="btn btn-ghost">Lewati semua</a>
            </div>
        </form>
    </div>
</x-layouts.app>
