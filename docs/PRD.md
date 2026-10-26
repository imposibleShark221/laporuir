PRD: Sistem Pelaporan Fasilitas Fakultas Teknik UIR
Dokumen ini menjelaskan konsep, aturan, dan kebutuhan produk agar agent atau developer yang mengerjakan proyek ini memahami apa yang dibangun dan mengapa.
Versi: 0.2 (revisi 26 Oktober 2026) · Bahasa antarmuka: Indonesia

1. Ringkasan
Website tempat mahasiswa dan dosen Fakultas Teknik Universitas Islam Riau (FT UIR) melaporkan kerusakan atau kekurangan fasilitas kampus. Admin (staf TU) melihat laporan, menentukan tindakan, dan memperbarui statusnya.
Ciri utama produk:
• Halaman utama berupa satu gambar besar FT UIR dengan lima area yang dapat diklik. Pengguna memilih lokasi lewat gambar, bukan lewat menu.
• Pengguna melihat laporan yang sudah ada sebelum membuat laporan baru, supaya tidak ada laporan ganda.
• Pengguna dapat menaikkan tingkat urgensi laporan yang sudah ada, tanpa membuat laporan baru.
• Admin (staf TU) menentukan status dan tindakan untuk tiap laporan.

2. Latar belakang dan masalah
Saat ini belum ada tempat terpusat untuk melaporkan masalah fasilitas di FT. Akibatnya:
• Laporan tersebar dan sulit dilacak.
• Masalah yang sama dilaporkan berulang kali oleh orang berbeda.
• Admin (staf TU) sulit menentukan prioritas karena tidak ada indikator seberapa mendesak suatu masalah.
• Pelapor tidak tahu apakah laporannya sudah ditindaklanjuti.

3. Tujuan
1. Memusatkan semua laporan fasilitas FT UIR di satu sistem.
2. Menekan laporan ganda dengan menampilkan laporan yang sudah ada sebelum pelapor membuat yang baru.
3. Membantu admin (staf TU) menentukan prioritas lewat nilai urgensi dari banyak pengguna.
4. Memberi transparansi status dan tindakan kepada pelapor.
Bukan tujuan (di luar cakupan)
• Fakultas lain atau kampus UIR secara keseluruhan. Cakupan hanya Fakultas Teknik.
• Pembayaran, manajemen aset, inventaris, atau penjadwalan teknisi.
• Notifikasi email, WhatsApp, atau push (bisa menjadi fase berikutnya).
• Login dan register: ditunda pada fase pertama (lihat bagian 11).

4. Pengguna dan peran
Peran
Hak akses
Mahasiswa
Melihat gambar lokasi dan laporan, membuat laporan, memberi rating urgensi
Dosen
Sama dengan mahasiswa
Admin (staf TU)
Semua hak di atas, ditambah mengubah status laporan dan mengisi tindakan yang diambil
Sistem hanya dipakai tiga peran di atas. Tidak ada pengguna publik.
Saat ini admin dipegang oleh satu akun staf Tata Usaha FT UIR. Kolom peran (mahasiswa | dosen | admin) tetap disediakan agar akun admin lain bisa ditambahkan di kemudian hari.

5. Alur pengguna utama
5.1 Melapor
1. Pengguna membuka halaman utama dan melihat gambar besar FT.
2. Pengguna klik area Gedung A, B, C, D, atau Area Sekitar FT.
3. Di bawah gambar muncul daftar laporan untuk lokasi itu.
4. Pengguna memeriksa apakah masalahnya sudah dilaporkan.
    ◦ Sudah ada: pengguna menekan tombol urgensi pada laporan tersebut.
    ◦ Belum ada: pengguna menekan Buat laporan.
5. Pengguna mengisi form (bagian 7.3) dan mengirim.
6. Laporan muncul di daftar dengan status Baru.
5.2 Admin (staf TU) menindaklanjuti
1. Admin (staf TU) membuka lokasi dan melihat laporan, diurutkan menurut urgensi.
2. Admin (staf TU) mengubah status (Baru, Diproses, Selesai) dan menulis tindakan yang diambil.
3. Perubahan langsung terlihat oleh semua pengguna.

