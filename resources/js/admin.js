import Alpine from 'alpinejs';
import {
    bulkUpdateStatus,
    getAllReports,
    getActivityLog,
    getStats,
    REPORTS_CHANGED_EVENT,
    updateReport,
} from './lib/reports.js';

const DATE_FORMAT = new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
});

const DATE_TIME_FORMAT = new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
});

const LONG_DATE_FORMAT = new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
});

const FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

function formatDate(value) {
    const date = new Date(value);

    return Number.isNaN(date.getTime()) ? value : DATE_FORMAT.format(date);
}

function formatDateTime(value) {
    const date = new Date(value);

    return Number.isNaN(date.getTime()) ? value : DATE_TIME_FORMAT.format(date);
}

function formatLongDate(value) {
    const date = new Date(value);

    return Number.isNaN(date.getTime()) ? value : LONG_DATE_FORMAT.format(date);
}

function textMatches(value, query) {
    return String(value ?? '').toLowerCase().includes(query);
}

window.adminApp = (config = {}) => ({
    page: config.page ?? 'dashboard',
    buildings: config.buildings ?? [],
    categories: config.categories ?? [],

    reports: getAllReports(),
    activities: getActivityLog(),
    stats: getStats(),

    sidebarOpen: false,
    toast: '',
    toastTimer: null,

    filters: {
        q: '',
        gedung: '',
        kategori: '',
        status: '',
        dari: '',
        sampai: '',
        sort: 'urgent',
    },
    pageIndex: 1,
    perPage: 10,
    selectedIds: [],

    drawerOpen: false,
    detailId: null,
    draft: { status: '', tindakan_admin: '' },
    lastFocused: null,

    confirmOpen: false,
    confirmStatus: '',
    confirmIds: [],
    confirmFocused: null,

    activityType: '',
    activityTypes: [
        { value: '', label: 'Semua aktivitas' },
        { value: 'laporan_baru', label: 'Laporan baru' },
        { value: 'status', label: 'Perubahan status' },
        { value: 'tindakan', label: 'Tindakan admin' },
    ],

    treatmentTemplates: [
        'Teknisi dijadwalkan',
        'Menunggu pengadaan suku cadang',
        'Sudah diperbaiki',
    ],

    init() {
        const params = new URLSearchParams(window.location.search);
        const gedung = params.get('gedung');

        if (gedung && this.buildings.some((building) => building.kode === gedung)) {
            this.filters.gedung = gedung;
        }

        const laporan = params.get('laporan');

        if (laporan) {
            this.$nextTick(() => this.openDetail(laporan));
        }

        window.addEventListener(REPORTS_CHANGED_EVENT, () => this.refreshData());
    },

    refreshData() {
        this.reports = getAllReports();
        this.activities = getActivityLog();
        this.stats = getStats();
        this.selectedIds = this.selectedIds.filter((id) => this.reports.some((report) => report.id === id));

        if (this.pageIndex > this.pageCount) {
            this.pageIndex = this.pageCount;
        }

        if (this.drawerOpen && this.detail) {
            this.draft = {
                status: this.detail.status,
                tindakan_admin: this.detail.tindakan_admin ?? '',
            };
        }
    },

    // --- Utilitas tampilan ----------------------------------------------------
    formatDate,
    formatDateTime,
    formatLongDate,

    buildingName(code) {
        return this.buildings.find((building) => building.kode === code)?.nama ?? code;
    },

    urgencyLevel(value) {
        if (value >= 7) {
            return { label: 'Tinggi', tone: 'high' };
        }

        if (value >= 4) {
            return { label: 'Sedang', tone: 'medium' };
        }

        return { label: 'Rendah', tone: 'low' };
    },

    urgencyWidth(value) {
        return `${Math.min(100, Math.max(8, value * 10))}%`;
    },

    voteCount(report) {
        // Data contoh belum punya jumlah vote terpisah; laporan baru mulai dari 1.
        return Math.max(0, report.nilai_urgensi - 1);
    },

    openSidebar() {
        this.sidebarOpen = true;
        this.$nextTick(() => this.$refs.sidebarClose?.focus());
    },

    closeSidebar(restoreFocus = true) {
        this.sidebarOpen = false;

        if (restoreFocus) {
            this.$nextTick(() => this.$refs.menuButton?.focus());
        }
    },

    showToast(message) {
        this.toast = message;

        if (this.toastTimer) {
            window.clearTimeout(this.toastTimer);
        }

        this.toastTimer = window.setTimeout(() => {
            this.toast = '';
        }, 4000);
    },

    trapFocus(event, ref) {
        const dialog = this.$refs[ref];

        if (!dialog) {
            return;
        }

        const focusable = [...dialog.querySelectorAll(FOCUSABLE)]
            .filter((element) => element.getClientRects().length > 0);

        if (focusable.length === 0) {
            event.preventDefault();
            return;
        }

        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        const active = document.activeElement;

        if (event.shiftKey) {
            if (active === first || !dialog.contains(active)) {
                event.preventDefault();
                last.focus();
            }

            return;
        }

        if (active === last || !dialog.contains(active)) {
            event.preventDefault();
            first.focus();
        }
    },

    focusWhenReady(id, attempts = 10) {
        const element = document.getElementById(id);

        if (element && element.getClientRects().length > 0) {
            element.focus();
            return;
        }

        if (attempts > 0) {
            window.requestAnimationFrame(() => this.focusWhenReady(id, attempts - 1));
        }
    },

    // --- Daftar laporan: filter, urutan, paginasi ------------------------------
    get filteredReports() {
        const query = this.filters.q.trim().toLowerCase();
        const from = this.filters.dari ? new Date(`${this.filters.dari}T00:00:00`) : null;
        const to = this.filters.sampai ? new Date(`${this.filters.sampai}T23:59:59`) : null;

        const items = this.reports.filter((report) => {
            if (query) {
                const haystack = [report.id, report.fasilitas, report.ruangan, report.kategori, report.deskripsi ?? ''];
                const match = haystack.some((value) => textMatches(value, query));

                if (!match) {
                    return false;
                }
            }

            if (this.filters.gedung && report.gedung !== this.filters.gedung) {
                return false;
            }

            if (this.filters.kategori && report.kategori !== this.filters.kategori) {
                return false;
            }

            if (this.filters.status && report.status !== this.filters.status) {
                return false;
            }

            const created = new Date(report.dibuat_pada);

            if (from && created < from) {
                return false;
            }

            if (to && created > to) {
                return false;
            }

            return true;
        });

        return [...items].sort((a, b) => {
            if (this.filters.sort === 'terbaru') {
                return new Date(b.dibuat_pada) - new Date(a.dibuat_pada);
            }

            if (this.filters.sort === 'terlama') {
                return new Date(a.dibuat_pada) - new Date(b.dibuat_pada);
            }

            const urgency = b.nilai_urgensi - a.nilai_urgensi;

            return urgency !== 0 ? urgency : new Date(b.dibuat_pada) - new Date(a.dibuat_pada);
        });
    },

    get pageCount() {
        return Math.max(1, Math.ceil(this.filteredReports.length / this.perPage));
    },

    get pagedReports() {
        const start = (this.pageIndex - 1) * this.perPage;

        return this.filteredReports.slice(start, start + this.perPage);
    },

    get resultRange() {
        if (this.filteredReports.length === 0) {
            return '0 laporan';
        }

        const start = (this.pageIndex - 1) * this.perPage + 1;
        const end = Math.min(this.pageIndex * this.perPage, this.filteredReports.length);

        return `${start}–${end} dari ${this.filteredReports.length} laporan`;
    },

    goToPage(next) {
        this.pageIndex = Math.min(Math.max(1, next), this.pageCount);
    },

    resetPage() {
        this.pageIndex = 1;
    },

    get activeFilterChips() {
        const chips = [];

        if (this.filters.q.trim()) {
            chips.push({ key: 'q', label: `Pencarian: "${this.filters.q.trim()}"` });
        }

        if (this.filters.gedung) {
            chips.push({ key: 'gedung', label: `Gedung: ${this.buildingName(this.filters.gedung)}` });
        }

        if (this.filters.kategori) {
            chips.push({ key: 'kategori', label: `Kategori: ${this.filters.kategori}` });
        }

        if (this.filters.status) {
            chips.push({ key: 'status', label: `Status: ${this.filters.status}` });
        }

        if (this.filters.dari) {
            chips.push({ key: 'dari', label: `Dari: ${formatDate(this.filters.dari)}` });
        }

        if (this.filters.sampai) {
            chips.push({ key: 'sampai', label: `Sampai: ${formatDate(this.filters.sampai)}` });
        }

        return chips;
    },

    get hasFilters() {
        return this.activeFilterChips.length > 0;
    },

    clearFilter(key) {
        this.filters[key] = '';
        this.pageIndex = 1;
    },

    resetFilters() {
        this.filters = {
            q: '',
            gedung: '',
            kategori: '',
            status: '',
            dari: '',
            sampai: '',
            sort: 'urgent',
        };
        this.pageIndex = 1;
    },

    // --- Pilihan baris dan aksi massal ----------------------------------------
    isSelected(id) {
        return this.selectedIds.includes(id);
    },

    toggleSelect(id) {
        this.selectedIds = this.isSelected(id)
            ? this.selectedIds.filter((item) => item !== id)
            : [...this.selectedIds, id];
    },

    get allOnPageSelected() {
        return this.pagedReports.length > 0
            && this.pagedReports.every((report) => this.isSelected(report.id));
    },

    toggleAllOnPage() {
        const ids = this.pagedReports.map((report) => report.id);

        if (this.allOnPageSelected) {
            this.selectedIds = this.selectedIds.filter((id) => !ids.includes(id));
            return;
        }

        this.selectedIds = [...new Set([...this.selectedIds, ...ids])];
    },

    clearSelection() {
        this.selectedIds = [];
    },

    openBulkConfirm(status) {
        if (this.selectedIds.length === 0) {
            return;
        }

        this.confirmStatus = status;
        this.confirmIds = [...this.selectedIds];
        this.confirmFocused = document.activeElement;
        this.confirmOpen = true;

        this.$nextTick(() => this.focusWhenReady('confirm-cancel'));
    },

    closeBulkConfirm(restoreFocus = true) {
        const trigger = this.confirmFocused;

        this.confirmOpen = false;
        this.confirmStatus = '';
        this.confirmIds = [];
        this.confirmFocused = null;

        if (restoreFocus && trigger instanceof HTMLElement && trigger.isConnected) {
            this.$nextTick(() => trigger.focus({ preventScroll: true }));
        }
    },

    get confirmMessage() {
        const count = this.confirmIds.length;

        return `${count} laporan akan ditandai ${this.confirmStatus}. Perubahan langsung terlihat di sisi pengguna.`;
    },

    runBulkUpdate() {
        const status = this.confirmStatus;
        const changed = bulkUpdateStatus(this.confirmIds, status);

        this.closeBulkConfirm(false);
        this.clearSelection();

        if (changed > 0) {
            this.showToast(`${changed} laporan ditandai ${status}. Perubahan disimpan`);
            this.$nextTick(() => this.focusWhenReady('reports-result-summary'));
        } else {
            this.showToast('Tidak ada status yang berubah');
        }
    },

    // --- Panel detail ---------------------------------------------------------
    get detail() {
        return this.reports.find((report) => report.id === this.detailId) ?? null;
    },

    get detailHistory() {
        if (!this.detailId) {
            return [];
        }

        return this.activities
            .filter((activity) => activity.reportId === this.detailId)
            .slice(0, 5);
    },

    get detailChanged() {
        if (!this.detail) {
            return false;
        }

        return this.draft.status !== this.detail.status
            || this.draft.tindakan_admin !== (this.detail.tindakan_admin ?? '');
    },

    openDetail(id) {
        const report = this.reports.find((item) => item.id === id);

        if (!report) {
            return;
        }

        this.detailId = id;
        this.draft = {
            status: report.status,
            tindakan_admin: report.tindakan_admin ?? '',
        };
        this.lastFocused = document.activeElement;
        this.drawerOpen = true;

        this.$nextTick(() => this.focusWhenReady('detail-close'));
    },

    closeDetail(restoreFocus = true) {
        const trigger = this.lastFocused;

        this.drawerOpen = false;
        this.detailId = null;
        this.draft = { status: '', tindakan_admin: '' };
        this.lastFocused = null;

        if (restoreFocus && trigger instanceof HTMLElement && trigger.isConnected) {
            this.$nextTick(() => trigger.focus({ preventScroll: true }));
        }
    },

    applyTemplate(text) {
        const current = this.draft.tindakan_admin.trim();

        this.draft.tindakan_admin = current ? `${current}\n${text}` : text;
    },

    saveDetail() {
        if (!this.detailId || !this.detail) {
            return;
        }

        if (!this.detailChanged) {
            this.showToast('Tidak ada perubahan untuk disimpan');
            return;
        }

        updateReport(this.detailId, {
            status: this.draft.status,
            tindakan_admin: this.draft.tindakan_admin,
        });

        this.showToast('Perubahan disimpan');
        this.$nextTick(() => this.focusWhenReady('detail-save'));
    },

    markProcessed(id) {
        updateReport(id, { status: 'Diproses' });
        this.showToast('Laporan ditandai Diproses. Perubahan disimpan');
    },

    // --- Dashboard ------------------------------------------------------------
    get needAttention() {
        return this.reports
            .filter((report) => report.status !== 'Selesai')
            .sort((a, b) => b.nilai_urgensi - a.nilai_urgensi || new Date(a.dibuat_pada) - new Date(b.dibuat_pada))
            .slice(0, 5);
    },

    get buildingTotals() {
        return this.buildings.map((building) => ({
            code: building.kode,
            label: building.nama,
            total: this.reports.filter((report) => report.gedung === building.kode).length,
        }));
    },

    get buildingAxis() {
        const highest = Math.max(0, ...this.buildingTotals.map((entry) => entry.total));
        const rawMax = highest + 1;
        let step = 1;

        // Untuk nilai besar, pakai langkah bulat (2/5/10) agar sumbu tetap terbaca.
        if (rawMax > 8) {
            const magnitude = 10 ** Math.floor(Math.log10(rawMax / 5));
            const normalized = rawMax / 5 / magnitude;
            const factor = normalized <= 1.5 ? 1 : normalized <= 3 ? 2 : normalized <= 7 ? 5 : 10;
            step = Math.max(1, Math.round(factor * magnitude));
        }

        const max = Math.ceil(rawMax / step) * step;
        const ticks = [];

        for (let value = 0; value <= max; value += step) {
            ticks.push(value);
        }

        return { max, ticks };
    },

    get buildingBars() {
        const { max } = this.buildingAxis;

        return this.buildingTotals.map((entry) => ({
            ...entry,
            widthPct: max === 0 ? 0 : Math.round((entry.total / max) * 1000) / 10,
        }));
    },

    get categoryBars() {
        const entries = Object.entries(this.stats.perCategory)
            .map(([name, count]) => ({ name, count }))
            .sort((a, b) => b.count - a.count);

        const max = Math.max(1, ...entries.map((entry) => entry.count));

        return entries.map((entry) => ({
            ...entry,
            widthPct: Math.round((entry.count / max) * 100),
            sharePct: this.stats.total === 0
                ? 0
                : Math.round((entry.count / this.stats.total) * 100),
        }));
    },

    get locationCards() {
        return this.buildings.map((building) => {
            const items = this.reports.filter((report) => report.gedung === building.kode);
            const active = items.filter((report) => report.status !== 'Selesai');
            const urgent = [...active].sort((a, b) => b.nilai_urgensi - a.nilai_urgensi)[0] ?? null;

            const roomCounts = {};
            items.forEach((report) => {
                roomCounts[report.ruangan] = (roomCounts[report.ruangan] ?? 0) + 1;
            });

            const topRooms = Object.entries(roomCounts)
                .map(([name, count]) => ({ name, count }))
                .sort((a, b) => b.count - a.count || a.name.localeCompare(b.name, 'id'))
                .slice(0, 3);

            return {
                ...building,
                total: items.length,
                active: active.length,
                selesai: items.length - active.length,
                urgent,
                topRooms,
            };
        });
    },

    // --- Log aktivitas --------------------------------------------------------
    get filteredActivities() {
        if (!this.activityType) {
            return this.activities;
        }

        return this.activities.filter((activity) => activity.type === this.activityType);
    },

    get activityGroups() {
        const groups = [];

        this.filteredActivities.forEach((activity) => {
            const label = formatLongDate(activity.at);
            let group = groups.find((item) => item.label === label);

            if (!group) {
                group = { label, items: [] };
                groups.push(group);
            }

            group.items.push(activity);
        });

        return groups;
    },

    activityLabel(type) {
        return this.activityTypes.find((item) => item.value === type)?.label ?? 'Aktivitas';
    },

    activityIcon(type) {
        if (type === 'status') {
            return 'status';
        }

        if (type === 'tindakan') {
            return 'tindakan';
        }

        return 'baru';
    },

    countActivitiesOfType(type) {
        if (!type) {
            return this.activities.length;
        }

        return this.activities.filter((activity) => activity.type === type).length;
    },
});

Alpine.start();
