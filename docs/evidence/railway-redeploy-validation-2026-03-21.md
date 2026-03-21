# Railway Redeploy Validation — 2026-03-21

## Ringkasan
- **Waktu eksekusi (UTC):** 2026-03-21T23:27:21Z
- **Commit Dockerfile terverifikasi sudah masuk di branch saat ini:** `7c872f1` (`fix: add libpq-dev for pdo_pgsql build`)
- **Status validasi otomatis dari environment ini:** **BLOCKED** (Railway CLI, Docker, dan akses dashboard Railway tidak tersedia)

## Permintaan dan Status
1. Trigger redeploy Railway setelah commit Dockerfile masuk.
   - **Status:** ❌ Belum bisa dieksekusi dari environment ini.
2. Cek build log dan pastikan tidak ada error `Cannot find libpq-fe.h`.
   - **Status:** ❌ Belum bisa diverifikasi langsung dari build log Railway.
3. Pastikan langkah `docker-php-ext-install ... pdo_pgsql` sukses.
   - **Status:** ❌ Belum bisa diverifikasi langsung dari build log Railway.
4. Jika build hijau, lanjut ke shell deploy dan jalankan migrasi DB.
   - **Status:** ❌ Tidak bisa lanjut karena status build belum tervalidasi.
5. Dokumentasikan hasil validasi (timestamp + screenshot build log hijau).
   - **Status:** ⚠️ Timestamp tercatat di dokumen ini; screenshot build hijau belum tersedia karena tidak ada akses Railway UI/API.

## Bukti Teknis dari Environment Ini
Perintah berikut dijalankan untuk memastikan keterbatasan akses:

```bash
railway --version
# /bin/bash: line 1: railway: command not found

docker version --format '{{.Server.Version}}'
# /bin/bash: line 1: docker: command not found
```

## Checklist Eksekusi untuk Operator Railway
Jalankan langkah berikut pada mesin/operator yang memiliki akses Railway project:

1. Trigger redeploy service yang relevan.
2. Pantau build log dan pastikan:
   - Tidak muncul `Cannot find libpq-fe.h`
   - Step `docker-php-ext-install intl zip pdo pdo_pgsql` selesai sukses.
3. Jika build hijau, masuk ke deploy shell lalu jalankan:

```bash
php artisan migrate --force
```

4. Simpan bukti release:
   - Timestamp deploy sukses
   - Screenshot build log hijau
   - (Opsional) screenshot output migrasi sukses

## Catatan
Dockerfile pada repository sudah mencantumkan `libpq-dev` dan instalasi extension `pdo_pgsql`, sehingga secara konfigurasi source code sudah selaras dengan error yang sebelumnya terjadi.
