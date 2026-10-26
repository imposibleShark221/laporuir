// Satu-satunya modul yang membaca data laporan contoh.
// Tampilan (Blade/Alpine) harus memanggil fungsi di modul ini, bukan membaca JSON sendiri.
// Ganti dengan panggilan API saat backend dibuat.

import reportsSample from '../../../data/reports.sample.json';

const VOTE_STORAGE_KEY = 'lapor-ftuir-urgency-votes-v1';

// Salinan di memori agar nilai urgensi bisa berubah selama halaman terbuka.
const reports = reportsSample.reports.map((report) => ({ ...report }));

// Fallback bila localStorage tidak tersedia atau diblokir browser.
const memoryVotes = new Set();
let storedVotes = null;

function readStoredVotes() {
    if (storedVotes !== null) {
        return storedVotes;
    }

    storedVotes = new Set();

    try {
        const raw = window.localStorage.getItem(VOTE_STORAGE_KEY);

        if (raw) {
            const parsed = JSON.parse(raw);

            if (Array.isArray(parsed)) {
                parsed.forEach((id) => storedVotes.add(id));
            }
        }
    } catch (error) {
        // localStorage tidak tersedia: gunakan memoryVotes selama halaman terbuka.
    }

    return storedVotes;
}

function rememberVote(id) {
    memoryVotes.add(id);
    storedVotes = readStoredVotes();
    storedVotes.add(id);

    try {
        window.localStorage.setItem(VOTE_STORAGE_KEY, JSON.stringify([...storedVotes]));
    } catch (error) {
        // Gagal menyimpan: status tetap diingat lewat memoryVotes pada sesi ini.
    }
}

function cloneReport(report) {
    return { ...report };
}

// Terapkan suara yang tersimpan agar nilai di layar tetap konsisten setelah muat ulang.
// Ini hanya simulasi tampilan; nilai sebenarnya nanti berasal dari server.
const votesOnLoad = readStoredVotes();
reports.forEach((report) => {
    if (votesOnLoad.has(report.id) && report.status !== 'Selesai') {
        report.nilai_urgensi += 1;
    }
});

/**
 * Ambil laporan untuk satu lokasi. Kode lokasi: A, B, C, D, atau E.
 * @param {string} kode
 * @returns {Array<object>}
 */
export function getReportsByLocation(kode) {
    return reports
        .filter((report) => report.gedung === kode)
        .map(cloneReport);
}

/**
 * Jumlah laporan aktif (status selain Selesai) per kode lokasi.
 * Dipakai untuk badge pada peta.
 * @returns {Record<string, number>}
 */
export function getActiveReportCounts() {
    const counts = {};

    reports.forEach((report) => {
        if (report.status !== 'Selesai') {
            counts[report.gedung] = (counts[report.gedung] ?? 0) + 1;
        }
    });

    return counts;
}

/**
 * Cari laporan aktif (status selain Selesai) dengan ruangan dan fasilitas yang sama.
 * Dipakai untuk peringatan duplikat pada form.
 * @param {string} ruangan
 * @param {string} fasilitas
 * @param {string} [gedung] kode lokasi, opsional
 * @returns {Array<object>}
 */
export function getActiveReportsByRoomAndFacility(ruangan, fasilitas, gedung) {
    return reports
        .filter((report) => (
            report.status !== 'Selesai'
            && report.ruangan === ruangan
            && report.fasilitas === fasilitas
            && (!gedung || report.gedung === gedung)
        ))
        .map(cloneReport);
}

/**
 * Simpan laporan baru di memori saja.
 * ganti dengan panggilan API saat backend dibuat
 * Validasi sebenarnya (termasuk penyimpanan foto) dibuat di server nanti.
 * @param {object} data
 * @returns {object} laporan yang baru dibuat
 */
export function createReport(data) {
    const lastNumber = reports.reduce((max, report) => {
        const number = Number.parseInt(String(report.id).replace(/\D/g, ''), 10);

        return Number.isNaN(number) ? max : Math.max(max, number);
    }, 0);

    const report = {
        id: `RPT-${String(lastNumber + 1).padStart(3, '0')}`,
        gedung: data.gedung,
        lantai: data.lantai,
        ruangan: data.ruangan,
        kategori: data.kategori,
        fasilitas: data.fasilitas,
        deskripsi: data.deskripsi || null,
        foto: data.foto || null,
        status: 'Baru',
        tindakan_admin: null,
        nilai_urgensi: 1,
        dibuat_pada: new Date().toISOString(),
    };

    reports.push(report);

    return cloneReport(report);
}

/**
 * Cek apakah pengguna ini sudah menilai laporan tersebut.
 * @param {string} id
 * @returns {boolean}
 */
export function hasVoted(id) {
    return memoryVotes.has(id) || readStoredVotes().has(id);
}

/**
 * Simulasi menambah nilai urgensi sebanyak 1.
 * Satu pengguna hanya boleh menilai sekali dan laporan Selesai tidak bisa dinilai.
 * Ini HANYA simulasi tampilan. Validasi sebenarnya (termasuk identitas akun)
 * harus dibuat di server saat backend tersedia.
 * @param {string} id
 * @returns {object|null} laporan terbaru, atau null bila tidak ditemukan
 */
export function voteUrgency(id) {
    const report = reports.find((item) => item.id === id);

    if (!report) {
        return null;
    }

    if (report.status === 'Selesai' || hasVoted(id)) {
        return cloneReport(report);
    }

    report.nilai_urgensi += 1;
    rememberVote(id);

    return cloneReport(report);
}
