# Product Requirements Document (PRD)
# Suluh — Platform Roadmap Karir Personal Indonesia

> *"Suluh tidak memilihkan jalan. Ia hanya menerangi agar kamu bisa melihat dan memilih sendiri."*

**Versi:** 1.0.0  
**Status:** Final Draft — Siap Implementasi  
**Tanggal:** Mei 2026  
**Referensi:** SRS SkillPath v1.0, Sesi Grill-Me PRD (Mei 2026)

---

## Daftar Isi

1. [Latar Belakang & Masalah](#1-latar-belakang--masalah)
2. [Visi & Misi Produk](#2-visi--misi-produk)
3. [Product Constitution](#3-product-constitution)
4. [Target Pengguna & Konteks](#4-target-pengguna--konteks)
5. [Fitur & Requirements](#5-fitur--requirements)
6. [Arsitektur Teknis](#6-arsitektur-teknis)
7. [Alur Pengguna (User Flow)](#7-alur-pengguna-user-flow)
8. [Pengukuran Dampak](#8-pengukuran-dampak)
9. [Privasi & Etika Data](#9-privasi--etika-data)
10. [Kebijakan Sunset](#10-kebijakan-sunset)
11. [Keberlanjutan Nilai](#11-keberlanjutan-nilai)
12. [Roadmap Pengembangan](#12-roadmap-pengembangan)
13. [Metrik Keberhasilan](#13-metrik-keberhasilan)

---

## 1. Latar Belakang & Masalah

### 1.1 Konteks

Indonesia menghadapi paradoks yang nyata: angka pengangguran terdidik terus tinggi bukan karena kurangnya lapangan kerja, tapi karena ada jurang yang lebar antara apa yang diajarkan institusi pendidikan dengan apa yang dibutuhkan industri. Jutaan mahasiswa lulus setiap tahun tanpa peta yang jelas tentang ke mana mereka harus melangkah.

Platform karir yang ada hari ini — baik global maupun lokal — gagal menjawab kebutuhan ini karena tiga alasan utama:

- **Tidak personal.** Rekomendasi didasarkan pada data populasi, bukan profil individu.
- **Tidak kontekstual.** Tidak mempertimbangkan tekanan nyata yang dihadapi pengguna: finansial, keluarga, waktu.
- **Tidak bisa dibuktikan.** Klaim dampak tidak didukung oleh data yang transparan dan bisa diverifikasi publik.

### 1.2 Masalah yang Diselesaikan

| # | Masalah | Dampak |
|---|---------|--------|
| P1 | Mahasiswa tidak tahu karir apa yang sesuai dengan kepribadian dan potensi mereka | Keputusan karir berdasarkan tren atau tekanan sosial, bukan data diri |
| P2 | Roadmap karir yang tersedia bersifat generik dan tidak mempertimbangkan skill yang sudah dimiliki | Pemborosan waktu belajar pada hal yang tidak relevan |
| P3 | Tidak ada ruang aman untuk mengeksplorasi dan berubah arah tanpa stigma | Pengguna terjebak pada pilihan pertama karena takut dianggap gagal |
| P4 | Platform sejenis tidak bisa membuktikan dampaknya secara kuantitatif | Tidak ada bukti bahwa platform benar-benar membantu |

---

## 2. Visi & Misi Produk

### Visi

> Setiap orang Indonesia, di manapun mereka berada dan apapun latar belakangnya, punya akses ke panduan karir yang benar-benar personal, jujur, dan berpihak pada mereka.

### Misi

Membangun infrastruktur digital yang menerangi perjalanan karir pengguna melalui data ilmiah, narasi yang manusiawi, dan pengukuran dampak yang transparan — tanpa memungut biaya, tanpa menghakimi, dan tanpa mengeksploitasi data mereka.

### Nama & Filosofi

**Suluh** berasal dari kata bahasa Indonesia yang berarti obor atau lentera. Filosofinya: suluh tidak memilihkan jalan, ia hanya menerangi sehingga orang bisa melihat dan memutuskan sendiri. Ini adalah pernyataan tentang bagaimana platform memandang penggunanya — sebagai subjek yang berdaulat atas keputusan hidupnya, bukan objek yang perlu diarahkan.

---

## 3. Product Constitution

> Dokumen ini adalah referensi pertama untuk setiap perdebatan desain dan keputusan teknis. Dibaca ulang setiap awal sprint.

### 3.1 Prinsip Utama

**P1 — Pengguna adalah subjek, bukan objek.**  
Platform menyediakan informasi dan panduan, tapi tidak pernah memutuskan untuk pengguna. Setiap momen keputusan kritis dirancang untuk memperkuat otonomi pengguna, bukan menguranginya.

**P2 — Privasi adalah arsitektur, bukan niat baik.**  
Perlindungan data tidak bergantung pada janji atau kebijakan privasi yang tidak dibaca. Ia ditegakkan secara teknis melalui enkripsi, pseudonymization, dan data minimization sejak hari pertama.

**P3 — Setiap eksplorasi adalah kemajuan.**  
Tidak ada pengguna yang gagal di Suluh. Pivot karir adalah data, bukan kelemahan. Riwayat perjalanan adalah bukti pertumbuhan, bukan rekam jejak kegagalan.

**P4 — Dampak harus bisa dibuktikan.**  
Klaim manfaat tanpa data adalah kebohongan yang terstruktur. Suluh membangun infrastruktur pengukuran sejak hari pertama dan mempublikasikan hasilnya secara terbuka.

**P5 — Gratis bukan fitur, tapi komitmen.**  
Aksesibilitas adalah prasyarat dampak sosial. Model bisnis tidak boleh menciptakan insentif yang bertentangan dengan kepentingan pengguna.

### 3.2 Anti-Prinsip

Hal-hal yang **tidak boleh** dilakukan, apapun alasannya:

- ❌ Menjual atau memonetisasi data individual pengguna dalam bentuk apapun
- ❌ Menampilkan rekomendasi berbayar sebagai rekomendasi organik tanpa label yang jelas
- ❌ Membuat pengguna merasa bersalah atas pilihan karir atau keputusan pivot mereka
- ❌ Menggunakan dark pattern untuk mendorong pengguna memberikan lebih banyak data dari yang diperlukan
- ❌ Membuat keputusan atas nama pengguna tanpa konfirmasi eksplisit mereka

### 3.3 Hierarki Konflik

Ketika dua prinsip bertabrakan, urutan prioritas adalah:

1. **Keamanan & privasi pengguna** (tidak bisa dikompromikan)
2. **Otonomi pengguna** (keputusan ada di tangan pengguna)
3. **Akurasi rekomendasi** (lebih baik tidak merekomendasikan daripada merekomendasikan yang salah)
4. **Kemudahan penggunaan** (penting, tapi tidak mengalahkan tiga di atas)
5. **Efisiensi teknis** (dioptimalkan tanpa mengorbankan empat di atas)

### 3.4 Contoh Keputusan Nyata

| Skenario | Keputusan yang Salah | Keputusan yang Benar |
|----------|---------------------|---------------------|
| Ingin meningkatkan engagement | Kirim notifikasi push agresif setiap hari | Kirim notifikasi hanya untuk momen yang relevan secara kontekstual |
| Butuh data lebih untuk akurasi | Minta data sensitif saat onboarding | Kumpulkan data secara bertahap saat pengguna sudah merasakan manfaat |
| Dapat tawaran kemitraan institusi | Berikan akses data individual pengguna | Berikan hanya data agregat anonim dengan persetujuan komite etika |
| Pengguna ingin pivot karir | Tanya "yakin mau buang semua progress?" | Tanya "apa yang kamu pelajari dari perjalanan sejauh ini?" |

---

## 4. Target Pengguna & Konteks

### 4.1 Segmen Pengguna

Suluh tidak mengelompokkan pengguna berdasarkan label demografis, tapi berdasarkan **tingkat konteks dunia kerja** yang mereka miliki — diukur dari kombinasi data onboarding, pertanyaan kesiapan eksplorasi, dan perilaku awal di platform.

| Segmen Konteks | Karakteristik | Pendekatan Platform |
|---------------|---------------|---------------------|
| **Konteks Rendah** | Pelajar SMA/SMK, belum punya gambaran dunia kerja, eksplorasi terbuka | Narasi eksploratif, membangun izin untuk tidak tahu, banyak pilihan yang dipresentasikan sebagai kemungkinan |
| **Konteks Sedang** | Mahasiswa, sudah punya gambaran umum tapi belum konkret | Narasi yang memvalidasi keingintahuan, membantu mempersempit fokus secara bertahap |
| **Konteks Tinggi** | Fresh graduate, pekerja yang ingin pivot, sudah punya pengalaman | Narasi yang menghargai pengalaman, menonjolkan transferable skills, dukungan keputusan yang lebih direktif |

### 4.2 Context Score

Platform membangun **Context Score** yang bergerak dinamis berdasarkan:

- Data onboarding: usia, pendidikan terakhir, pengalaman kerja
- Pertanyaan kesiapan eksplorasi saat asesmen
- Perilaku di platform: kecepatan pengambilan keputusan, frekuensi kembali, pola interaksi
- Sinyal tekanan yang diberikan secara eksplisit: kondisi finansial, tekanan keluarga, ketersediaan waktu

Context Score menentukan: kedalaman narasi, tingkat direktifitas rekomendasi, jumlah opsi yang ditampilkan di momen keputusan kritis.

### 4.3 Peran dalam Sistem

| Peran | Deskripsi | Hak Akses |
|-------|-----------|-----------|
| **Pengguna** | Pengguna utama platform | Asesmen, roadmap personal, progress tracking, export data |
| **Mentor** | Tumbuh organik dari komunitas pengguna aktif | Akses profil mentee, beri masukan roadmap, validasi jalur karir |
| **Institusi** | Kampus, sekolah, lembaga mitra | Data agregat anonim peserta, laporan karir, tidak ada akses individual |
| **Admin** | Pengelola platform | Manajemen konten, kurator karir & skill, statistik global, komite etika |

---

## 5. Fitur & Requirements

### 5.1 MVP — Fase 1

#### FR-01: Autentikasi Multi-mekanisme

- Web session via email/password dan OAuth Google
- JWT untuk akses API (digunakan institusi mitra dan integrasi eksternal)
- Basic Auth untuk endpoint internal tertentu
- API Key untuk akses data publik (karir, statistik dampak)

#### FR-02: Authorization Berbasis Policy

Implementasi Laravel Gates & Policies per resource:

- `AssessmentPolicy` — pengguna hanya bisa melihat hasil milik sendiri
- `RoadmapPolicy` — mentor bisa memberi komentar, institusi hanya akses agregat
- `ProgressPolicy` — hanya pemilik yang bisa mengubah status skill
- `AdminPolicy` — akses fitur administrasi dan statistik global
- Gate `view-public-impact` — terbuka untuk semua termasuk tamu
- Gate `manage-mentee` — mentor ke profil mentee yang ditugaskan

#### FR-03: Career Assessment (RIASEC + Big Five)

- Minimal 30 pertanyaan berbasis skenario sehari-hari (bukan tes psikologi kaku)
- Setiap pertanyaan dikaitkan dengan dimensi RIASEC dan Big Five trait
- Hasil berupa enam skor RIASEC dan lima skor Big Five

#### FR-04: Scoring Engine & Rekomendasi

- Menghitung skor kecocokan antara profil pengguna dengan setiap karir di database
- Menghasilkan tiga rekomendasi karir teratas dengan persentase kecocokan
- Setiap rekomendasi dilengkapi narasi dinamis yang menjelaskan *mengapa* karir ini cocok berdasarkan profil pengguna spesifik
- Narasi dihasilkan melalui LLM (triggered, bukan streaming) dan di-cache

#### FR-05: Pemilihan Karir oleh Pengguna

- Pengguna memilih sendiri dari tiga rekomendasi
- Sistem tidak memaksakan pilihan tertinggi sebagai default
- Pengguna bisa melihat detail setiap opsi sebelum memutuskan

#### FR-06: Dynamic Roadmap Generator

- Roadmap disusun berdasarkan karir yang dipilih dan skill yang sudah dimiliki pengguna
- Urutan: beginner → intermediate → advanced
- Setiap tahap berisi: nama skill, estimasi waktu, level, dan sumber belajar yang direkomendasikan
- Skill dari roadmap lama yang sudah selesai diakui otomatis di roadmap baru

#### FR-07: Progress Tracker

- Status skill: `not_started` → `learning` → `in_progress` → `done`
- Setiap perubahan status dicatat dengan timestamp
- Career Readiness Score (CRS) diperbarui otomatis setiap ada perubahan status

#### FR-08: Skill Gap Analysis

- Perbandingan visual antara skill yang dikuasai vs standar industri karir yang dipilih
- Ditampilkan sebagai grafik dan daftar prioritas skill yang perlu dikerjakan

#### FR-09: Validasi Skill (Enrichment, Bukan Tembok)

- **Skill teknis:** kuis deskriptif berbasis skenario, tidak ada jawaban benar/salah, mendorong refleksi
- **Skill non-teknis:** diukur dari perilaku nyata pengguna di platform (kontribusi komunitas, konsistensi, responsivitas)
- Tidak ada pengguna yang ditolak karena tidak punya bukti formal
- Setiap partisipasi adalah bukti

#### FR-10: Fitur Pivot Karir

- Pengguna bisa berpindah karir kapan saja tanpa penalti
- Riwayat roadmap lama diarsipkan (bukan dihapus) dan ditampilkan sebagai "perjalanan"
- Proses pivot difasilitasi melalui flow refleksi terstruktur (2–3 layar) dengan pertanyaan seperti: *"Apa yang kamu pelajari dari perjalanan sejauh ini?"*
- Riwayat pivot bersifat privat — hanya bisa dilihat pengguna sendiri
- Narasi milestone pivot dihasilkan LLM dan di-cache

#### FR-11: Blade Components

Komponen reusable yang diimplementasikan:

- `<x-roadmap-card>` — kartu satu tahap roadmap
- `<x-skill-badge>` — badge skill per level dan status
- `<x-progress-bar>` — bar persentase CRS dengan animasi
- `<x-assessment-question>` — pertanyaan asesmen interaktif
- `<x-impact-stat>` — statistik dampak untuk halaman publik
- `<x-career-card>` — kartu rekomendasi karir dengan narasi dinamis
- `<x-context-prompt>` — pertanyaan kontekstual berbasis Context Score

#### FR-12: Admin Dashboard

- Statistik agregat: pengguna aktif, rata-rata CRS, distribusi karir, sebaran wilayah
- Manajemen konten: karir, skill, pertanyaan asesmen
- Monitoring komite etika: permintaan penggunaan data eksternal

#### FR-13: Public Impact Dashboard (`/impact`)

- Dapat diakses siapa saja tanpa login
- Menampilkan: total pengguna aktif, rata-rata peningkatan CRS, % pengguna yang lapor perubahan karir positif, sebaran per provinsi
- Diperbarui otomatis setiap 24 jam
- Data yang ditampilkan adalah agregat anonim yang sudah melalui anonimisasi teknis

#### FR-14: Survei Dampak Longitudinal

- Terkirim otomatis pada bulan ke-3 dan ke-6 setelah onboarding
- Pertanyaan: apakah sudah dapat pekerjaan baru? Pivot karir? Naik posisi? CRS saat ini?
- Hasil tersimpan di tabel `impact_surveys` dengan kolom `crs_before` dan `crs_after`

#### FR-15: Export Data Pengguna

- Pengguna bisa mengekspor seluruh data miliknya kapan saja
- Format: PDF (ringkasan perjalanan karir) dan JSON (data mentah)
- Data yang diekspor: profil, hasil asesmen, roadmap, progress, riwayat pivot, hasil survei

---

## 6. Arsitektur Teknis

### 6.1 Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 11 |
| Frontend | Blade + Livewire |
| Database | MySQL 8 |
| Cache | Redis |
| Auth Web | Laravel Session + OAuth Google |
| Auth API | JWT (tymon/jwt-auth) |
| LLM Narasi | Google AI Studio (Gemini 2.5 Flash) → Groq (Llama 3.3 70B) sebagai fallback |
| Queue | Laravel Queue + Redis driver |
| Storage | Local / S3-compatible untuk export file |

### 6.2 Repository Pattern

```
app/
├── Repositories/
│   ├── Contracts/
│   │   ├── UserRepositoryInterface.php
│   │   ├── AssessmentRepositoryInterface.php
│   │   ├── RoadmapRepositoryInterface.php
│   │   ├── ProgressRepositoryInterface.php
│   │   └── ImpactRepositoryInterface.php
│   └── Eloquent/
│       ├── UserRepository.php
│       ├── AssessmentRepository.php
│       ├── RoadmapRepository.php
│       ├── ProgressRepository.php
│       └── ImpactRepository.php
├── Services/
│   ├── ScoringService.php
│   ├── RoadmapGeneratorService.php
│   ├── ContextScoreService.php
│   ├── LLMNarrativeService.php
│   └── ImpactCalculatorService.php
```

### 6.3 Strategi LLM (Hybrid)

```
Momen yang di-trigger LLM (bukan streaming):
├── Narasi rekomendasi karir (saat asesmen selesai)
├── Refleksi pivot (saat pengguna memulai proses pivot)
├── Milestone narrative (saat CRS mencapai 25%, 50%, 75%, 100%)
└── Transfer insight (skill lama yang relevan di roadmap baru)

Strategi cache:
└── Narasi disimpan di database setelah dihasilkan
    └── Hanya di-regenerasi jika profil pengguna berubah signifikan

Fallback chain:
└── Google AI Studio → Groq → Template rule-based (emergency fallback)
```

### 6.4 Skema Database

```sql
users               — profil + pseudonymized identifier + personality_scores (JSON)
careers             — karir + riasec_code + industry_standard
skills              — skill per karir + level + resources (JSON) + estimated_hours
user_progress       — status skill per pengguna + timestamps
assessment_questions — pertanyaan + riasec_category + big_five_trait + weight
assessment_results  — skor RIASEC + Big Five + top_career_ids + CRS
roadmap_archives    — roadmap lama yang diarsipkan saat pivot
impact_surveys      — survei 3 & 6 bulan + crs_before + crs_after
mentor_feedbacks    — feedback mentor ke pengguna
context_scores      — Context Score pengguna yang diperbarui secara dinamis
llm_narrative_cache — narasi yang sudah dihasilkan LLM + hash profil pengguna
```

### 6.5 API Endpoints

| Method | Endpoint | Auth | Keterangan |
|--------|----------|------|-----------|
| POST | `/api/v1/auth/register` | — | Registrasi pengguna baru |
| POST | `/api/v1/auth/login` | Basic Auth | Login, terima JWT |
| POST | `/api/v1/auth/logout` | JWT | Invalidate token |
| GET | `/api/v1/user/profile` | JWT | Profil pengguna |
| PUT | `/api/v1/user/profile` | JWT | Update profil |
| GET | `/api/v1/careers` | API Key | List karir tersedia |
| POST | `/api/v1/assessment/submit` | JWT | Submit jawaban asesmen |
| GET | `/api/v1/roadmap/current` | JWT | Roadmap aktif |
| PATCH | `/api/v1/progress/update` | JWT | Update status skill |
| POST | `/api/v1/roadmap/pivot` | JWT | Inisiasi proses pivot |
| GET | `/api/v1/skill-gap` | JWT | Analisis skill gap |
| GET | `/api/v1/export/json` | JWT | Export data JSON |
| GET | `/api/v1/export/pdf` | JWT | Export data PDF |
| GET | `/api/v1/impact/public` | API Key | Statistik dampak publik |

---

## 7. Alur Pengguna (User Flow)

### 7.1 Alur Utama

```
[Daftar / Login]
       ↓
[Onboarding — isi profil dasar: usia, pendidikan, pengalaman]
       ↓
[Pertanyaan kesiapan eksplorasi — menentukan Context Score awal]
       ↓
[Asesmen RIASEC + Big Five — 30 pertanyaan skenario]
       ↓
[Scoring Engine — hitung kecocokan karir]
       ↓
[Tampil 3 rekomendasi karir + narasi dinamis per opsi]
       ↓
[Pengguna memilih sendiri]
       ↓
[RoadmapGeneratorService — susun roadmap personal]
       ↓
[Progress Tracking — tandai skill, CRS naik]
       ↓
[Validasi enrichment — kuis deskriptif / perilaku platform]
       ↓
[Survei bulan ke-3 dan ke-6 — ukur dampak nyata]
```

### 7.2 Alur Pivot

```
[Pengguna memutuskan ingin pivot]
       ↓
[Flow refleksi terstruktur — "Apa yang kamu pelajari?"]
       ↓
[LLM generate narasi transfer insight]
       ↓
[Roadmap lama diarsipkan — bukan dihapus]
       ↓
[Skill yang sudah done diakui otomatis di roadmap baru]
       ↓
[Roadmap baru dimulai dengan fondasi yang sudah ada]
```

---

## 8. Pengukuran Dampak

### 8.1 Metrik Primer

| Metrik | Cara Ukur | Target MVP |
|--------|-----------|-----------|
| Career Readiness Score (CRS) | Skill done / total skill × 100% | Rata-rata CRS pengguna aktif ≥ 40% setelah 3 bulan |
| Skill Gap Reduction | Selisih skill gap sebelum dan sesudah | Penurunan rata-rata 30% setelah 3 bulan |
| Pivot Rate | % pengguna yang melakukan pivot | Dimonitor, bukan ditarget — pivot adalah data, bukan masalah |
| Impact Survey Response Rate | % pengguna yang mengisi survei 3 & 6 bulan | ≥ 60% |
| Career Change Report | % pengguna yang lapor perubahan karir positif di survei | Dimonitor sejak bulan ke-6 |

### 8.2 Metrik Sekunder

- Jumlah pengguna aktif (DAU/MAU)
- Retention rate bulan ke-3
- Completion rate asesmen
- Waktu rata-rata dari daftar ke milestone pertama
- Sebaran pengguna per provinsi

### 8.3 Publikasi Data

Semua metrik primer dalam bentuk agregat anonim dipublikasikan di `/impact` dan diperbarui setiap 24 jam. Tidak ada data individual yang ditampilkan.

---

## 9. Privasi & Etika Data

### 9.1 Prinsip Teknis

- **Data minimization:** hanya kumpulkan data yang benar-benar diperlukan untuk fungsi platform
- **Pseudonymization:** identifier pengguna di tabel analitik diganti pseudonym yang tidak bisa di-reverse tanpa encryption key terpisah
- **Encryption at-rest:** kolom sensitif (personality_scores, skor asesmen) dienkripsi di level database
- **Encryption in-transit:** seluruh komunikasi wajib HTTPS

### 9.2 Penggunaan Data Eksternal

Data agregat anonim boleh digunakan untuk riset dan laporan industri **hanya jika** tiga syarat terpenuhi:

1. Anonimisasi ditegakkan secara teknis (bukan hanya niat baik) — tidak ada kombinasi atribut yang memungkinkan re-identifikasi
2. Persetujuan diberikan oleh pengguna **setelah** merasakan manfaat platform minimal 30 hari aktif, dengan bahasa yang jujur dan tidak manipulatif
3. Keputusan disetujui oleh **Komite Etika Data**

### 9.3 Komite Etika Data

- Memiliki **kuasa veto yang mengikat** atas setiap keputusan penggunaan data eksternal
- Dipilih secara transparan dari keragaman demografi pengguna
- Komposisi: perwakilan pengguna (mayoritas), tim teknis (minoritas), pihak independen (1 orang)
- Rapat dan keputusan dipublikasikan di halaman transparansi platform

---

## 10. Kebijakan Sunset

Ditulis dan dipublikasikan sejak hari pertama di halaman `/sunset-policy`.

### 10.1 Hak Export Pengguna

- Pengguna bisa mengekspor seluruh data miliknya **kapan saja** dalam format PDF dan JSON
- Export tersedia tanpa syarat, tanpa batas frekuensi

### 10.2 Prosedur Penutupan Platform

Jika platform berhenti beroperasi:

1. **Hari 0:** Pengumuman resmi via email, notifikasi in-app, dan media sosial
2. **Hari 1–90 (Grace Period):** Platform tetap aktif dalam mode read-only, fitur export tetap berjalan penuh, pengingat export dikirim di hari ke-30, 60, dan 80
3. **Hari 90:** Seluruh data pengguna yang tidak diekspor **dihapus permanen**
4. Penghapusan divalidasi melalui konfirmasi berbasis hash yang dipublikasikan sebagai bukti audit

### 10.3 Perlindungan terhadap Akuisisi

Jika platform diakuisisi oleh pihak lain:

- Pengguna diberitahu minimal 60 hari sebelum akuisisi efektif
- Pengguna punya hak untuk menghapus akun dan seluruh data sebelum akuisisi
- Acquirer tidak mewarisi data pengguna yang sudah melakukan penghapusan

---

## 11. Keberlanjutan Nilai

### 11.1 Tiga Lapisan Penjaga Nilai

**Lapisan 1 — Product Constitution**  
Dokumen satu halaman yang hidup di root repositori (`/CONSTITUTION.md`). Berisi prinsip, anti-prinsip, hierarki konflik, dan contoh keputusan nyata. Dibaca ulang di awal setiap sprint.

**Lapisan 2 — Checklist Prinsip**  
Dijalankan di setiap code review dan design review:

```
□ Apakah fitur ini memperkuat atau mengurangi otonomi pengguna?
□ Apakah ada data yang dikumpulkan melebihi yang diperlukan?
□ Apakah pengguna bisa dengan mudah memahami apa yang terjadi dengan data mereka?
□ Apakah ada dark pattern dalam alur ini?
□ Apakah keputusan ini konsisten dengan Product Constitution?
```

**Lapisan 3 — Values Guardian**  
Satu orang yang berperan sebagai penanya dan pengingat, bukan polisi. Bertugas setiap sprint untuk:
- Menghubungkan keputusan teknis yang diambil dengan nilai yang sudah disepakati
- Mengajukan pertanyaan *"Apakah ini konsisten dengan siapa kita?"* bukan *"Ini melanggar aturan"*
- Rotasi peran setiap dua sprint agar semua anggota tim merasakan perspektif ini

---

## 12. Roadmap Pengembangan

### Fase 1 — MVP (Bulan 1–3)

- [ ] Autentikasi: session web + JWT API + Basic Auth + API Key
- [ ] Authorization: Gates & Policies (Assessment, Roadmap, Progress, Admin)
- [ ] Repository Pattern + Service Layer
- [ ] Asesmen RIASEC + Big Five (30 pertanyaan)
- [ ] Scoring Engine + 3 rekomendasi karir
- [ ] Narasi dinamis via LLM (triggered + cached)
- [ ] Dynamic Roadmap Generator
- [ ] Progress Tracker + CRS
- [ ] Skill Gap Analysis
- [ ] Fitur Pivot dengan flow refleksi
- [ ] Blade Components (7 komponen)
- [ ] Admin Dashboard
- [ ] Public Impact Dashboard (`/impact`)
- [ ] Fitur Export (PDF + JSON)
- [ ] Database migration + relasi Eloquent lengkap

### Fase 2 — Pertumbuhan (Bulan 4–6)

- [ ] Sistem mentor organik dari komunitas
- [ ] Survei dampak longitudinal (bulan ke-3 dan ke-6)
- [ ] Context Score dinamis
- [ ] Notifikasi progress yang kontekstual
- [ ] Integrasi data loker Indonesia (Jobstreet, Glints)
- [ ] Halaman profil publik opsional
- [ ] Komite Etika Data — mekanisme voting dan publikasi keputusan

### Fase 3 — Skala (Bulan 7–12)

- [ ] Modul institusi mitra (kampus, lembaga pelatihan)
- [ ] Fine-tuned open-source LLM (pengganti Google AI Studio)
- [ ] Analitik lanjutan untuk riset dampak
- [ ] API publik untuk peneliti akademik

---

## 13. Metrik Keberhasilan MVP

Platform dianggap berhasil di akhir Fase 1 jika:

| Indikator | Target |
|-----------|--------|
| Pengguna yang menyelesaikan asesmen | ≥ 100 pengguna |
| Completion rate asesmen | ≥ 80% dari yang memulai |
| Pengguna yang mencapai milestone pertama roadmap | ≥ 60% |
| Rata-rata CRS setelah 1 bulan aktif | ≥ 25% |
| Tidak ada insiden kebocoran data | 0 insiden |
| Response rate survei bulan ke-3 | ≥ 60% |
| Pengguna yang lapor dampak positif (kualitatif) | ≥ 10 testimoni terverifikasi |

---

*PRD ini adalah dokumen hidup. Setiap perubahan signifikan dicatat di changelog dan dikomunikasikan ke seluruh tim sebelum diimplementasikan.*

*Versi berikutnya akan dibuat setelah Fase 1 selesai dan data pengguna nyata tersedia.*

---

**Direview oleh:** Sesi Grill-Me PRD, Mei 2026  
**Status:** Siap untuk implementasi Fase 1
