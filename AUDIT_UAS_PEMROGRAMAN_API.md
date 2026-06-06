# ✅ AUDIT UAS PEMROGRAMAN API — SULUH CAREER ROADMAP

> **Mata Kuliah:** Pemrograman API | **Bentuk:** Proyek Kelompok | **Framework:** Laravel 11

---

## 📋 CHECKLIST SYARAT & KETENTUAN UAS

| No | Syarat | Status | Bukti / Keterangan |
|----|--------|--------|--------------------|
| 1 | Aplikasi berbasis web | ✅ **TERPENUHI** | Laravel 11 + Blade Views |
| 2 | Tema bebas, berbasis API | ✅ **TERPENUHI** | Platform Suluh Career Roadmap (karir & kepribadian) |
| 3 | Framework: Laravel | ✅ **TERPENUHI** | Laravel 11 (`composer.json`) |
| 4 | Fitur Migrasi & Relasi Tabel | ✅ **TERPENUHI** | 28 file migration + foreign key relasi |
| 5 | JWT Authorization | ✅ **TERPENUHI** | Library `php-open-source-saver/jwt-auth ^2.7` |
| 6 | Basic Auth | ✅ **TERPENUHI** | Middleware `BasicAuth.php`, dipakai di `POST /auth/login` |
| 7 | API Key | ✅ **TERPENUHI** | Middleware `ApiKeyAuth.php`, header `X-API-KEY` |
| 8 | OAuth2 (opsional) | ℹ️ **N/A** | Tertulis opsional, sudah ada Google OAuth via Socialite |
| 9 | Testing API dengan Postman | ✅ **TERPENUHI** | `Suluh_API_Postman_Collection.json` (baru dibuat) |
| 10 | Dokumentasi API | ✅ **TERPENUHI** | Auto-generated via Scramble (`GET /docs/api`) |

**KESIMPULAN: Semua syarat wajib TERPENUHI ✅**

---

## 🗂️ 1. MIGRASI & RELASI TABEL (28 Tabel)

### Daftar Tabel & Relasinya

```
users (induk)
├── assessment_results  →  foreignId('user_id') + foreignId('chosen_career_id')
├── user_progress       →  foreignId('user_id') + foreignId('skill_id')
├── roadmap_archives    →  foreignId('user_id') + foreignId('career_id')
├── impact_surveys      →  foreignId('user_id')
├── mentor_feedback     →  foreignId('user_id') + foreignId('mentor_id')
├── context_scores      →  foreignId('user_id')
├── llm_narrative_caches→  foreignId('user_id')
├── skill_validations   →  foreignId('user_id') + foreignId('skill_id')
├── ethics_decisions    →  foreignId('user_id')
└── notifications       →  (morphable, via Laravel)

careers (induk)
└── skills              →  foreignId('career_id') → cascadeOnDelete
```

### File Migrasi Penting
| File | Isi Relasi |
|------|------------|
| `create_assessment_results_table.php` | `foreignId('user_id')`, `foreignId('chosen_career_id')` ke `careers` |
| `create_user_progress_table.php` | `foreignId('user_id')`, `foreignId('skill_id')` + `unique(['user_id','skill_id'])` |
| `create_skills_table.php` | `foreignId('career_id')` → cascade delete |
| `create_mentor_feedback_table.php` | `foreignId('user_id')`, `foreignId('mentor_id')` |

---

## 🔐 2. JWT AUTHORIZATION

### Konfigurasi
- **Library:** `php-open-source-saver/jwt-auth ^2.7`
- **Guard:** `config/auth.php` → guard `api` menggunakan driver `jwt`
- **Config:** `config/jwt.php` (auto-generated oleh library)

### Cara Kerja
```
Client                              Server
  |                                   |
  |-- POST /auth/register ----------->|
  |<-- 201 { token: "eyJ..." } -------|
  |                                   |
  |-- POST /auth/login (Basic Auth) ->|  ← Header: Authorization: Basic base64(email:pass)
  |<-- 200 { token: "eyJ..." } -------|
  |                                   |
  |-- GET /api/v1/user/profile ------>|  ← Header: Authorization: Bearer eyJ...
  |   (middleware auth:api)           |
  |<-- 200 { profile: {...} } --------|
  |                                   |
  |-- POST /auth/logout ------------->|  ← Token di-blacklist di server
  |<-- 200 { "Logout berhasil" } -----|
```

