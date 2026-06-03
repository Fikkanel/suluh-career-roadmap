# LAPORAN KONTRIBUSI INDIVIDU — PENGEMBANGAN & PERBAIKAN SULUH PLATFORM
Dokumen ini merangkum seluruh fitur, perbaikan bug, dan logika bisnis spesifik yang saya rancang, kembangkan, dan perbaiki pada platform **Suluh**. Dokumen ini dapat digunakan sebagai lampiran atau bahan presentasi untuk membuktikan kontribusi individu saya dalam proyek tim.

---

## 📌 RINGKASAN KONTRIBUSI UTAMA
Secara garis besar, kontribusi individu saya berfokus pada **tiga pilar utama**:
1.  **Penguatan Keamanan & Pembatasan Role (Security Boundaries):** Melindungi akun administrator dari sabotase internal dan mengamankan pendaftaran institusi.
2.  **Pemecahan & Refaktorisasi Antarmuka Admin (UI/UX Refactoring):** Restrukturisasi halaman admin monolitik menjadi manajemen terpisah yang konsisten dan memperbaiki layout scroll & visual modal.
3.  **Pengembangan Fitur Otomatisasi (Automation Features):** Pembuatan generator API Key mandiri untuk Mitra Institusi dan generator akun acak dinamis pada kelola pengguna admin.

---

## 🛠️ DETAIL IMPLEMENTASI FITUR & PERBAIKAN (BAGIAN SAYA)

### 1. Perbaikan Bug Layout Global & Error Login Instansi/Mentor
*   **Masalah:** Saat pengguna dengan peran selain reguler (seperti Mitra Institusi atau Mentor) masuk ke dashboard mereka, sistem melempar error `Undefined variable: hasCareer` di layout utama (`components/layouts/app.blade.php`). Hal ini terjadi karena variabel `$hasCareer` hanya dihitung untuk pengguna biasa yang wajib memiliki karir terpilih.
*   **Solusi & Perbaikan Saya:** 
    *   Saya mendeklarasikan variabel `$hasCareer` secara global pada bagian paling atas layout (`components/layouts/app.blade.php`) dan memberikan nilai default `false`.
    *   Saya menambahkan kondisional pengecekan peran di layout, sehingga pengecekan status karir hanya dievaluasi jika pengguna yang login adalah mentee biasa. Hal ini membuat dashboard Mitra Institusi dan Mentor dapat dibuka dengan aman tanpa error login lagi.

### 2. Fitur Manajemen API Key Mandiri untuk Mitra Institusi (Fase 3)
*   **Deskripsi:** Mitra Institusi (seperti Universitas) membutuhkan API Key untuk mengintegrasikan data tren karir agregat mahasiswa mereka ke sistem internal kampus.
*   **Implementasi Saya:**
    *   **Migrasi Basis Data:** Saya membuat migrasi untuk menambahkan kolom `api_key` ke tabel `users` guna menyimpan token API unik institusi.
    *   **Generator API Key:** Saya menulis fungsi `generateApiKey` dan `revokeApiKey` di [InstitutionDashboardController.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/app/Http/Controllers/Institution/InstitutionDashboardController.php).
    *   **Integrasi UI:** Saya mendesain panel "Kunci API Riset & Integrasi" di dashboard institusi dengan clipboard copy, status badge aktif/nonaktif, dan tombol pemicu generate/revoke instan.
    *   **Middleware Keamanan:** Saya menyesuaikan `ApiKeyAuth.php` untuk memvalidasi token API institusi saat mengakses data analitik riset di `/api/v1/research/...`.

### 3. Pemisahan Halaman Manajemen Admin (Content Management Refactoring)
*   **Masalah:** Sebelumnya, seluruh manajemen konten (karir, pertanyaan asesmen, etika data, dan pengguna) digabung dalam satu halaman monolitik `/admin/management` yang sangat panjang, lambat di-load, dan merusak tata letak scroll (konten terpotong saat di-scroll).
*   **Solusi & Refaktorisasi Saya:**
    *   Saya memecah halaman monolitik tersebut menjadi 4 halaman manajemen terpisah:
        1.  **Kelola Karir** (`/admin/careers` -> [careers.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/admin/careers.blade.php))
        2.  **Kelola Asesmen** (`/admin/questions` -> [questions.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/admin/questions.blade.php))
        3.  **Komite Etika** (`/admin/ethics` -> [ethics.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/admin/ethics.blade.php))
        4.  **Kelola Pengguna** (`/admin/users` -> [users.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/admin/users.blade.php))
    *   Saya merancang ulang sidebar menu admin di layout untuk mengarahkan pengguna ke rute masing-masing modul tersebut secara konsisten.
    *   Saya memperbaiki pembungkus layout utama admin dengan CSS flexbox dan pengaturan scrollbar agar konten utama memiliki kontainer scroll terisolasi, mengeliminasi bug scroll bertumpuk.

