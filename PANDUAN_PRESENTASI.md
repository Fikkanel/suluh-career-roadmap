# PANDUAN KUASAI PRESENTASI UAS — SULUH PLATFORM
Dokumen ini disusun khusus sebagai bahan persiapan presentasi ujian akhir (UAS). Dokumen ini merangkum seluruh aspek teknis, keputusan arsitektur, dan alur sistem agar Anda dapat menjawab pertanyaan dosen penguji dengan percaya diri.

---

## 📌 1. FILOSOFI & KONSEP UTAMA (The "Why")
Jika dosen bertanya, **"Apa itu Suluh dan mengapa membuat platform ini?"**, jawablah dengan poin-poin berikut:

*   **Nama & Filosofi:** *Suluh* berarti obor/lentera. Filosofinya adalah **"Suluh tidak memilihkan jalan. Ia hanya menerangi agar pengguna dapat melihat dan memutuskan jalannya sendiri."** Ini menunjukkan bahwa otonomi mutlak ada di tangan pengguna (tidak seperti platform lain yang memaksakan pilihan karir).
*   **Paradoks Pengangguran Terdik:** Indonesia memiliki angka pengangguran terdidik tinggi bukan karena lowongan kerja tidak ada, melainkan karena adanya jurang pemisah (*gap*) antara kompetensi lulusan akademis dengan kebutuhan industri, serta minimnya bimbingan karir yang personal dan kontekstual.
*   **Prinsip Utama (Product Constitution):**
    1.  *User* adalah subjek yang berdaulat, bukan objek untuk dieksploitasi datanya.
    2.  Privasi secara arsitektur (*privacy by design*), bukan sekadar janji kebijakan privasi.
    3.  Setiap eksplorasi adalah kemajuan (pivot karir bukan kegagalan, progres belajar lama tetap diarsipkan dan diakui).
    4.  Transparansi dampak (metrik agregat dipublikasikan secara terbuka di `/impact` agar dapat diaudit publik).

---

## 🛠️ 2. STACK TEKNOLOGI & ARSITEKTUR TEKNIS (The "How")
Dosen sering menguji pemahaman arsitektur. Berikut ringkasan stack yang digunakan:

*   **Backend:** Laravel 11 (PHP 8.2+).
*   **Frontend:** Blade Template + Laravel Livewire (untuk interaksi dinamis seperti pengerjaan kuis, chat, dan modal secara real-time tanpa reload halaman).
*   **Database:** MySQL 8.
*   **Caching & Queue:** Redis / Database driver.
*   **AI Engine:** Google Gemini 2.5 Flash (via API) sebagai mesin utama, dengan otomatisasi *fallback* ke Groq API (Llama 3.3 70B) jika kunci utama terkena pembatasan frekuensi kueri (*Rate Limit*).

### Pola Desain: Repository Pattern
Mengapa menggunakan *Repository Pattern*?
*   **Pemisahan Tanggung Jawab (*Separation of Concerns*):** Controller tidak melakukan query SQL/Eloquent secara langsung. Controller memanggil method dari *Repository Interface* (e.g., `UserRepositoryInterface`).
*   **Kemudahan Pengujian (*Mockability*):** Logika bisnis di Service Layer dapat diuji tanpa harus menyentuh database nyata, cukup dengan membuat mock dari repositori terkait.
*   **Fleksibilitas Database:** Jika di masa depan database diganti (misalnya dari MySQL ke MongoDB), kita cukup membuat kelas repositori baru yang mengimplementasikan interface yang sama, tanpa mengubah kode di controller.

---

## 📊 3. DUA ALGORITMA UTAMA (Teknis Mendalam)
Ini adalah **"Jantung"** dari proyek Anda. Dosen pasti akan menanyakan bagaimana AI dan kalkulasi skor bekerja.

### A. Algoritma Penskoran Minat (RIASEC + Big Five)
*   **Metode:** Jarak Euklidian (*Euclidean Distance*).
*   **Cara Kerja:**
    1.  Pengguna menjawab 30 pertanyaan skenario. Jawaban tersebut dikalkulasi menjadi vektor skor 6 dimensi RIASEC (Realistic, Investigative, Artistic, Social, Enterprising, Conventional) berkisar nilai 0-10.
    2.  Setiap karir di database memiliki standar koordinat RIASEC baku (misal Software Engineer memiliki kecenderungan tinggi pada *Investigative* dan *Realistic*).
    3.  Sistem menghitung jarak ruang multidimensi antara vektor pengguna dengan vektor karir.
    4.  Jarak terpendek menunjukkan tingkat kecocokan tertinggi. Jarak dinormalisasi menjadi persentase kecocokan 0-100% menggunakan rumus:
        $$\text{Match } \% = \left(1 - \frac{\text{Distance}}{\text{Max Distance}}\right) \times 100\%$$
    5.  Tiga karir dengan persentase kecocokan tertinggi disodorkan kepada pengguna.

