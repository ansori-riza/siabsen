# SiAbsen — Sistem Absensi Sekolah/Pondok (RFID + Fingerprint)

SiAbsen adalah aplikasi absensi sekolah/pondok berbasis Laravel untuk mencatat kehadiran murid dan guru secara digital melalui perangkat hardware (RFID + fingerprint), dengan monitoring terpusat lewat admin panel.

> **Update:** Admin panel sekarang menggunakan **AdminLTE** (Bootstrap) setelah migrasi dari Filament karena issue asset URL di deployment.
> Dokumentasi migrasi lengkap: [docs/ADMINLTE_MIGRATION.md](docs/ADMINLTE_MIGRATION.md)

## Fitur Utama Phase 1

- **Admin panel (AdminLTE)** untuk manajemen master data sekolah, guru, murid, kelas, jadwal, perangkat, dan absensi.
- **API device** untuk menerima event absensi dari perangkat (ESP32/vendor adapter) dengan autentikasi `X-Device-Key`.
- **Manajemen jadwal** masuk/pulang murid & guru beserta toleransi keterlambatan.
- **Monitoring absensi** real-time + input/manual correction.
- **Role user** berbasis kebutuhan operasional sekolah (`super_admin`, `operator`, `wali_kelas`, `kepala_sekolah`).

## Arsitektur Singkat

Alur sistem inti:

1. **Aplikasi Laravel (core platform)**
   - Menyediakan panel admin, API, logic status kehadiran, laporan.
   - Stack: Laravel 12.x + AdminLTE 3 + Bootstrap 4 + Blade
2. **Perangkat absensi**
   - ESP32 custom atau device vendor via **middleware adapter** untuk standarisasi payload/event.
3. **Database**
   - Menyimpan data master, jadwal, event absensi, perangkat.

Skema sederhana:

`Device (RFID/Fingerprint) -> API Laravel -> Database -> AdminLTE Dashboard`

## Dokumentasi Terkait

- **[Product Requirement Document (PRD)](docs/PRD.md)** - Spesifikasi lengkap produk
- **[Migration Report (ADMINLTE_MIGRATION.md)](docs/ADMINLTE_MIGRATION.md)** - Dokumentasi migrasi Filament → AdminLTE
- **[API Hardware](docs/API_HARDWARE.md)** - Kontrak API untuk perangkat
- **[Hardware Compatibility](docs/HARDWARE_COMPATIBILITY.md)** - Daftar hardware yang support
- **[Runbook Hardware Onboarding](docs/RUNBOOK_HARDWARE_ONBOARDING.md)** - Panduan onboarding perangkat
- **[Deployment Checklist Phase 1](docs/DEPLOYMENT_PHASE1.md)** - Checklist deployment

## Quick Start (Local Development)

### 1. Install dependency

```bash
composer install
```

### 2. Setup `.env`

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Migrasi + seed data awal

```bash
php artisan migrate --seed
```

### 4. Jalankan aplikasi

```bash
php artisan serve
```

Akses admin panel: http://localhost:8000/login

### 5. Login default

- **Email:** admin@siabsen.com
- **Password:** password

## Konfigurasi Penting

Pastikan variabel berikut benar:

- **APP_URL**
  - `APP_URL` harus sesuai domain yang diakses user.
  - Contoh: `https://siabsen-riza.zocomputer.io`
- **Database**
  - `DB_CONNECTION=sqlite` (development)
  - Production: ganti ke `mysql` atau `pgsql`
- **Timezone**
  - Set `APP_TIMEZONE=Asia/Jakarta`

## API Ringkas untuk Hardware

Base path API: `/api`

### POST /api/absensi
- Header wajib: `X-Device-Key: <device_key>`
- Payload minimal:
  - `tipe`: `masuk` / `pulang`
  - `rfid_uid` atau `fingerprint_id`

### POST /api/perangkat/heartbeat
- Update status/last seen perangkat.

### GET /api/perangkat/sync
- Sinkronisasi konfigurasi ke perangkat.

Lihat kontrak lengkap di [docs/API_HARDWARE.md](docs/API_HARDWARE.md).

## Role & Akses

Role operasional:

- **super_admin**: kontrol penuh sistem, pengaturan global, user management.
- **operator**: operasional harian (master data, monitoring, koreksi).
- **wali_kelas**: pantau absensi kelas, verifikasi terbatas.
- **kepala_sekolah**: dashboard dan laporan monitoring.

## Status Implementasi

### AdminLTE Panel (Aktif)
- ✅ Dashboard monitoring
- ✅ CRUD Master Data (Sekolah, Guru, Murid, Kelas)
- ✅ Jadwal Sekolah
- ✅ Manajemen Perangkat
- ✅ Monitoring Absensi + Input Manual
- ✅ Authentication system

### API Device (WIP)
- ⚠️ Routes terdefinisi, logic implementasi ongoing

### Phase 2+ (Roadmap)
- 🔵 Fingerprint enrollment UI
- 🔵 RFID enrollment via web
- 🔵 Notifikasi WhatsApp
- 🔵 Mobile app portal

## Troubleshooting Umum

### 1. Asset tidak load / tampilan berantakan
```bash
php artisan optimize:clear
```

### 2. Auth device gagal (401)
Pastikan header `X-Device-Key` dikirim di setiap request.

### 3. Database error
```bash
php artisan migrate:fresh --seed
```

## Deployment Produksi

1. `php artisan migrate --force`
2. Verifikasi login panel admin
3. Setup backup harian

## Kontribusi Tim

### Branching Standard
- `main` → branch stabil/produksi
- `feature/<nama-fitur>` → fitur baru
- `fix/<nama-bug>` → perbaikan bug

### Commit Convention
- `feat: tambah endpoint baru`
- `fix: validasi input`
- `docs: update dokumentasi`

## Kontak & Support

- **Zo Space URL:** https://siabsen-riza.zocomputer.io
- **API Base URL:** https://siabsen-riza.zocomputer.io/api

---

**Last Updated:** 22 Maret 2026  
**Versi:** 1.0 (AdminLTE Migration)  
**Stack:** Laravel 12.x + AdminLTE 3 + Bootstrap 4