# Hardware Compatibility Matrix

Dokumen ini merangkum opsi perangkat absensi yang paling sering diminta client, termasuk pendekatan integrasi, dukungan realtime, estimasi effort, status uji, dan keterbatasan yang perlu disampaikan ke tim sales/implementasi.

## Ringkasan Phase 1

Fokus **Phase 1** adalah perangkat yang punya jalur integrasi paling cepat (API/SDK matang, dokumentasi stabil, dan komunitas implementasi luas).

## Tabel Kompatibilitas Vendor/Model

| Vendor | Model | Metode Integrasi | Realtime Support | Effort Level | Status Uji | Recommended for Phase 1 | Known Limitation |
|---|---|---|---|---|---|---|---|
| ZKTeco | K40 Pro | Native SDK (ZKEM/Standalone SDK) + pull log periodik | Partial (near realtime via polling) | Medium | Lab test selesai, pilot terbatas | ✅ Ya | Tidak semua firmware expose event push; sering butuh whitelist IP statis. |
| ZKTeco | MB20-VL | Middleware (service connector) + SDK lokal | Ya (webhook dari middleware) | Medium-High | Lab test berjalan | ⚪ Opsional | Face recognition sensitif pencahayaan; tuning threshold wajib saat onboarding. |
| Solution X | X105-C | Native API (HTTP/JSON) | Ya (event push native) | Low | UAT internal lulus | ✅ Ya | Batas rate API relatif ketat saat sinkronisasi awal user dalam jumlah besar. |
| Fingerspot | Revo FF-153BNC | SDK vendor + middleware collector | Partial | Medium | Belum diuji penuh (hanya smoke test) | ⚪ Belum | Dokumentasi versi firmware tidak konsisten, perlu validasi per batch device. |
| Suprema | BioStation 3 | Native API + official SDK | Ya | Medium | Belum (menunggu unit demo) | ⚪ Belum | Lisensi fitur enterprise dapat menambah biaya implementasi per site. |
| Hikvision | DS-K1T341AMF | Middleware (ISAPI bridge) | Partial | High | Belum | ⚪ Belum | Integrasi lintas versi firmware kompleks; event mapping kadang berubah setelah update. |

## Catatan Implementasi

- Untuk proposal awal ke client, prioritaskan model dengan penanda **✅ Ya** pada kolom **Recommended for Phase 1**.
- Model dengan status **Belum** di kolom **Status Uji** sebaiknya hanya dijual sebagai opsi *by request* dengan disclaimer timeline integrasi.
- Kolom **Known Limitation** wajib disosialisasikan di fase presales agar ekspektasi SLA dan effort onsite realistis.
