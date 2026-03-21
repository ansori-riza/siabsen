# SiAbsen - Progress Report

> Dokumen ini berisi status implementasi SiAbsen (Sistem Absensi Sekolah Berbasis RFID & Fingerprint)
> Tanggal: 21 Maret 2026
> Status: Phase 1 - MVP Development

---

## ✅ APA YANG SUDAH DIKERJAKAN

### 1. Database & Migration (100%)
- ✅ Migration untuk semua tabel:
  - `sekolahs` - Data sekolah
  - `users` - User admin dengan role-based access
  - `gurus` - Data guru (dengan RFID & Fingerprint ID)
  - `murids` - Data murid/siswa (dengan RFID UID)
  - `kelas` - Data kelas
  - `jadwal_sekolahs` - Jadwal masuk/pulang untuk murid & guru
  - `perangkats` - Manajemen device ESP32
  - `absensis` - Record absensi dengan polymorphic (murid/guru)
  - `izins` - Data izin/sakit
  - `audit_logs` - Audit trail perubahan data

- ✅ Database menggunakan SQLite (untuk development)
- ✅ Polymorphic relationships: absensis dan izins bisa mengakses murid atau guru
- ✅ Soft deletes untuk data integrity

### 2. Models (100%)
- ✅ Sekolah - Model dengan relasi ke semua entitas
- ✅ User - Authentication dengan role (super_admin, operator, wali_kelas, kepala_sekolah)
- ✅ Guru - Dengan RFID dan Fingerprint ID
- ✅ Murid - Dengan RFID UID dan data ortu
- ✅ Kelas - Relasi ke wali kelas (guru) dan murid
- ✅ JadwalSekolah - Jadwal terpisah untuk murid dan guru
- ✅ Perangkat - Device management dengan device_key
- ✅ Absensi - Polymorphic, bisa untuk murid atau guru
- ✅ Izin - Perizinan masuk/pulang
- ✅ AuditLog - Immutable audit trail

### 3. Filament Admin Panel (90%)
- ✅ Filament v3.x terinstall
- ✅ AdminPanelProvider terkonfigurasi
- ✅ Resources:
  - SekolahResource - Manajemen data sekolah
  - GuruResource - CRUD guru + enrollment RFID/Fingerprint
  - MuridResource - CRUD murid + enrollment RFID
  - KelasResource - Manajemen kelas
  - JadwalSekolahResource - Pengaturan jadwal
  - PerangkatResource - Manajemen device ESP32
  - AbsensiResource - Monitoring & input manual absensi
- ⚠️ Widget Dashboard (partial) - Statistik sederhana

### 4. API Endpoints (95%)
- ✅ POST `/api/absensi` - Endpoint untuk ESP32 (RFID/Fingerprint)
- ✅ POST `/api/perangkat/heartbeat` - Device keep-alive
- ✅ GET `/api/perangkat/sync` - Sync config ke device
- ✅ Middleware `ValidateDeviceKey` - API key authentication
- ⚠️ Offline buffer belum diimplementasikan di ESP32

### 5. Services (100%)
- ✅ AbsensiService - Logic untuk tentukan status (Hadir/Terlambat/Alpha)
- ✅ Dapat menghitung status otomatis berdasarkan jadwal dan toleransi waktu

### 6. Command & Scheduler (80%)
- ✅ `absensi:create-alpha` - Command untuk generate record Alpha otomatis
- ✅ Schedule di console.php untuk jalan setiap pagi 00:01
- ✅ AdminSeeder - Seed data admin default

### 7. Deployment (70%)
- ✅ GitHub repo: https://github.com/ansoririza/siabsen (Private)
- ✅ Zo Space service terdeploy: https://siabsen-riza.zocomputer.io
- ✅ Database SQLite terkonfigurasi
- ✅ Admin panel bisa diakses (login berfungsi)

---

## ❌ KENDALA & ISSUES

### A. Kendala Coding (Implementasi)

#### 1. Filament Pages (RESOLVED ✅)
**Masalah:** Error `Call to a member function getPages() on null`

**Penyebab:** Filament v3 menggunakan struktur yang berbeda dari v2. Setiap Resource harus punya Pages class terpisah di folder `Pages/`.

**Solusi:** Membuat Pages class untuk setiap resource:
```
SekolahResource/Pages/
  - ListSekolahs.php
  - CreateSekolah.php
  - EditSekolah.php

GuruResource/Pages/
  - ListGurus.php
  - CreateGuru.php
  - EditGuru.php

// ... dst
```

