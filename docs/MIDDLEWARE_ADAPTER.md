# Middleware Adapter untuk Integrasi Vendor Device

Dokumen ini menjelaskan pola integrasi standar agar berbagai perangkat absensi dari vendor berbeda bisa masuk ke sistem SiAbsen secara konsisten.

## Tujuan

- Menyediakan lapisan translasi dari format event vendor ke format internal SiAbsen.
- Memisahkan kompleksitas protokol/vendor dari API inti SiAbsen.
- Menjamin reliability melalui retry, dead-letter queue, dan audit payload mentah.

---

## Arsitektur: Vendor Device -> Adapter -> SiAbsen API

```text
+-----------------+        +----------------------+        +-------------------+
| Vendor Device   | -----> | Middleware Adapter   | -----> | SiAbsen API       |
| (ZKTeco, X, Y)  | event  | (normalize + validate|  HTTP  | /api/absensi      |
|                 |        | + enrich + route)    |        | /api/device/*     |
+-----------------+        +----------------------+        +-------------------+
                                  |
                                  +--> Retry Queue
                                  +--> Dead-Letter Queue (DLQ)
                                  +--> Raw Payload Audit Store
```

### Peran setiap komponen

1. **Vendor Device**
   - Mengirim event absensi sesuai format/protokol vendor (HTTP push, webhook, file drop, MQTT, dsb).

2. **Middleware Adapter**
   - Menerima payload mentah dari vendor.
   - Melakukan autentikasi sumber (device key, signature, IP allowlist bila ada).
   - Memetakan field vendor ke **canonical event schema** internal.
   - Validasi field wajib dan normalisasi value (timezone, enum, tipe data).
   - Meneruskan event yang sudah valid ke SiAbsen API.
   - Menangani kegagalan pengiriman lewat retry + DLQ.

3. **SiAbsen API**
   - Menerima event dalam format internal yang stabil.
   - Menjalankan aturan bisnis absensi (masuk/pulang, terlambat, anti-double tap, dsb).

---

## Canonical Event Schema (Internal)

Schema canonical ini menjadi kontrak tunggal antara Adapter dan SiAbsen API.

```json
{
  "event_id": "uuid-v7",
  "event_time": "2026-03-21T07:01:33+07:00",
  "received_at": "2026-03-21T00:01:35Z",
  "source": {
    "vendor": "zkteco",
    "device_id": "ZK-01-LAB1",
    "site_id": "SMA01",
    "adapter_version": "1.3.0"
  },
  "employee": {
    "employee_code": "EMP-0001",
    "rfid_uid": "04A3B91C2D",
    "fingerprint_id": 17
  },
  "attendance": {
    "type": "masuk",
    "method": "rfid",
    "status_hint": "hadir"
  },
  "meta": {
    "vendor_event_id": "evt-984433",
    "vendor_payload_hash": "sha256:...",
    "idempotency_key": "zkteco:ZK-01-LAB1:evt-984433"
  },
  "raw_payload_ref": "s3://siabsen-raw/vendor=zkteco/date=2026-03-21/evt-984433.json"
}
```

### Field wajib minimal

- `event_id`
- `event_time`
- `source.vendor`
- `source.device_id`
- Salah satu identitas pegawai: `employee.employee_code` **atau** `employee.rfid_uid` **atau** `employee.fingerprint_id`
- `attendance.type`
- `attendance.method`
- `meta.idempotency_key`

### Normalisasi nilai

- `attendance.type`: `masuk | pulang`
- `attendance.method`: `rfid | fingerprint | face | pin | card`
- `event_time`: wajib ISO-8601, sertakan offset timezone.
- Semua ID string di-trim, kosong -> `null`.

---

## Mapping Field Vendor -> Field Internal

Contoh mapping generik (sesuaikan per vendor):

