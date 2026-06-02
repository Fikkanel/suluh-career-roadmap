# Suluh Product Constitution

> *"Suluh tidak memilihkan jalan. Ia hanya menerangi agar kamu bisa melihat dan memilih sendiri."*

**Versi:** 1.0.0 | **Status:** Aktif | Dibaca ulang setiap awal sprint.

---

## Prinsip Utama

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

---

## Anti-Prinsip

Hal-hal yang **tidak boleh** dilakukan, apapun alasannya:

- ❌ Menjual atau memonetisasi data individual pengguna dalam bentuk apapun
- ❌ Menampilkan rekomendasi berbayar sebagai rekomendasi organik tanpa label yang jelas
- ❌ Membuat pengguna merasa bersalah atas pilihan karir atau keputusan pivot mereka
- ❌ Menggunakan dark pattern untuk mendorong pengguna memberikan lebih banyak data dari yang diperlukan
- ❌ Membuat keputusan atas nama pengguna tanpa konfirmasi eksplisit mereka

---

## Hierarki Konflik

Ketika dua prinsip bertabrakan, urutan prioritas adalah:

1. **Keamanan & privasi pengguna** (tidak bisa dikompromikan)
2. **Otonomi pengguna** (keputusan ada di tangan pengguna)
3. **Akurasi rekomendasi** (lebih baik tidak merekomendasikan daripada merekomendasikan yang salah)
4. **Kemudahan penggunaan** (penting, tapi tidak mengalahkan tiga di atas)
5. **Efisiensi teknis** (dioptimalkan tanpa mengorbankan empat di atas)

---

## Contoh Keputusan Nyata

| Skenario | Keputusan yang Salah | Keputusan yang Benar |
|----------|---------------------|---------------------|
| Ingin meningkatkan engagement | Kirim notifikasi push agresif setiap hari | Kirim notifikasi hanya untuk momen yang relevan secara kontekstual |
| Butuh data lebih untuk akurasi | Minta data sensitif saat onboarding | Kumpulkan data secara bertahap saat pengguna sudah merasakan manfaat |
| Dapat tawaran kemitraan institusi | Berikan akses data individual pengguna | Berikan hanya data agregat anonim dengan persetujuan komite etika |
| Pengguna ingin pivot karir | Tanya "yakin mau buang semua progress?" | Tanya "apa yang kamu pelajari dari perjalanan sejauh ini?" |

---

## Copy yang Dilarang

- ❌ "karir terbaik untukmu"
- ❌ "kamu salah memilih"
- ❌ "progress gagal"
- ❌ "kamu tertinggal"
- ❌ "streak kamu terputus"

## Copy yang Dianjurkan

- ✅ "Kemungkinan yang layak kamu eksplorasi"
- ✅ "Arah yang selaras dengan pola jawabanmu"
- ✅ "Kamu tetap memegang keputusan akhirnya"
- ✅ "Yang sudah kamu pelajari tetap bernilai"
- ✅ "Perjalanan sebelumnya tidak hilang"

---

## Checklist Sprint Review

Dijalankan di setiap code review dan design review:

- [ ] Apakah fitur ini memperkuat atau mengurangi otonomi pengguna?
- [ ] Apakah ada data yang dikumpulkan melebihi yang diperlukan?
- [ ] Apakah pengguna bisa dengan mudah memahami apa yang terjadi dengan data mereka?
- [ ] Apakah ada dark pattern dalam alur ini?
- [ ] Apakah keputusan ini konsisten dengan Product Constitution?

---

*Dokumen ini hidup di root repositori dan dibaca ulang di awal setiap sprint.*