**Status:** ✅ Fixed

---

#### 2. Database Migration Conflict (RESOLVED ✅)
**Masalah:** Kolom `role`, `sekolah_id`, `guru_id` tidak ada di tabel `users`

**Penyebab:** Migration `0001_01_01_000000_create_users_table.php` dari Laravel default tidak punya kolom custom. Migration tambahan tidak jalan sebelum tabel sekolahs dan gurus dibuat.

**Solusi:** Membuat migration baru `add_sekolah_and_role_to_users_table` dengan proper foreign key constraints

**Status:** ✅ Fixed

---

#### 3. Polymorphic Absensi (COMPLEX ✅)
**Masalah:** Bagaimana menyimpan absensi untuk murid DAN guru dalam satu tabel?

**Solusi:** Menggunakan Laravel Polymorphic Relations:
```php
// Migration
$table->unsignedBigInteger('subject_id');
$table->string('subject_type'); // 'App\Models\Murid' atau 'App\Models\Guru'

// Model
public function subject(): MorphTo {
    return $this->morphTo();
}
```

**Status:** ✅ Implemented

---

### B. Kendala Deployment (Server)

#### 1. Asset URL Filament (PERSISTENT ⚠️)
**Masalah:** CSS/JS Filament di-load dari TCP address internal (`http://ts2.zocomputer.io:10950`) bukan dari HTTPS publik (`https://siabsen-riza.zocomputer.io`)

**Dampak:** 
- Browser block mixed content (HTTP di HTTPS page)
- Tampilan admin panel "unstyled" - putih polos tanpa CSS
- Fungsionalitas ada tapi UI rusak

**Percobaan Fix:**
1. ✅ Set `ASSET_URL=https://siabsen-riza.zocomputer.io` di .env
2. ✅ Buat ForceHttps middleware
3. ✅ Modifikasi AppServiceProvider untuk force HTTPS URL
4. ✅ Clear all cache (config, route, view, compiled)

**Hasil:** Semua percobaan gagal. Filament tetap generate URL asset dari internal address.

**Root Cause:** Issue di Filament v3 + PHP built-in server (`php -S`) + Reverse Proxy (Zo Space)

**Workaround:** 
- Clone repo dan jalankan di local dengan `php artisan serve` atau nginx/apache
- Deploy ke shared hosting dengan proper SSL
- Deploy ke VPS dengan domain sendiri

**Status:** ⚠️ Known Issue - Perlu riset lebih lanjut atau bantuan dari Filament community

---

#### 2. GitHub Private Repo Access (RESOLVED ✅)
**Masalah:** Tidak bisa push ke private repo `ansoririza/siabsen` tanpa Personal Access Token (PAT)

**Solusi:** User provide PAT (`REDACTED`) dan berhasil push

**Status:** ✅ Fixed

---

#### 3. Database PostgreSQL vs SQLite (RESOLVED ✅)
**Masalah:** PRD mensyaratkan PostgreSQL tapi Zo Space tidak punya PostgreSQL service yang siap pakai

**Solusi:** Gunakan SQLite untuk development deployment. Production nanti bisa ganti ke MySQL/PostgreSQL via .env

**Status:** ✅ Fixed (SQLite untuk dev, MySQL/PostgreSQL untuk production)

---

## 📋 PHASE 1 CHECKLIST

### Milestone Phase 1 (Updated)
- [x] Stream **Core Absensi Stabil** dipisahkan dari stream adapter vendor
- [x] Stream **Adapter Vendor** ditetapkan sebagai jalur delivery terpisah
- [x] Vendor prioritas pertama: **Solution / ZKTeco ecosystem**
- [x] Deliverable Core: endpoint stabil, auth, audit, dashboard
- [x] Deliverable Adapter: connector, mapping event, retry queue, monitoring
- [x] Acceptance criteria per stream didefinisikan di `docs/PRD.md` (Section X)

### Core Functionality
- [x] Database schema lengkap
- [x] Models dengan relasi
- [x] Filament admin panel
- [x] API untuk ESP32
- [x] Logic absensi (Hadir/Terlambat/Alpha)
- [x] Seeder data default

### Features
- [x] CRUD Sekolah
- [x] CRUD Guru (dengan RFID & Fingerprint)
- [x] CRUD Murid (dengan RFID)
- [x] CRUD Kelas
- [x] Jadwal Sekolah (terpisah murid & guru)
- [x] Manajemen Perangkat (ESP32)
- [x] Monitoring Absensi
- [x] Input Manual Absensi
- [x] Audit Trail

