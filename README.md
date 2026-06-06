# 🧭 Suluh — Platform Roadmap Karir Personal

> **Suluh** adalah platform penentu arah dan bimbingan karir personal yang dirancang khusus untuk mahasiswa dan pencari kerja di Indonesia. Platform ini mengutamakan kedaulatan data pengguna, privasi maksimal, dan bimbingan berbasis data nyata yang objektif.

Proyek ini dibuat untuk memenuhi Tugas Akhir / Ujian Akhir Semester (UAS) mata kuliah **Pemrograman Web Lanjut / Pemrograman API**.

---

## ✨ Fitur Utama (Features)

1. **🧭 Asesmen Karir Adaptif (RIASEC & Big Five)**
   - Pertanyaan multi-tipe (Pilihan Ganda, Skala 1-10, Refleksi Teks).
   - Perhitungan skor kecocokan karir menggunakan algoritma *Euclidean Distance* terhadap model minat karir RIASEC & trait kepribadian Big Five.
   - Narasi rekomendasi karir personal yang digenerasikan secara dinamis menggunakan LLM API (Gemini / Groq) lengkap dengan database caching.

2. **🏫 Onboarding Kampus & Penyaringan Dashboard Mitra**
   - **Mandatory University Name:** Pengguna baru (student/mentee) wajib mengisi nama kampus/universitas saat onboarding sebelum dapat menggunakan platform.
   - **Dashboard Mitra Terfilter:** Dashboard Institusi Mitra menyajikan visualisasi data (Rata-rata CRS, Sebaran Karir, Pertumbuhan Bulanan) yang disaring dinamis hanya untuk mahasiswa dari kampus mereka.

3. **🗺️ Roadmap Karir & Pivot**
   - Menghasilkan daftar skill terstruktur (Fondasi, Menengah, Lanjutan) yang disesuaikan dengan karir yang dipilih.
   - Perhitungan **Career Readiness Score (CRS)** secara real-time berdasarkan persentase penyelesaian skill pada roadmap.
   - Fleksibilitas melakukan **"Pivot Karir"** kapan saja dengan analisis pemindahan skill lama yang relevan (*transferable skills*) oleh AI.

4. **🔑 Integrasi API & Pengaman Token**
   - **Bearer JWT Auth:** Melindungi endpoint user autentikasi internal.
   - **Basic Auth:** Digunakan saat login API untuk menukarkan email & password dengan token JWT.
   - **Subdomain Redirect Middleware:** Seluruh akses web/browser pada subdomain `api.suluhkarir.my.id` otomatis dialihkan ke domain utama `suluhkarir.my.id`, membatasi subdomain ini khusus untuk endpoint API.
   - **Mitra API Key Filtering (`X-API-KEY`):** Mitra dapat mengenerate API Key acak kriptografis (`slh_inst_...`) dari dashboard mereka. Request API menggunakan key mitra otomatis memfilter respon statistik khusus untuk kampus tersebut, sedangkan Master API Key mengembalikan data global agregat.

5. **🔒 Kedaulatan & Transparansi Data (Privacy First)**
   - **Komite Etika Data:** Voting transparan pada proposal pemrosesan data oleh pihak ketiga.
   - **Sunset Policy:** Kebijakan penghapusan data otomatis saat akun tidak aktif.
   - **Ekspor Data Mandiri:** Mengunduh portofolio data dalam format **PDF resmi** atau berkas **JSON mentah**.
   - Enkripsi database untuk data kepribadian dan jawaban survei sensitif.

---

## 🛠️ Arsitektur & Teknologi (Tech Stack)

