# LAPORAN PENGUJIAN AUTOMATED 2 — Platform Suluh (Fase 2 & 3)
**Tanggal:** 30 Mei 2026  
**Tester:** Manual + Agent-Assisted Browser Testing  
**Fokus:** Fitur Fase 2 (Pertumbuhan) & Fase 3 (Skala) yang baru diimplementasikan  
**Referensi Sebelumnya:** LAPORAN_PENGUJIAN_AUTOMATED.md (Fase 1 — 27 Mei 2026)

---

## RINGKASAN EKSEKUTIF

| Metrik | Nilai |
|---|---|
| Total Skenario Baru | 12 |
| **PASS** | **11 (91.7%)** |
| **PARTIAL** | **1 (8.3%)** |
| **FAILED** | **0 (0%)** |
| Fitur yang Diuji | Fase 2 & 3 |

**Catatan:** Seluruh 4 critical issues dari laporan sebelumnya telah diselesaikan. Pengujian ini berfokus pada fitur-fitur BARU yang diimplementasikan pada Fase 2 & 3.

---

## LINGKUNGAN PENGUJIAN

- **URL:** `http://127.0.0.1:8000`
- **Database:** MySQL (suluh)
- **Laravel Version:** 11.x
- **Akun Uji:** Sesuai seeder (`test@example.com`, `admin@suluh.id`, `mentor@suluh.id`, `kampus@suluh.id`)

---

## SKENARIO 2.1 — Komite Etika Data (Halaman Publik)

**URL:** `/ethics`  
**Akses:** Publik (tanpa login) & Login  
**Status: ✅ PASS**

| Langkah | Aksi | Hasil |
|---|---|---|
| 1 | Buka `/ethics` tanpa login | Halaman terbuka dengan layout publik (header Suluh) |
| 2 | Verifikasi konten | 3 proposal default tampil dengan status badge berwarna |
| 3 | Login sebagai `test@example.com` | Buka `/ethics` kembali |
| 4 | Verifikasi layout | Header sidebar app muncul (konsisten dengan halaman lain) |
| 5 | Klik Setuju/Tolak pada proposal "voting" | Vote berhasil, angka bertambah, flash message muncul |

**Temuan:** Layout header sebelumnya berbeda (standalone HTML). Sekarang menggunakan layout dinamis (`x-dynamic-component`) — konsisten.

---

## SKENARIO 2.2 — Admin: CRUD Komite Etika Data

**URL:** `/admin/management`  
**Akses:** Admin (`admin@suluh.id`)  
**Status: ✅ PASS**

| Langkah | Aksi | Hasil |
|---|---|---|
| 1 | Login sebagai admin, buka `/admin/management` | Halaman management terbuka |
| 2 | Scroll ke bagian "Komite Etika Data" | Tabel dengan 3 proposal default tampil |
| 3 | Klik "Tambah Proposal" | Modal form muncul dengan animasi |
| 4 | Isi: Judul, Konteks, Status = "voting", Keputusan kosong | Form terisi |
| 5 | Klik "Simpan Proposal" | Flash success muncul, proposal baru tampil di tabel |
| 6 | Buka `/ethics` (publik) | Proposal baru tampil di halaman publik |
| 7 | Kembali ke admin, klik "Hapus" pada proposal | Konfirmasi muncul, proposal terhapus dari tabel |

---

## SKENARIO 2.3 — Pendaftaran Institusi Mandiri

**URL:** `/register/institution`  
**Status: ✅ PASS**

| Langkah | Aksi | Hasil |
|---|---|---|
| 1 | Buka `/register` | Form registrasi biasa tampil |
| 2 | Scroll ke bawah | Link "🏛️ Daftar sebagai Institusi Mitra →" tampil di footer form |
| 3 | Klik link tersebut | Redirect ke `/register/institution` |
| 4 | Isi form dengan kode salah: `KODE-SALAH` | Error "Kode akses institusi tidak valid" muncul |
| 5 | Isi form dengan kode benar: `SULUH-MITRA-2026` | Submit berhasil |
| 6 | Verifikasi redirect | Langsung ke `institution/dashboard` |
| 7 | Verifikasi sidebar | Menu khusus institusi tampil |

**Catatan:** Kode akses berfungsi sebagai gatekeeper. Institusi tidak bisa mendaftar tanpa kode resmi dari tim Suluh.

---

## SKENARIO 2.4 — Dashboard Institusi Mitra

**URL:** `/institution/dashboard`  
**Akses:** `kampus@suluh.id` / `password`  
**Status: ✅ PASS**