### Deployment
- [x] GitHub repo
- [x] Zo Space service
- [x] Database migrated
- [x] Admin user seeded
- [⚠️] Filament asset URL (issue)

---

## ✅ CHECKLIST OPERASIONAL GO-LIVE

### A. Konfigurasi Wajib Sebelum Go-Live
- [ ] Set tipe institusi (SD/SMP/SMA/SMK atau custom institusi)
- [ ] Set kamus label operasional (hadir, terlambat, izin, sakit, alpha, pulang)
- [ ] Mapping role pengguna (super admin, operator, wali kelas, kepala sekolah)
- [ ] Set jadwal masuk/pulang per role target (guru dan murid)
- [ ] Set perangkat aktif per titik absensi (gerbang, lobby, ruang guru)

### B. Validasi Pra-Go-Live
- [ ] Cek label tampil konsisten di dashboard (widget, statistik, status harian)
- [ ] Cek label dan opsi status di form guru/murid
- [ ] Cek label dan status pada laporan (rekap harian, bulanan, export)
- [ ] Verifikasi role access: user hanya melihat data sesuai hak akses
- [ ] Verifikasi jadwal efektif dipakai pada proses hitung status absensi

### C. UAT (1 Hari Operasional Penuh)
- [ ] Simulasi tap **masuk** pada jam normal dan terlambat
- [ ] Simulasi tap **pulang** untuk guru dan murid
- [ ] Uji koreksi absensi manual oleh operator (ubah status/jam/catatan)
- [ ] Uji export laporan harian setelah jam pulang
- [ ] Cocokkan hasil export dengan data dashboard dan data detail absensi

### D. PIC & SLA Minggu Pertama Go-Live
- [ ] Tetapkan PIC utama operasional (nama + kontak)
- [ ] Tetapkan PIC teknis aplikasi/API/perangkat (nama + kontak)
- [ ] Tetapkan jalur eskalasi (Operator → PIC Operasional → PIC Teknis)
- [ ] SLA respon cepat: maksimal 15 menit sejak tiket diterima
- [ ] SLA perbaikan cepat:
  - [ ] Critical (absensi tidak bisa masuk): maksimal 2 jam
  - [ ] Major (sebagian fitur gagal): maksimal 4 jam
  - [ ] Minor (UI/laporan minor mismatch): maksimal 1x24 jam

### E. Operasional Harian (Wajib Standby)
- [ ] Tetapkan PIC teknis yang standby pada jam masuk dan jam pulang.
- [ ] Catat insiden harian (device offline, data tidak masuk, user error input).
- [ ] Tetapkan SLA respon cepat untuk isu operasional (target maksimal 30 menit).
- [ ] Lakukan review harian singkat dan patch minor bila ditemukan bottleneck.

---

## 🚀 NEXT STEPS (Phase 2)

### ESP32 Hardware (Belum Mulai)
- [ ] Coding firmware ESP32
- [ ] Integrasi RFID reader (RC522)
- [ ] Integrasi Fingerprint sensor (AS608/R307)
- [ ] LCD + Buzzer + LED feedback
- [ ] Offline buffer di SPIFFS
- [ ] Auto-sync saat online

### Phase 2 Features
- [ ] Fingerprint untuk murid
- [ ] Notifikasi WhatsApp ke orang tua
- [ ] Mobile app portal murid/ortu
- [ ] Laporan PDF/Excel
- [ ] Multi-sekolah (tenant)

