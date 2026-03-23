# Middleware Architecture untuk Device Komersial

## Status Implementasi

**Last Updated:** 23 Maret 2026
**Phase:** Phase 1 - Adapter Stream

---

## Ringkasan

SiAbsen mendukung dua jenis perangkat:

1. **ESP32 Custom** - Hardware custom dengan firmware sendiri
2. **Device Komersial** - Solution, ZKTeco, Hikvision, dll

Untuk device komersial, diperlukan middleware/adapter yang bertindak sebagai jembatan antara protokol vendor dan format internal SiAbsen.

---

## Arsitektur High-Level

```
┌─────────────────┐
│  Device ESP32   │──────────┐
│  (Custom)       │          │
└─────────────────┘          │
                             │
┌─────────────────┐         │    ┌──────────────────┐
│  Device ZKTeco  │────┐     │    │                  │
└─────────────────┘    │     │    │   SiAbsen API    │
                       │     ├───▶│   /api/absensi   │
┌─────────────────┐    │     │    │                  │
│  Device Solution│────┤     │    └──────────────────┘
└─────────────────┘    │     │
                       │     │
┌─────────────────┐    │     │
│  Device Hikvision──┘     │
└─────────────────┘          │
                             │
                    ┌────────┴────────┐
                    │   Middleware/   │
                    │   Adapter       │
                    │   (Phase 1)     │
                    └─────────────────┘
```

---

## Tabel Perangkat

### Database Schema

```php
// Perangkat Model
protected $fillable = [
    'sekolah_id',
    'kelas_id',
    'nama',
    'lokasi',
    'device_key',      // Unique key untuk autentikasi
    'tipe',            // gerbang | kelas
    'vendor_type',     // esp32 | solution | zkteco | hikvision | other
    'status',          // online | offline | maintenance
    'last_ping',
    'is_active',
];
```

### Vendor Types

| Vendor | Label | Middleware Status | Priority |
|--------|-------|-------------------|----------|
| `esp32` | ESP32 Custom | ❌ Belum ada | - |
| `solution` | Solution | ❌ Perlu dikembangkan | **HIGH** |
| `zkteco` | ZKTeco | ❌ Perlu dikembangkan | **HIGH** |
| `hikvision` | Hikvision | ❌ Perlu dikembangkan | MEDIUM |
| `other` | Lainnya | ❌ Perlu dikembangkan | LOW |

---

## Middleware Requirements (Phase 1 Adapter Stream)

### 1. Connector untuk Solution/ZKTeco

**Tujuan:**
- Terhubung ke perangkat Solution/ZKTeco via network (TCP/IP)
- Mengambil event kehadiran (RFID tap, fingerprint)
- Normalisasi event ke format internal SiAbsen

**Deliverables:**
- [ ] Connector Solution
- [ ] Connector ZKTeco
- [ ] Mapping event → internal format
- [ ] Retry queue untuk event gagal
- [ ] Monitoring adapter

### 2. Event Format Mapping

**Vendor Event (contoh ZKTeco):**
```json
{
  "device_id": "192.168.1.100",
  "event_type": "RFID_TAP",
  "timestamp": "2026-03-23T08:30:00Z",
  "user_id": "12345",
  "card_number": "04AB12CD"
}
```

**Internal Format (SiAbsen):**
```json
{
  "device_key": "DEV-69C09530930C9",
  "rfid_uid": "04AB12CD",
  "tipe": "masuk",
  "timestamp": "2026-03-23T08:30:00Z"
}
```

### 3. Retry Queue

**Flow:**
1. Event masuk dari device
2. Coba kirim ke API SiAbsen
3. Jika gagal → masuk queue
4. Retry dengan exponential backoff (1s, 2s, 4s, 8s, 16s, 32s, 60s)
5. Max retry: 10x
6. Setelah 10x gagal → tandai sebagai "permanent failure" untuk investigasi

### 4. Monitoring

**Metrics yang perlu dipantau:**
- Status koneksi device (online/offline)
- Jumlah event berhasil diproses
- Jumlah event gagal
- Retry count
- Queue depth
- Last error message

---

## Implementation Roadmap

### Week 1-2: Research & Design
- [ ] Dokumentasi protokol Solution
- [ ] Dokumentasi protokol ZKTeco
- [ ] Desain arsitektur middleware
- [ ] Definisi API contract

### Week 3-4: Development
- [ ] Implementasi Solution connector
- [ ] Implementasi ZKTeco connector
- [ ] Event mapping layer
- [ ] Retry queue system

### Week 5: Testing
- [ ] Unit tests
- [ ] Integration tests dengan device real
- [ ] Load testing

### Week 6: Deployment
- [ ] Deploy ke staging
- [ ] Monitoring setup
- [ ] Documentation

---

## Acceptance Criteria

### A. Core Requirements
- [ ] Connector dapat terhubung ke minimal 1 perangkat uji
- [ ] Event dapat dikonversi ke format internal tanpa kehilangan field wajib
- [ ] Retry queue berjalan dengan exponential backoff
- [ ] Monitoring menampilkan status koneksi dan error rate

### B. Non-Functional Requirements
- Response time < 500ms untuk RFID event
- Response time < 700ms untuk fingerprint event
- 99.5% uptime untuk adapter service
- Offline buffer untuk minimal 500 events

---

## Directory Structure (Proposed)

```
SiAbsen/
├── app/
│   ├── Services/
│   │   ├── Adapters/
│   │   │   ├── AdapterInterface.php
│   │   │   ├── SolutionAdapter.php
│   │   │   ├── ZKTecoAdapter.php
│   │   │   └── HikvisionAdapter.php
│   │   └── EventProcessor.php
│   └── Jobs/
│       ├── ProcessAttendanceEvent.php
│       └── RetryFailedEvent.php
├── config/
│   └── adapters.php
└── routes/
    └── adapter-webhook.php (optional)
```

---

## Next Steps

1. **User harus setujui prioritas vendor** - Solution/ZKTeco atau Hikvision dulu?
2. **Perlu akses ke device real** untuk testing
3. **Perlu dokumentasi API vendor** (Solution/ZKTeco/Hikvision)

---

## References

- PRD Section X: Acceptance Criteria — Stream Adapter Vendor
- docs/API_HARDWARE.md
- docs/DEPLOYMENT_PHASE1.md