6. Lokasi
Lima lokasi yang bisa dipilih:
• Gedung A
• Gedung B
• Gedung C
• Gedung D
• Area Sekitar FT (di luar gedung: parkiran, kantin, musala, taman, selasar, toilet umum, lapangan, dan sejenisnya)
Struktur data lokasi: Gedung → Lantai → Ruangan. Untuk Area Sekitar FT tidak ada lantai, jadi dipakai satu lantai khusus bernama "Area luar" yang berisi daftar area.
Catatan: daftar lantai dan ruangan masih data contoh. Daftar asli harus diisi pemilik proyek. Data ini harus disimpan sebagai data konfigurasi (bukan hardcode di logika), agar mudah diubah.

7. Kebutuhan fungsional
7.1 Halaman utama dan gambar lokasi (prioritas utama)
• Halaman utama berupa satu gambar besar (foto/denah) yang memenuhi sebagian besar layar.
• Gambar memiliki lima area klik dengan tata letak: Gedung A di kiri atas, Gedung B di kanan atas, Gedung C di kiri bawah, Gedung D di kanan bawah, dan strip lebar Area Sekitar FT di bagian bawah.
• Foto/denah asli menyusul; sementara memakai placeholder berupa blok berwarna dengan label.
• Peta SVG interaktif tidak dipakai. Interaksi dilakukan dengan gambar dan area klik di atasnya.
• Lima area bisa diklik, di-hover, dan difokus dengan keyboard.
• Satu lokasi terpilih pada satu waktu, dengan penanda visual yang jelas.
• Tiap gedung menampilkan jumlah laporan aktif (status selain Selesai).
• Posisi area disimpan sebagai data konfigurasi agar foto/denah asli mudah menggantikan placeholder.
• Tombol Buat laporan tersedia di header. Jika belum ada lokasi dipilih, sistem meminta pengguna memilih area di gambar dulu.
7.2 Daftar laporan per lokasi
Muncul di bawah gambar setelah lokasi dipilih.
Setiap item menampilkan: foto, fasilitas, ruangan, lantai, kategori, tanggal, status, deskripsi (jika ada), tindakan admin (staf TU) (jika ada), dan nilai urgensi.
Fitur daftar:
• Filter status: Baru, Diproses, Selesai.
• Filter kategori.
• Urutan: paling urgent (default) atau terbaru.
• Tombol Ini juga urgent pada setiap laporan.
• Keadaan kosong: pesan yang mengarahkan pengguna membuat laporan.
7.3 Form membuat laporan
Field
Tipe
Wajib
Aturan
Gedung
Teks (read-only)
Ya
Terisi otomatis dari area yang dipilih pada gambar
Lantai
Dropdown
Ya
Pilihan sesuai gedung
Ruangan
Dropdown
Ya
Hanya menampilkan ruangan di lantai terpilih. Contoh: lantai 1 punya 7 ruangan, maka tampil 7 ruangan itu saja. Nonaktif sebelum lantai dipilih
Kategori
Dropdown
Ya
Listrik & Elektronik, Furniture, Bangunan, Fasilitas Umum
Fasilitas
Dropdown
Ya
Pilihan bergantung pada kategori. Nonaktif sebelum kategori dipilih
Deskripsi
Teks panjang
Tidak
Opsional
Foto laporan
Unggah gambar
Ya
Laporan tidak bisa dikirim tanpa foto
Contoh daftar fasilitas per kategori (dapat dikembangkan):
• Listrik & Elektronik: TV, AC, kipas, infokus, lampu, stop kontak
• Furniture: meja, kursi, lemari, papan tulis, rak
• Bangunan: tembok, plafon, lantai, pintu, jendela, atap
• Fasilitas Umum: toilet, keran air, tangga, tempat sampah, jalan atau paving, pagar
Perilaku tambahan:
• Peringatan duplikat: setelah ruangan dan fasilitas dipilih, jika sudah ada laporan aktif dengan kombinasi yang sama, tampilkan peringatan yang menyarankan pengguna memberi rating urgensi. Pengguna tetap boleh melanjutkan.
• Foto diperkecil di sisi klien sebelum diunggah (sisi terpanjang sekitar 640 px).
• Laporan baru otomatis berstatus Baru dan bernilai urgensi 1.
7.4 Rating urgensi
• Tombol "Ini juga urgent" menambah nilai urgensi laporan sebesar 1.
• Satu pengguna hanya bisa menilai satu kali per laporan.
• Pada fase 1 tanpa login, identitas pengguna diambil dari perangkat/browser; setelah login, identitas diambil dari akun.
• Laporan berstatus Selesai tidak bisa dinilai lagi.
• Nilai urgensi dipakai untuk mengurutkan daftar.
7.5 Fitur admin (staf TU)
• Mengubah status: Baru → Diproses → Selesai.
• Mengisi atau mengubah teks tindakan yang diambil.
• Admin (staf TU) menentukan sendiri tindakan; sistem tidak menetapkannya otomatis.
• Hanya admin (staf TU) yang boleh melakukan perubahan ini (saat prototipe, dipakai saklar "Mode admin (demo)" yang harus diganti dengan pembatasan peran sungguhan setelah login ada).