### Deployment Production
- [ ] Setup VPS/Shared hosting dengan domain
- [ ] SSL certificate (Let's Encrypt)
- [ ] Database PostgreSQL/MySQL
- [ ] Queue worker (Redis/Supervisor)
- [ ] Backup otomatis

---

## 💡 REKOMENDASI

### Untuk Development Lanjut:
1. **Clone repo local:**
   ```bash
   git clone https://github.com/ansoririza/siabsen.git
   cd siabsen
   composer install
   php artisan serve
   ```
   Akses: http://localhost:8000/admin

2. **Setup ESP32:**
   - Install Arduino IDE/PlatformIO
   - Install library: MFRC522, Adafruit_Fingerprint, ESPAsyncWebServer
   - Upload firmware ke ESP32

3. **Testing API:**
   ```bash
   curl -X POST http://localhost:8000/api/absensi \
     -H "Content-Type: application/json" \
     -H "X-Device-Key: your-device-key" \
     -d '{"rfid_uid":"04AB12CD","tipe":"masuk"}'
   ```

### Untuk Production:
- Gunakan shared hosting (Hostinger/Bluehost) atau VPS (DigitalOcean/Linode/AWS)
- Setup domain dengan SSL
- Ganti database ke MySQL/PostgreSQL di .env
- Setup backup harian

---

## 📞 KONTAK & SUPPORT

- **GitHub Issues:** https://github.com/ansoririza/siabsen/issues
- **Zo Space URL:** https://siabsen-riza.zocomputer.io (admin panel dengan UI terbatas)
- **API Base URL:** https://siabsen-riza.zocomputer.io/api/v1

---

## 📝 CATATAN TEKNIS

### Struktur Folder Penting:
```
SiAbsen/
├── app/
│   ├── Filament/Resources/     # Admin panel resources
│   ├── Http/
│   │   ├── Controllers/Api/     # API controllers
│   │   └── Middleware/          # Custom middleware
│   ├── Models/                  # All models
│   └── Services/              # Business logic
├── database/migrations/         # All migrations
├── routes/
│   ├── api.php                # API routes
│   └── web.php                # Web routes
└── .env.example               # Environment template
```

### Environment Variables:
```env
APP_URL=https://siabsen.zo.computer  # Sesuaikan dengan domain
DB_CONNECTION=sqlite                 # Ganti ke mysql/pgsql untuk production
ASSET_URL=${APP_URL}                 # Untuk force HTTPS assets
```

### User Login Default:
- **Email:** admin@siabsen.test
- **Password:** password123

---

*Dokumen ini akan diupdate saat progress berlanjut.*


## 📈 RENCANA KPI BULAN PERTAMA (MINGGU 1-4)

1. **Definisi KPI minimum yang dipantau**
   - **Persentase absensi tercatat otomatis** = (jumlah absensi otomatis ÷ total absensi) × 100%.
   - **Jumlah koreksi manual** per hari/per minggu (edit absensi karena salah status/jam/subjek).
   - **Waktu rekap harian** = durasi dari akhir jam pulang sampai rekap siap diverifikasi operator.

2. **Baseline minggu pertama sebagai pembanding**
   - Minggu pertama dijadikan **baseline resmi** untuk ketiga KPI.
   - Simpan baseline per hari dan rerata mingguan agar tren minggu 2-4 bisa dibandingkan langsung.

3. **Ambang sehat operasional**
   - Persentase absensi otomatis: **>95%** (minimal sehat).
   - Koreksi manual: **<=5% dari total absensi harian** atau turun konsisten dari baseline.
   - Waktu rekap harian: **<=30 menit** setelah jam pulang berakhir.

4. **Jadwal evaluasi mingguan (1 bulan pertama)**
   - Evaluasi setiap akhir minggu pada **Minggu 1, 2, 3, dan 4**.
   - Format evaluasi: ringkas capaian KPI vs baseline, akar masalah utama, dan action item minggu berikutnya.
   - PIC evaluasi: operator + penanggung jawab teknis + kepala sekolah/wakil yang ditunjuk.

---

## 🧾 CATATAN DEPLOYMENT INTERNAL TIM

### 21 Maret 2026 — Verifikasi pasca migrasi (Railway)

- Target langkah:
  1. Buka shell service `siabsen` di Railway.
  2. Jalankan `php artisan migrate --force`.
  3. Jalankan `php artisan db:seed --force`.
  4. Verifikasi endpoint health `/up`.
- Status eksekusi dari environment automation saat ini:
  - ⚠️ **Belum bisa dieksekusi langsung ke Railway** karena sesi ini tidak terhubung ke dashboard/shell Railway service.
  - ⚠️ Validasi lokal juga terblokir dependency: `composer install` gagal pada PHP `8.5.3-dev` karena paket lock (`openspout/openspout v4.28.5`) hanya mendukung `~8.2/~8.3/~8.4`.
- Dampak:
  - Perintah migrasi/seed **belum tervalidasi** pada service Railway dari sesi ini.
  - Endpoint `/up` **belum bisa dikonfirmasi** dari sesi ini sebagai bukti pasca migrasi.
- Tindak lanjut operasional (jalankan di Railway shell service `siabsen`):
  ```bash
  php artisan migrate --force
  php artisan db:seed --force
  curl -i "$APP_URL/up"
  ```
  Kriteria lulus: respons HTTP `200` pada `/up` setelah dua perintah artisan selesai tanpa error.
