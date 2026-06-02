<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssessmentQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            // === Original 10 questions (order 1-10) ===
            ['prompt' => 'Kamu diminta memimpin diskusi kelompok besar yang belum pernah kamu kenal. Apa reaksi pertamamu?', 'riasec_category' => 'E', 'big_five_trait' => 'Extraversion', 'weight' => 1.0, 'type' => 'single_choice', 'order' => 1, 'options' => ['a' => 'Langsung maju — saya suka memimpin', 'b' => 'Agak ragu, tapi akan coba', 'c' => 'Lebih nyaman jadi peserta dulu', 'd' => 'Saya lebih suka peran di balik layar']],
            ['prompt' => 'Saat mengerjakan proyek panjang, kamu lebih suka bekerja...', 'riasec_category' => 'I', 'big_five_trait' => 'Openness', 'weight' => 1.0, 'type' => 'single_choice', 'order' => 2, 'options' => ['a' => 'Sendiri dengan tenang dan fokus', 'b' => 'Kolaborasi intens dengan tim kecil', 'c' => 'Koordinasi besar lintas tim', 'd' => 'Campuran tergantung fase']],
            ['prompt' => 'Seberapa nyaman kamu dengan angka, data, dan analisis?', 'riasec_category' => 'I', 'big_five_trait' => 'Conscientiousness', 'weight' => 1.5, 'type' => 'scale', 'order' => 3, 'options' => null],
            ['prompt' => 'Ada keputusan besar yang harus diambil hari ini. Cara kamu memulai adalah...', 'riasec_category' => 'C', 'big_five_trait' => 'Conscientiousness', 'weight' => 1.0, 'type' => 'single_choice', 'order' => 4, 'options' => ['a' => 'Mengumpulkan semua data yang relevan dulu', 'b' => 'Mendiskusikan dengan orang yang terpercaya', 'c' => 'Mempercayai intuisi saya', 'd' => 'Mencari preseden atau contoh serupa']],
            ['prompt' => 'Seberapa besar kamu menikmati membantu orang lain memecahkan masalah mereka?', 'riasec_category' => 'S', 'big_five_trait' => 'Agreeableness', 'weight' => 1.0, 'type' => 'scale', 'order' => 5, 'options' => null],
            ['prompt' => 'Ketika kamu menemukan cara baru yang lebih efisien untuk melakukan sesuatu, kamu biasanya...', 'riasec_category' => 'I', 'big_five_trait' => 'Openness', 'weight' => 1.2, 'type' => 'single_choice', 'order' => 6, 'options' => ['a' => 'Langsung menggunakannya sendiri', 'b' => 'Berbagi ke tim agar semua bisa ikut', 'c' => 'Mendokumentasikan untuk referensi', 'd' => 'Masih memvalidasi dulu sebelum dipakai']],
            ['prompt' => 'Kamu lebih senang membuat sesuatu yang bisa dilihat dan dirasakan langsung (fisik atau visual) daripada konsep abstrak.', 'riasec_category' => 'R', 'big_five_trait' => 'Openness', 'weight' => 1.0, 'type' => 'scale', 'order' => 7, 'options' => null],
            ['prompt' => 'Bayangkan kamu harus menyampaikan ide kompleks ke orang yang tidak berpengalaman di bidang itu. Reaksimu adalah...', 'riasec_category' => 'S', 'big_five_trait' => 'Extraversion', 'weight' => 1.0, 'type' => 'single_choice', 'order' => 8, 'options' => ['a' => 'Tantangan menyenangkan — saya suka menjelaskan', 'b' => 'Perlu persiapan matang dulu', 'c' => 'Lebih nyaman lewat tulisan atau diagram', 'd' => 'Tergantung konteks dan audiensnya']],
            ['prompt' => 'Seberapa penting bagimu bahwa pekerjaan yang kamu lakukan memiliki dampak sosial yang nyata?', 'riasec_category' => 'S', 'big_five_trait' => 'Agreeableness', 'weight' => 1.3, 'type' => 'scale', 'order' => 9, 'options' => null],
            ['prompt' => 'Ceritakan satu momen saat kamu merasa paling engaged dan termotivasi dalam belajar atau bekerja. Apa yang membuat itu berbeda?', 'riasec_category' => 'I', 'big_five_trait' => 'Openness', 'weight' => 1.0, 'type' => 'text_reflection', 'order' => 10, 'options' => null],

            // === 20 Additional questions (order 11-30) ===

            // R (Realistic) — 4 more
            ['prompt' => 'Kamu diminta memperbaiki peralatan yang rusak di rumah. Apa yang kamu lakukan pertama?', 'riasec_category' => 'R', 'big_five_trait' => 'Conscientiousness', 'weight' => 1.0, 'type' => 'single_choice', 'order' => 11, 'options' => ['a' => 'Bongkar dan coba perbaiki sendiri', 'b' => 'Cari tutorial online dulu', 'c' => 'Panggil teknisi saja', 'd' => 'Cari alternatif lain yang bisa dipakai']],
            ['prompt' => 'Seberapa suka kamu bekerja dengan tangan, alat, atau bahan fisik?', 'riasec_category' => 'R', 'big_five_trait' => 'Openness', 'weight' => 1.0, 'type' => 'scale', 'order' => 12, 'options' => null],
            ['prompt' => 'Ketika ada proyek yang butuh ketelitian teknis (misalnya merakit, mengukur, mengkalibrasi), kamu...', 'riasec_category' => 'R', 'big_five_trait' => 'Conscientiousness', 'weight' => 1.2, 'type' => 'single_choice', 'order' => 13, 'options' => ['a' => 'Langsung tertarik — saya suka hal teknis', 'b' => 'Bisa tapi butuh panduan', 'c' => 'Lebih baik serahkan ke ahlinya', 'd' => 'Tergantung tingkat kesulitannya']],
            ['prompt' => 'Kamu lebih memilih pekerjaan yang menghasilkan output nyata (bangunan, produk, kode) daripada laporan atau presentasi.', 'riasec_category' => 'R', 'big_five_trait' => 'Extraversion', 'weight' => 1.0, 'type' => 'scale', 'order' => 14, 'options' => null],

            // A (Artistic) — 5 more
            ['prompt' => 'Kamu diberi tugas mendesain poster untuk acara kampus. Pendekatanmu adalah...', 'riasec_category' => 'A', 'big_five_trait' => 'Openness', 'weight' => 1.0, 'type' => 'single_choice', 'order' => 15, 'options' => ['a' => 'Langsung sketsa — biar kreativitas mengalir', 'b' => 'Cari referensi dulu, lalu adaptasi', 'c' => 'Pakai template yang sudah ada', 'd' => 'Diskusi dulu dengan tim tentang konsep']],
            ['prompt' => 'Seberapa sering kamu mendapat ide yang tidak konvensional untuk menyelesaikan masalah?', 'riasec_category' => 'A', 'big_five_trait' => 'Openness', 'weight' => 1.2, 'type' => 'scale', 'order' => 16, 'options' => null],
            ['prompt' => 'Ketika aturan atau prosedur terasa terlalu kaku, kamu biasanya...', 'riasec_category' => 'A', 'big_five_trait' => 'Openness', 'weight' => 1.0, 'type' => 'single_choice', 'order' => 17, 'options' => ['a' => 'Cari cara kreatif tetap memenuhi tujuannya', 'b' => 'Ikuti saja — aturan ada untuk alasan', 'c' => 'Tanya atasan apakah bisa fleksibel', 'd' => 'Buat pendekatan alternatif dan ajukan']],
            ['prompt' => 'Kamu merasa paling hidup saat bisa mengekspresikan diri melalui karya, desain, atau tulisan.', 'riasec_category' => 'A', 'big_five_trait' => 'Extraversion', 'weight' => 1.1, 'type' => 'scale', 'order' => 18, 'options' => null],
            ['prompt' => 'Dalam mengerjakan tugas kelompok, kamu paling berkontribusi di bagian...', 'riasec_category' => 'A', 'big_five_trait' => 'Agreeableness', 'weight' => 1.0, 'type' => 'single_choice', 'order' => 19, 'options' => ['a' => 'Konsep dan visualisasi kreatif', 'b' => 'Riset dan pengumpulan data', 'c' => 'Koordinasi dan komunikasi tim', 'd' => 'Eksekusi teknis dan implementasi']],

            // E (Enterprising) — 4 more
            ['prompt' => 'Temanmu punya usaha kecil dan minta kamu bantu mengembangkan. Langkah pertamamu...', 'riasec_category' => 'E', 'big_five_trait' => 'Extraversion', 'weight' => 1.0, 'type' => 'single_choice', 'order' => 20, 'options' => ['a' => 'Ajukan strategi pertumbuhan langsung', 'b' => 'Pelajari dulu model bisnisnya', 'c' => 'Bantu di bagian operasional saja', 'd' => 'Sambungkan dengan kontak yang relevan']],
            ['prompt' => 'Seberapa nyaman kamu meyakinkan orang lain tentang ide atau pendapatmu?', 'riasec_category' => 'E', 'big_five_trait' => 'Extraversion', 'weight' => 1.2, 'type' => 'scale', 'order' => 21, 'options' => null],
            ['prompt' => 'Ketika ada peluang baru yang berisiko tapi potensial besar, kamu cenderung...', 'riasec_category' => 'E', 'big_five_trait' => 'Neuroticism', 'weight' => 1.0, 'type' => 'single_choice', 'order' => 22, 'options' => ['a' => 'Ambil peluang itu — tanpa risiko tidak ada kemajuan', 'b' => 'Hitung risikonya dulu secara matang', 'c' => 'Tunggu sampai ada lebih banyak kepastian', 'd' => 'Diskusi dengan orang yang lebih berpengalaman']],
            ['prompt' => 'Kamu lebih tertarik mengelola sumber daya (orang, anggaran, waktu) daripada mengerjakan detail teknis sendiri.', 'riasec_category' => 'E', 'big_five_trait' => 'Conscientiousness', 'weight' => 1.0, 'type' => 'scale', 'order' => 23, 'options' => null],

            // C (Conventional) — 3 more
            ['prompt' => 'Kamu diminta mengelola spreadsheet data besar. Reaksi pertamamu...', 'riasec_category' => 'C', 'big_five_trait' => 'Conscientiousness', 'weight' => 1.0, 'type' => 'single_choice', 'order' => 24, 'options' => ['a' => 'Langsung tertarik — saya suka keteraturan data', 'b' => 'Bisa dikerjakan tapi bukan favorit', 'c' => 'Cari cara otomatisasi supaya lebih efisien', 'd' => 'Minta bantuan orang yang lebih ahli']],
            ['prompt' => 'Seberapa penting bagimu bahwa pekerjaan memiliki prosedur yang jelas dan terstruktur?', 'riasec_category' => 'C', 'big_five_trait' => 'Conscientiousness', 'weight' => 1.2, 'type' => 'scale', 'order' => 25, 'options' => null],
            ['prompt' => 'Ketika lingkungan kerja tidak teratur atau tidak ada SOP, kamu biasanya...', 'riasec_category' => 'C', 'big_five_trait' => 'Neuroticism', 'weight' => 1.0, 'type' => 'single_choice', 'order' => 26, 'options' => ['a' => 'Langsung buat struktur dan aturannya sendiri', 'b' => 'Adaptasi saja dengan kondisi yang ada', 'c' => 'Merasa tidak nyaman tapi tetap jalan', 'd' => 'Ajukan ke pemimpin untuk buat pedoman']],

            // Neuroticism — 4 more (currently 0 questions cover this)
            ['prompt' => 'Ketika menghadapi tenggat waktu yang ketat, kamu biasanya merasa...', 'riasec_category' => 'C', 'big_five_trait' => 'Neuroticism', 'weight' => 1.3, 'type' => 'scale', 'order' => 27, 'options' => null],
            ['prompt' => 'Saat menerima kritik terhadap hasil kerjamu, reaksi emosional pertama adalah...', 'riasec_category' => 'S', 'big_five_trait' => 'Neuroticism', 'weight' => 1.0, 'type' => 'single_choice', 'order' => 28, 'options' => ['a' => 'Terima dengan tenang dan evaluasi', 'b' => 'Sedikit tersinggung tapi dipikirkan', 'c' => 'Coba pertahankan argumen saya dulu', 'd' => 'Butuh waktu sendiri dulu sebelum merespons']],
            ['prompt' => 'Seberapa sering kamu merasa cemas tentang masa depan karirmu?', 'riasec_category' => 'I', 'big_five_trait' => 'Neuroticism', 'weight' => 1.0, 'type' => 'scale', 'order' => 29, 'options' => null],
            ['prompt' => 'Ceritakan situasi di mana kamu harus keluar dari zona nyamanmu. Apa yang kamu rasakan dan bagaimana kamu menghadapinya?', 'riasec_category' => 'A', 'big_five_trait' => 'Neuroticism', 'weight' => 1.0, 'type' => 'text_reflection', 'order' => 30, 'options' => null],
        ];

        foreach ($questions as $q) {
            \App\Models\AssessmentQuestion::updateOrCreate(
                ['prompt' => $q['prompt']],
                array_merge($q, ['is_active' => true])
            );
        }
    }
}
