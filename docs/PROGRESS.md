# PROGRESS — Lapor Fasilitas FT UIR

Terakhir diperbarui: 26 Oktober 2026

## Selesai

- [2026-10-26] Membaca `prd.txt` (196 baris) dan menyalin isinya ke `docs/PRD.md`; menambahkan klarifikasi pemilik bahwa kode "E" berarti Area Sekitar FT.
- [2026-10-26] Membuat struktur folder `docs/` dan `data/`; folder `src/` untuk rencana React dibatalkan dan dihapus setelah stack berubah ke Laravel.
- [2026-10-26] Menulis `AGENTS.md`: tujuan, cakupan, peran, aturan utama, dan cara kerja.
- [2026-10-26] Menulis `README.md`: deskripsi proyek dan placeholder cara menjalankan.
- [2026-10-26] Menulis `data/locations.json`: contoh Gedung A–D dan Area Sekitar FT, semua ditandai `"sample": true`.
- [2026-10-26] Menulis `data/facilities.json`: 4 kategori dan daftar fasilitas contoh.
- [2026-10-26] Mencatat jawaban pemilik proyek: pelapor teridentifikasi, admin staf Tata Usaha, laporan Selesai tetap tampil, batas 5 laporan/hari, database ditunda.
- [2026-10-26] Merevisi `docs/PRD.md` ke versi 0.2: gambar besar + lima area klik, stack Laravel + Blade + Tailwind + Alpine, admin (staf TU) satu akun, dan riwayat revisi. Menghapus `prd.txt`.
- [2026-10-26] Menambahkan konfigurasi `layout` (posisi area dalam persen) ke `data/locations.json`.
- [2026-10-26] Mencatat jawaban pertanyaan terbuka: data contoh/placeholder, batas 5 final, akun awal admin@example.com lewat seeder, arsip fase 4, satu admin.
- [2026-10-26] Memasang Laravel 13 lewat Composer ke folder sementara, lalu menyalin skeleton ke root proyek tanpa menghapus `AGENTS.md`, `README.md`, `docs/`, dan `data/`. `AGENTS.md` dan `README.md` milik proyek dipertahankan.
- [2026-10-26] Mengatur `.env` dan `.env.example`: `APP_NAME="Lapor Fasilitas FT UIR"`, `DB_CONNECTION=sqlite`, `APP_URL=http://localhost:8000`; membuat application key dan menjalankan migrasi bawaan Laravel pada `database/database.sqlite`.
- [2026-10-26] Memasang dependensi npm (Tailwind CSS 4, plugin Vite Tailwind, Vite 8) dan `alpinejs`; `npm run build` berhasil.
- [2026-10-26] Membuat `HomeController` dan route `/` (`home`). Controller membaca `data/locations.json` dan meneruskan posisi area (persen) serta rasio gambar ke tampilan.
- [2026-10-26] Membuat `resources/views/home.blade.php`: header dengan judul "Lapor Fasilitas Fakultas Teknik UIR" dan tombol "Buat laporan", satu placeholder peta dengan lima area klik (A, B, C, D, Area Sekitar FT), penanda area terpilih, nama lokasi terpilih, dan catatan bahwa form belum tersedia.
- [2026-10-26] Menambahkan interaksi Alpine di `resources/js/app.js`: satu area terpilih pada satu waktu, tombol Enter/Space berfungsi karena area memakai `<button>`, `aria-pressed`, pengumuman nama lokasi lewat `aria-live`, dan mode terang/gelap yang disimpan di `localStorage`.
- [2026-10-26] Menambahkan gaya proyek di `resources/css/app.css`: hijau tua `#0F5B3A`, latar krem, judul serif, gaya area terpilih, dukungan mode gelap, dan `prefers-reduced-motion`.
- [2026-10-26] Verifikasi di browser: klik area memilih satu area dan menampilkan namanya; Tab + Enter memilih area; tombol "Buat laporan" saat itu menampilkan catatan belum tersedia; mode gelap/terang bertahan setelah muat ulang. Pemeriksaan lebar 320 px dan 390 px tidak ada overflow horizontal; area terpendek 44 px.
- [2026-10-26] Audit Lighthouse: aksesibilitas 100, best practices 100, SEO 100. Perbaikan yang dilakukan: menghapus teks petunjuk di dalam tombol area agar nama aksesibel cocok, menaikkan kontras teks abu-abu di mode gelap, dan memberi tinggi minimum 44 px pada area.
- [2026-10-26] Memperbarui `README.md` dengan prasyarat, pemasangan, cara menjalankan (`composer run dev` atau `php artisan serve` + `npm run dev`), build aset, dan catatan PATH Laravel Herd.
- [2026-10-26] Membuat `data/reports.sample.json` berisi 10 laporan contoh (2 per area A–E) dengan status Baru/Diproses/Selesai dan nilai urgensi bervariasi; semua foto masih `null`. Konsistensi gedung, lantai, ruangan, kategori, dan fasilitas divalidasi terhadap `data/locations.json` dan `data/facilities.json`.
- [2026-10-26] Membuat `resources/js/lib/reports.js` sebagai satu-satunya modul akses data laporan: `getReportsByLocation`, `getActiveReportCounts`, `hasVoted`, `voteUrgency`, `getActiveReportsByRoomAndFacility`, dan `createReport`. Data masih contoh; modul diberi komentar "ganti dengan panggilan API saat backend dibuat".
- [2026-10-26] Menambahkan daftar laporan per lokasi di `home.blade.php` dan `app.js`: foto/placeholder, fasilitas, ruangan, lantai, kategori, tanggal, status (teks + warna), deskripsi, tindakan admin, dan nilai urgensi; filter status/kategori; urutan paling urgent (default) atau terbaru; badge laporan aktif di peta; keadaan kosong; serta tombol "Laporkan di sini".
- [2026-10-26] Menambahkan simulasi rating urgensi "Ini juga urgent": nilai tampilan bertambah 1, satu kali per laporan lewat `localStorage` (dengan fallback memori bila tidak tersedia), tombol berubah menjadi "Sudah dinilai", dan laporan Selesai tidak dapat dinilai. Validasi sebenarnya tetap dibuat di server nanti.
- [2026-10-26] Menyelaraskan nama lantai Area Sekitar FT menjadi "Area luar" di `data/locations.json` dan `data/reports.sample.json`. `HomeController` sekarang meneruskan konfigurasi gedung → lantai → ruangan serta kategori → fasilitas untuk form, tanpa membaca data laporan di Blade.
- [2026-10-26] Membuat modal form laporan (PRD 7.3) di `home.blade.php`: dibuka dari header "Buat laporan" dan tombol "Laporkan di sini"; header tanpa lokasi terpilih menampilkan pesan "Pilih lokasi di peta dulu" tanpa membuka form. Modal dapat ditutup dengan Batal atau Esc, fokus keyboard terkurung di dalam dialog, dan fokus dikembalikan ke tombol pembuka.
- [2026-10-26] Melengkapi field form: Gedung read-only, Lantai dari data lokasi, Ruangan mengikuti lantai (nonaktif sebelum lantai dipilih dan reset saat lantai berubah), Kategori, Fasilitas mengikuti kategori (nonaktif sebelum kategori dipilih dan reset saat kategori berubah), Deskripsi opsional dengan penghitung 500 karakter, serta Foto wajib dengan pratinjau dan tombol "Ganti foto".
- [2026-10-26] Menambahkan pengolahan foto di sisi klien memakai canvas: hanya JPG/PNG/WEBP, maksimal 5 MB sebelum dikecilkan, sisi terpanjang 1280 px, kualitas JPEG 0.8, lalu disimpan sebagai data URL di state. (PRD 7.3 masih menyebut sekitar 640 px; tugas terbaru meminta 1280 px dan perlu diselaraskan dengan pemilik proyek.)
- [2026-10-26] Menambahkan peringatan duplikat di dalam form setelah ruangan dan fasilitas dipilih: jumlah laporan aktif dengan kombinasi sama, ringkasan tiap laporan, dan tombol "Beri urgensi pada laporan ini" yang menutup form, menggulir ke laporan, serta memindahkan fokus ke tombol rating. Pengguna tetap boleh melanjutkan membuat laporan baru.
- [2026-10-26] Menambahkan validasi dan pengiriman: pesan galat inline di bawah field, fokus ke field pertama yang salah, pesan foto wajib "Unggah foto kerusakan atau kekurangan fasilitas", pencegahan klik ganda, laporan baru berstatus Baru dengan urgensi 1, badge peta diperbarui, dan notifikasi "Laporan terkirim".
- [2026-10-26] Verifikasi: `npm run build` berhasil; Lighthouse aksesibilitas 100 untuk halaman awal; tampilan desktop/mobile serta mode terang/gelap diperiksa lewat Chrome headless; tidak ada overflow horizontal pada lebar 390 px; cascading field, reset ruangan/fasilitas, peringatan duplikat, scroll ke laporan, focus trap, Esc/Batal, dan pemulihan fokus diuji.
- [2026-10-26] Catatan penting: laporan baru hanya tersimpan di memori modul dan hilang saat halaman dimuat ulang. Rating urgensi hanya simulasi tampilan dan disimpan per browser; belum ada database, API, penyimpanan foto di server, login, atau tampilan admin.