### Endpoint yang Dilindungi JWT
| Endpoint | Method | Keterangan |
|----------|--------|------------|
| `/api/v1/auth/logout` | POST | Invalidate token |
| `/api/v1/user/profile` | GET | Lihat profil |
| `/api/v1/user/profile` | PUT | Update profil |
| `/api/v1/assessment/submit` | POST | Submit asesmen |
| `/api/v1/roadmap/current` | GET | Lihat roadmap |
| `/api/v1/roadmap/pivot` | POST | Pivot karir |
| `/api/v1/progress/update` | PATCH | Update skill |
| `/api/v1/progress/summary` | GET | Ringkasan CRS |
| `/api/v1/skill-gap` | GET | Analisis skill gap |
| `/api/v1/export/json` | GET | Export JSON |
| `/api/v1/export/pdf` | GET | Export PDF |

---

## 🔑 3. BASIC AUTH

### Implementasi
**File:** `app/Http/Middleware/BasicAuth.php`

```php
// Middleware memeriksa header Authorization: Basic <base64>
$header = $request->header('Authorization', '');

if (! str_starts_with($header, 'Basic ')) {
    return response()->json(['message' => 'Basic Auth header diperlukan.'], 401);
}

$decoded = base64_decode(substr($header, 6));
[$email, $password] = array_pad(explode(':', $decoded, 2), 2, '');

if (! Auth::once(['email' => $email, 'password' => $password])) {
    return response()->json(['message' => 'Email atau kata sandi tidak cocok.'], 401);
}
```

### Digunakan Di
```php
// routes/api.php
Route::post('auth/login', [AuthController::class, 'login'])->middleware('basic.auth');
```

**Cara Testing di Postman:**
1. Buka tab `Authorization`
2. Pilih `Basic Auth`
3. Isi Username = email, Password = password
4. Postman otomatis encode ke `Authorization: Basic <base64>`

---

## 🗝️ 4. API KEY AUTH

### Implementasi
**File:** `app/Http/Middleware/ApiKeyAuth.php`

```php
$key = $request->header('X-API-KEY');

// Cek master key dari .env
$validMasterKey = config('services.api.key');
if ($key === $validMasterKey) {
    return $next($request);
}

// Cek API Key institusi mitra dari database
$isValidInstitutionKey = User::where('role', 'institution')
    ->whereNotNull('api_key')
    ->where('api_key', $key)
    ->exists();
```

### Endpoint yang Dilindungi API Key
| Endpoint | Method | Akses |
|----------|--------|-------|
| `/api/v1/careers` | GET | Publik + institusi |
| `/api/v1/impact/public` | GET | Data dampak publik |
| `/api/v1/research/summary` | GET | Data riset agregat |
| `/api/v1/research/career-distribution` | GET | Sebaran karir |
| `/api/v1/research/crs-trend` | GET | Tren CRS per bulan |
| `/api/v1/research/pivot-analysis` | GET | Analisis pivot |

---

## 📡 5. DAFTAR LENGKAP ENDPOINT API

### Base URL: `http://localhost:8000/api/v1`

| # | Method | Endpoint | Auth | Keterangan |
|---|--------|----------|------|------------|
| 1 | `POST` | `/auth/register` | — | Daftar akun baru |
| 2 | `POST` | `/auth/login` | Basic Auth | Login → JWT Token |
| 3 | `POST` | `/auth/logout` | JWT | Logout |
| 4 | `GET` | `/user/profile` | JWT | Lihat profil |
| 5 | `PUT` | `/user/profile` | JWT | Update profil |
| 6 | `POST` | `/assessment/submit` | JWT | Submit jawaban asesmen |
| 7 | `GET` | `/roadmap/current` | JWT | Roadmap karir aktif |
| 8 | `POST` | `/roadmap/pivot` | JWT | Pivot (ganti) karir |
| 9 | `PATCH` | `/progress/update` | JWT | Update status belajar skill |
| 10 | `GET` | `/progress/summary` | JWT | Ringkasan CRS & progress |
| 11 | `GET` | `/skill-gap` | JWT | Analisis kesenjangan skill |
| 12 | `GET` | `/export/json` | JWT | Unduh data JSON |
| 13 | `GET` | `/export/pdf` | JWT | Unduh portofolio PDF |
| 14 | `GET` | `/careers` | API Key | Daftar semua karir |
| 15 | `GET` | `/impact/public` | API Key | Data dampak publik |
| 16 | `GET` | `/research/summary` | API Key | Statistik platform |
| 17 | `GET` | `/research/career-distribution` | API Key | Sebaran pilihan karir |
| 18 | `GET` | `/research/crs-trend` | API Key | Tren CRS 6 bulan |
| 19 | `GET` | `/research/pivot-analysis` | API Key | Analisis pivot karir |

