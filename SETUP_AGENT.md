# SETUP_AGENT — Panduan Setup untuk Agent AI

Dokumen ini ditujukan untuk agent AI yang baru saja meng-clone repo ini. Ikuti langkahnya berurutan, periksa hasil tiap langkah, dan berhenti jika ada yang gagal.

## 1. Tujuan dan ringkasan proyek

- **Lapor Fasilitas FT UIR** adalah website untuk melaporkan kerusakan/kekurangan fasilitas Fakultas Teknik Universitas Islam Riau (hanya Gedung A–D dan Area Sekitar FT).
- Stack: **Laravel 13 + Blade + Tailwind CSS 4 + Alpine.js**, Vite sebagai build tool, **SQLite** untuk kebutuhan bawaan Laravel.
- **Status saat ini: UI dengan data contoh.** Halaman pengguna dan panel admin sudah berjalan, tetapi belum ada backend.
- Data laporan dibaca dari `data/reports.sample.json` lewat `resources/js/lib/reports.js`. Laporan baru hanya tersimpan di memori (hilang saat reload); perubahan admin disimpan di `localStorage` browser.
- **Belum ada**: API/backend laporan, tabel database laporan, upload foto ke server, login/register, dan pembatasan akses admin. Jangan mencari atau membuatnya kecuali diminta.

## 2. Aturan untuk agent

1. Jalankan langkah secara berurutan. Periksa hasil tiap langkah sebelum lanjut.
2. **Minta izin pengguna** sebelum memasang perangkat lunak tingkat sistem (PHP, Composer, Node.js, Herd, dsb.), mengubah `PATH`, atau mengubah kebijakan eksekusi PowerShell.
3. **Jangan** menghapus file, menjalankan perintah destruktif (`migrate:fresh` pada data pengguna, `rm -rf`, `git reset --hard`), atau mengubah kode aplikasi.
4. Jangan membuat, menampilkan, atau meng-commit rahasia (kata sandi, token, isi `.env`).
5. Jika ada langkah gagal: tampilkan pesan error asli, jelaskan penyebab paling mungkin, lalu berhenti dan tanyakan. Jangan menebak-nebak berulang.

## 3. Persyaratan dan cara memeriksa

| Persyaratan | Perintah cek | Nilai yang diharapkan |
| --- | --- | --- |
| PHP **8.3+** | `php -v` | `PHP 8.3.x` atau lebih baru (lihat catatan) |
| Composer 2.x | `composer -V` | `Composer version 2.x` |
| Node.js **20.19+** atau **22.12+** | `node -v` | `v20.19+` / `v22.12+` |
| npm | `npm -v` | `10.x` atau lebih baru |
| Git | `git --version` | `git version 2.x` |
| Ekstensi PHP | `php -m` | memuat `curl`, `fileinfo`, `mbstring`, `openssl`, `pdo_sqlite`, `sqlite3`, `zip` |

> **Catatan PHP:** meskipun sebagian panduan lama menyebut PHP 8.2, `composer.json` proyek ini mensyaratkan `"php": "^8.3"`. Gunakan **PHP 8.3 atau lebih baru**.

### 3.1 Alur pemasangan untuk agent

**a. Periksa yang sudah terpasang lebih dulu.** Jalankan di terminal:

```bash
php -v
composer -V
node -v
npm -v
git --version
php -m
```

Catat yang **belum ada** atau **versinya terlalu lama**. Jangan memasang ulang yang sudah memenuhi syarat.

**b. Tampilkan daftar rencana dan minta persetujuan.** Sebelum menjalankan perintah pemasangan apa pun, tampilkan tabel berisi: paket, versi yang akan dipasang, alasan, dan perkiraan perintah. Lalu **tunggu persetujuan pengguna**. Jangan lanjut tanpa jawaban "ya/setuju".

Contoh pesan:

> Akan memasang: PHP 8.4 NTS, Composer, Node.js LTS, dan Git lewat winget. Lanjutkan? (ya/tidak)

**c. Setelah pemasangan selesai.** Beri tahu pengguna untuk **menutup dan membuka ulang terminal** (atau me-restart aplikasi agent) karena `PATH` baru belum terbaca di sesi lama. Setelah itu ulangi langkah (a) dan pastikan semua lolos sebelum lanjut ke bagian 4.

