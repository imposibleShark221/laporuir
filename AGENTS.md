# AGENTS.md — Lapor Fasilitas FT UIR

> Dokumen kerja agent. Sumber kebenaran: `docs/PRD.md` (satu-satunya versi PRD); catatan kerja: `docs/PROGRESS.md`.

## Tujuan proyek

Memusatkan laporan kerusakan atau kekurangan fasilitas FT UIR dalam satu website, menekan laporan ganda dengan menampilkan laporan yang sudah ada, membantu admin (staf TU) memprioritaskan lewat rating urgensi, dan memberi transparansi status serta tindakan kepada pelapor.

## Cakupan

- Hanya Fakultas Teknik Universitas Islam Riau (FT UIR).
- Lokasi: Gedung A, B, C, D, dan Area Sekitar FT (parkiran, kantin, musala, taman, selasar, toilet umum, lapangan, dan sejenisnya).
- Struktur lokasi: Gedung → Lantai → Ruangan. Area Sekitar FT memakai satu lantai "Area luar".
- Kode "E" pada model data merujuk ke Area Sekitar FT, bukan gedung tambahan.
- Halaman utama memakai satu gambar besar berisi lima area klik: A dan B di baris atas, C dan D di baris kedua, Area Sekitar FT di strip bawah. Foto/denah asli menyusul; sementara memakai placeholder blok berwarna.
- Posisi area disimpan di `data/locations.json`; tidak memakai peta SVG.
- Di luar cakupan: fakultas/kampus lain, pembayaran, manajemen aset/inventaris, penjadwalan teknisi, notifikasi (fase lanjutan), serta login/register (ditunda).

## Peran dan hak

- Mahasiswa: melihat gambar lokasi dan laporan, membuat laporan, memberi rating urgensi.
- Dosen: sama dengan mahasiswa.
- Admin (staf TU): satu akun staf Tata Usaha FT UIR untuk saat ini (akun awal admin@example.com; password lewat seeder, tidak ditulis di dokumen); semua hak di atas, ditambah mengubah status laporan dan mengisi tindakan yang diambil.
- Pelapor teridentifikasi lewat akun; login baru aktif pada Fase 3. Pada Fase 1, rating ganda dicegah lewat identitas perangkat/browser. Tidak ada pengguna publik; prototipe memakai saklar "Mode admin (demo)".

## Aturan utama

- Foto laporan wajib; laporan tidak bisa dikirim tanpa foto.
- Ruangan mengikuti lantai yang dipilih, dan lantai mengikuti gedung yang dipilih.
- Fasilitas mengikuti kategori yang dipilih.
- Satu pengguna/akun hanya boleh memberi satu rating urgensi per laporan; laporan berstatus Selesai tidak bisa dinilai.
- Status Baru/Diproses/Selesai dan teks tindakan hanya diubah admin (staf TU).
- Laporan baru otomatis berstatus Baru dengan nilai urgensi 1.
- Laporan Selesai tetap ditampilkan dan dapat difilter; arsip otomatis masuk fase lanjutan.
- Batas spam: maksimal 5 laporan per pengguna per hari (final), berlaku setelah login.
- Login/register belum ada pada fase 1.
- Data lokasi, posisi area, dan fasilitas disimpan sebagai konfigurasi, bukan hardcode di logika. Data lantai/ruangan dan foto/denah asli belum ada; gunakan data contoh dan placeholder.

## Cara kerja

1. Baca `docs/PRD.md` dan `docs/PROGRESS.md` di awal setiap sesi.
2. Kerjakan satu tugas per sesi; jangan menambah fitur atau mengubah cakupan tanpa bertanya.
3. Perbarui `docs/PROGRESS.md` setelah tugas selesai.
4. Jika ada hal yang tidak jelas atau bertentangan, tanyakan dulu sebelum mengerjakan.
5. Stack: Laravel + Blade + Tailwind CSS + Alpine.js; Vite hanya build tool; SQLite untuk pengembangan, database laporan ditunda; tanpa React/API terpisah. Login fase 3 memakai Laravel Breeze.
