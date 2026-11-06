// Satu-satunya modul yang membaca data laporan contoh.
// Tampilan (Blade/Alpine) harus memanggil fungsi di modul ini, bukan membaca JSON sendiri.
// Ganti dengan panggilan API saat backend dibuat.

import reportsSample from '../../../data/reports.sample.json';

const VOTE_STORAGE_KEY = 'lapor-ftuir-urgency-votes-v1';
const ADMIN_STATE_KEY = 'lapor-ftuir-admin-state-v1';
const STATUSES = ['Baru', 'Diproses', 'Selesai'];

// Event internal: halaman pengguna memakainya agar badge peta dan daftar laporan
// ikut berubah ketika admin memperbarui status di tab/halaman lain.
export const REPORTS_CHANGED_EVENT = 'reports:changed';

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

// --- Data bersama untuk panel admin -------------------------------------------
// Perubahan admin disimpan sebagai lapisan di localStorage supaya tetap sama
// saat berpindah halaman atau membuka tab lain.
// ganti dengan panggilan API saat backend dibuat
let adminState = { updates: {}, activities: [] };

function loadAdminState() {
    try {
        const raw = window.localStorage.getItem(ADMIN_STATE_KEY);

        if (!raw) {
            return;
        }

        const parsed = JSON.parse(raw);

        adminState = {
            updates: parsed && typeof parsed.updates === 'object' && parsed.updates !== null
                ? parsed.updates
                : {},
            activities: Array.isArray(parsed?.activities) ? parsed.activities : [],
        };
    } catch (error) {
        // localStorage tidak tersedia: perubahan admin hanya bertahan di memori.
    }
}

function saveAdminState() {
    try {
        window.localStorage.setItem(ADMIN_STATE_KEY, JSON.stringify(adminState));
    } catch (error) {
        // Gagal menyimpan: perubahan tetap ada di memori selama halaman terbuka.
    }
}

function emitChange() {
    try {
        window.dispatchEvent(new CustomEvent(REPORTS_CHANGED_EVENT));
    } catch (error) {
        // Lingkungan tanpa CustomEvent: tampilan diperbarui saat halaman dimuat ulang.
    }
}

function isValidStatus(status) {
    return STATUSES.includes(status);
}

/**
 * Gabungkan laporan contoh dengan perubahan admin.
 * @param {object} report
 * @returns {object}
 */
function withOverlay(report) {
    const update = adminState.updates[report.id];

    if (!update) {
        return cloneReport(report);
    }

    return {
        ...report,
        status: isValidStatus(update.status) ? update.status : report.status,
        tindakan_admin: update.tindakan_admin ?? report.tindakan_admin,
    };
}

/** Ringkasan aktivitas contoh, dihitung tetap dari data contoh asli. */
function seedActivities() {
    const items = [];

    reportsSample.reports.forEach((report) => {
        const created = new Date(report.dibuat_pada).getTime();
        const base = {
            reportId: report.id,
            gedung: report.gedung,
            fasilitas: report.fasilitas,
            ruangan: report.ruangan,
        };

        items.push({
            ...base,
            id: `seed-baru-${report.id}`,
            type: 'laporan_baru',
            at: report.dibuat_pada,
            text: `Laporan baru: ${report.fasilitas} ${report.ruangan}`,
        });

        if (report.status !== 'Baru') {
            items.push({
                ...base,
                id: `seed-status-${report.id}`,
                type: 'status',
                to: report.status,
                at: new Date(created + 5 * 60 * 60 * 1000).toISOString(),
                text: `Admin mengubah status ${report.fasilitas} ${report.ruangan} menjadi ${report.status}`,
            });
        }

        if (report.tindakan_admin) {
            items.push({
                ...base,
                id: `seed-tindakan-${report.id}`,
                type: 'tindakan',
                at: new Date(created + 7 * 60 * 60 * 1000).toISOString(),
                text: `Admin menulis tindakan pada ${report.fasilitas} ${report.ruangan}`,
            });
        }
    });

    return items;
}

loadAdminState();

try {
    window.addEventListener('storage', (event) => {
        if (event.key === ADMIN_STATE_KEY) {
            loadAdminState();
            emitChange();
        }
    });
} catch (error) {
    // Tanpa dukungan storage event, halaman tetap sinkron saat dimuat ulang.
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
        .map(withOverlay);
}

/**
 * Jumlah laporan aktif (status selain Selesai) per kode lokasi.
 * Dipakai untuk badge pada peta.
 * @returns {Record<string, number>}
 */