### B. Generator Peta Jalan Pembelajaran Dinamis (AI Roadmap)
*   **Masalah:** Bagaimana jika karir baru ditambahkan oleh admin namun belum ada daftar skill-nya di database?
*   **Solusi (AI Dynamic Seeding):**
    1.  Saat pengguna memilih karir tersebut, sistem mendeteksi daftar skill bernilai kosong di database.
    2.  Backend secara asinkron memicu API LLM (Gemini/Groq) dengan prompt terstruktur, mengirimkan variabel `nama_karir` dan `jurusan_kuliah` pengguna.
    3.  AI merumuskan 6-8 skill industri nyata yang dibagi ke dalam 3 tingkat kematangan: **Fondasi** (*Foundation*), **Menengah** (*Intermediate*), dan **Lanjutan** (*Advanced*).
    4.  Respons berbentuk format JSON dibaca oleh backend dan langsung di-seed secara permanen ke tabel `skills` database.
    5.  Akses berikutnya untuk pengguna lain yang memilih karir yang sama akan langsung dibaca secara lokal dari database cache (~10ms) tanpa memanggil AI eksternal lagi (menghemat kuota token & performa cepat).

---

## 🛡️ 4. PRIVASI DATA & KEAMANAN LANJUTAN
Suluh dirancang agar patuh terhadap **UU Pelindungan Data Pribadi (UU PDP)**. Berikut adalah poin-poin pertahanan datanya:

1.  **Pseudonymization (Penyamaran Identitas):** 
    Tabel analitik eksternal (seperti `assessment_results` dan `impact_surveys`) tidak menyimpan kunci asing `user_id` secara langsung untuk kebutuhan riset publik. Kolom digantikan oleh `pseudonym_id` yang dihasilkan secara acak melalui fungsi hash unik di model. Identitas asli mentee tidak dapat dilacak dari tabel publik tersebut.
2.  **Encryption at-Rest (Enkripsi DB):**
    Kolom bernilai sensitif seperti jawaban kuesioner RIASEC, Big Five, dan jawaban survei longitudinal disimpan dalam keadaan terenkripsi di database menggunakan fitur enkripsi native Laravel (`hashed` cast & encryptor).
3.  **Transparansi Mitra Institusi:**
    Mitra Kampus/Sekolah **tidak diberikan** akses untuk melihat data pribadi siswa secara individual (nama/email/jawaban pribadi). Mitra hanya disajikan **Dashboard Analitik Agregat** (statistik persentase, diagram sebaran karir, tren pertumbuhan) demi menjaga kerahasiaan siswa.
4.  **Sunset Policy & Ekspor Data:**
    Sesuai hak subjek data UU PDP, pengguna dapat mengekspor seluruh datanya kapan saja dalam format PDF (cetak ringkasan) dan data mentah JSON secara gratis via POST request (terlindungi token CSRF). Jika platform ditutup, grace period 90 hari diberikan bagi user untuk mengunduh data sebelum dihapus permanen.

---

## 🆕 5. FITUR BARU YANG TELAH DISELESAIKAN (Fase 2 & 3)
Jika dosen bertanya, **"Apa saja pengembangan terbaru pada sistem ini di fase akhir?"**, jelaskan fitur-fitur berikut:

*   **Pemisahan Panel Kelola Admin:** Menu admin tidak lagi digabung dalam satu halaman monolitik yang lambat. Sekarang dipisah secara konsisten menjadi 4 modul dengan layout menu navigasi sidebar yang rapi:
    *   `admin/careers` (Kelola data karir).
    *   `admin/questions` (Kelola bank soal asesmen).
    *   `admin/ethics` (Kelola transparansi keputusan pemrosesan data).
    *   `admin/users` (Kelola akun pengguna).