**d. Khusus PHP di Windows — aktifkan ekstensi.**

1. Cek file konfigurasi yang dipakai:

   ```powershell
   php --ini
   ```

2. Jika **Loaded Configuration File** bernilai `(none)` atau `php.ini` belum ada, cari folder `php.exe` lalu salin `php.ini-development`:

   ```powershell
   $phpDir = Split-Path (Get-Command php.exe).Source -Parent
   Copy-Item "$phpDir\php.ini-development" "$phpDir\php.ini"
   "php.ini dibuat di: $phpDir\php.ini"
   ```

   Jika `php` dibungkus launcher lain (misalnya Herd), gunakan folder yang ditunjukkan `php --ini`. Jika folder PHP berada di `Program Files`, jalankan PowerShell sebagai Administrator atau minta pengguna menyalin/mengedit file tersebut.

3. Buka `php.ini` tersebut. Hapus tanda `;` di awal baris `extension_dir = "ext"` dan baris ekstensi berikut:

   ```ini
   extension=curl
   extension=fileinfo
   extension=mbstring
   extension=openssl
   extension=pdo_sqlite
   extension=sqlite3
   extension=zip
   ```

4. Verifikasi:

   ```powershell
   php -m
   ```

   Ketujuh ekstensi harus muncul. **Beri tahu pengguna file `php.ini` mana yang diubah** (tampilkan path lengkapnya).

**e. Jika pemasangan gagal.** Hentikan proses, tampilkan **pesan error asli** dari terminal, dan jelaskan penyebab paling mungkin. Kasus khusus:

- `winget` tidak tersedia atau `winget` tidak dikenali → hentikan dan tawarkan pemasangan manual dari situs resmi.
- Muncul prompt **UAC** → minta pengguna menyetujui prompt tersebut; agent tidak boleh mengabaikan atau mem-bypass UAC.
- Perintah `winget` gagal → jangan mengulang terus-menerus. Tampilkan error dan tawarkan langkah manual.

### 3.2 Perintah pemasangan per sistem operasi

#### Windows (winget — prioritas)

Jalankan hanya untuk paket yang belum memenuhi syarat, setelah pengguna menyetujui:

```powershell
winget install --id Git.Git --exact --accept-source-agreements
winget install --id OpenJS.NodeJS.LTS --exact --accept-source-agreements
winget install --id PHP.PHP.NTS.8.4 --exact --accept-source-agreements
winget install --id Composer.Composer --exact --accept-source-agreements
```

Catatan:

- `PHP.PHP.NTS.8.4` adalah PHP 8.4 Non-Thread Safe; versi ini memenuhi syarat `^8.3`.
- Jika sebuah ID tidak ditemukan, cari dengan `winget search <nama>` lalu sesuaikan **hanya dalam daftar paket yang diizinkan**.
- Pada sumber winget saat dokumen ini diuji, `Composer.Composer` **tidak ditemukan**. Jika `winget search composer` juga tidak menemukan Composer resmi, **jangan memasang paket lain yang mirip**; gunakan installer resmi dari <https://getcomposer.org/download/> (`Composer-Setup.exe`) dan minta pengguna menjalankannya.
- Semua perintah di atas dapat memunculkan UAC. Minta pengguna menyetujuinya.