8. Model data (usulan, Laravel)
Database menyusul. Model berikut dapat dibuat sebagai Eloquent model dan migration.
Location
• id, kode_gedung: A | B | C | D | E, nama, lantai, ruangan
• E = Area Sekitar FT.
AreaLayout
• id, location_id, label, x, y, width, height (persentase), timestamps
FacilityCategory
• id, nama
Facility
• id, kategori_id, nama
Report
• id, user_id, location_id, facility_id
• deskripsi (opsional)
• foto_path (wajib)
• status: Baru | Diproses | Selesai
• tindakan_admin (teks)
• nilai_urgensi (angka, mulai dari 1)
• timestamps
UrgencyVote
• id, report_id, user_id, created_at
• Aturan unik: satu pasangan (report_id, user_id)
User
• id, name, email, password, role: mahasiswa | dosen | admin
• Satu akun admin (staf TU) dibuat lewat seeder. Akun awal: admin@example.com; password diatur di seeder dan tidak ditulis di dokumen.
Catatan Laravel:
• Foto disimpan di storage Laravel.
• Validasi dan aturan peran dilakukan di sisi server setelah login (Laravel Breeze) tersedia.

9. Kebutuhan non-fungsional
• Stack: Laravel + Blade + Tailwind CSS + Alpine.js. Vite hanya sebagai build tool bawaan Laravel. Tidak memakai React atau frontend/API terpisah.
• Responsif: nyaman di HP, karena sebagian besar pelapor akan memakai ponsel. Area sentuh minimal 44 px.
• Aksesibilitas: navigasi keyboard pada area klik, label ARIA pada area, fokus terlihat, warna bukan satu-satunya penanda.
• Tema: mendukung mode terang dan gelap.
• Performa: foto dikompres sebelum diunggah; daftar laporan dimuat per lokasi.
• Keamanan (fase login): hanya admin (staf TU) yang bisa mengubah status; validasi sisi server untuk semua input, termasuk wajib foto dan satu rating per pengguna per laporan.

