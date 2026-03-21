# Manual Test Case: Label Domain Filament (Pondok vs Sekolah Umum)

## Tujuan
Memastikan istilah domain pada halaman Filament mengikuti konfigurasi:
- `guru_label`
- `class_guardian_label`
- `student_label`

Serta memastikan fallback default tetap dipakai jika konfigurasi belum diisi.

## Prasyarat
1. Aplikasi bisa diakses dan login ke panel Filament sebagai admin.
2. Minimal ada 2 data `sekolahs`:
   - `institution_type = pondok`
   - `institution_type = sekolah_umum`
3. User admin terhubung ke salah satu sekolah saat login.

## Skenario A — Sekolah Umum
1. Pastikan `config/domain_labels.php` untuk `sekolah_umum` berisi default:
   - `guru_label = Guru`
   - `class_guardian_label = Wali Kelas`
2. Login sebagai user yang terhubung ke sekolah dengan `institution_type = sekolah_umum`.
3. Buka halaman Filament berikut:
   - Resource Guru (navigation + judul halaman)
   - Resource Kelas (field/kolom wali kelas)
   - Resource Jadwal Sekolah (opsi role target guru/murid)
   - Resource Absensi + widget terkait (label role/subject type)
4. Verifikasi tampilan:
   - Tampil istilah **Guru**
   - Tampil istilah **Wali Kelas**
   - Tidak muncul istilah **Musyrif Kelas** pada konteks sekolah umum.

## Skenario B — Pondok
1. Pastikan `config/domain_labels.php` untuk `pondok` berisi:
   - `guru_label = Ustadz/Pengajar`
   - `class_guardian_label = Musyrif Kelas`
2. Login sebagai user yang terhubung ke sekolah dengan `institution_type = pondok`.
3. Buka halaman Filament yang sama seperti Skenario A.
4. Verifikasi tampilan:
   - Tampil istilah **Ustadz/Pengajar** menggantikan Guru.
   - Tampil istilah **Musyrif Kelas** menggantikan Wali Kelas.

## Skenario C — Fallback Default Konfigurasi Kosong
1. Kosongkan sementara nilai label pada tipe institusi aktif di `config/domain_labels.php`
   (misalnya hapus/blank salah satu key `guru_label` atau `class_guardian_label`).
2. Reload halaman Filament yang menampilkan label domain.
3. Verifikasi fallback:
   - Jika `guru_label` kosong, label kembali ke default `Guru`.
   - Jika `class_guardian_label` kosong, label kembali ke default `Wali Kelas`.
   - Jika `student_label` kosong, label kembali ke default `Murid`.

## Ekspektasi Akhir
- Semua label domain di komponen Filament mengambil nilai dari konfigurasi.
- Tidak ada hardcoded istilah domain utama di komponen yang seharusnya dinamis.
- Perbedaan istilah Pondok vs Sekolah Umum tervalidasi.