| Langkah | Aksi | Hasil |
|---|---|---|
| 1 | Login sebagai `kampus@suluh.id` | Auto-redirect ke `/institution/dashboard` |
| 2 | Verifikasi notice privasi | Banner "Data Agregat Anonim" tampil di bagian atas |
| 3 | Cek stat cards | 4 kartu: Total Pengguna, Aktif 30 Hari, Rata-rata CRS, Pivot Rate |
| 4 | Verifikasi Donut Chart | Sebaran pilihan karir tampil (jika data ada) |
| 5 | Verifikasi Bar Chart | Distribusi progres CRS tampil |
| 6 | Verifikasi Line Chart | Tren pertumbuhan 6 bulan tampil |
| 7 | Cek tabel detail | Nama karir, kode RIASEC, jumlah pengguna, proporsi |
| 8 | Uji keamanan: akses tanpa login | Redirect ke login (middleware berfungsi) |

---

## SKENARIO 2.5 — Rekomendasi Lowongan Kerja di Dashboard

**URL:** `/dashboard`  
**Akses:** `test@example.com` (harus sudah selesai asesmen)  
**Status: ✅ PASS**

| Langkah | Aksi | Hasil |
|---|---|---|
| 1 | Login sebagai `test@example.com` | Dashboard terbuka |
| 2 | Cek apakah ada karir terpilih | Jika belum, selesaikan asesmen dulu |
| 3 | Lihat section "Rekomendasi Pekerjaan (Auto-Match)" | Section tampil di bawah milestone narrative |
| 4 | Verifikasi konten card loker | Nama jabatan sesuai karir, perusahaan, lokasi, estimasi gaji, % match |
| 5 | Verifikasi % match | Angka dinamis berbeda dari 100% (dihitung dari CRS) |
| 6 | Klik tombol "Lamar" | Alert simulasi MVP muncul dengan penjelasan sumber data |

---

## SKENARIO 2.6 — API Docs Interaktif

**URL:** `/api-docs`  
**Akses:** Publik  
**Status: ✅ PASS**

| Langkah | Aksi | Hasil |
|---|---|---|
| 1 | Buka `/api-docs` | Halaman dokumentasi premium tampil |
| 2 | Klik endpoint "Ringkasan Statistik Platform" | Card membuka (accordion) |
| 3 | Verifikasi contoh request | cURL command dengan header X-API-KEY tampil |
| 4 | Verifikasi contoh respons | JSON sample tampil dengan syntax highlighting |
| 5 | Klik "Coba Sekarang" | Request dikirim ke API langsung dari browser |
| 6 | Verifikasi respons live | JSON nyata dari database tampil di bawah card |
| 7 | Uji endpoint lain: career-distribution | Respons tampil dengan data karir agregat |

---

## SKENARIO 2.7 — API Research Endpoints (4 Endpoint)

**Base URL:** `http://127.0.0.1:8000/api/v1/research/`  
**Auth Header:** `X-API-KEY: suluh-api-key-2024`  
**Status: ✅ PASS**

| # | Endpoint | Tanpa Key | Dengan Key | Struktur Respons |
|---|---|---|---|---|
| 1 | `/research/summary` | 401 ✅ | 200 ✅ | `total_users`, `active_users_30d`, `avg_career_readiness`, `pivot_rate_pct` |
| 2 | `/research/career-distribution` | 401 ✅ | 200 ✅ | Array: `career`, `riasec_code`, `total_users` |
| 3 | `/research/crs-trend` | 401 ✅ | 200 ✅ | Array: `month`, `avg_crs`, `active_users` |
| 4 | `/research/pivot-analysis` | 401 ✅ | 200 ✅ | `total_pivots`, `unique_pivoters`, `pivot_rate_pct`, `pivot_distribution` |

**Semua endpoint:** Mengembalikan field `success`, `endpoint`, `note` (privacy disclaimer), `data`, `cached_at`.

---

## SKENARIO 2.8 — Navbar & Footer Publik (API Docs Link)

**Status: ✅ PASS**

| Langkah | Aksi | Hasil |
|---|---|---|
| 1 | Buka halaman landing `/` | Navbar tampil |
| 2 | Verifikasi link "API Docs" di navbar | Link tampil dan aktif bold saat di halaman `/api-docs` |
| 3 | Buka footer | Link "API Publik Peneliti" tampil di kolom navigasi footer |
| 4 | Klik dari navbar | Navigasi berhasil ke `/api-docs` |

---

## SKENARIO 2.9 — Pengujian Keamanan Role (RBAC Lanjutan)

**Status: ✅ PASS**

| Skenario | Aksi | Hasil Diharapkan | Status |
|---|---|---|---|
| User biasa akses `/admin` | GET `/admin` | 403 Redirect ke dashboard | ✅ PASS |
| User biasa akses `/institution/dashboard` | GET tanpa sesi | 302 Redirect ke login | ✅ PASS |
| Mentor akses `/admin` | Login mentor, akses `/admin` | Redirect ke mentor dashboard | ✅ PASS |
| Institusi akses `/admin` | Login institusi, akses `/admin` | 403 atau redirect | ✅ PASS |
| Admin akses `/institution/dashboard` | Login admin | Redirect ke admin dashboard | ✅ PASS |
| API tanpa key: `/api/v1/research/*` | GET tanpa header | 401 Unauthorized | ✅ PASS |
| API key salah | Header: `X-API-KEY: wrong` | 401 Unauthorized | ✅ PASS |

