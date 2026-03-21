# Deployment Checklist Phase 1

Dokumen ini menjadi checklist operasional implementasi lapangan untuk deployment Phase 1.

## Pre-Deploy (H-1)

- [ ] Pastikan backup database terbaru sudah dibuat dan dapat direstore (uji restore singkat di environment non-produksi).
- [ ] Pastikan backup file penting (storage, konfigurasi, dokumen lampiran jika ada) sudah dibuat.
- [ ] Validasi file `.env` produksi (APP_ENV, APP_DEBUG, APP_KEY, DB, CACHE, QUEUE, MAIL, dan variabel integrasi pihak ketiga).
- [ ] Pastikan sertifikat HTTPS aktif, valid, dan tidak mendekati masa kedaluwarsa.
- [ ] Pastikan konfigurasi cron scheduler aktif untuk menjalankan `php artisan schedule:run` setiap menit.
- [ ] Pastikan PIC teknis, PIC bisnis, dan jalur eskalasi insiden sudah dikonfirmasi sebelum go-live.

## Deployment Steps (Hari H)

- [ ] Masuk ke server produksi dengan user/deployment account yang sesuai.
- [ ] Jalankan `git pull` pada branch/tag rilis yang sudah disetujui.
- [ ] Verifikasi `railway.json` pada branch rilis: `deploy.startCommand` saat ini fokus pada cache/warmup + `php artisan serve` dan **belum** menjalankan `php artisan migrate --force` otomatis.
- [ ] **Railway (service `siabsen`)**: pastikan environment variable `RAILWAY_DOCKERFILE_PATH=Dockerfile` sudah diset dan variabel khusus Railpack/Nixpacks yang tidak dipakai sudah dihapus agar konfigurasi build tidak rancu.
- [ ] **Railway (APP_URL)**: jika `RAILWAY_STATIC_URL` tidak tersedia, isi `APP_URL` secara eksplisit dengan domain aktif service (`https://<service>.up.railway.app`) agar URL absolut, redirect, dan cookie tetap konsisten.
- [ ] Jalankan `composer install --no-dev --optimize-autoloader`.
- [ ] Jalankan `npm ci && npm run build`.
- [ ] Jalankan `php artisan migrate --force`.
- [ ] Jalankan verifikasi cepat endpoint health setelah migrasi: `GET /up` harus `200 OK`.
- [ ] Jalankan verifikasi login panel admin setelah migrasi (pastikan autentikasi berhasil dan dashboard termuat normal).
- [ ] Jalankan `php artisan db:seed --force` **hanya** sesuai kebijakan seed (lihat bagian "Kebijakan Seed Produksi").
- [ ] Jalankan optimasi cache Laravel (`php artisan optimize`, `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache` sesuai kebutuhan aplikasi).
- [ ] Restart service aplikasi (PHP-FPM, web server, queue worker, dan process manager terkait) sesuai standar environment.
- [ ] Verifikasi log aplikasi dan log web server tidak menunjukkan error kritikal pasca restart.

## Kebijakan Seed Produksi

Untuk mencegah data duplikat, seed **tidak** dijalankan otomatis pada setiap deploy.

- **First deploy / inisialisasi environment baru**: jalankan `php artisan db:seed --force` untuk data baseline (contoh: admin awal, data referensi statis).
- **Deploy rutin (harian/per release)**: **jangan** jalankan seed global. Jalankan hanya jika ada kebutuhan data baru yang terkontrol.
- Jika butuh data tambahan saat deploy rutin, gunakan seeder idempotent (mis. `updateOrCreate`) dan eksekusi seeder spesifik:
  - `php artisan db:seed --class=NamaSeeder --force`
- Dokumentasikan setiap eksekusi seed di log rilis/deployment notes (siapa, kapan, seeder apa, dampaknya).

### Catatan Deployment Railway (Dockerfile)

Untuk memastikan Railway membangun image dari `Dockerfile` repository:

1. Buka project **SiAbsen** → service **`siabsen`**.
2. Masuk ke tab **Variables**.
3. Tambahkan/ubah variabel berikut:
   - **Key**: `RAILWAY_DOCKERFILE_PATH`
   - **Value**: `Dockerfile`
   - **Key**: `APP_URL`
   - **Value**: `https://<service>.up.railway.app` (gunakan domain aktif service `siabsen`; jangan kosong)
4. Hapus variabel lama yang khusus untuk Railpack/Nixpacks (contoh `NIXPACKS_NODE_VERSION`, `NPM_CONFIG_OPTIONAL`) jika sudah tidak digunakan.
5. Simpan perubahan, lalu trigger **Redeploy** service `siabsen`.
6. Verifikasi build log menampilkan instalasi extension PHP dari Dockerfile, terutama baris `docker-php-ext-install intl zip ...`.
7. Simpan hasil verifikasi ke bukti operasional (contoh: `docs/evidence/railway-dockerfile-redeploy-2026-03-21.md`).

## Post-Deploy Smoke Test

- [ ] Uji endpoint health `/up` (harus merespons normal / `200 OK`).
- [ ] Uji login admin panel (autentikasi berhasil, role/permission sesuai).
- [ ] Uji CRUD master data utama (create, read, update, delete) untuk memastikan integritas modul inti.
- [ ] Uji API device: heartbeat dari device diterima sistem.
- [ ] Uji API device: sinkronisasi (sync) data/konfigurasi device berjalan normal.
- [ ] Uji API device: kirim data absensi dari device dan pastikan data tersimpan.
- [ ] Verifikasi hasil perhitungan status kehadiran (hadir/terlambat) sesuai aturan jam kerja yang berlaku.

## Hardware UAT

- [ ] Uji skenario jam masuk dari perangkat fisik dan pastikan tercatat benar.
- [ ] Uji skenario jam pulang dari perangkat fisik dan pastikan tercatat benar.
- [ ] Uji skenario invalid credential (RFID/fingerprint tidak valid) dan pastikan sistem menolak dengan benar.
- [ ] Uji skenario jaringan putus (offline/intermittent), lalu pastikan mekanisme retry/sinkronisasi ulang berjalan.
- [ ] Dokumentasikan hasil UAT perangkat beserta bukti uji (log, screenshot, timestamp).

## Rollback Plan

- [ ] Definisikan trigger rollback (mis. error kritikal, downtime > SLA, data inconsistency mayor) dan pastikan disetujui PIC.
- [ ] Hentikan sementara trafik/tulisan data jika diperlukan untuk mencegah inkonsistensi lanjutan.
- [ ] Restore database dari backup terakhir yang tervalidasi.
- [ ] Rollback code ke commit/tag rilis stabil sebelumnya.
- [ ] Jalankan ulang langkah verifikasi minimum (health API, login, fungsi inti, dan integrasi device).
- [ ] Komunikasikan status rollback ke stakeholder dan catat insiden untuk post-mortem.

## Hypercare Minggu Pertama

- [ ] Tetapkan PIC standby harian (teknis dan operasional) selama minggu pertama pasca go-live.
- [ ] Tetapkan SLA penanganan insiden (respon awal, mitigasi, dan resolusi) dan bagikan ke tim terkait.
- [ ] Lakukan review harian (incident review, bug backlog, performa sistem, dan umpan balik pengguna).
- [ ] Pantau metrik utama (error rate, response time, job queue, sinkronisasi device, dan keberhasilan absensi).
- [ ] Susun ringkasan status harian untuk manajemen selama periode hypercare.
