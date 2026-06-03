# PETA STRUKTUR FILE TAMPILAN (BLADE VIEWS) — SULUH CAREER ROADMAP
Dokumen ini memetakan seluruh file tampilan (*Blade views*) di dalam direktori `resources/views/` berdasarkan kategori fungsinya (Publik, Admin, Input Pengguna, Dasbor khusus, dan Komponen Reusable). Gunakan dokumen ini sebagai contekan (*cheat sheet*) saat presentasi UAS jika dosen menanyakan lokasi file tertentu.

---

## 📂 1. LAYOUT & TEMPLATE UTAMA
File-file di kategori ini menjadi kerangka HTML dasar, pembungkus aset CSS/JS (`@vite`), serta menu navigasi sidebar/header.

| Lokasi File | Fungsi Utama | Keterangan Teknis |
|---|---|---|
| [layouts/app.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/layouts/app.blade.php) | **Layout Utama Mentee / Pengguna Biasa** | Menyediakan sidebar navigasi mentee dan memuat **widget chatbot asisten karir** di pojok kanan bawah secara global. |
| [layouts/admin.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/layouts/admin.blade.php) | **Layout Utama Panel Admin** | Menyediakan sidebar navigasi khusus kelola konten admin dan memuat script pembantu modal `@stack('modals')`. |
| [layouts/auth.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/layouts/auth.blade.php) | **Layout Halaman Otentikasi** | Kerangka minimalis tanpa sidebar untuk login dan registrasi. |
| [layouts/public.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/layouts/public.blade.php) | **Layout Halaman Publik** | Kerangka dengan navigasi header atas (*navbar*) dan footer publik. |

---

## 🌐 2. HALAMAN AKSES PUBLIK (Tanpa Login / Bebas Akses)
Halaman yang dapat diakses oleh siapa saja (tamu/guest) melalui peramban.

| Lokasi File | Fungsi Tampilan | Deskripsi Halaman |
|---|---|---|
| [welcome.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/welcome.blade.php) | **Landing Page** | Halaman utama penyambutan platform Suluh, berisi visi, diagram filosofi, dan tombol ajakan mendaftar. |
| [public/ethics.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/public/ethics.blade.php) | **Transparansi Komite Etika Data** | Menampilkan daftar proposal kebijakan pengolahan data publik dan status voting keputusan. |
| [public/api-docs.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/public/api-docs.blade.php) | **Dokumentasi API Publik** | Halaman interaktif bagi peneliti riset yang menampilkan list API endpoint, contoh cURL, dan panel test live respons JSON. |

---

## 🔑 3. HALAMAN LOGIN & REGISTRASI (Authentication)
Wadah entri akun untuk masuk ke dalam sistem.

| Lokasi File | Jenis Input | Deskripsi Form |
|---|---|---|
| [auth/login.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/auth/login.blade.php) | **Form Input Kredensial** | Kolom Email, Password, Checkbox Remember Me, dan Tombol Login Google OAuth. |
| [auth/register.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/auth/register.blade.php) | **Form Register Mentee** | Kolom Nama Lengkap, Email, Password, Konfirmasi Password, serta penanganan error validasi merah per-field. |
| [auth/register-institution.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/auth/register-institution.blade.php) | **Form Register Institusi Mitra** | Input data sekolah/kampus ditambah **kolom validasi kode akses** (`SULUH-MITRA-2026`) sebagai pengaman. |

---

## 🧭 4. HALAMAN KHUSUS MENTEE & INPUT PENGGUNA
Halaman aplikasi setelah mentee berhasil masuk (*authenticated*), berisi menu pelacakan karir dan pengerjaan kuesioner.