---

## SKENARIO 2.10 — Skill Validation (FR-09)

**URL:** `/skill/{skillId}/validate`  
**Akses:** User yang sudah punya roadmap  
**Status: ✅ PASS**

| Langkah | Aksi | Hasil |
|---|---|---|
| 1 | Login, buka `/skill-progress` | Daftar skill tampil |
| 2 | Klik nama skill yang belum done | Redirect ke `/skill/{id}/validate` |
| 3 | Baca soal skenario | Pertanyaan kuis berbasis skenario tampil |
| 4 | Pilih jawaban dan submit | Sistem menilai jawaban |
| 5 | Verifikasi feedback | Hasil validasi tampil (berhasil/perlu belajar lagi) |

---

## SKENARIO 2.11 — Survei Dampak Longitudinal (FR-14)

**URL:** `/survey/month_3` dan `/survey/month_6`  
**Status: ✅ PASS**

| Langkah | Aksi | Hasil |
|---|---|---|
| 1 | Login, navigasi ke `/survey/month_3` | Form survei dampak tampil |
| 2 | Isi pertanyaan (perubahan karir, kenaikan gaji, dll) | Form terisi |
| 3 | Submit survei | Data tersimpan, redirect dengan success message |
| 4 | Cek `/survey/month_6` | Form survei 6 bulan tampil dengan format serupa |

---

## SKENARIO 2.12 — Dashboard Auto-Hide Header (UX)

**Status: ✅ PARTIAL**

| Langkah | Aksi | Hasil |
|---|---|---|
| 1 | Login, buka `/dashboard` | Header tampil di atas |
| 2 | Scroll ke bawah perlahan | Header menyembunyikan diri (translateY ke atas) ✅ |
| 3 | Scroll ke atas | Header muncul kembali ✅ |
| 4 | Tarik konten sampai mentok atas | Header tetap diam, konten bounce normal ✅ |
| 5 | Uji di halaman asesmen | Konten bisa scroll, header stabil ✅ |
| 6 | Uji di mobile viewport | **Header spacing kurang optimal pada viewport < 400px** ⚠️ |

**Status PARTIAL:** Perilaku auto-hide berfungsi di desktop. Perlu penyesuaian minor untuk viewport sangat kecil.

---

## TEMUAN & CATATAN

### ✅ Tidak Ada Critical Issues Baru
Seluruh fitur Fase 2 & 3 berfungsi sesuai desain PRD.

### ⚠️ Minor Issues (Non-Blocker)
1. **Mobile Viewport < 400px:** Spacing header area asesmen kurang optimal di layar sangat kecil.
2. **Cache API Research:** Data API di-cache 1 jam — jika database baru di-reset, data lama masih muncul sampai cache expire.
3. **Rekomendasi Loker:** Data loker bersifat dummy/simulasi (belum integrasi API Glints/Jobstreet asli) — ini by design untuk MVP.

---

## KESIMPULAN

Platform Suluh Fase 2 & 3 telah diuji dengan **11 dari 12 skenario PASS penuh** dan 1 skenario PARTIAL (minor UX di mobile). Tidak ada critical issues baru yang ditemukan.

**Fitur-fitur baru beroperasi sesuai spesifikasi PRD Suluh:**
- ✅ Transparansi Etika Data (publik + admin CRUD)
- ✅ Pendaftaran Institusi Mandiri dengan gatekeeper kode akses
- ✅ Dashboard Analitik Agregat Institusi (privacy-compliant)
- ✅ Rekomendasi Loker berbasis CRS (MVP)
- ✅ API Publik Peneliti (4 endpoint + dokumentasi interaktif)
- ✅ RBAC diperluas dan terverifikasi
- ✅ Auto-hide header untuk UX premium (berfungsi di desktop)

**Platform siap untuk demonstrasi dan pengujian UAT final.**

---

## PEMBARUAN PASCA-LENGUJUAN (Final Polish)

Berdasarkan tinjauan tambahan, skenario perbaikan kritis untuk UI dan migrasi data telah dijalankan secara sukses:
- **Pengujian UI Export (POST Form):** Mengganti elemen link menjadi tombol POST, ekspor PDF/JSON lulus pengujian (tidak lagi error Method Not Allowed saat diklik).
- **Update Nama Pengguna:** Skenario update profil (Nama Lengkap) berhasil dengan slug public username yang ter-regenerasi otomatis menyesuaikan nama baru (jika user belum menetapkan username unik).
- **Validasi Migrasi Data (Seeder):** Perintah automated `php artisan migrate:fresh --seed` sukses memuat data 8 karir dan skenario akun pengguna progresif tanpa hambatan relasional (100% PASS).
