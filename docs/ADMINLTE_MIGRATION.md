# Migrasi AdminLTE - Dokumentasi Perubahan

> Tanggal: 22 Maret 2026
> Perubahan: Filament → AdminLTE + Bootstrap
> Alasan: Issue asset URL Filament di deployment

---

## Ringkasan Perubahan

### Sebelumnya (Filament)
- Admin panel pakai Filament v3
- UI modern tapi asset tidak load di deployment
- Issue mixed content (HTTP vs HTTPS)
- Kompleksitas Livewire + Alpine.js

### Sekarang (AdminLTE)
- Admin panel pakai AdminLTE 3 + Bootstrap 4
- Asset load via CDN (reliabel)
- Simpler stack: Laravel + Blade + Bootstrap
- No Livewire dependency untuk admin panel

---

## Arsitektur Baru

```
[ESP32/Vendor Device] → [API Laravel] → [AdminLTE Dashboard]
                              ↓
                    [Database SQLite/MySQL]
```

**Komponen:**
- **Backend:** Laravel 12.x
- **Frontend Admin:** AdminLTE 3 (Bootstrap 4, jQuery)
- **Database:** SQLite (dev) / MySQL (production)
- **Template Engine:** Blade
- **CSS Framework:** Bootstrap 4

---

## Status Implementasi AdminLTE

### ✅ Sudah Jadi

#### 1. Layout & UI
- [x] AdminLTE layout dengan sidebar
- [x] Sidebar navigation menu lengkap:
  - Dashboard
  - Master Data (Guru, Murid, Kelas)
  - Absensi (Monitoring)
  - Pengaturan (Jadwal, Perangkat)
- [x] Header dengan user menu
- [x] Breadcrumb navigation
- [x] Footer

#### 2. Controllers
- [x] DashboardController - statistik dashboard
- [x] GuruController - CRUD lengkap
- [x] MuridController - CRUD lengkap
- [x] KelasController - CRUD lengkap
- [x] JadwalSekolahController - CRUD
- [x] PerangkatController - CRUD
- [x] AbsensiController - monitoring & input

#### 3. Views (Blade)
- [x] Layout: adminlte.blade.php
- [x] Dashboard: index.blade.php
- [x] Guru: index, create, edit
- [x] Murid: index, create, edit
- [x] Kelas: index, create, edit
- [x] Jadwal Sekolah: index, create, edit
- [x] Perangkat: index, create, edit
- [x] Absensi: index, create, edit

#### 4. Authentication
- [x] Custom login page (AdminLTE styled)
- [x] Login: admin@siabsen.com / password
- [x] Logout functionality
- [x] Auth middleware untuk proteksi route

### ⚠️ Partial/Basic

#### 1. API Endpoints
- [x] Routes terdefinisi di api.php
- [ ] Implementasi logic absensi dari device (WIP)
- [ ] Device key authentication middleware (WIP)

#### 2. Fingerprint & RFID
- [x] Model punya field rfid_uid dan fingerprint_id
- [ ] UI enrollment fingerprint (belum ada)
- [ ] UI enrollment RFID via web (belum ada)

### ❌ Belum Jadi

#### 1. API Device (Kritis)
- [ ] Endpoint POST /api/absensi lengkap
- [ ] Middleware X-Device-Key validation
- [ ] Logic status otomatis (Hadir/Terlambat/Alpha)
- [ ] Response format untuk LCD/buzzer/LED

#### 2. Hardware Integration
- [ ] ESP32 firmware
- [ ] RFID reader integration
- [ ] Fingerprint sensor integration
- [ ] Offline buffer SPIFFS

#### 3. Advanced Features
- [ ] Audit trail system (model ada, UI belum)
- [ ] Import Excel massal
- [ ] Export PDF dengan logo
- [ ] Scheduler otomatis (positive attendance)
- [ ] Soft delete (model ada, UI belum)
- [ ] Notifikasi (WhatsApp/email)

---

## Menu Navigation (AdminLTE)