### 4. Perbaikan Menyeluruh Bug UI Admin: Backdrop Modal & Tombol Batal
*   **Masalah:** 
    1.  Backdrop hitam transparan modal form (seperti form tambah karir/pertanyaan) tidak menutupi seluruh layar (hanya menutupi area konten utama) karena terperangkap dalam properti CSS `relative` dan `overflow-hidden` dari elemen induk.
    2.  Tombol "Batal" di dalam form modal tidak berfungsi untuk menutup modal.
*   **Perbaikan Saya:**
    *   **Reposition DOM Modal:** Saya merelokasi semua kode HTML modal dari dalam kontainer halaman ke dalam direktif `@push('modals') ... @endpush`. Hal ini mendorong elemen modal ke `@stack('modals')` di akhir layout [admin.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/layouts/admin.blade.php) tepat sebelum tag `</body>` sehingga properti `position: fixed` modal dapat mendominasi layar secara merata.
    *   **Fungsi Javascript Penutup:** Saya menulis fungsi pembantu `closeModal(id)` yang membersihkan properti inline `display: flex` dan menambahkan kembali kelas CSS `.hidden` secara sinkron, membuat tombol **Batal** dan klik area luar modal bekerja 100% sempurna.

### 5. Fitur Otomatisasi Generate Akun Pengguna Baru
*   **Deskripsi:** Admin sering kali perlu membuat akun uji coba secara cepat tanpa perlu mengisi nama dan password secara manual satu per satu.
*   **Implementasi Saya:**
    *   Saya menambahkan modal form "Tambah Pengguna Baru" di halaman **Kelola Pengguna** (`admin/users`).
    *   Saya mengimplementasikan asisten **"Generate Data Acak"** berbasis Javascript (client-side) di form modal tersebut.
    *   Tombol ini secara instan menghasilkan nama lengkap Indonesia acak, alamat email dengan domain `@suluh.id`, dan password acak kuat sepanjang 12 karakter.
    *   Saya melindungi penayangan kredensial akun hasil generate dari celah XSS dengan menerapkan konversi HTML entity di Blade.

### 6. Proteksi Keamanan Akun Administrator (Security Boundary)
*   **Masalah:** Sebelumnya tidak ada pengaman yang mencegah sesama administrator mengubah peran atau menghapus akun administrator lain. Hal ini berbahaya bagi keamanan internal sistem.
*   **Implementasi Saya:**
    *   **Pertahanan Frontend (UI):** Di tabel pengguna (`admin/users.blade.php`), saya menyembunyikan tombol aksi **Ubah Peran** dan **Hapus** untuk baris pengguna yang memiliki status `is_admin === true`, dan menggantinya dengan teks informatif *"Tidak dapat diubah"*. Ini mencegah kesalahan klik oleh sesama admin.
    *   **Pertahanan Backend (Controller):** Saya menambahkan logika validasi keamanan di [AdminManagementController.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/app/Http/Controllers/Admin/AdminManagementController.php):
        *   Fungsi `updateUser()` akan menolak dan melempar pesan error jika user yang diubah rolenya berstatus admin.
        *   Fungsi `destroyUser()` akan langsung memblokir permintaan hapus jika target user adalah akun admin.

---

## 📈 KUALITAS & PENGUJIAN KODE (TEST SUITES)
Saya juga menulis skenario pengujian otomatis untuk memvalidasi bagian pekerjaan saya. Semua tes ini tersimpan di folder `tests/Feature/Web` dan dijalankan melalui PHPUnit:

*   [AdminSeparateManagementTest.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/tests/Feature/Web/AdminSeparateManagementTest.php):
    *   `test_guest_cannot_access_any_admin_pages` (Memastikan guest diblokir).
    *   `test_regular_user_cannot_access_any_admin_pages` (Memastikan mentee diblokir).
    *   `test_admin_can_access_careers_page` (Memastikan halaman kelola karir terpisah dapat diakses admin).
    *   `test_admin_can_access_questions_page` (Memastikan halaman kelola asesmen dapat diakses admin).
    *   `test_admin_can_access_users_page` (Memastikan halaman kelola pengguna dapat diakses admin).
    *   `test_admin_can_generate_and_store_new_user` (Memverifikasi input generate user baru berhasil disimpan).
    *   `test_admin_cannot_update_role_of_another_admin` (**Keamanan backend:** Memastikan admin tidak bisa merubah peran admin lain).
    *   `test_admin_cannot_delete_another_admin` (**Keamanan backend:** Memastikan admin tidak bisa menghapus admin lain).
*   [AdminAndInstitutionTest.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/tests/Feature/Web/AdminAndInstitutionTest.php):
    *   `test_institution_user_can_generate_and_revoke_api_key` (Memverifikasi alur siklus hidup API key di dasbor institusi).
    *   `test_admin_user_can_update_user_roles_and_delete_users` (Memverifikasi fungsionalitas CRUD user non-admin).

### Hasil Akhir Unit Testing:
Seluruh test suite di atas berjalan **100% HIJAU (Passed)** tanpa ada kegagalan, memastikan stabilitas kode yang saya buat sangat baik.
