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
- [ ] **Railway (service `web`)**: pastikan environment variable `NIXPACKS_NODE_VERSION=22` sudah diset agar fase build memakai Node.js 22 dan tidak memunculkan warning kompatibilitas Vite.
- [ ] Jalankan `composer install --no-dev --optimize-autoloader`.
- [ ] Jalankan `npm ci && npm run build`.
- [ ] Jalankan `php artisan migrate --force`.
- [ ] Jalankan `php artisan db:seed --force`.
- [ ] Jalankan optimasi cache Laravel (`php artisan optimize`, `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache` sesuai kebutuhan aplikasi).
- [ ] Restart service aplikasi (PHP-FPM, web server, queue worker, dan process manager terkait) sesuai standar environment.
- [ ] Verifikasi log aplikasi dan log web server tidak menunjukkan error kritikal pasca restart.

### Catatan Deployment Railway

Untuk mencegah kegagalan build front-end di Railway:

1. Buka project **SiAbsen** → service **`web`**.
2. Masuk ke tab **Variables**.
3. Tambahkan variabel:
   - **Key**: `NIXPACKS_NODE_VERSION`
   - **Value**: `22`
4. Simpan, lalu trigger **Redeploy**.
5. Verifikasi di log fase `npm run build` bahwa pesan berikut **tidak muncul lagi**:
   - `Vite requires Node.js version 20.19+ or 22+`

## Post-Deploy Smoke Test

- [ ] Uji health API (endpoint health-check merespons normal).
- [ ] Uji login admin (autentikasi berhasil, role/permission sesuai).
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