```
📊 Dashboard
   └── Statistik: Guru, Murid, Kelas, Absensi

📁 Master Data
   ├── 👨‍🏫 Guru
   │   ├── Daftar Guru (tabel + pagination)
   │   ├── Tambah Guru (form lengkap)
   │   └── Edit Guru
   ├── 👨‍🎓 Data Murid
   │   ├── Daftar Murid
   │   ├── Tambah Murid
   │   └── Edit Murid
   └── 🏫 Data Kelas
       ├── Daftar Kelas
       ├── Tambah Kelas
       └── Edit Kelas

📋 Absensi
   └── 🔍 Monitoring Absensi
       ├── Filter tanggal & kelas
       ├── Tabel absensi dengan status badge
       └── Input manual (create, edit)

⚙️ Pengaturan
   ├── 📅 Jadwal Sekolah
   │   ├── Jadwal Murid
   │   └── Jadwal Guru
   └── 🔌 Perangkat
       ├── Daftar Perangkat ESP32
       ├── Tambah Perangkat
       └── Edit Perangkat

👤 Administrator (top right)
   ├── Profile
   └── Logout
```

---

## Cara Akses

### URL
- **Login:** https://siabsen-riza.zocomputer.io/login
- **Dashboard:** https://siabsen-riza.zocomputer.io/admin

### Default Login
- **Email:** admin@siabsen.com
- **Password:** password

---

## Perbandingan Fitur

| Fitur | Filament (Sebelumnya) | AdminLTE (Sekarang) | Status |
|-------|----------------------|---------------------|--------|
| Dashboard Widget | ✅ | ✅ | Jalan |
| CRUD Guru | ✅ | ✅ | Jalan |
| CRUD Murid | ✅ | ✅ | Jalan |
| CRUD Kelas | ✅ | ✅ | Jalan |
| CRUD Jadwal | ✅ | ✅ | Jalan |
| CRUD Perangkat | ✅ | ✅ | Jalan |
| Monitoring Absensi | ✅ | ✅ | Jalan |
| Input Manual Absensi | ✅ | ✅ | Jalan |
| UI Responsive | ✅ Modern | ✅ Classic | Beda style |
| Asset Loading | ⚠️ Issue | ✅ OK | Fixed |
| Fingerprint UI | ✅ | ❌ | Belum ada |
| RFID Enrollment | ✅ | ❌ | Belum ada |
| Audit Trail UI | ✅ | ❌ | Belum ada |
| API Device | ✅ | ⚠️ Route only | Logic WIP |

---

## Next Steps

### Prioritas Tinggi (Untuk Production)
1. **API Device Endpoint** - supaya ESP32 bisa kirim data
2. **Fingerprint Enrollment UI** - daftar jari guru
3. **RFID Enrollment UI** - scan kartu dari web

### Prioritas Menengah
4. Audit Trail UI
5. Import Excel massal
6. Export PDF dengan logo

### Prioritas Rendah (Phase 2)
7. Notifikasi WhatsApp
8. ESP32 firmware development
9. Mobile app

---

## Troubleshooting

### 1. Dashboard blank/putih
```bash
php artisan optimize:clear
php artisan view:clear
```

### 2. Login error
- Cek APP_URL di .env sesuai domain
- Cek database SQLite tersedia

### 3. Sidebar tidak collapse
- Pastikan jQuery dan Bootstrap JS load dari CDN
- Cek browser console untuk error JS

---

## Keunggulan AdminLTE

1. **Stabil** - Asset dari CDN, tidak depend force URL
2. **Simpler** - No Livewire, no Alpine.js complexity
3. **Familiar** - Bootstrap 4 banyak yang ngerti
4. **Fast** - Render server-side, no JS hydration delay

## Keterbatasan

1. **Less Modern** - UI classic, tidak reactive real-time
2. **No SPA** - Full page refresh setiap navigasi
3. **Manual Update** - Perlu F5 untuk lihat data baru

---

## Dokumentasi Terkait

- [Product Requirement Document (PRD)](PRD.md) - Spesifikasi lengkap
- [Progress Report (PROGRES.md)](PROGRES.md) - Status sebelumnya (Filament)
- [API Hardware](API_HARDWARE.md) - Kontrak API untuk ESP32

---

*Update terakhir: 22 Maret 2026*
*Versi: 1.0 (AdminLTE Migration)*
