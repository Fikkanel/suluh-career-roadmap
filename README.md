# 🧭 Suluh — Platform Roadmap Karir Personal

> **Suluh** adalah platform penentu arah dan bimbingan karir personal yang dirancang khusus untuk mahasiswa dan pencari kerja di Indonesia. Platform ini mengutamakan kedaulatan data pengguna, privasi maksimal, dan bimbingan berbasis data nyata yang objektif.

Proyek ini dibuat untuk memenuhi Tugas Akhir / Ujian Akhir Semester (UAS) mata kuliah **Pemrograman Web Lanjut**.

---

## ✨ Fitur Utama (Features)

1. **🧭 Asesmen Karir Adaptif (RIASEC & Big Five)**
   - Pertanyaan multi-tipe (Pilihan Ganda, Skala 1-10, Refleksi Teks).
   - Perhitungan skor kecocokan karir menggunakan algoritma cosine similarity terhadap model minat karir RIASEC & trait kepribadian Big Five.
   - Narasi rekomendasi karir personal yang digenerasikan secara dinamis menggunakan LLM API (Groq Llama-3.3 / Gemini).

2. **💾 Jeda & Simpan Draft Asesmen**
   - Pengguna dapat menjeda pengerjaan asesmen di tengah jalan dan melanjutkannya kapan saja tanpa kehilangan progres.
   - Fitur **"Lewati Semua"** untuk melewati pertanyaan opsional dan melihat estimasi rekomendasi secara instan.

3. **🗺️ Roadmap Karir Mandiri**
   - Menghasilkan daftar skill terstruktur (dari tingkat Pemula hingga Mahir) yang disesuaikan dengan karir yang dipilih.
   - Perhitungan **Career Readiness Score (CRS)** secara real-time berdasarkan persentase penyelesaian skill pada roadmap.
   - Fleksibilitas melakukan **"Pivot Karir"** kapan saja jika pengguna berubah pikiran tanpa ada sanksi visual/psikologis.

4. **🔒 Kedaulatan & Transparansi Data (Privacy First)**
   - **Komite Etika Data:** Pengguna dapat memberikan suara (voting) secara transparan pada proposal pemanfaatan data agregat oleh pihak ketiga (misal: mitra loker).
   - **Sunset Policy:** Kebijakan penghapusan data otomatis secara bertahap saat akun tidak aktif (Hari ke-0, Hari ke-1, Hari ke-90).
   - **Ekspor Data Mandiri:** Mengunduh seluruh profil, arsip roadmap, dan data pribadi dalam format **PDF resmi** atau berkas **JSON mentah**.
   - Enkripsi kolom data sensitif (skor kepribadian, refleksi teks) untuk mencegah kebocoran privasi dari sisi database/admin.

5. **🧑‍🏫 Kolaborasi Mentor & Institusi**
   - **Ruang Mentor:** Mentor dapat melihat list mentee secara real-time, memantau skor CRS, dan mengirimkan umpan balik tertulis.
   - **Dashboard Institusi:** Dashboard analitik agregat terenkripsi untuk mengamati tren sebaran karir mahasiswa di bawah naungan kampus mitra.

---

## 🛠️ Arsitektur & Teknologi (Tech Stack)

* **Framework:** [Laravel 11](https://laravel.com) (PHP 8.3+)
* **Database:** SQLite (Testing / Lokal) & MySQL (Lokal / Production)
* **Frontend:** Vanilla CSS (curated HSL/OKLCH brand palette) & JavaScript (Vite asset bundler)
* **Autentikasi:** Laravel Session (Web) & JWT Auth / Basic Auth (API)
* **OAuth:** Laravel Socialite (Google Login Integration)
* **LLM Integration:** Groq API SDK (Llama 3.3 70B) & Gemini API Endpoint
* **Package Testing:** PHPUnit (Feature, Unit, & Compliance Test)

---

## 🚀 Panduan Instalasi (Installation)

Ikuti langkah-langkah berikut untuk menjalankan proyek **Suluh** di mesin lokal Anda:

### 1. Klon Repositori
```bash
git clone https://github.com/Fikkanel/suluh-career-roadmap.git
cd suluh-career-roadmap
```

### 2. Instal Dependensi Composer (PHP) & NPM (JavaScript)
```bash
composer install
npm install
```

### 3. Konfigurasi Environment File
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Buka file `.env` baru Anda dan sesuaikan kredensial berikut:
* `DB_DATABASE=suluh` (Sesuaikan dengan nama database lokal Anda)
* `GROQ_API_KEY=` (Isi dengan kunci API Groq Anda jika ingin menguji fitur narasi LLM)
* `GOOGLE_CLIENT_ID=` & `GOOGLE_CLIENT_SECRET=` (Untuk pengujian Login Google)

Setelah itu, generate application key:
```bash
php artisan key:generate
php artisan jwt:secret
```

### 4. Jalankan Migrasi & Database Seeder
Pastikan server database Anda aktif, kemudian jalankan migrasi untuk membuat tabel beserta data awal bawaan (mock questions, careers, & users):
```bash
php artisan migrate --seed
```

### 5. Compile Aset & Jalankan Server Lokal
Lakukan compile asset menggunakan Vite:
```bash
npm run build
```
Jalankan server pengembangan Laravel:
```bash
php artisan serve
```
Buka **[http://127.0.0.1:8000](http://127.0.0.1:8000)** di browser Anda.

---

## 🧪 Menjalankan Pengujian (Testing)

Proyek ini dilengkapi dengan 101 pengujian fungsionalitas otomatis (Feature & Unit Testing) menggunakan PHPUnit:

```bash
php artisan test
```

Semua pengujian dirancang database-agnostic dan dijamin lulus 100% baik pada driver MySQL maupun SQLite.

---

## ⚖️ Lisensi
Aplikasi ini bersifat open-source di bawah lisensi [MIT License](LICENSE).
