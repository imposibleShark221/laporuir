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
• Header berupa bilah hijau tua polos berisi logo Universitas Islam Riau (`public/images/logo-uir.png`, tampil tanpa kotak latar) dan judul "Lapor Fasilitas Fakultas Teknik UIR" rata kiri, tanpa saklar tema. Tidak ada tombol "Buat laporan" di header.
• Halaman utama menampilkan lima kartu lokasi dalam grid dua kolom: Gedung A, B, C, D (masing-masing 4:3) dan kartu Area Sekitar FT selebar dua kolom di bawahnya.
• Setiap kartu memakai foto asli dari `public/images/lokasi` dengan `object-fit: cover` dan posisi pemotongan dari field `foto_posisi` (default center). Di atas foto ada lapisan hijau tipis rgba(15, 91, 58, 0.18) dan gradien gelap di bagian bawah agar teks terbaca; kontras teks memenuhi WCAG AA.
• Nama lokasi tampil satu kali sebagai label besar di sudut kiri bawah foto: "Gedung A" sampai "Gedung D", dan "Area Sekitar FT". Tidak ada huruf gedung besar yang mengulang nama (tidak ada "A Gedung A").
• Jumlah laporan aktif tampil sebagai angka besar di sudut kanan atas kartu di atas blok berlatar lembut (hijau muda, bukan putih), dengan teks "laporan aktif" minimal 13px. Kartu tanpa laporan aktif menampilkan "Tidak ada laporan aktif".
• Kartu terpilih ditandai bingkai merah bata 3px dan teks "Dipilih"; fokus keyboard terlihat, hover memperbesar foto sedikit, dan animasi dimatikan untuk `prefers-reduced-motion`.
• Area Sekitar FT memakai foto `AreaSekitarFT.jpeg`. Jika foto tidak tersedia, kartu memakai latar hijau tua berpola garis halus dengan teks putih.
• Petunjuk di bawah judul: "Pilih gedung atau area untuk melihat laporan yang sudah ada."
• Laporan dibuat lewat tombol "Laporkan di sini" pada daftar laporan setelah lokasi dipilih; tidak ada pembuatan laporan sebelum lokasi dipilih.
• Posisi area lama dan tautan foto disimpan sebagai data konfigurasi agar mudah diganti tanpa mengubah logika. Denah asli belum tersedia.
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
Ya (kecuali Area Sekitar FT)
Pilihan sesuai gedung. Untuk Area Sekitar FT field disembunyikan dan otomatis memakai "Area luar".
Ruangan / Lokasi
Dropdown
Ya
Hanya menampilkan ruangan di lantai terpilih. Contoh: lantai 1 punya 7 ruangan, maka tampil 7 ruangan itu saja. Nonaktif sebelum lantai dipilih. Untuk Area Sekitar FT label menjadi "Lokasi" (tanpa lantai) dan ada opsi "Lainnya" untuk mengisi nama lokasi bebas.
Kategori
Dropdown
Ya
Listrik & Elektronik, Furniture, Bangunan, Fasilitas Umum
Fasilitas
Dropdown
Ya (kecuali Area Sekitar FT)
Pilihan bergantung pada kategori. Nonaktif sebelum kategori dipilih. Untuk Area Sekitar FT field disembunyikan; kategori dipakai sebagai isian fasilitas laporan.
Deskripsi
Teks panjang
Tidak (wajib untuk Area Sekitar FT)
Opsional untuk gedung. Untuk Area Sekitar FT wajib diisi dan diberi catatan agar kondisi dijelaskan sedetail mungkin.
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
• Pada Area Sekitar FT, lantai tidak ditampilkan dan otomatis diisi "Area luar". Dropdown "Lokasi" berisi Kantin, Musala, Toilet Umum, Lapangan, dan "Lainnya"; memilih "Lainnya" menampilkan isian teks nama lokasi (maksimal 100 karakter) yang wajib diisi.
• Pada Area Sekitar FT, field Fasilitas disembunyikan (kondisi area tidak tetap) dan kategori dipakai sebagai isian fasilitas laporan. Deskripsi menjadi wajib, dengan catatan "jelaskan kondisi sedetail mungkin — apa yang rusak, di mana persisnya, sejak kapan, dan dampaknya" agar laporan cepat diproses.
• Peringatan duplikat: setelah ruangan/lokasi dan fasilitas (atau kategori untuk Area Sekitar FT) dipilih, jika sudah ada laporan aktif dengan kombinasi yang sama, tampilkan peringatan yang menyarankan pengguna memberi rating urgensi. Pengguna tetap boleh melanjutkan.
• Foto hanya boleh berformat JPG/JPEG (tombol pilih file memakai `accept="image/jpeg,.jpg,.jpeg"`). Berkas lain ditolak dengan pesan "Pilih foto berformat JPG atau JPEG."
• Ukuran foto maksimal 2 MB sebelum diperkecil; berkas lebih besar ditolak dengan pesan "Ukuran foto maksimal 2 MB."
• Foto diperkecil di sisi klien sebelum diunggah (sisi terpanjang sekitar 1280 px, kualitas 0.8, hasil selalu JPEG).
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
• Hanya admin (staf TU) yang boleh melakukan perubahan ini. Selama fase 1 belum ada login, panel admin berupa tampilan (UI) dengan banner "Mode pratinjau: akses admin belum dibatasi"; pembatasan peran sungguhan menyusul di fase login.
• Panel admin memakai layout terpisah dari halaman pengguna pada route `/admin`: sidebar hijau tua (Dashboard, Laporan, Lokasi, Log Aktivitas) dan header berisi judul halaman, "Admin (staf TU)", serta tombol "Lihat situs". Di layar HP sidebar menjadi menu tersembunyi (hamburger/drawer).
• Dashboard: kartu ringkasan Total laporan, Baru, Diproses, dan Selesai; daftar "Perlu perhatian" berisi 5 laporan aktif dengan urgensi tertinggi beserta tombol cepat "Proses" dan "Buka"; dua grafik yang digambar sendiri dengan SVG/CSS tanpa library tambahan — distribusi laporan masuk per gedung (bar chart horizontal: satu batang hijau tua per gedung dalam urutan Gedung A–E, label nama lokasi, angka jumlah di dalam batang, sumbu dengan garis bantu vertikal dan angka) dan laporan per kategori (batang horizontal). Semua grafik punya label teks dan tabel ringkas alternatif untuk pembaca layar, dan warna bukan satu-satunya pembeda.
• Halaman Laporan: tabel semua laporan (foto kecil, fasilitas, lokasi gedung/lantai/ruangan, kategori, urgensi, status, tanggal, aksi) dengan pencarian teks, filter gedung/kategori/status/rentang tanggal, urutan (urgensi, terbaru, terlama), filter aktif sebagai chip yang bisa dihapus, serta paginasi 10 baris per halaman dan jumlah hasil. Kolom urgensi menampilkan angka, batang kecil bertingkat (rendah/sedang/tinggi), dan teks. Beberapa baris dapat dipilih lewat checkbox untuk aksi massal "Tandai Diproses" dan "Tandai Selesai" dengan dialog konfirmasi.
• Panel detail (drawer dari kanan) saat baris diklik: foto besar, semua field laporan, jumlah vote urgensi, kontrol segmen status (Baru/Diproses/Selesai), kotak "Tindakan yang diambil" dengan tombol templat cepat ("Teknisi dijadwalkan", "Menunggu pengadaan suku cadang", "Sudah diperbaiki"), tombol "Simpan perubahan" dengan notifikasi "Perubahan disimpan", dan riwayat singkat perubahan laporan tersebut. Drawer menjebak fokus, dapat ditutup dengan Esc, dan mengembalikan fokus ke pemicunya.
• Halaman Lokasi: kartu ringkasan per gedung berisi jumlah laporan aktif, laporan paling urgent, dan ruangan yang paling sering dilaporkan; kartu mengarah ke halaman Laporan dengan filter gedung terisi.
• Log Aktivitas: daftar kronologis kejadian ("Admin mengubah status AC A203 menjadi Diproses", "Laporan baru: Infokus Lab Komputer 1") yang terisi dari data contoh dan bertambah saat admin melakukan perubahan pada sesi ini, dengan filter jenis aktivitas.
• Perubahan status dan tindakan dari panel admin langsung terlihat di sisi pengguna (badge pada peta dan daftar laporan) selama aplikasi masih terbuka.
• Panel admin masih memakai data contoh `data/reports.sample.json` lewat `resources/js/lib/reports.js`; perubahan disimpan sebagai lapisan sementara di `localStorage` browser, bukan di server, dan akan diganti panggilan API saat backend dibuat.

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
• Tema: hanya mode terang. Tidak ada saklar terang/gelap dan preferensi tema tidak disimpan.
• Performa: foto dikompres sebelum diunggah; daftar laporan dimuat per lokasi.
• Keamanan (fase login): hanya admin (staf TU) yang bisa mengubah status; validasi sisi server untuk semua input, termasuk wajib foto dan satu rating per pengguna per laporan.

