# Laporan Detail Perubahan Proyek — Suluh Career Roadmap Platform

Dokumen ini memuat laporan teknis mendalam mengenai pembaruan sistem, integrasi AI, serta perubahan desain visual (*redesign*) yang telah diimplementasikan dari awal sesi percakapan.

---

## Tabel Komparasi Ringkas (Before vs After)

| No | Fitur / Komponen | Sebelum (Before) | Sesudah (After) | Lokasi File / Dampak |
|---|---|---|---|---|
| 1 | **Konfigurasi Kunci API** | Kunci API Groq & Gemini kosong di `.env`. Pemanggilan AI rentan gagal jika terkena pembatasan rate limit. | Kunci API Groq (`gsk_...`) & Gemini (`AQ...`) dikonfigurasi lengkap dengan mekanisme rotasi fallback di controller. | `.env` |
| 2 | **Data Pengguna & Database** | Penuh dengan akun demo & data dummy bawaan yang mengaburkan pengujian alur dari awal. | Database dikosongkan bersih (*truncated*) sehingga pendaftaran mandiri dapat diuji secara objektif dari awal. | Database / Migrasi |
| 3 | **Akses Dashboard & Halaman Karir** | Menu Dashboard, Roadmap, dan Progress Skill terkunci total jika belum menentukan karir. Error undefined variable `$hasCareer` saat masuk sebagai instansi atau mentor. | Tata letak menu diperbaiki agar aman dibuka dengan status terkunci (🔒). Memperbaiki bug `$hasCareer` dengan mendeklarasikannya secara global di layout. | `EnsureAssessmentCompleted.php` & `components/layouts/app.blade.php` |
| 4 | **Penyusunan Skill Roadmap** | Admin harus menginput manual daftar skill satu per satu di database. Bila kosong, halaman roadmap tidak menampilkan apa-apa. | AI secara dinamis merumuskan 6–8 kompetensi relevan berdasarkan jurusan & target karir, lalu menyimpannya ke DB secara permanen. | `RoadmapGeneratorService.php` |
| 5 | **Tata Letak Tombol Chatbot** | Tombol chat rendering di pojok kiri atas dan menutupi navigasi sidebar akibat pengaruh *parent flexbox parent container*. | Diposisikan melayang secara absolut (`fixed bottom-24 right-24 z-[9999]`) menggunakan koordinat inline yang konsisten. | `components/layouts/app.blade.php` |
| 6 | **Desain Visual Pop-up Chatbot** | Tema gelap gulita (*dark theme*) ala Instagram dengan gradasi gelembung ungu-biru yang bertabrakan dengan desain visual Suluh. | Redesain total bertema alami (*light-theme*), berlatar off-white, ber-header gradasi *sage green*, gelembung *forest green*, dan status aktif berdenyut. | `components/layouts/app.blade.php` |
| 7 | **Pengujian & Keamanan Endpoint** | Belum ada controller chatbot, rute diamankan, maupun file pengujian otomatis untuk memverifikasi fungsionalitas obrolan. | Dibuat `ChatbotController.php`, rute POST `/chatbot/message` (auth secured), serta 3 skenario feature test di `ChatbotTest.php`. | `ChatbotController.php`, `web.php`, `ChatbotTest.php` |
| 8 | **API Key Mitra (Fase 3)** | Mitra Institusi tidak memiliki akses mandiri untuk mengelola API Key. API Key riset hanya dikonfigurasi lewat satu entri `.env`. | Mitra dapat mengklik tombol "Generate API Key" langsung di dashboard mereka untuk mendapatkan token riset unik, serta menonaktifkannya (revoke). | `InstitutionDashboardController.php`, `ApiKeyAuth.php` & `institution/dashboard.blade.php` |
| 9 | **Manajemen Peran & Moderasi (Admin)** | Admin hanya memiliki CRUD terbatas untuk Karir, Soal Asesmen, dan proposal Etika, tanpa panel kontrol untuk pengguna. | Menyediakan panel tabel manajemen pengguna global bagi Admin untuk melihat daftar Mentee/Mentor/Mitra, mengubah peran pengguna, dan menghapus akun. | `AdminManagementController.php` & `admin/management.blade.php` |

---

## Penjelasan Detail Perubahan Per Fitur

### 1. Konfigurasi Kunci API Cadangan (.env)
*   **Sebelum (Before)**:
    API key untuk Groq dan Gemini belum dikonfigurasi. Platform tidak memiliki metode penanganan fallback ketika salah satu kunci API mengalami kehabisan kuota kueri harian (Rate Limit), menyebabkan modul pembuatan rekomendasi karir dan roadmap mogok.