*   **Fitur Generate Akun Admin:** Di menu kelola pengguna, admin dapat menekan tombol tambah user baru dan memanfaatkan tombol **"Generate Data Acak"** untuk mengisi form secara otomatis (Nama Indonesia, email `@suluh.id`, password aman) menggunakan logika client-side JS yang aman dari XSS.
*   **Proteksi Hak Akses Admin:** Pengaman backend & frontend di mana akun dengan status administrator (`is_admin = true`) tidak dapat dirubah perannya atau dihapus oleh administrator lain untuk mencegah sabotase internal.
*   **Pendaftaran Institusi Mandiri:** Institusi mitra (seperti Universitas) dapat mendaftar sendiri melalui halaman `/register/institution` dengan syarat harus memasukkan kode akses resmi yang valid dari sistem (`SULUH-MITRA-2026`).
*   **API Riset Publik & Docs Interaktif:** Halaman `/api-docs` menyediakan dokumentasi API lengkap dengan accordion, contoh request cURL, dan tombol **"Coba Sekarang"** untuk memicu penembakan langsung ke endpoint dan menampilkan JSON respon live di browser.
*   **Chatbot Asisten Karir:** Widget chatbot melayang (*floating UI*) di pojok kanan bawah yang dirancang ulang dengan tema cerah alami (*earthy-tones*) menyesuaikan branding Suluh. Dilengkapi status dot aktif berdenyut dan pembatasan session memori riwayat chat agar terhindar dari pemborosan kuota API LLM.
*   **Rekomendasi Loker (Auto-Match):** Dashboard mentee yang memiliki CRS ≥ 15% secara otomatis menampilkan widget kartu lowongan kerja simulasi dari Glints/Jobstreet yang jabatannya selaras dengan target karir yang dipilih pengguna.

---

## ❓ 6. KISI-KISI Q&A DOSEN PENGUJI (Cheat Sheet)

### Q1: "Bagaimana penanganan error jika API Key Gemini Anda habis kuota atau mati di tengah jalan?"
> **Jawaban:** "Kami menerapkan mekanisme **Fallback API Chain** di `LLMNarrativeService.php`. Jika pemanggilan Google Gemini gagal atau mengembalikan status rate limit, backend akan menangkap error tersebut lewat try-catch, lalu secara otomatis memutar rotasi kunci API cadangan menggunakan Groq API (Llama 3.3 70B). Jika kedua layanan API mati, sistem akan menggunakan template teks default berbasis aturan (*rule-based*) agar aplikasi tidak crash dan pengguna tetap mendapatkan rekomendasi."

### Q2: "Mengapa tombol Batal pada modal Admin awalnya tidak berfungsi dan backdrop hitamnya tidak menutupi seluruh layar, lalu bagaimana Anda memperbaikinya?"
> **Jawaban:** 
> 1. *Penyebab modal menggantung:* Awalnya tombol Batal hanya menyematkan kelas `.hidden` secara dinamis tanpa membersihkan property inline `display: flex` yang dipicu oleh fungsi pembuka modal.
> 2. *Penyebab backdrop tidak penuh:* Elemen modal diletakkan di dalam kontainer konten utama yang memiliki properti `relative` atau `overflow-hidden`, sehingga visual gelap modal terpotong oleh batas sidebar.
> 3. *Solusi:* Saya membuat fungsi Javascript terpusat `closeModal(id)` yang membersihkan inline style dan kelas CSS secara konsisten. Elemen modal juga didorong keluar dari kontainer konten menggunakan direktif `@push('modals')` agar dimuat di bagian paling bawah layout admin tepat sebelum tag `</body>` pada `@stack('modals')`."

### Q3: "Mengapa admin tidak boleh merubah peran atau menghapus sesama admin?"
> **Jawaban:** "Ini adalah standar keamanan pembatasan hak akses (*RBAC Security Boundary*). Mencegah terjadinya eskalasi hak akses atau sabotase di mana seorang admin menghapus admin lain dari sistem. Di level UI, tombol Ubah Peran dan Hapus untuk baris admin akan digantikan label 'Tidak dapat diubah'. Di level controller (`AdminManagementController.php`), terdapat pengecekan logic if-statement yang langsung menolak request PUT/DELETE jika target user memiliki bendera `is_admin = true`."

### Q4: "Bagaimana Anda memverifikasi bahwa perubahan fitur baru ini tidak merusak fitur-fitur yang sudah ada sebelumnya?"
> **Jawaban:** "Kami menerapkan pengujian otomatis terintegrasi menggunakan PHPUnit. Kami membuat kelas pengujian baru seperti `AdminSeparateManagementTest.php` untuk memverifikasi fungsionalitas CRUD terpisah, validasi generate data acak, dan proteksi admin. Seluruh rangkaian pengujian (121 unit & feature tests, 292 assertions) berhasil dijalankan 100% lulus tanpa kegagalan (*100% passed hijau*)."

### Q5: "Apa bedanya status 'learning' dan 'in_progress' pada pelacakan progres skill mentee?"
> **Jawaban:** "Secara fungsi perhitungan CRS, keduanya bernilai sama (belum dihitung sebagai selesai). Namun secara pengalaman pengguna (UX), kami memisahkannya untuk kenyamanan belajar: 'learning' ditujukan untuk tahap awal saat pengguna mulai membaca materi ajar, sedangkan 'in_progress' digunakan saat pengguna sedang mempraktikkan materi atau bersiap mengajukan kuis validasi skill."