Alternatif manual (hanya jika pengguna memilih): Laravel Herd untuk Windows (<https://herd.laravel.com/windows>) sudah termasuk PHP dan Composer. Jangan memasang Herd otomatis.

#### macOS (Homebrew)

```bash
brew update
brew install php composer node git
```

Periksa `php -v` (harus 8.3+), `node -v` (harus 20.19+ atau 22.12+), lalu `php -m` untuk ekstensi. Jika Homebrew belum ada, minta pengguna memasangnya dari <https://brew.sh> terlebih dahulu.

#### Linux (Debian/Ubuntu — apt)

```bash
sudo apt update
sudo apt install -y php php-cli php-curl php-mbstring php-xml php-zip php-sqlite3 composer git nodejs npm
```

Periksa versi:

```bash
php -v
node -v
npm -v
php -m
```

Jika `node -v` masih di bawah 20, pasang Node.js LTS dari repo resmi NodeSource (bukan skrip acak):

```bash
sudo apt install -y ca-certificates curl gnupg
sudo mkdir -p /etc/apt/keyrings
curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | sudo gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg
echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_22.x nodistro main" | sudo tee /etc/apt/sources.list.d/nodesource.list
sudo apt update
sudo apt install -y nodejs
```

Jika distribusi menyediakan PHP lebih baru dengan nama paket berversi (misalnya `php8.4-cli`), gunakan paket tersebut selama versinya 8.3+.

### 3.3 Larangan saat memasang

- Jangan memasang paket di luar daftar: **PHP 8.3+, Composer, Node.js LTS (20.19+/22.12+), npm, Git, dan ekstensi PHP yang disebut di atas**.
- Jangan mengubah `PATH` atau kebijakan eksekusi PowerShell (`Set-ExecutionPolicy`) tanpa menjelaskan alasannya dan mendapat izin pengguna.
- Jangan menjalankan skrip dari sumber yang tidak dikenal, termasuk pola `curl ... | bash` atau `iwr ... | iex` dari situs acak. Gunakan installer/resmi repo resmi.
- Jangan menonaktifkan verifikasi keamanan, UAC, atau antivirus demi mempercepat pemasangan.

## 4. Langkah instalasi

Jalankan dari folder root proyek.

### 4.1 Pasang dependensi PHP

```bash
composer install
```

Periksa: tidak ada error dan folder `vendor/` terbentuk.

### 4.2 Siapkan `.env` dan application key

**PowerShell:**

```powershell
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
php artisan key:generate
```

**bash/macOS/Linux:**

```bash
[ -f .env ] || cp .env.example .env
php artisan key:generate
```

Jangan menampilkan isi `.env` dan jangan meng-commit-nya.

### 4.3 Siapkan database SQLite

Pastikan `.env` berisi `DB_CONNECTION=sqlite` (nilai bawaan `.env.example`). Jika `DB_DATABASE` tidak diisi, Laravel memakai `database/database.sqlite`.

**PowerShell:**

```powershell
if (-not (Test-Path database\database.sqlite)) { New-Item database\database.sqlite -ItemType File | Out-Null }
```

**bash/macOS/Linux:**

```bash
touch database/database.sqlite
```

### 4.4 Jalankan migrasi

Proyek ini **memiliki migration bawaan Laravel** (`users`, `cache`, `jobs`). Migration ini dibutuhkan karena `.env.example` memakai `SESSION_DRIVER=database`, `CACHE_STORE=database`, dan `QUEUE_CONNECTION=database`.

```bash
php artisan migrate
```

Periksa: semua migration berstatus `DONE` dan `database/database.sqlite` terisi.

> Migration ini **tidak** membuat tabel laporan. Data laporan tetap berupa file JSON contoh. Jangan menjalankan `migrate:fresh`.

### 4.5 Pasang dependensi frontend

```bash
npm install
```

### 4.6 Build aset

Untuk sekali jalan (tanpa Vite dev server):

```bash
npm run build
```

Periksa: `public/build/manifest.json` terbentuk dan tidak ada error.

## 5. Cara menjalankan

Ada dua proses yang harus berjalan bersamaan. Jalankan masing-masing di **terminal terpisah** dan biarkan keduanya terbuka.

**Terminal 1 — server Laravel:**

```bash
php artisan serve
```

Buka: <http://127.0.0.1:8000>

**Terminal 2 — Vite dev server:**

```bash
npm run dev
```

Vite biasanya berjalan di <http://localhost:5173>. Jangan menutup terminal ini selama pengembangan.

- Halaman pengguna: <http://127.0.0.1:8000>
- Admin pratinjau: <http://127.0.0.1:8000/admin> (dashboard), `/admin/laporan`, `/admin/lokasi`, `/admin/aktivitas`

**Mode alternatif:** jika sudah menjalankan `npm run build`, cukup jalankan `php artisan serve`; aset dibaca dari hasil build. Jika mengubah CSS/JS, jalankan `npm run build` lagi.

Opsional (satu perintah): `composer run dev` menjalankan server + Vite sekaligus. Jika perintah ini gagal, kembali ke dua terminal di atas.

## 6. Verifikasi

Lakukan pemeriksaan berikut setelah kedua proses berjalan:

- [ ] `php artisan serve` menampilkan `Server running on [http://127.0.0.1:8000]` tanpa error.
- [ ] `npm run dev` menampilkan `VITE ... ready` dan `Local: http://localhost:5173/` tanpa error.
- [ ] Halaman utama memuat **lima kartu lokasi**: Gedung A, Gedung B, Gedung C, Gedung D, dan Area Sekitar FT.
- [ ] Klik salah satu kartu (misalnya Gedung A) → daftar laporan muncul di bawahnya.
- [ ] `/admin` terbuka dan menampilkan ringkasan serta grafik "Distribusi laporan masuk".
- [ ] Tidak ada error di konsol browser.

**Jika Vite tidak berjalan:** halaman bisa tampil tanpa gaya (CSS mentah) atau muncul error **"Vite manifest not found"** jika `public/build` belum ada. Solusi: jalankan `npm run dev` di terminal kedua, atau jalankan `npm run build` lalu muat ulang halaman.

## 7. Pemecahan masalah

| Masalah | Penyebab mungkin | Solusi |
| --- | --- | --- |
| `php` / `composer` tidak dikenali setelah instalasi | `PATH` belum diperbarui atau terminal belum dibuka ulang | Tutup dan buka terminal baru. Di Windows dengan Herd: `$env:Path = "$env:USERPROFILE\.config\herd\bin;$env:Path"`. Lalu cek `php -v` dan `composer -V`. |
| Port 8000 sudah dipakai | Proses lain (server lama) memakai port itu | Cek dengan `netstat -ano \| findstr :8000`, hentikan proses **milik sendiri** lewat Task Manager, atau jalankan `php artisan serve --port=8001` lalu buka `http://127.0.0.1:8001`. |
| Port 5173 sudah dipakai | Vite lain masih berjalan | Vite otomatis memilih port berikutnya — lihat URL di terminal. Atau `npm run dev -- --port 5174`; Laravel membaca `public/hot` secara otomatis. |
| Error **"Vite manifest not found"** | `public/build` belum ada dan Vite dev server tidak berjalan | Jalankan `npm run dev`, atau `npm run build` sekali lalu jalankan `php artisan serve`. |
| Ekstensi PHP belum aktif | Baris `extension=...` di `php.ini` masih dikomentari | Jalankan `php -m`, edit `php.ini` (`php --ini`), aktifkan ekstensi yang kurang, buka terminal baru. |
| Izin folder `storage` / `bootstrap/cache` | Folder tidak bisa ditulis (umum di Linux/macOS) | Linux/macOS: `chmod -R ug+rwx storage bootstrap/cache`. Windows: pastikan folder tidak read-only. Lalu `php artisan optimize:clear`. |
| Foto lokasi tidak muncul | Nama file/path tidak cocok atau beda huruf besar-kecil | Pastikan `public/images/lokasi/GedungA.jpeg`–`GedungD.jpeg` dan `AreaSekitarFT.jpeg` ada. Path di `data/locations.json` peka huruf besar-kecil di Linux. Tidak perlu `storage:link` karena gambar ada di `public/`. |
| Halaman 500 setelah clone | `.env` belum ada, `APP_KEY` kosong, atau database belum dibuat | Ulangi langkah 4.2 dan 4.3, lalu `php artisan config:clear` dan `php artisan view:clear`. |

Selalu tampilkan pesan error asli dari terminal/browser saat melaporkan masalah.

## 8. Catatan penting

- **Data masih contoh.** `data/locations.json`, `data/facilities.json`, dan `data/reports.sample.json` ditandai `"sample": true` (15 laporan contoh). Jangan menganggapnya data produksi.
- **Foto di `public/images/lokasi/` adalah milik pemilik proyek.** Jangan memakai ulang untuk proyek lain tanpa izin.
- **Admin belum dibatasi.** `/admin` adalah mode pratinjau tanpa login atau peran — bukan untuk produksi.
- **Yang belum ada dan jangan dicari:** backend/API laporan, tabel database laporan, CRUD laporan di server, upload foto ke server, login/register, pembatasan peran, notifikasi.
- Laporan yang dibuat pengguna hanya ada di memori dan hilang saat reload. Perubahan admin disimpan di `localStorage` dengan kunci `lapor-ftuir-admin-state-v1`.
- Jangan meng-commit `.env`, `vendor/`, `node_modules/`, `public/build/`, atau `database/*.sqlite`.