## Sedang dikerjakan

- Tidak ada. Daftar laporan, rating urgensi, dan form pembuatan laporan (UI) selesai; menunggu instruksi untuk fitur berikutnya.

## Berikutnya

Langkah teknis terdekat (sesuai fase 1):

- "Mode admin (demo)" untuk mengubah status dan mengisi tindakan.
- Menyelaraskan ukuran pengecilan foto di PRD 7.3 (640 px) dengan implementasi tugas terbaru (1280 px) sebelum backend dibuat.
- Backend Laravel: database, validasi server, penyimpanan foto, API laporan, dan login/peran pada fase berikutnya.

Fase 1 (sekarang): prototipe tanpa login.

- Halaman utama berupa satu gambar besar dengan lima area klik (placeholder), daftar laporan per lokasi, form laporan, rating urgensi, mode admin demo.
- Data masih disimpan di browser masing-masing pengguna (belum dibagikan).

Fase 2: backend dan data bersama.

- Database Laravel (SQLite untuk pengembangan; jenis database produksi ditentukan nanti), penyimpanan foto di storage Laravel, data yang sama untuk semua pengguna.

Fase 3: login dan peran.

- Register/login memakai Laravel Breeze (mahasiswa, dosen, admin); pembatasan fitur admin berdasarkan peran; satu akun admin (staf TU) lewat seeder; satu rating per akun per laporan; batas 5 laporan per pengguna per hari.