10. Desain
• Nuansa kampus Islam yang bersih dan tenang.
• Warna: hijau tua (#0F5B3A) sebagai warna utama, hijau muda untuk gedung, latar krem keabu-abuan, amber untuk penanda peringatan.
• Tipografi: judul serif (Georgia), isi sans-serif sistem.
• Placeholder area berupa blok berwarna dengan label yang jelas. Foto/denah asli menyusul.
• Tanpa gradien dekoratif dan efek berlebihan. Interaksi (hover, buka form, konfirmasi) boleh bergerak halus; hormati pengaturan "reduced motion".
• Teks antarmuka memakai bahasa sederhana. Tombol menyebut aksinya dengan jelas ("Kirim laporan", "Simpan"). Pesan galat menjelaskan apa yang salah dan cara memperbaikinya.

11. Fase pengerjaan
Fase 1 (sekarang): prototipe tanpa login
• Halaman utama berupa satu gambar besar dengan lima area klik (placeholder), daftar laporan, form, rating urgensi, mode admin demo.
• Data masih disimpan di browser masing-masing pengguna (belum dibagikan antar pengguna).
Fase 2: backend dan data bersama
• Database Laravel (jenis belum dipilih), penyimpanan foto di storage Laravel.
• Semua pengguna melihat data yang sama.
Fase 3: login dan peran
• Register dan login memakai Laravel Breeze (mahasiswa, dosen, admin).
• Pembatasan fitur admin berdasarkan peran.
• Satu akun admin (staf TU) dibuat lewat seeder; kolom peran tersedia untuk penambahan.
• Satu rating per akun per laporan; batas 5 laporan per pengguna per hari.
Fase 4: pengembangan lanjutan (opsional)
• Notifikasi perubahan status ke pelapor.
• Riwayat perubahan status.
• Arsip otomatis laporan Selesai.
• Rekap dan statistik untuk admin (staf TU).
• Ekspor laporan.

12. Kriteria penerimaan (fase 1)
1. Halaman utama menampilkan satu gambar besar dengan area Gedung A, B, C, D dan Area Sekitar FT yang dapat diklik.
2. Klik area menampilkan daftar laporan lokasi itu di bawah gambar.
3. Form tidak bisa dikirim tanpa lantai, ruangan, kategori, fasilitas, dan foto.
4. Dropdown ruangan hanya berisi ruangan di lantai terpilih, dan dropdown fasilitas hanya berisi fasilitas dari kategori terpilih.
5. Kolom gedung di form terisi otomatis dari lokasi yang dipilih.
6. Peringatan duplikat muncul jika ada laporan aktif dengan ruangan dan fasilitas yang sama.
7. Rating urgensi menaikkan nilai sebesar 1, dan tidak bisa diberikan dua kali oleh pengguna yang sama pada laporan yang sama.
8. Admin (staf TU) dapat mengubah status dan mengisi tindakan, dan perubahan tampil di daftar.
9. Tampilan berfungsi baik di layar HP dan desktop.

13. Pertanyaan terbuka dan keputusan
Sudah dijawab:
• Daftar lantai dan ruangan asli: belum tersedia. Gunakan data contoh dengan `sample: true` sampai data asli diisi.
• Foto/denah asli FT UIR: belum tersedia. Gunakan placeholder blok berwarna sampai aset asli ada.
• Batas laporan: final 5 laporan per pengguna per hari; diterapkan saat login tersedia.
• Akun admin awal: admin@example.com. Password dibuat lewat seeder dan tidak ditulis di dokumen. Ganti dengan akun staf TU asli nanti.
• Arsip laporan Selesai: tidak ada arsip otomatis di fase 1. Laporan Selesai tetap tampil dan bisa difilter; arsip ditunda ke fase 4.
• Jumlah admin: satu akun admin dulu.
Masih menunggu:
• Data lantai/ruangan asli dan foto/denah asli.
• Akun staf TU asli pengganti admin@example.com.

14. Riwayat revisi
• 26 Oktober 2026 — Versi 0.2
    ◦ Halaman utama diubah dari peta SVG interaktif menjadi satu gambar besar dengan lima area klik (Gedung A, B, C, D, dan Area Sekitar FT) sesuai sketsa pemilik. Foto/denah asli menyusul; sementara memakai placeholder berupa blok berwarna.
    ◦ Posisi area dipindahkan ke data konfigurasi agar mudah diganti dengan foto atau denah asli.
    ◦ Stack ditetapkan Laravel + Blade + Tailwind CSS + Alpine.js; Vite hanya sebagai build tool. Tidak memakai React atau frontend/API terpisah. Login fase 3 memakai Laravel Breeze. Keputusan ini menggantikan catatan sebelumnya (React + Vite + Supabase).
    ◦ Admin menjadi "Admin (staf TU)"; satu akun staf Tata Usaha untuk saat ini, dibuat lewat seeder, dengan kolom peran tetap disediakan.
    ◦ Pelapor teridentifikasi lewat akun; pada fase 1 rating ganda dicegah lewat identitas perangkat/browser.
    ◦ Laporan Selesai tetap tampil dengan filter status; arsip otomatis direncanakan untuk fase lanjutan.
    ◦ Batas 5 laporan per pengguna per hari ditetapkan final dan diterapkan saat login tersedia.
    ◦ Jawaban pertanyaan terbuka dicatat di bagian 13: data contoh/placeholder, batas 5 final, akun awal admin@example.com lewat seeder, arsip fase 4, satu admin.
    ◦ Kode "E" pada model lokasi berarti Area Sekitar FT.
    ◦ Menyesuaikan bagian 8, 9, 10, 11, 12, dan 13. Menghapus prd.txt agar docs/PRD.md menjadi satu-satunya versi PRD.
    ◦ Alasan: menyatukan keputusan pemilik, menjaga PRD sebagai sumber kebenaran, dan menyiapkan UI tanpa peta/denah asli.
• 0.1 — Draf awal sebelum revisi 26 Oktober 2026.