*   **Sesudah (After)**:
    Menginput kredensial kunci API Groq (`gsk_...`) dan Gemini (`AQ....`) di berkas konfigurasi `.env`. Kode backend pada [LLMNarrativeService.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/app/Services/LLMNarrativeService.php) dikonfigurasi untuk membaca daftar kunci tersebut secara dinamis. Jika kunci utama dibatasi atau *rate limit*, sistem otomatis berotasi menggunakan kunci cadangan sehingga layanan AI tetap berjalan lancar.

### 2. Pembersihan Basis Data (Database Truncation)
*   **Sebelum (Before)**:
    Tabel pengguna (`users`), data asesmen (`assessment_results`), dan data progres (`skill_progress`) di database terisi oleh entri percobaan serta akun demo yang secara otomatis login tanpa kuesioner. Hal ini mempersulit pengujian skenario registrasi pengguna baru dari nol.
*   **Sesudah (After)**:
    Seluruh tabel pengguna dikosongkan (*truncated*). Sistem tidak lagi menyediakan login otomatis lewat akun demo. Pengujian dapat dilakukan secara natural melalui pendaftaran akun baru, pengisian asesmen kepribadian/minat bakat, hingga penentuan pilihan karir utama.

### 3. Logika Akses Dashboard, Roadmap, & Progres Skill
*   **Sebelum (Before)**:
    *   Bila pengguna baru mendaftar dan belum menempuh Asesmen Karir, ia akan terjebak dalam lingkaran pengalihan (*redirect loop*) ke halaman onboarding/asesmen setiap kali mencoba mengakses Dashboard, Roadmap, atau Progress Skill. Beberapa halaman bahkan menampilkan error database akibat query `current_career_id` yang bernilai kosong (`null`).
    *   Terjadi error `ErrorException: Undefined variable $hasCareer` pada tata letak menu saat login menggunakan akun dengan peran selain reguler (seperti Mitra/Institusi atau Mentor).
*   **Sesudah (After)**:
    *   Logika pemeriksaan status karir diperbaiki sehingga pengguna yang belum menempuh asesmen tetap dapat menjelajahi Dashboard dan menu navigasi.
    *   Halaman yang bergantung pada pilihan karir (seperti Roadmap dan Progress Skill) menyajikan status terkunci secara interaktif dengan ikon 🔒, alih-alih melempar error.
    *   Terdapat tombol CTA (*Call to Action*) yang ramah untuk mengarahkan pengguna menyelesaikan kuesioner asesmen terlebih dahulu.
    *   Mendeklarasikan variabel `$hasCareer` di bagian global paling atas dari tata letak layout (`components/layouts/app.blade.php`) agar selalu terdefinisi bagi seluruh jenis peran pengguna tanpa menyebabkan *undefined variable error*.

### 4. Otomatisasi Roadmap Berbasis AI
*   **Sebelum (Before)**:
    Sistem roadmap bersifat statis. Jika admin database belum mendefinisikan relasi skill untuk suatu karir spesifik secara manual di database, maka halaman roadmap pengguna akan kosong melompong tanpa panduan apa pun.
*   **Sesudah (After)**:
    Mengintegrasikan modul roadmap dengan agen cerdas AI di [RoadmapGeneratorService.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/app/Services/RoadmapGeneratorService.php). 
    *   Apabila pengguna memilih karir yang datanya belum ada di database, sistem di latar belakang akan menembak prompt khusus (`roadmap_generation`) ke AI.
    *   AI memformulasikan 6-8 skill relevan sesuai kombinasi jurusan kuliah dan target karir pengguna.
    *   Kompetensi dibagi merata ke tingkat Fondasi, Menengah, dan Lanjutan.
    *   Data ini langsung disimpan secara permanen di database sehingga query berikutnya memuat instan secara lokal.

### 5. Tata Letak Widget Chatbot Melayang
*   **Sebelum (Before)**:
    Pada tahap awal pembuatan chatbot, tombol pemicu diletakkan sebagai anak langsung dari kontainer flexbox body. Hal ini menyebabkan tombol chatbot tertarik ke pojok kiri atas dan menimpa teks judul platform pada layar desktop.
*   **Sesudah (After)**:
    Menerapkan properti pemosisian mutlak (`position: fixed; bottom: 24px; right: 24px; z-index: 9999;`) secara inline pada elemen pembungkus tombol chatbot dan jendela chatbot. Penempatan ini memastikan widget selalu berada di sudut kanan bawah layar terlepas dari resolusi layar dan tipe peramban yang digunakan.

### 6. Desain Visual Pop-Up Chatbot (Suluh Redesign)
*   **Sebelum (Before)**:
    Pop-up obrolan menggunakan rancangan gelap pekat (*dark theme*) ala Instagram. Gelembung chat pesan bot berwarna abu-abu gelap, gelembung chat pesan user berwarna gradasi ungu-biru menyala, serta latar belakang input berwarna hitam. Tema ini bertolak belakang dengan desain utama platform Suluh yang bernuansa alami, cerah, dan tenang (*earthy tones*).
