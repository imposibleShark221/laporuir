# Lapor Fasilitas FT UIR

Website pelaporan fasilitas Fakultas Teknik Universitas Islam Riau. Mahasiswa dan dosen dapat memilih lokasi lewat satu gambar besar dengan lima area klik, melihat laporan yang sudah ada, membuat laporan baru (dengan foto wajib), dan menaikkan urgensi laporan. Admin (staf TU) dapat mengubah status dan menulis tindakan yang diambil.

Cakupan: hanya FT UIR — Gedung A, B, C, D, dan Area Sekitar FT.

Status saat ini: **Fase 1 — UI inti sudah berjalan**. Halaman utama menampilkan placeholder peta dengan lima area klik (posisi dibaca dari `data/locations.json`), daftar laporan per lokasi dengan filter dan urutan, simulasi rating urgensi, serta modal form pembuatan laporan dengan foto wajib. Semua data laporan masih contoh; laporan baru hanya tersimpan di memori dan hilang saat halaman dimuat ulang. Belum ada database laporan, penyimpanan foto di server, tampilan admin, atau login.

## Dokumentasi

- `docs/PRD.md` — dokumen kebutuhan produk (sumber kebenaran, satu-satunya versi PRD).
- `docs/PROGRESS.md` — catatan kemajuan, keputusan, dan pertanyaan yang masih terbuka.
- `AGENTS.md` — ringkasan aturan kerja untuk agent/developer.

## Data konfigurasi

- `data/locations.json` — struktur gedung → lantai → ruangan, plus posisi lima area pada gambar (masih data contoh, `sample: true`).
- `data/facilities.json` — kategori dan daftar fasilitas.
- `data/reports.sample.json` — 10 laporan contoh untuk UI daftar laporan; foto masih `null`.

## Teknologi

- Laravel 13 + Blade + Tailwind CSS 4 + Alpine.js. Vite hanya sebagai build tool bawaan Laravel.
- Database pengembangan: SQLite. Database untuk laporan belum dibuat.
- Tidak memakai React, frontend terpisah, atau API terpisah.
- Login fase 3 memakai Laravel Breeze.

## Struktur singkat

- `routes/web.php` — route `/` ke `HomeController`.
- `app/Http/Controllers/HomeController.php` — membaca `data/locations.json` dan `data/facilities.json`; meneruskan posisi area, rasio gambar, serta konfigurasi gedung/lantai/ruangan dan kategori/fasilitas untuk form.
- `resources/views/home.blade.php` — header, placeholder peta, lima area klik, daftar laporan, filter, modal form laporan, dan notifikasi.
- `resources/js/app.js` — status Alpine: lokasi terpilih, daftar/filter laporan, rating urgensi, form laporan, validasi, pengolahan foto, peringatan duplikat, dan mode terang/gelap.
- `resources/js/lib/reports.js` — satu-satunya modul akses data laporan (`getReportsByLocation`, `getActiveReportCounts`, `hasVoted`, `voteUrgency`, `getActiveReportsByRoomAndFacility`, `createReport`); data masih contoh dan penggantian ke API ditandai komentar.
- `resources/css/app.css` — Tailwind, warna proyek, gaya area terpilih, badge status, kontrol form, dan dukungan mode gelap.

## Cara menjalankan

### Prasyarat

- PHP 8.3 atau lebih baru dengan ekstensi `curl`, `fileinfo`, `mbstring`, `openssl`, `pdo_sqlite`, `sqlite3`, dan `zip`.
- Composer 2.
- Node.js 20.19+ atau 22.12+ dan npm.

Cek versi:

```powershell
php -v
composer -V
node -v
npm -v
```

### Pemasangan pertama

Jalankan di folder proyek:

```powershell
composer install
npm install
```

Salin `.env.example` menjadi `.env` (lewati jika `.env` sudah ada), lalu buat application key:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Pengembangan memakai SQLite. Pastikan file database ada, lalu jalankan migrasi bawaan Laravel:

```powershell
if (-not (Test-Path database\database.sqlite)) { New-Item database\database.sqlite -ItemType File }
php artisan migrate
```

Cara cepat (opsional): setelah `database/database.sqlite` dibuat, `composer run setup` menjalankan `composer install`, menyiapkan `.env`, membuat key, migrasi, `npm install`, dan build aset sekaligus.

### Menjalankan aplikasi

Pilihan 1 — satu perintah (server Laravel + Vite):

```powershell
composer run dev
```

Pilihan 2 — dua terminal:

```powershell
# Terminal 1
php artisan serve
```

```powershell
# Terminal 2
npm run dev
```

Buka http://127.0.0.1:8000.

Untuk memakai aset hasil build (tanpa Vite dev server):

```powershell
npm run build
php artisan serve
```

### Berhenti

Tekan `Ctrl+C` pada terminal yang menjalankan server/Vite.

### Jika `php` atau `composer` tidak dikenali

Di mesin pengembangan ini keduanya tersedia lewat Laravel Herd. Tambahkan folder Herd ke PATH pada sesi PowerShell sebelum menjalankan perintah:

```powershell
$env:Path = "$env:USERPROFILE\.config\herd\bin;$env:Path"
```