* **Framework:** [Laravel 11](https://laravel.com) (PHP 8.2+)
* **Database:** MySQL 8.0 (Lokal & Production)
* **Branding Assets:** Logo resmi (`logo.png`, `logo.jpg`) & favicon situs (`favicon.ico`)
* **Autentikasi:** Laravel Session (Web), JWT Auth (API), Basic Auth (API Login), & API Key Header (`X-API-KEY`)
* **OAuth:** Google Login Integration (Laravel Socialite)
* **AI Integration:** Google Gemini 2.5 Flash & Groq API Fallback (Llama 3.3)
* **Dokumentasi API:** OpenAPI interaktif via Scramble (`/docs/api`) & Postman Collection (`Suluh_API_Postman_Collection.json`)
* **Testing:** PHPUnit (Feature & Unit Test)

---

## 📡 Integrasi & Dokumentasi API

Seluruh fungsionalitas backend **Suluh** dapat diakses menggunakan REST API yang aman. Platform membedakan akses API berdasarkan tipe klien:

### 1. Sistem Autentikasi & Akses
* **Mitra & Research API (Header `X-API-KEY`):**
  - **Master API Key:** Digunakan untuk riset publik global (mengembalikan data statistik agregat nasional).
  - **Mitra API Key:** Dihasilkan secara acak dinamis melalui dashboard institusi mitra (format: `slh_inst_[random_string]`). Request API menggunakan key ini secara otomatis menyaring data statistik agar **hanya menampilkan data mahasiswa dari universitas mitra tersebut**.
* **Internal User API (Header `Authorization: Bearer <JWT_TOKEN>`):**
  - Mengamankan data pribadi mahasiswa/mentee. Token diperoleh dengan menukarkan email & password pada endpoint `/auth/login` menggunakan **Basic Authentication** (`Authorization: Basic <base64_email_password>`).

### 2. Pengamanan Subdomain (`api.suluhkarir.my.id`)
* Untuk menjaga kebersihan arsitektur, subdomain `api.suluhkarir.my.id` hanya melayani endpoint API (`/api/*`), health check (`/up`), dan dokumentasi Scramble (`/docs/*`).
* Akses browser langsung/web HTML ke subdomain `api.suluhkarir.my.id` (misalnya membuka dashboard web atau halaman landing) akan ditangkap oleh middleware `PreventWebAccessFromApiDomain` dan dialihkan (302 Redirect) secara otomatis ke domain utama `https://suluhkarir.my.id/`.

### 3. Daftar Endpoint Utama
Berikut adalah ringkasan endpoint API yang tersedia di **Suluh**:

| Tipe Auth | Method | Endpoint | Deskripsi |
|---|---|---|---|
| **Tanpa Auth** | `POST` | `/api/v1/auth/register` | Pendaftaran akun mahasiswa/mentee baru. |
| **Basic Auth** | `POST` | `/api/v1/auth/login` | Login menukarkan kredensial dengan Bearer JWT token. |
| **JWT Bearer** | `POST` | `/api/v1/auth/logout` | Menghapus masa aktif token JWT saat ini. |
| **JWT Bearer** | `GET` | `/api/v1/user/profile` | Mengambil informasi profil login aktif (termasuk nama kampus). |
| **JWT Bearer** | `PUT` | `/api/v1/user/profile` | Memperbarui profil pengguna. |
| **JWT Bearer** | `POST` | `/api/v1/assessment/submit` | Menyimpan jawaban kuesioner RIASEC & Big Five serta menghitung kecocokan karir. |
| **JWT Bearer** | `GET` | `/api/v1/roadmap/current` | Mengambil peta jalan belajar aktif beserta daftar keahlian (Fondasi, Menengah, Lanjutan). |
| **JWT Bearer** | `PATCH` | `/api/v1/progress/update` | Memperbarui status progres skill (`done`, `in_progress`, `learning`). |
| **JWT Bearer** | `GET` | `/api/v1/progress/summary` | Mengambil metrik penyelesaian roadmap dan Career Readiness Score (CRS). |
| **JWT Bearer** | `POST` | `/api/v1/roadmap/pivot` | Memproses pergantian karir dan mentransfer modul keahlian yang relevan. |
| **JWT Bearer** | `GET` | `/api/v1/skill-gap` | Menganalisis kesenjangan skill aktif terhadap target karir. |
| **JWT Bearer** | `GET` | `/api/v1/export/json` | Mengunduh portofolio data pribadi mentah format JSON (portabilitas data). |
| **JWT Bearer** | `GET` | `/api/v1/export/pdf` | Mengunduh berkas laporan portofolio resmi format PDF. |
| **API Key** | `GET` | `/api/v1/careers` | Mengambil katalog daftar karir industri beserta bobot kecocokan RIASEC. |
| **API Key** | `GET` | `/api/v1/research/summary` | Statistik agregat platform (dinamis terfilter per kampus jika menggunakan key mitra). |
| **API Key** | `GET` | `/api/v1/research/career-distribution` | Distribusi pilihan karir terpopuler. |
| **API Key** | `GET` | `/api/v1/research/crs-trend` | Tren rata-rata Career Readiness Score (CRS) dalam 6 bulan terakhir. |
| **API Key** | `GET` | `/api/v1/research/pivot-analysis` | Analisis tren pergantian karir (pivot rate) pengguna. |

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
* `GEMINI_API_KEY=` & `GROQ_API_KEY=` (Kunci API kecerdasan buatan untuk generator naratif)
* `API_KEY=suluh-api-key-2024` (Master API Key untuk pengujian endpoint riset publik)

Setelah itu, generate application key & JWT secret:
```bash
php artisan key:generate
php artisan jwt:secret
```

### 4. Jalankan Migrasi & Database Seeder
Pastikan server database Anda aktif, kemudian jalankan migrasi untuk membuat tabel beserta data awal bawaan:
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

Proyek ini dilengkapi dengan 124 pengujian fungsionalitas otomatis (Feature & Unit Testing) menggunakan PHPUnit:

```bash
php artisan test
```

Semua pengujian dirancang database-agnostic dan dijamin lulus 100% baik pada driver MySQL maupun SQLite.

---

## ⚖️ Lisensi

Aplikasi ini bersifat open-source di bawah lisensi [MIT License](LICENSE).