export function getActiveReportCounts() {
    const counts = {};

    reports.forEach((report) => {
        if (withOverlay(report).status !== 'Selesai') {
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
        .map(withOverlay)
        .filter((report) => (
            report.status !== 'Selesai'
            && report.ruangan === ruangan
            && report.fasilitas === fasilitas
            && (!gedung || report.gedung === gedung)
        ));
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

    if (withOverlay(report).status === 'Selesai' || hasVoted(id)) {
        return withOverlay(report);
    }

    report.nilai_urgensi += 1;
    rememberVote(id);

    return withOverlay(report);
}

// --- Fungsi panel admin -------------------------------------------------------
// Semua fungsi di bawah ini membaca data yang sama dengan halaman pengguna.
// ganti dengan panggilan API saat backend dibuat

/**
 * Semua laporan, sudah termasuk perubahan status/tindakan dari admin.
 * @returns {Array<object>}
 */
export function getAllReports() {
    return reports.map(withOverlay);
}

/**
 * Ubah status dan/atau tindakan satu laporan.
 * ganti dengan panggilan API saat backend dibuat
 * @param {string} id
 * @param {{status?: string, tindakan_admin?: string|null}} changes
 * @returns {object|null} laporan setelah diubah
 */
export function updateReport(id, { status, tindakan_admin } = {}) {
    const report = reports.find((item) => item.id === id);

    if (!report) {
        return null;
    }

    const current = withOverlay(report);
    const next = { ...current };
    const entries = [];
    const now = new Date().toISOString();

    if (status && isValidStatus(status) && status !== current.status) {
        next.status = status;
        entries.push({
            type: 'status',
            to: status,
            text: `Admin mengubah status ${current.fasilitas} ${current.ruangan} menjadi ${status}`,
        });
    }

    if (typeof tindakan_admin === 'string' && tindakan_admin.trim() !== (current.tindakan_admin ?? '')) {
        next.tindakan_admin = tindakan_admin.trim() || null;
        entries.push({
            type: 'tindakan',
            text: `Admin memperbarui tindakan ${current.fasilitas} ${current.ruangan}`,
        });
    }

    if (entries.length === 0) {
        return current;
    }

    adminState.updates[id] = {
        status: next.status,
        tindakan_admin: next.tindakan_admin,
    };

    entries.forEach((entry, index) => {
        adminState.activities.push({
            ...entry,
            id: `act-${Date.now()}-${index}`,
            reportId: id,
            gedung: current.gedung,
            fasilitas: current.fasilitas,
            ruangan: current.ruangan,
            at: now,
        });
    });

    saveAdminState();
    emitChange();

    return withOverlay(report);
}

/**
 * Ubah status beberapa laporan sekaligus (aksi massal).
 * ganti dengan panggilan API saat backend dibuat
 * @param {Array<string>} ids
 * @param {string} status
 * @returns {number} jumlah laporan yang benar-benar berubah
 */
export function bulkUpdateStatus(ids, status) {
    if (!isValidStatus(status)) {
        return 0;
    }

    const now = new Date().toISOString();
    let changed = 0;

    ids.forEach((id) => {
        const report = reports.find((item) => item.id === id);

        if (!report) {
            return;
        }

        const current = withOverlay(report);

        if (current.status === status) {
            return;
        }

        adminState.updates[id] = {
            status,
            tindakan_admin: current.tindakan_admin,
        };
        adminState.activities.push({
            id: `act-${Date.now()}-${changed}`,
            type: 'status',
            to: status,
            reportId: id,
            gedung: current.gedung,
            fasilitas: current.fasilitas,
            ruangan: current.ruangan,
            at: now,
            text: `Admin mengubah status ${current.fasilitas} ${current.ruangan} menjadi ${status} (aksi massal)`,
        });
        changed += 1;
    });

    if (changed > 0) {
        saveAdminState();
        emitChange();
    }

    return changed;
}

/**
 * Log aktivitas kronologis: contoh (seed) + perubahan pada sesi ini.
 * ganti dengan panggilan API saat backend dibuat
 * @returns {Array<{id: string, type: string, at: string, text: string, reportId?: string, gedung?: string}>}
 */
export function getActivityLog() {
    const items = [...seedActivities(), ...adminState.activities];

    return items.sort((a, b) => new Date(b.at) - new Date(a.at));
}

/**
 * Ringkasan angka untuk dashboard admin.
 * ganti dengan panggilan API saat backend dibuat
 * @returns {object}
 */
export function getStats() {
    const all = reports.map(withOverlay);
    // Data contoh bertanggal Oktober 2026. Untuk perbandingan, pakai laporan
    // terbaru sebagai acuan agar angka "7 hari terakhir" tetap bermakna.
    const latest = all.reduce((max, report) => {
        const time = new Date(report.dibuat_pada).getTime();

        return Number.isFinite(time) && time > max ? time : max;
    }, 0);
    const now = new Date(latest || Date.now());
    const sevenDaysAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
    const activities = getActivityLog();

    const stats = {
        total: all.length,
        baru: 0,
        diproses: 0,
        selesai: 0,
        avgUrgensi: 0,
        createdLast7: 0,
        statusLast7: { Baru: 0, Diproses: 0, Selesai: 0 },
        perCategory: {},
    };

    all.forEach((report) => {
        const key = report.status;

        if (key === 'Baru') stats.baru += 1;
        if (key === 'Diproses') stats.diproses += 1;
        if (key === 'Selesai') stats.selesai += 1;

        stats.avgUrgensi += report.nilai_urgensi;

        stats.perCategory[report.kategori] = (stats.perCategory[report.kategori] ?? 0) + 1;

        if (new Date(report.dibuat_pada) >= sevenDaysAgo) {
            stats.createdLast7 += 1;
        }
    });

    activities.forEach((activity) => {
        if (activity.type !== 'status') {
            return;
        }

        if (new Date(activity.at) >= sevenDaysAgo && activity.to && stats.statusLast7[activity.to] !== undefined) {
            stats.statusLast7[activity.to] += 1;
        }
    });

    stats.avgUrgensi = all.length === 0
        ? 0
        : Math.round((stats.avgUrgensi / all.length) * 10) / 10;

    return stats;
}
