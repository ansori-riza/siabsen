# SiAbsen — Sistem Absensi Sekolah/Pondok (RFID + Fingerprint)

SiAbsen adalah aplikasi absensi sekolah/pondok berbasis Laravel untuk mencatat kehadiran murid dan guru secara digital melalui perangkat hardware (RFID + fingerprint), dengan monitoring terpusat lewat admin panel.

## Fitur Utama Phase 1

- **Admin panel (Filament)** untuk manajemen master data sekolah, guru, murid, kelas, jadwal, perangkat, dan absensi.
- **API device** untuk menerima event absensi dari perangkat (ESP32/vendor adapter) dengan autentikasi `X-Device-Key`.
- **Manajemen jadwal** masuk/pulang murid & guru beserta toleransi keterlambatan.
- **Monitoring absensi** real-time + input/manual correction dengan jejak audit.
- **Role user** berbasis kebutuhan operasional sekolah (`super_admin`, `operator`, `wali_kelas`, `kepala_sekolah`).

## Arsitektur Singkat

Alur sistem inti:

1. **Aplikasi Laravel (core platform)**
   - Menyediakan panel admin, API, logic status kehadiran, laporan, dan scheduler.
2. **Perangkat absensi**
   - ESP32 custom atau device vendor via **middleware adapter** untuk standarisasi payload/event.
3. **Database**
   - Menyimpan data master, jadwal, event absensi, izin, perangkat, dan audit log.

Skema sederhana:

`Device (RFID/Fingerprint) -> API Laravel -> Service/Rule Engine -> Database -> Dashboard/Reporting`

## Dokumentasi Terkait

- [Product Requirement Document (PRD)](docs/PRD.md)
- [Progress Implementasi](docs/PROGRES.md)
- [API Hardware](docs/API_HARDWARE.md)
- [Hardware Compatibility](docs/HARDWARE_COMPATIBILITY.md)
- [Runbook Hardware Onboarding](docs/RUNBOOK_HARDWARE_ONBOARDING.md)
- [Deployment Checklist Phase 1](docs/DEPLOYMENT_PHASE1.md)

## Quick Start (Local Development)

> Contoh berikut asumsi environment lokal Linux/macOS + PHP + Composer + Node.js tersedia.

1. **Install dependency**

   ```bash
   composer install
   npm install
   ```

2. **Setup `.env`**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Migrasi + seed data awal**

   ```bash
   php artisan migrate --seed
   ```

4. **Build asset + jalankan app**

   ```bash
   npm run dev
   php artisan serve
   ```

5. **(Opsional) Jalankan queue worker & scheduler loop di lokal**

   ```bash
   php artisan queue:work
   php artisan schedule:work
   ```

## Konfigurasi Penting

Pastikan variabel berikut benar sebelum onboarding device atau go-live:

- **APP URL**
  - `APP_URL` harus sesuai domain/aplikasi yang diakses user.
  - Untuk deploy Railway, isi `APP_URL` secara **eksplisit** dengan domain aktif service, mis. `https://<service>.up.railway.app`, khususnya saat variabel otomatis seperti `RAILWAY_STATIC_URL` tidak tersedia.
  - Jika `APP_URL` salah, dampaknya bisa meluas: redirect login/logout melenceng, callback autentikasi gagal, domain/scope cookie tidak cocok (session sering logout), dan URL asset (CSS/JS/admin panel) bisa mengarah ke host yang salah.
