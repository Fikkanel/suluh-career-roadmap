# Skenario Pengujian (User Acceptance Testing) - Platform Suluh

Dokumen ini berisi langkah-langkah komprehensif untuk memvalidasi fitur-fitur utama Platform Suluh mulai dari Fase 1 hingga Fase 3, termasuk pengujian peran ganda (RBAC), fitur analitik, dan API.

---

## 1. Pengujian Halaman Publik (Unauthenticated)

**Tujuan:** Memastikan pengunjung dapat melihat informasi inti tanpa harus login dan tata letak publik berfungsi sempurna.

*   [ ] **Halaman Landing (`/`)**: Buka halaman utama. Pastikan UI merespons dengan baik, animasi berjalan halus, dan *scroll* menggunakan 2 jari (di *trackpad*) berjalan normal tanpa efek "memantul" di ujung halaman.
*   [ ] **Dampak (`/impact`)**: Klik menu "Dampak" di header. Pastikan Anda melihat grafik visual (Donut Chart / Bar Chart) yang menyajikan tren karir secara agregat dan anonim.
*   [ ] **Kebijakan Sunset (`/sunset-policy`)**: Buka menu ini dan pastikan manifesto kepemilikan data dan jaminan jika layanan tutup sudah tertulis dengan jelas.

---

## 2. Pengujian Alur Pengguna Utama (Role: User)

**Tujuan:** Memvalidasi fitur personalisasi karir, navigasi, dan kepemilikan data bagi pengguna umum.

*   [ ] **Registrasi Akun**: Klik "Mulai", buat akun baru.
*   [ ] **Formulir Asesmen Karir**:
    *   Sistem harus otomatis mengarahkan pengguna baru ke halaman Asesmen.
    *   Pilih beberapa jawaban pada pilihan ganda.
    *   **Validasi Real-time Progress Bar:** Pastikan angka persentase di bagian atas langsung meningkat, dan *bar* indikator di sebelah teks "Pertanyaan X dari 30" langsung terisi warna hijau penuh (100%) setiap kali pertanyaan dijawab.
    *   Selesaikan dan *Submit* asesmen.
*   [ ] **Dashboard Personal**:
    *   Buka **Roadmap** (`/roadmap`): Pastikan tampil rekomendasi tahapan karir.
    *   Cek tombol **Pivot**: Klik tombol "Pivot" (di pojok kanan atas layar desktop), simulasikan proses pergantian arah karir.
*   [ ] **Unduh / Ekspor Data**:
    *   Buka menu **Ekspor Data** (`/export`) di panel sidebar.
    *   Klik tombol unduh dan pastikan *file* format JSON (yang memuat data riwayat dan profil) berhasil terunduh. Ini memastikan Fase 2: Data Ownership berjalan.

---

## 3. Pengujian Manajemen Peran / Hak Akses (RBAC)

**Tujuan:** Memastikan setiap tipe pengguna (*Role*) hanya bisa melihat dan mengakses fitur yang menjadi haknya.

*(Gunakan data seeder bawaan aplikasi untuk pengujian ini. Klik tombol "Keluar" di sidebar bawah sebelum mengganti akun)*

### A. Skenario Mentor
*   **Login Email:** `mentor@suluh.id`
*   **Password:** `password`
*   **Checklist Validasi:**
    *   [ ] Setelah login, navigasi sidebar memuat kategori khusus: **Ruang Mentor**.
    *   [ ] Sidebar tidak memuat navigasi ke "Asesmen Karir" atau fitur "Pivot" (karena fitur tersebut khusus pengguna yang mencari karir).

### B. Skenario Mitra Institusi (Universitas/Pemerintah)
*   **Login Email:** `kampus@suluh.id`
*   **Password:** `password`
*   **Checklist Validasi:**
    *   [ ] Pengguna institusi secara otomatis di-redirect ke `institution/dashboard` setelah login (bukan ke halaman user biasa).
    *   [ ] Sidebar menampilkan menu eksklusif: **Mitra Institusi > Dashboard Analitik**.
    *   [ ] Halaman Dashboard Analitik memuat grafik agregat tren pasar tanpa menampilkan identitas personal individu mahasiswa/pengguna.
    *   [ ] **Uji Keamanan:** Salin URL `/institution/dashboard`. Buka tab penyamaran (Incognito), coba kunjungi URL tersebut tanpa login. Pastikan Anda ditolak (diarahkan ke halaman login) — Membuktikan *Middleware IsInstitution* berfungsi.

### C. Skenario Admin
*   **Login Email:** `admin@suluh.id`
*   **Password:** `password`
*   **Checklist Validasi:**
    *   [ ] Admin dapat mengakses rute manajerial, tetapi tidak bisa mengekspor atau melihat secara paksa kunci data privat (*private keys*) pengguna lain.

---

## 4. Pengujian API Peneliti Akademik

**Tujuan:** Memastikan endpoint distribusi data riset beroperasi dengan aman menggunakan sistem proteksi *API Key*.

1. Buka aplikasi seperti **Postman**, **Insomnia**, atau ekstensi VS Code **ThunderClient**.
2. Buat sebuah **GET Request** menuju URL: `http://127.0.0.1:8000/api/research/summary`
3. Klik **Send** *(Tanpa memasukkan API Key)*.
   *   [ ] **Validasi:** Anda harus mendapat balasan *Error 401 Unauthorized* (Akses Ditolak).
4. Tambahkan **Header**:
   *   Key: `X-API-KEY`
   *   Value: `suluh-api-key-2024`
5. Klik **Send** ulang.
   *   [ ] **Validasi:** Permintaan harus berhasil (*Status 200 OK*) dan merespons dengan format struktur JSON yang berisi tren karir anonim.

*Catatan: Endpoint lain yang bisa diuji termasuk `/api/research/career-distribution`, `/api/research/crs-trend`, dan `/api/research/pivot-analysis`.*