### A. Tampilan Berbasis Formulir / Input Pengguna (User Inputs)
| Lokasi File | Jenis Input Pengguna | Tujuan Formulir |
|---|---|---|
| [app/onboarding.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/app/onboarding.blade.php) | **Form Onboarding** | Mengisi Usia, Pendidikan Terakhir, dan Pengalaman Kerja. |
| [app/assessment.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/app/assessment.blade.php) | **Form Kuesioner Asesmen** | Menyajikan 30 soal skenario RIASEC + Big Five (dilengkapi tombol Simpan Draft & Submit). |
| [app/skill-validation.blade.php](file:///c:/FIKKAN%20/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/app/skill-validation.blade.php) | **Form Kuis Validasi Skill** | Pertanyaan skenario studi kasus teknis/non-teknis dengan input teks esai deskriptif. |
| [app/survey.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/app/survey.blade.php) | **Form Survei Longitudinal** | Input status pekerjaan, penyerapan skill, dan penilaian dampak platform di bulan ke-3 & ke-6. |
| [app/pivot.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/app/pivot.blade.php) | **Form Refleksi Pivot Karir** | Input refleksi mandiri sebelum berpindah jalur karir. |
| [app/profile-settings.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/app/profile-settings.blade.php) | **Form Pengaturan Profil** | Kolom perubahan Nama Lengkap, Username Publik, dan unggah foto profil (*avatar*). |

### B. Tampilan Halaman Dashboard & Evaluasi
| Lokasi File | Fungsi Utama Halaman | Elemen Penting di Dalamnya |
|---|---|---|
| [app/dashboard.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/app/dashboard.blade.php) | **Dashboard Mentee** | Kartu CRS progres, kotak **Umpan Balik Mentor**, dan **Daftar Rekomendasi Loker Auto-Match**. |
| [app/assessment-result.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/app/assessment-result.blade.php) | **Hasil Tes Kepribadian** | Grafik radar kepribadian RIASEC, detail trait Big Five, dan 3 kartu rekomendasi karir AI. |
| [app/career-detail.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/app/career-detail.blade.php) | **Informasi Detail Karir** | Grafik kesenjangan skill (*skill gap chart*), rentang gaji, dan tombol konfirmasi pilih karir. |
| [app/roadmap.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/app/roadmap.blade.php) | **Roadmap Visual** | Garis linier langkah ajar ber-ikon (Fondasi, Menengah, Lanjutan) buatan AI. |
| [app/skill-progress.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/app/skill-progress.blade.php) | **Skill Tracker** | Checklist dan dropdown status per skill (not started, learning, in progress, done). |
| [app/export.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/app/export.blade.php) | **Halaman Ekspor Data** | Halaman berisi tombol download data mentah JSON dan cetak ringkasan PDF via POST form aman. |
| [app/archive.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/app/archive.blade.php) | **Arsip Perjalanan** | Riwayat peta jalan karir terdahulu sebelum mentee melakukan pivot karir. |
| [app/notifications.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/app/notifications.blade.php) | **Notifikasi Sistem** | Riwayat notifikasi status progres atau feedback baru. |

---

## 👥 5. HALAMAN AKSES KHUSUS ADMIN (Admin Panel)
Halaman yang dilindungi middleware `admin` untuk mengelola operasional basis data konten.

| Lokasi File | Fungsi Manajemen Konten | Elemen Form & Input Admin |
|---|---|---|
| [admin/dashboard.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/admin/dashboard.blade.php) | **Statistik Global Platform** | Kartu data total pengguna, grafik sebaran wilayah, dan peta distribusi karir. |
| [admin/careers.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/admin/careers.blade.php) | **Kelola Katalog Karir** | Tabel data karir dan **Modal Input Tambah/Edit Karir** (Nama karir, deskripsi, kode RIASEC, rentang gaji). |
| [admin/questions.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/admin/questions.blade.php) | **Kelola Soal Asesmen** | Tabel bank soal dan **Modal Input Tambah Soal** (Naskah skenario, opsi jawaban, bobot RIASEC/Big Five). |
| [admin/ethics.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/admin/ethics.blade.php) | **Moderasi Komite Etika** | Tabel list proposal dan **Modal Input Tambah Proposal** (Judul kebijakan, konteks data, status voting, keputusan). |
| [admin/users.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/admin/users.blade.php) | **Kelola Peran & Moderasi User** | Tabel list user, modal edit role/hapus (terproteksi untuk admin), dan **Modal Form Generate Akun Baru Acak** (JS). |

---

## 🏛️ 6. HALAMAN AKSES MENTOR & INSTITUSI MITRA
Halaman bagi entitas pengawas (mentor dan universitas/lembaga mitra).

| Lokasi File | Fungsi Halaman | Elemen Input / Tampilan |
|---|---|---|
| [mentor/dashboard.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/mentor/dashboard.blade.php) | **Dashboard Mentor** | Menampilkan daftar seluruh mentee yang berada di bawah bimbingannya. |
| [mentor/mentee-detail.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/mentor/mentee-detail.blade.php) | **Detail Progres Mentee** | Grafik Radar Skill Gap mentee, checklist progres skill, dan **Form Input Masukan Feedback Mentor** kualitatif. |
| [institution/dashboard.blade.php](file:///c:/FIKKAN/kuliah/SEMESTER%204/PEMROGAMAN%20WEB%20LANJUT/TUGAS/UAS/pemwebapiuas/pemwebapiuas/resources/views/institution/dashboard.blade.php) | **Dashboard Agregat Kampus** | Grafik Donut sebaran karir siswa, Grafik Bar distribusi CRS, Grafik Line tren pertumbuhan, dan **Panel Generate/Revoke API Key**. |

---

## 🧩 7. KOMPONEN REUSABLE (Blade Components)
Komponen-komponen UI kecil yang dipanggil berulang kali di berbagai view menggunakan tag `<x-...>`.

*   `components/assessment-question.blade.php`: Merender butir soal kuesioner dengan styling input radio reaktif.
*   `components/career-card.blade.php`: Merender kartu ringkasan karir rekomendasi beserta persentase match.
*   `components/roadmap-card.blade.php`: Merender satu kotak unit kompetensi visual di halaman roadmap.
*   `components/skill-badge.blade.php`: Merender lencana level keahlian (Foundation, Intermediate, Advanced) dan status progres.
*   `components/progress-bar.blade.php`: Bar persentase Career Readiness Score (CRS) dengan animasi transisi CSS.
*   `components/skill-gap-chart.blade.php`: Visualisasi diagram kesenjangan skill mentee dibandingkan standar industri.
*   `components/privacy-notice.blade.php`: Banner peringatan privasi data agregat anonim (dipanggil di dashboard institusi).
*   `components/data-export-panel.blade.php`: Komponen pembungkus panel form POST untuk ekspor data.
*   `components/reflection-prompt.blade.php`: Kuesioner pertanyaan reflektif saat pengguna melakukan pivot.
*   `components/context-prompt.blade.php`: Banner modal penyesuaian pertanyaan kontekstual berdasarkan Context Score pengguna.
*   `components/impact-stat.blade.php`: Kotak metrik statistik kuantitatif pada halaman `/impact` publik.
