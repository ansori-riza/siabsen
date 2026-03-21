# Runbook Hardware Onboarding

Dokumen ini menjadi panduan operasional untuk onboarding perangkat hardware absensi dari awal hingga validasi operasional 1 hari penuh.

## 1) Registrasi Device

1. Login ke panel admin SIABSEN.
2. Masuk ke menu **Perangkat** lalu klik **Tambah Perangkat**.
3. Isi data minimum:
   - **Nama perangkat** (contoh: `Gate Utama SMP 1`).
   - **Sekolah** yang akan menggunakan perangkat.
   - **Device ID / serial number** sesuai label hardware.
   - **Status awal**: aktif.
4. Simpan data perangkat.
5. Verifikasi perangkat muncul pada daftar perangkat dengan sekolah dan device ID yang benar.

## 2) Set Device Key

1. Buka detail perangkat yang baru diregistrasi.
2. Generate atau input **device key** sesuai kebijakan keamanan internal.
3. Simpan perubahan.
4. Konfigurasikan device fisik agar menggunakan:
   - **Base URL API** SIABSEN.
   - **Device ID** yang terdaftar.
   - **Device Key** yang sama seperti di panel.
5. Lakukan restart service aplikasi device jika diperlukan.

> Catatan: device key bersifat rahasia, jangan dibagikan di grup umum atau tiket tanpa masking.

## 3) Test Heartbeat

Tujuan: memastikan device dapat melakukan autentikasi dan mengirim status online.

1. Pastikan device terhubung ke jaringan.
2. Trigger heartbeat dari device (otomatis berkala atau manual dari menu diagnostik device).
3. Verifikasi di panel/admin log:
   - Device terdeteksi **online**.
   - Timestamp heartbeat berubah ke waktu terbaru.
4. Jika heartbeat gagal, lanjut ke bagian troubleshooting **auth fail** atau **device offline**.

## 4) Test Sync

Tujuan: memastikan sinkronisasi data master (jadwal, pengguna, atau konfigurasi) berjalan.

1. Jalankan proses sync dari device (manual) atau tunggu jadwal sync otomatis.
2. Cek log device: tidak ada error 4xx/5xx.
3. Cek server/log aplikasi:
   - Request sync diterima.
   - Response sukses (status 200/2xx).
4. Uji perubahan kecil (misalnya update jadwal), lalu jalankan sync ulang dan pastikan data di device ikut berubah.

## 5) Test Absensi Masuk/Pulang

Tujuan: memastikan alur absensi end-to-end valid.

1. Siapkan minimal 1 akun uji (siswa/guru) yang valid.
2. Lakukan scan/absensi **masuk** di device.
3. Verifikasi data masuk di sistem:
   - Muncul di daftar absensi.
   - Jenis tercatat sebagai **masuk**.
   - Timestamp sesuai waktu pengujian.
4. Lakukan scan/absensi **pulang** untuk akun yang sama.
5. Verifikasi data pulang:
   - Muncul sebagai record **pulang**.
   - Tidak menimpa data masuk.
   - Terkait ke identitas pengguna yang benar.

---

## Checklist UAT 1 Hari Operasional

Gunakan checklist ini saat simulasi/operasional hari pertama.

- [ ] Device online sejak sebelum jam masuk.
- [ ] Heartbeat stabil sepanjang hari (tidak putus berkepanjangan).
- [ ] Sync pagi berhasil sebelum jam absensi masuk.
- [ ] Absensi masuk dapat direkam oleh beberapa akun uji/aktual.
- [ ] Data absensi masuk tampil di dashboard/admin tanpa keterlambatan signifikan.
- [ ] Sync siang/sore (jika ada) berjalan normal.
- [ ] Absensi pulang dapat direkam normal.
- [ ] Data masuk/pulang konsisten (tidak duplikat anomali).
- [ ] Tidak ada error auth berulang pada log device/server.
- [ ] Rekap akhir hari dapat ditarik/dilihat oleh operator.

Kriteria lulus UAT harian: seluruh poin kritikal (online, auth, sync, absensi masuk/pulang, rekap) terpenuhi tanpa insiden mayor.

---

## Troubleshooting Singkat (Kasus Umum)

### A) Auth fail

**Gejala:** response 401/403, device ditolak server, heartbeat/sync gagal.

**Langkah cepat:**
1. Cocokkan **device key** di panel dan di konfigurasi device (perhatikan spasi/karakter tersembunyi).
2. Pastikan **device ID** yang dikirim device sesuai data registrasi.
3. Regenerate device key, simpan, lalu update ulang di device.
4. Restart service device dan ulang test heartbeat.
5. Jika masih gagal, cek middleware/auth log API untuk detail penolakan.

### B) Device offline

**Gejala:** status offline, heartbeat tidak masuk.

**Langkah cepat:**
1. Cek koneksi jaringan device (LAN/Wi-Fi, gateway, DNS, internet/intranet sesuai arsitektur).
2. Ping/curl base URL API dari device (jika tersedia shell/diagnostik).
3. Pastikan waktu sistem device benar (NTP), karena selisih waktu ekstrem bisa memicu kegagalan validasi tertentu.
4. Cek service aplikasi device berjalan normal.
5. Reboot device bila perlu, lalu monitor heartbeat 5-10 menit.

### C) Data tidak masuk

**Gejala:** scan berhasil di device, tetapi data absensi tidak muncul di server/dashboard.

**Langkah cepat:**
1. Cek antrean lokal/outbox di device (jika ada), pastikan request terkirim.
2. Cek log API server untuk endpoint absensi: ada request masuk atau tidak.
3. Validasi payload wajib (user ID, waktu, tipe masuk/pulang, device ID) lengkap.
4. Cek timezone device dan server (hindari timestamp bergeser hari).
5. Jalankan sync ulang atau resend data dari device.
6. Jika masih gagal, ambil contoh 1 transaksi (timestamp + user) untuk tracing end-to-end di log.

---

## Catatan Operasional

- Simpan bukti uji (screenshot/log ringkas) saat onboarding sebagai arsip audit.
- Lakukan onboarding di luar jam sibuk bila memungkinkan.
- Setelah go-live, pantau 1-3 hari pertama untuk memastikan stabilitas perangkat.
