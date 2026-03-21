# API Hardware Integration

Dokumen ini menjelaskan kontrak API minimum untuk integrasi perangkat hardware (mis. ESP32 + RFID/Fingerprint) dengan aplikasi SIABSEN.

## Base URL

- Development: `http://localhost:8000/api`
- Production: `https://<domain-anda>/api`

## Endpoint Utama Absensi

- **Method**: `POST`
- **Path**: `/absensi`
- **Tujuan**: Mencatat absensi dari perangkat menggunakan RFID atau fingerprint.

Contoh request:

```http
POST /api/absensi HTTP/1.1
Host: siabsen.example.com
Content-Type: application/json
Accept: application/json
X-Device-Key: <DEVICE_KEY_AKTIF>

{
  "tipe": "masuk",
  "rfid_uid": "04A3B91C2D"
}
```

## Header Wajib

Setiap request ke endpoint perangkat yang dilindungi wajib mengirim:

- `X-Device-Key: <device_key>`

Jika header tidak ada atau tidak valid, server akan merespon `401 Unauthorized`.

## Payload Absensi (RFID / Fingerprint)

### Field wajib

- `tipe` (string): `masuk` atau `pulang`

### Field identitas (minimal salah satu)

- `rfid_uid` (string, opsional)
- `fingerprint_id` (integer, opsional)

> Minimal salah satu dari `rfid_uid` atau `fingerprint_id` harus dikirim.

Contoh payload RFID:

```json
{
  "tipe": "masuk",
  "rfid_uid": "04A3B91C2D"
}
```

Contoh payload Fingerprint:

```json
{
  "tipe": "masuk",
  "fingerprint_id": 17
}
```

## Response Sukses

Status HTTP: `200 OK`

```json
{
  "success": true,
  "nama": "Budi Santoso",
  "status": "hadir",
  "metode": "rfid",
  "waktu": "07:01",
  "feedback": {
    "lcd_text": "Budi Santoso ✅ HADIR - 07:01 (rfid)",
    "buzzer": "beep_short",
    "led_color": "green"
  }
}
```

Catatan:
- `status` bisa bernilai `hadir` atau `terlambat`.
- Tap berulang cepat (anti-double tap) tetap bisa mendapat sukses dengan feedback khusus (`SUDAH TAP`).

## Response Error

### 401 - Header device key hilang / invalid

```json
{
  "success": false,
  "message": "Missing X-Device-Key header"
}
```

atau:

```json
{
  "success": false,
  "message": "Invalid or inactive device key"
}
```

### 400 - Payload tidak valid

```json
{
  "success": false,
  "pesan": "RFID UID atau Fingerprint ID harus diisi",
  "feedback": {
    "lcd_text": "Tidak dikenal ❌",
    "buzzer": "beep_long",
    "led_color": "red"
  }
}
```

### 404 - Identitas tidak ditemukan

```json
{
  "success": false,
  "pesan": "RFID/Fingerprint tidak dikenal",
  "feedback": {
    "lcd_text": "Tidak dikenal ❌",
    "buzzer": "beep_long",
    "led_color": "red"
  }
}
```

## Retry Strategy (Rekomendasi Perangkat)

Gunakan retry ringan agar stabil di jaringan lokal/sekolah:

1. **Timeout request**: 3–5 detik.
2. **Retry hanya untuk error jaringan / 5xx**:
   - Retry ke-1: tunggu 1 detik
   - Retry ke-2: tunggu 2 detik
   - Retry ke-3: tunggu 4 detik
3. **Jangan retry untuk 4xx** (`400`, `401`, `404`) karena biasanya masalah payload atau kredensial.
4. Simpan antrean lokal (ring buffer) maksimal N event saat offline, lalu kirim ulang saat koneksi pulih.
5. Sertakan `request_id` lokal (opsional) untuk membantu deduplikasi jika perangkat mengirim ulang event.

## Security Minimum

Untuk implementasi minimal yang aman di skala kecil:

1. Gunakan **HTTPS** di semua environment selain lokal.
2. Simpan `X-Device-Key` di firmware/storage terenkripsi sebisa mungkin dan **jangan log** key penuh.
3. Rotasi `device_key` berkala (mis. per 3–6 bulan) atau segera saat perangkat hilang.
4. Batasi akses API di firewall (allowlist IP jaringan sekolah jika memungkinkan).
5. Audit log request gagal (`401`, `404`) untuk deteksi penyalahgunaan.
6. Selalu verifikasi `is_active` perangkat sebelum menerima absensi.

## Public API Security Baseline

Kebijakan ringan untuk tim kecil dengan resource terbatas:

- **Auth**: Semua endpoint perangkat wajib `X-Device-Key` unik per perangkat.
- **Transport**: TLS 1.2+ wajib untuk publik internet.
- **Rate limit**: Terapkan batas dasar (contoh 60 req/menit per device key).
- **Input validation**: Tolak payload yang tidak sesuai skema.
- **Error hygiene**: Pesan error tidak boleh membocorkan detail internal (stack trace, query SQL, path server).
- **Monitoring minimum**: Pantau metrik sederhana (success rate, 401 rate, latency p95).
- **Incident response ringan**: Jika key bocor, nonaktifkan perangkat dan generate key baru dalam hari yang sama.

## Endpoint Perangkat Lainnya

Selain absensi, tersedia endpoint perangkat berikut (tetap memerlukan `X-Device-Key`):

- `POST /device/heartbeat` — update status perangkat
- `GET /device/sync` — sinkronisasi data referensi untuk perangkat

