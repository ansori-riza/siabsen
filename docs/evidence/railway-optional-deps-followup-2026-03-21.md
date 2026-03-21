# Railway Build Follow-up — Optional Dependencies

Tanggal: 2026-03-21
Service: `web`

## Tujuan
Memastikan build Railway tidak lagi menampilkan error:
`Cannot find native binding ... optional dependencies`.

## Langkah eksekusi (manual di Railway Dashboard)
1. Buka project SiAbsen → service `web`.
2. Buka tab **Variables**.
3. Tambahkan/update variable:
   - `NPM_CONFIG_OPTIONAL=false`
4. Klik **Deployments** → trigger **Redeploy**.
5. Pantau log build pada fase `npm ci` / `npm run build`.

## Hasil verifikasi
- Status: **Pending akses Railway dashboard**.
- Catatan: Environment repository ini tidak memiliki kredensial Railway, sehingga perubahan variable dan redeploy harus dieksekusi operator yang memiliki akses project Railway.

## Jika error masih muncul
Ambil 30 baris awal dari error terbaru pada build log (copy-paste dari Railway UI), lalu simpan di tiket incident dengan format:

```text
[Railway build id: <isi id>]
<30 baris awal error terbaru>
```

Template ringkas yang disarankan:

```text
Cannot find native binding ... optional dependencies
at ...
at ...
...
(hingga total 30 baris)
```