**Total: 19 endpoint API aktif ✅**

---

## 📘 6. DOKUMENTASI API

### Auto-Generated: Scramble
- **Library:** `dedoc/scramble ^0.12`
- **UI:** `GET http://localhost:8000/docs/api` — tampilan interaktif OpenAPI
- **JSON:** `GET http://localhost:8000/docs/api.json` — raw OpenAPI spec

### Manual: Postman Collection
- **File:** `Suluh_API_Postman_Collection.json` (di root proyek)
- **Import:** Postman → Import → pilih file JSON
- **Variabel:** `{{base_url}}`, `{{jwt_token}}`, `{{api_key}}`

---

## 🧪 7. PANDUAN TESTING POSTMAN (Step-by-Step)

### Setup Awal
1. Buka Postman → klik **Import** → pilih `Suluh_API_Postman_Collection.json`
2. Set variabel collection:
   - `base_url` = `http://localhost:8000`
   - `api_key` = (isi dengan nilai `RESEARCH_API_KEY` dari file `.env`)

### Alur Testing Lengkap

#### Tahap 1: Auth Flow
```
[1] POST /auth/register  → Daftar akun baru
     Body: { name, email, password, password_confirmation }
     → Simpan token dari response

[2] POST /auth/login     → Login dengan Basic Auth
     Authorization Tab: Basic Auth → email + password
     → Salin "token" → paste ke variabel {{jwt_token}}
```

#### Tahap 2: Endpoint JWT (Gunakan Bearer Token)
```
[3] GET  /user/profile          → Lihat profil
[4] PUT  /user/profile          → Update profil
[5] POST /assessment/submit     → Submit asesmen
[6] GET  /roadmap/current       → Lihat roadmap (butuh karir aktif)
[7] GET  /progress/summary      → CRS & ringkasan skill
[8] GET  /skill-gap             → Gap analysis
[9] PATCH /progress/update      → Update skill status
[10] GET  /export/json          → Export data
[11] POST /roadmap/pivot        → Pivot karir
[12] POST /auth/logout          → Logout
```

#### Tahap 3: API Key Endpoints
```
[13] GET /careers                          → Tambahkan header X-API-KEY
[14] GET /research/summary                 → Data agregat platform
[15] GET /research/career-distribution     → Distribusi karir
[16] GET /research/crs-trend               → Tren CRS bulanan
[17] GET /research/pivot-analysis          → Analisis pivot
```

---

## ⚠️ CATATAN PENTING UNTUK PRESENTASI

### Cara Mendapatkan API Key untuk Testing
Nilai API Key ada di file `.env`:
```env
RESEARCH_API_KEY=SULUH-RESEARCH-KEY-2026  # atau nama variabel yang digunakan
```

Atau generate API Key institusi dari panel admin → halaman Kelola Pengguna → akun dengan role `institution`.

### Cara Menjalankan Aplikasi
```bash
php artisan serve          # Jalankan server di localhost:8000
php artisan queue:work     # Jalankan queue untuk proses AI (opsional)
```

### URL Penting Saat Demo
| URL | Fungsi |
|-----|--------|
| `http://localhost:8000` | Halaman utama web |
| `http://localhost:8000/docs/api` | Dokumentasi API Scramble (interaktif) |
| `http://localhost:8000/api/v1/...` | Base URL endpoint API |

---

*Dokumen ini dibuat untuk keperluan UAS Pemrograman API — Suluh Career Roadmap*