10. Desain
• Nuansa kampus Islam yang bersih dan tenang.
• Warna: hijau tua UIR (#0F5B3A) dan putih sebagai dasar, dengan merah bata atap (#9C3D2E) sebagai satu-satunya aksen. Latar halaman putih bersih atau putih kehijauan sangat tipis, bukan krem. Tombol kuning tidak dipakai.
• Tipografi halaman utama memakai font sistem yang umum (`Segoe UI`, `system-ui`, `Arial`) agar ringan dan tidak bergantung pada unduhan font. Panel admin memakai Bricolage Grotesque untuk judul dan Public Sans untuk isi, keduanya dipasang lewat npm (`@fontsource-variable`) dan disajikan dari server sendiri, bukan CDN. Judul halaman memakai ukuran `clamp(32px, 5vw, 52px)` dengan berat tebal dan letter-spacing rapat; teks isi 16px; teks kecil minimal 13px; label tidak memakai huruf kapital semua. Fallback sans-serif disediakan.
• Latar tiap area gedung memakai foto asli dengan lapisan hijau tipis dan gradien gelap di bawah agar teks putih tetap terbaca. Area yang belum punya foto memakai blok hijau tua berpola garis halus.
• Tanpa gradien dekoratif dan efek berlebihan. Interaksi (hover, buka form, konfirmasi) boleh bergerak halus; hormati pengaturan "reduced motion".
• Teks antarmuka memakai bahasa sederhana. Tombol menyebut aksinya dengan jelas ("Kirim laporan", "Simpan"). Pesan galat menjelaskan apa yang salah dan cara memperbaikinya.

11. Fase pengerjaan
Fase 1 (sekarang): prototipe tanpa login
• Halaman utama berupa grid lima kartu lokasi berfoto (A–D dan Area Sekitar FT), daftar laporan, form, rating urgensi, dan panel admin (UI).
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
3. Form tidak bisa dikirim tanpa lantai (khusus Area Sekitar FT otomatis "Area luar"), ruangan/lokasi, kategori, fasilitas (khusus Area Sekitar FT memakai kategori), foto, dan deskripsi untuk Area Sekitar FT.
4. Dropdown ruangan hanya berisi ruangan di lantai terpilih, dan dropdown fasilitas hanya berisi fasilitas dari kategori terpilih (dropdown fasilitas tidak ada untuk Area Sekitar FT).
5. Kolom gedung di form terisi otomatis dari lokasi yang dipilih.
6. Peringatan duplikat muncul jika ada laporan aktif dengan ruangan/lokasi dan fasilitas yang sama (Area Sekitar FT memakai kategori).
7. Rating urgensi menaikkan nilai sebesar 1, dan tidak bisa diberikan dua kali oleh pengguna yang sama pada laporan yang sama.
8. Admin (staf TU) dapat mengubah status dan mengisi tindakan, dan perubahan tampil di daftar.
9. Tampilan berfungsi baik di layar HP dan desktop.

13. Pertanyaan terbuka dan keputusan
Sudah dijawab:
• Daftar lantai dan ruangan asli: belum tersedia. Gunakan data contoh dengan `sample: true` sampai data asli diisi.
• Foto gedung A–D dan Area Sekitar FT sudah tersedia di `public/images/lokasi`; denah asli dan foto laporan contoh belum tersedia.
• Batas laporan: final 5 laporan per pengguna per hari; diterapkan saat login tersedia.
• Akun admin awal: admin@example.com. Password dibuat lewat seeder dan tidak ditulis di dokumen. Ganti dengan akun staf TU asli nanti.
• Arsip laporan Selesai: tidak ada arsip otomatis di fase 1. Laporan Selesai tetap tampil dan bisa difilter; arsip ditunda ke fase 4.
• Jumlah admin: satu akun admin dulu.
Masih menunggu:
• Data lantai/ruangan asli dan foto/denah asli.
• Akun staf TU asli pengganti admin@example.com.

14. Riwayat revisi
• 6 November 2026 — Versi 0.6
    ◦ Form laporan menyesuaikan Area Sekitar FT: field Lantai disembunyikan (otomatis "Area luar"), label Ruangan menjadi "Lokasi", dan dropdown menambah opsi "Lainnya" dengan isian nama lokasi bebas.
    ◦ Khusus Area Sekitar FT, field Fasilitas dihapus dari form (kategori tetap ada dan dipakai sebagai isian fasilitas laporan), Deskripsi menjadi wajib, dan ditambahkan catatan agar pelapor menjelaskan kondisi sedetail mungkin.
    ◦ Grafik "Tren laporan masuk" dihapus dari dashboard admin. Dashboard kini hanya menampilkan grafik laporan per gedung dan per kategori; kartu "Perlu perhatian" berdampingan dengan grafik kategori. Data tren di `getStats()` ikut dihapus.
    ◦ Grafik "Laporan per gedung" diganti tampilannya menjadi "Distribusi laporan masuk" berupa bar chart horizontal: satu batang hijau tua (#0F5B3A) per gedung dengan urutan tetap sesuai konfigurasi (Gedung A–E, tidak diurutkan), label nama lokasi saja tanpa huruf kode, angka jumlah di dalam batang, sudut batang 3px, serta sumbu dengan garis bantu vertikal tipis dan angka 0 sampai batas atas (nilai tertinggi + 1, misalnya tertinggi 5 menjadi 0–6) agar batang terpanjang tidak memenuhi lebar penuh. Persentase dan kalimat penjelas dihapus; keterangan cukup judul + total laporan. Data contoh diperbarui menjadi 15 laporan dengan sebaran A 5, B 3, C 1, D 4, E 2. Grafik membentang selebar dua kolom, sedangkan "Laporan per kategori" dan "Perlu perhatian" berdampingan di bawahnya; di HP label pindah ke atas batang. Data `stats.perBuilding` dan perhitungan batang bertumpuk dihapus.
    ◦ Kartu "Rata-rata urgensi" dihapus dari dashboard admin. Ringkasan kini empat kartu (Total laporan, Baru, Diproses, Selesai) dengan grid 2 kolom di HP dan 4 kolom di layar lebar.
    ◦ Monogram "FT" pada header halaman utama dan panel admin diganti logo Universitas Islam Riau dari `public/images/logo-uir.png`. Di header halaman utama logo tampil langsung di atas latar hijau; di panel admin logo diletakkan dalam kotak putih kecil agar tetap kontras.
    ◦ Menyesuaikan bagian 7.3, 7.5, dan 12.
• 4 November 2026 — Versi 0.5
    ◦ Tampilan awal "Pilih lokasi" dan header dirombak agar tidak terasa templat: header hijau tua polos (monogram FT + judul), tanpa label kecil, tombol "Buat laporan", atau pil "5 area".
    ◦ Tombol "Buat laporan" di header dihapus. Form hanya dibuka lewat "Laporkan di sini" pada daftar laporan setelah lokasi dipilih; peringatan "Pilih lokasi di peta dulu" beserta logikanya dihapus.
    ◦ Lima area kini berupa kartu foto dalam grid dua kolom dengan sudut 5px, jarak 8–12px, tanpa bayangan; nama lokasi tampil satu kali sebagai label besar di sudut kiri bawah ("Gedung A", bukan "A Gedung A"), jumlah laporan aktif di blok berlatar hijau muda lembut sudut kanan atas, dan keadaan terpilih berupa bingkai merah bata 3px + teks "Dipilih".
    ◦ Area Sekitar FT memakai foto baru `AreaSekitarFT.jpeg`; foto tiap area memakai `object-position` dari field `foto_posisi`, lapisan hijau rgba(15, 91, 58, 0.18), dan gradien gelap untuk kontras WCAG AA.
    ◦ Warna disesuaikan: hijau tua #0F5B3A + putih dengan merah bata #9C3D2E sebagai satu-satunya aksen; latar krem diganti putih kehijauan tipis; tombol kuning dihapus.
    ◦ Tipografi: halaman utama memakai font sistem yang umum (Segoe UI/system-ui dengan fallback Arial); panel admin memakai @fontsource-variable/bricolage-grotesque untuk judul dan @fontsource-variable/public-sans untuk isi, dipasang lewat npm dan disajikan sendiri. Georgia dihapus dari seluruh situs, termasuk panel admin. Judul memakai clamp(32px, 5vw, 52px), teks isi 16px, teks kecil minimal 13px, label tanpa huruf kapital semua.
    ◦ Saklar tema terang/gelap dihapus dari halaman pengguna dan panel admin. Situs kini selalu memakai mode terang; skrip pembaca `localStorage`/`prefers-color-scheme` serta state `isDark`/`toggleTheme()` dihapus.
    ◦ Foto laporan dibatasi ke format JPG/JPEG saja (PNG/WEBP tidak lagi diterima) dan ukuran maksimal 2 MB sebelum diperkecil; pesan kesalahan disesuaikan.
    ◦ Menyesuaikan bagian 7.1, 7.3, 10, 11, dan 13.
• 3 November 2026 — Versi 0.4
    ◦ Menambahkan foto gedung A–D di `public/images/lokasi` sebagai latar tiap area pada pemilihan lokasi. Tautan foto disimpan per area di `data/locations.json` (`image`), bukan di logika tampilan.
    ◦ Area Sekitar FT tetap memakai blok warna karena fotonya belum tersedia; posisi area masih contoh.
    ◦ Menyesuaikan 7.1, 10, 11, dan 13 agar sesuai dengan latar foto dan sisa placeholder.
• 3 November 2026 — Versi 0.3
    ◦ Bagian 7.5 diperluas menjadi panel admin (staf TU) pada route `/admin` dengan layout terpisah: sidebar hijau tua, header dengan tombol "Lihat situs" dan saklar tema, serta banner "Mode pratinjau: akses admin belum dibatasi".
    ◦ Menetapkan isi UI panel admin: dashboard (kartu ringkasan, "Perlu perhatian", tiga grafik SVG/CSS dengan tabel alternatif), halaman Laporan (tabel, pencarian/filter/urutan, chip filter, urgensi angka + batang + teks, aksi massal dengan konfirmasi, paginasi 10 baris, drawer detail dengan ubah status, templat tindakan, simpan, dan riwayat), halaman Lokasi (kartu ringkasan per gedung), dan Log Aktivitas berfilter.
    ◦ Menegaskan perubahan status/tindakan langsung tampil di sisi pengguna, dan panel admin masih UI dengan data contoh `data/reports.sample.json` serta penyimpanan sementara `localStorage` (bukan server, bukan login/peran sungguhan).
    ◦ Catatan: saklar "Mode admin (demo)" pada versi 0.2 digantikan oleh halaman admin khusus ini.
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