| Vendor Field | Contoh Vendor Value | Canonical Field | Transformasi |
|---|---|---|---|
| `sn` / `device_sn` | `AE123456789` | `source.device_id` | direct map |
| `timestamp` / `punch_time` | `2026/03/21 07:01:33` | `event_time` | parse format lokal -> ISO-8601 |
| `user_id` / `pin` | `100234` | `employee.employee_code` | cast ke string |
| `cardno` / `rfid` | `04A3B91C2D` | `employee.rfid_uid` | uppercase + trim |
| `finger_id` | `17` | `employee.fingerprint_id` | cast ke integer |
| `verify_mode` | `1` | `attendance.method` | map enum vendor (1=fingerprint, 2=rfid, dst) |
| `io_mode` / `inout` | `0` | `attendance.type` | map enum vendor (0=masuk, 1=pulang) |
| `transaction_id` | `984433` | `meta.vendor_event_id` | direct map |
| N/A (derived) | - | `meta.idempotency_key` | compose: `{vendor}:{device}:{vendor_event_id}` |
| payload penuh | `{...}` | `raw_payload_ref` | simpan raw JSON ke storage audit |

### Contoh mapping konfigurasi per vendor (YAML)

```yaml
vendor: zkteco
mappings:
  source.device_id: $.device_sn
  event_time: $.punch_time
  employee.employee_code: $.user_id
  employee.rfid_uid: $.cardno
  employee.fingerprint_id: $.finger_id
  attendance.method:
    from: $.verify_mode
    enum:
      "1": fingerprint
      "2": rfid
  attendance.type:
    from: $.inout
    enum:
      "0": masuk
      "1": pulang
  meta.vendor_event_id: $.transaction_id
```

---

## Error Handling Flow

### 1) Retry (transient failure)

Digunakan untuk kegagalan sementara seperti timeout, koneksi putus, atau HTTP `5xx` dari SiAbsen API.

**Strategi rekomendasi**
- Max retry: **5 kali**.
- Backoff eksponensial + jitter (contoh: 1s, 2s, 4s, 8s, 16s ± random).
- Retry hanya untuk error transient:
  - network timeout/connection reset
  - HTTP `429`, `500`, `502`, `503`, `504`
- **Jangan retry** untuk `4xx` validasi (`400`, `401`, `403`, `404`, `422`) karena butuh perbaikan data/otorisasi.

### 2) Dead-Letter Queue (DLQ)

Event masuk DLQ jika:
- Retry habis (exhausted).
- Payload tidak bisa diparse/validasi canonical schema gagal.
- Mapping vendor tidak dikenali (mis. enum baru dari vendor).

**Data minimal di DLQ**
- `event_id`
- `vendor`
- `device_id`
- `failure_reason`
- `first_seen_at`, `last_retry_at`, `retry_count`
- `raw_payload_ref`

**Proses operasional DLQ**
- Dashboard monitoring jumlah DLQ per vendor/device.
- Mekanisme replay setelah root cause diperbaiki.
- SLA investigasi (mis. < 1 hari kerja untuk event kritikal).

### 3) Audit Raw Payload

Semua payload mentah vendor disimpan sebelum transformasi untuk keperluan forensik dan rekonsiliasi.

**Prinsip implementasi**
- Simpan immutable object (append-only) di object storage.
- Partisi path berdasarkan `vendor/date/device_id`.
- Simpan hash payload (`sha256`) untuk deteksi perubahan.
- Redaksi field sensitif (jika ada PII berlebih) sesuai kebijakan privasi.
- Atur retensi data (mis. 90–365 hari sesuai kebijakan organisasi).

Contoh struktur penyimpanan:

```text
raw-events/
  vendor=zkteco/
    date=2026-03-21/
      device=ZK-01-LAB1/
        evt-984433.json
```

---

## Alur End-to-End (Ringkas)

1. Vendor kirim event ke endpoint Adapter.
2. Adapter simpan raw payload ke audit store (`raw_payload_ref`).
3. Adapter transform + validasi ke canonical schema.
4. Adapter kirim ke SiAbsen API.
5. Jika gagal transient -> masuk retry queue.
6. Jika gagal permanen / retry habis -> masuk DLQ + alert.
7. Tim operasional analisis DLQ, perbaiki mapping/konfigurasi, lalu replay.

---

## Rekomendasi Implementasi Praktis

- Gunakan `idempotency_key` untuk mencegah duplikasi event saat replay/retry.
- Pisahkan konfigurasi mapping per vendor (file config, bukan hardcoded).
- Tambahkan metric minimal:
  - `ingest_count`
  - `success_count`
  - `retry_count`
  - `dlq_count`
  - `end_to_end_latency_p95`
- Siapkan alert untuk lonjakan `dlq_count` atau `retry_count` abnormal.

Dokumen ini dapat menjadi baseline untuk onboarding vendor baru tanpa mengubah kontrak inti SiAbsen API.