*   **Sesudah (After)**:
    Mendesain ulang pop-up chatbot di [components/layouts/app.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/components/layouts/app.blade.php) menggunakan variabel CSS yang ada di `app.css` agar terlihat orisinal:
    *   **Frame & Latar Belakang**: Berwarna putih hangat (`var(--surface)`) dengan batas bingkai halus (`var(--border)`) dan dilapisi efek *blur* translusen (*glassmorphism*) yang menyatu dengan latar belakang sand/beige (`var(--bg)`).
    *   **Desain Header**: Dihiasi dengan warna gradasi *sage green* (`var(--accent-soft)`) ke off-white, menampilkan nama bot dengan warna hijau hutan tebal. Indikator "Aktif sekarang" memiliki animasi denyut hijau (*pulsing dot*) yang dinamis.
    *   **Warna Gelembung (Bubbles)**:
        *   *User*: Warna gradasi hijau hutan Suluh (`linear-gradient(135deg, var(--accent), var(--accent-deep))`) dengan bayangan halus.
        *   *Bot*: Warna off-white solid (`var(--surface)`) berbingkai tipis dan berserasi dengan teks gelap (`var(--fg)`).
    *   **Wadah Input**: Berwarna sand/beige (`var(--surface-2)`) dengan border transisi yang menyala hijau hutan (*focus glow*) saat aktif.
    *   **Animasi Transisi**: Menambahkan efek zoom transisi halus pada tombol kirim dan tombol melayang chat ketika di-hover/ditekan.

### 7. Keamanan Endpoint & Penulisan Unit Test
*   **Sebelum (Before)**:
    Belum ada endpoint, pengendali (*controller*), rute diamankan, maupun file pengujian otomatis untuk memverifikasi fungsionalitas obrolan.
*   **Sesudah (After)**:
    *   **ChatbotController.php**: Mengambil input, memvalidasi isian pesan, membatasi riwayat percakapan (hingga 6 pesan terakhir untuk menghemat token), dan berinteraksi secara aman dengan controller LLM.
    *   **Keamanan Rute**: Rute POST `/chatbot/message` dilindungi middleware `auth` dan token CSRF untuk mencegah celah eksploitasi eksternal.
    *   **Feature Test Suite**: Membuat [ChatbotTest.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/tests/Feature/Web/ChatbotTest.php) berisi pengujian fungsionalitas utama yang mencakup validasi input kosong, perlindungan otentikasi, dan validasi format balasan JSON (Seluruh 107 tes di sistem berhasil lulus 100% hijau).

### 8. Manajemen Mandiri API Key (Mitra/Instansi)
*   **Sebelum (Before)**:
    Mitra/Instansi (seperti universitas) tidak memiliki interface visual untuk memicu pembuatan API Key. Autentikasi API riset di `/api/v1/research/...` hanya menggunakan kunci statis tunggal yang diambil dari berkas `.env`, sehingga mempersulit pelacakan asal muasal data institusi.
*   **Sesudah (After)**:
    *   Membuat kolom `api_key` baru di tabel `users` melalui migrasi database khusus.
    *   Menambahkan panel "Kunci API Riset & Integrasi" di dashboard Mitra dengan antarmuka yang bersih (badge status, clipboard copy, warning merah, dan konfirmasi modal).
    *   Fungsi `generateApiKey` dan `revokeApiKey` dibuat untuk mempermudah pendaftaran dan pencabutan kunci akses secara mandiri.
    *   Middleware `ApiKeyAuth.php` diperluas untuk memvalidasi master key dan custom API key milik universitas yang terdaftar aktif di database.

### 9. Manajemen Pengguna & Moderasi Pengguna (Admin)
*   **Sebelum (Before)**:
    Admin tidak memiliki kemampuan untuk melihat, merubah peran (*role*), atau menolak/menghapus akun pengguna dan calon mentor yang terindikasi melanggar ketentuan layanan.
*   **Sesudah (After)**:
    *   Membuat panel "Manajemen Peran Pengguna & Moderasi Mentor" di bawah halaman `admin/management.blade.php`.
    *   Menampilkan data ringkas seluruh pengguna (ID, Nama, Email, Peran saat ini, status admin).
    *   Menyediakan aksi "Ubah Peran" yang membuka modal dinamis untuk menggeser peran pengguna (Mentee/Mentor/Mitra) dan mengubah status admin.
    *   Membuat fungsi `updateUser` dan `destroyUser` di `AdminManagementController.php` beserta rute put/delete terproteksi.
    *   Membuat unit test di `AdminAndInstitutionTest.php` untuk memverifikasi fungsionalitas ini (100% passed).