Fase 4 (opsional): pengembangan lanjutan.

- Notifikasi perubahan status, arsip otomatis laporan Selesai, riwayat perubahan status, rekap dan statistik admin (staf TU), ekspor laporan.

## Keputusan

- `docs/PRD.md` adalah sumber kebenaran dan satu-satunya versi PRD; `prd.txt` sudah dihapus. Keputusan baru harus masuk ke PRD dan dicatat di sini.
- Cakupan gedung: A, B, C, D, dan Area Sekitar FT. Pada model `Location`, kode "E" berarti Area Sekitar FT (bukan gedung tambahan), sesuai `docs/PRD.md` bagian 8.
- Data lokasi dipisah di `data/locations.json` agar mudah diganti. Daftar lantai/ruangan asli harus diminta ke fakultas atau disurvei sebelum peluncuran.
- Halaman utama memakai satu gambar besar, bukan grid kartu dan bukan peta SVG. Lima area klik disusun: A kiri atas, B kanan atas, C kiri bawah, D kanan bawah, dan strip Area Sekitar FT di bawah.
- Foto/denah asli belum tersedia; sementara memakai placeholder blok berwarna dengan label. Posisi area disimpan di `data/locations.json` (persen) agar mudah disesuaikan.
- Pelapor teridentifikasi lewat akun (Fase 3), sehingga aturan satu rating per pengguna bisa ditegakkan dan admin (staf TU) dapat menghubungi pelapor. Pada Fase 1 tanpa login, rating ganda dicegah lewat identitas perangkat/browser.
- Admin (staf TU): satu akun staf Tata Usaha FT UIR untuk saat ini, dibuat lewat seeder. Akun awal admin@example.com; password diatur di seeder dan tidak ditulis di dokumen. Kolom peran (mahasiswa | dosen | admin) tetap disediakan agar bisa ditambah nanti.
- Laporan berstatus Selesai tetap tampil dan dapat difilter; tidak ada arsip otomatis di fase 1, arsip ditunda ke fase 4.
- Batas spam: final maksimal 5 laporan per pengguna per hari; diterapkan saat login tersedia.
- Stack: Laravel + Blade + Tailwind CSS + Alpine.js. Vite hanya build tool bawaan Laravel; tidak memakai React, frontend terpisah, atau API terpisah. Login fase 3 memakai Laravel Breeze. Keputusan ini menggantikan catatan sebelumnya (React + Vite + Supabase).
- Database pengembangan memakai SQLite (`DB_CONNECTION=sqlite`, `database/database.sqlite`) agar tidak perlu memasang server database. Jenis database untuk produksi ditentukan saat fase backend.
- Login ditunda ke Fase 3; prototipe memakai "Mode admin (demo)".
- Data lokasi dan fasilitas masih contoh; `data/locations.json` ditandai `"sample": true`, data asli diisi pemilik proyek. Data lantai/ruangan dan foto/denah asli belum ada; gunakan data contoh dan placeholder.