- **Database**
  - `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
- **Timezone**
  - Set `APP_TIMEZONE` (dan timezone server) agar status hadir/terlambat akurat.
- **Queue & Scheduler**
  - Tentukan `QUEUE_CONNECTION`.
  - Pastikan worker queue aktif untuk job background.
  - Pastikan scheduler aktif untuk task terjadwal (termasuk proses otomatis terkait absensi).

## API Ringkas untuk Hardware

Base path API: `/api`

- **POST** `/api/absensi`
  - Header wajib: `X-Device-Key: <device_key>`
  - Payload minimal:
    - `tipe`: `masuk` / `pulang`
    - salah satu identitas: `rfid_uid` **atau** `fingerprint_id`
- **POST** `/api/perangkat/heartbeat`
  - Update status/last seen perangkat.
- **GET** `/api/perangkat/sync`
  - Sinkronisasi konfigurasi/data referensi ke perangkat.

Lihat kontrak lengkap, contoh request/response, dan retry strategy di [docs/API_HARDWARE.md](docs/API_HARDWARE.md).

## Role & Akses

Role operasional saat ini:

- **super_admin**: kontrol penuh sistem, pengaturan global, dan user management.
- **operator**: operasional harian (master data, perangkat, monitoring, koreksi data sesuai kebijakan).
- **wali_kelas**: pantau absensi kelas binaan, verifikasi/koreksi terbatas, input izin.
- **kepala_sekolah**: akses dashboard dan laporan monitoring tingkat sekolah.

## Status Implementasi

Ringkasan posisi saat ini:

- **Phase 1 (MVP) — berjalan/aktif dikembangkan**
  - Fokus: core absensi stabil, admin panel, API device, role-based access, audit, monitoring.
  - Integrasi device custom (ESP32) dan jalur adapter vendor diposisikan terpisah agar core tetap stabil.
- **Roadmap berikutnya (Phase 2+)**
  - Penguatan kanal notifikasi, portal/mobile experience, perluasan skema verifikasi, dan integrasi lanjutan sesuai PRD.

Untuk detail progres terbaru per modul, issue aktif, checklist, dan next steps, lihat [docs/PROGRES.md](docs/PROGRES.md) dan [docs/PRD.md](docs/PRD.md).

## Troubleshooting Umum

### 1) Asset admin panel tidak load / tampilan berantakan

- Verifikasi `APP_URL` dan skema HTTPS/HTTP konsisten.
- Bersihkan cache aplikasi:

  ```bash
  php artisan optimize:clear
  ```

- Build ulang asset frontend:

  ```bash
  npm run build
  ```

- Cek reverse proxy/web server agar tidak mengubah host/protocol internal secara salah.

### 2) Auth device gagal (`401` / `Missing X-Device-Key`)

- Pastikan header `X-Device-Key` dikirim di setiap request device.
- Validasi `device_key` aktif dan cocok dengan data perangkat.
- Cek environment API target (jangan tertukar antara local/staging/production).

### 3) Scheduler tidak jalan

- Pastikan scheduler process aktif (`php artisan schedule:work`) **atau** cron server menjalankan `php artisan schedule:run` setiap menit.
- Pastikan timezone server sesuai konfigurasi aplikasi.
- Cek log aplikasi untuk command yang gagal.

## Catatan Operasional Deploy Produksi

- `railway.json` saat ini belum memasukkan migrate otomatis pada `deploy.startCommand`; migrasi tetap dijalankan sebagai langkah operasional terpisah.
- Urutan minimum setelah release:
  1. `php artisan migrate --force`
  2. Verifikasi `GET /up` mengembalikan `200 OK`
  3. Verifikasi login panel admin berhasil
  4. Jalankan seed bila diperlukan sesuai kebijakan
- Kebijakan seed:
  - **First deploy**: boleh menjalankan `php artisan db:seed --force` untuk bootstrap data awal.
  - **Deploy rutin**: hindari seed global untuk mencegah duplikasi; gunakan seeder idempotent/seeder spesifik bila ada kebutuhan data baru.

## Kontribusi Tim

### Branching Standard

- `main` -> branch stabil/produksi.
- Gunakan branch fitur/perbaikan dari `main` dengan pola:
  - `feature/<nama-fitur>`
  - `fix/<nama-bug>`
  - `chore/<nama-pekerjaan>`

Contoh:
- `feature/api-device-heartbeat`
- `fix/asset-url-filament`

### Commit Naming Convention

Gunakan gaya commit konsisten (disarankan Conventional Commits):

- `feat: tambah endpoint heartbeat perangkat`
- `fix: validasi header x-device-key`
- `docs: update runbook onboarding hardware`
- `chore: rapikan konfigurasi scheduler`

### Alur Pull Request (PR)

1. Buat branch dari `main`.
2. Implementasi perubahan + update dokumentasi terkait.
3. Jalankan pengujian/check lokal.
4. Push branch dan buka PR.
5. Isi deskripsi PR minimal: tujuan, scope, cara uji, risiko.
6. Minta review minimal 1 reviewer tim.
7. Merge setelah approval dan seluruh check wajib lulus.
