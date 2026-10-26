import Alpine from 'alpinejs';
import {
    createReport,
    getActiveReportCounts,
    getActiveReportsByRoomAndFacility,
    getReportsByLocation,
    hasVoted,
    voteUrgency,
} from './lib/reports.js';

const MAX_PHOTO_BYTES = 5 * 1024 * 1024;
const ALLOWED_PHOTO_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
const MAX_PHOTO_SIDE = 1280;
const PHOTO_QUALITY = 0.8;

function emptyReportForm() {
    return {
        gedungNama: '',
        lantai: '',
        ruangan: '',
        kategori: '',
        fasilitas: '',
        deskripsi: '',
        foto: null,
        fotoPreview: '',
        fotoName: '',
    };
}

/**
 * Kecilkan foto di sisi klien memakai canvas sebelum disimpan di state.
 * Sisi terpanjang maksimal 1280 px dan hasilnya berupa JPEG kualitas 0.8.
 * @param {File} file
 * @returns {Promise<string>} data URL hasil pengecilan
 */
function resizePhoto(file) {
    return new Promise((resolve, reject) => {
        const objectUrl = URL.createObjectURL(file);
        const image = new Image();

        image.onload = () => {
            URL.revokeObjectURL(objectUrl);

            const scale = Math.min(1, MAX_PHOTO_SIDE / Math.max(image.width, image.height));
            const width = Math.max(1, Math.round(image.width * scale));
            const height = Math.max(1, Math.round(image.height * scale));
            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;

            const context = canvas.getContext('2d');

            if (!context) {
                reject(new Error('Canvas tidak tersedia'));
                return;
            }

            // Latar putih agar PNG transparan tidak menjadi hitam saat diubah ke JPEG.
            context.fillStyle = '#ffffff';
            context.fillRect(0, 0, width, height);
            context.drawImage(image, 0, 0, width, height);

            resolve(canvas.toDataURL('image/jpeg', PHOTO_QUALITY));
        };

        image.onerror = () => {
            URL.revokeObjectURL(objectUrl);
            reject(new Error('Foto tidak bisa dibaca'));
        };

        image.src = objectUrl;
    });
}

window.homePage = (config = {}) => ({
    selected: null,
    selectedName: '',
    reports: [],
    reportCounts: getActiveReportCounts(),
    categories: (config.categories ?? []).map((category) => category.nama),
    formConfig: config,
    statusFilter: '',
    categoryFilter: '',
    sortBy: 'urgent',
    notice: '',
    voteNotice: '',
    submitNotice: '',
    submitNoticeTimer: null,
    isDark: document.documentElement.classList.contains('dark'),

    formOpen: false,
    form: emptyReportForm(),
    formErrors: {},
    submitting: false,
    lastFocused: null,

    get activeReportCount() {
        return this.reports.filter((report) => report.status !== 'Selesai').length;
    },

    get visibleReports() {
        let items = this.reports.filter((report) => {
            const matchStatus = !this.statusFilter || report.status === this.statusFilter;
            const matchCategory = !this.categoryFilter || report.kategori === this.categoryFilter;

            return matchStatus && matchCategory;
        });

        items = [...items];

        if (this.sortBy === 'latest') {
            items.sort((a, b) => new Date(b.dibuat_pada) - new Date(a.dibuat_pada));
        } else {
            items.sort((a, b) => {
                const urgency = b.nilai_urgensi - a.nilai_urgensi;

                return urgency !== 0 ? urgency : new Date(b.dibuat_pada) - new Date(a.dibuat_pada);
            });
        }

        return items;
    },

    get resultSummary() {
        if (!this.selected) {
            return '';
        }

        return `${this.visibleReports.length} laporan ditampilkan untuk ${this.selectedName}.`;
    },

    get floorOptions() {
        const building = (this.formConfig.buildings ?? [])
            .find((item) => item.kode === this.selected);

        return building?.floors ?? [];
    },

    get roomOptions() {
        const floor = this.floorOptions.find((item) => item.nama === this.form.lantai);

        return floor?.rooms ?? [];
    },

    get facilityOptions() {
        const category = (this.formConfig.categories ?? [])
            .find((item) => item.nama === this.form.kategori);

        return category?.facilities ?? [];
    },

    get duplicateReports() {
        if (!this.form.ruangan || !this.form.fasilitas) {
            return [];
        }

        return getActiveReportsByRoomAndFacility(
            this.form.ruangan,
            this.form.fasilitas,
            this.selected,
        );
    },

    select(code, name) {
        this.selected = code;
        this.selectedName = name;
        this.reports = getReportsByLocation(code);
        this.statusFilter = '';
        this.categoryFilter = '';
        this.notice = '';
        this.voteNotice = '';
        this.hideSubmitNotice();
    },

    requestReport() {
        if (!this.selected) {
            this.notice = 'Pilih lokasi di peta dulu';
            return;
        }

        this.openReportForm();
    },

    openReportForm() {
        if (!this.selected || this.formOpen) {
            return;
        }

        this.notice = '';
        this.lastFocused = document.activeElement;
        this.form = {
            ...emptyReportForm(),
            gedungNama: this.selectedName,
        };
        this.formErrors = {};
        this.submitting = false;
        this.formOpen = true;

        const photoInput = document.getElementById('report-photo');

        if (photoInput) {
            photoInput.value = '';
        }

        this.$nextTick(() => {
            this.focusWhenReady('report-floor');
        });
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

    closeForm(restoreFocus = true) {
        const trigger = this.lastFocused;

        this.formOpen = false;
        this.form = emptyReportForm();
        this.formErrors = {};
        this.submitting = false;
        this.lastFocused = null;

        const photoInput = document.getElementById('report-photo');

        if (photoInput) {
            photoInput.value = '';
        }

        if (restoreFocus && trigger instanceof HTMLElement && trigger.isConnected) {
            this.$nextTick(() => trigger.focus({ preventScroll: true }));
        }
    },

    trapFocus(event) {
        const dialog = this.$refs.reportDialog;

        if (!dialog) {
            return;
        }

        const focusable = [...dialog.querySelectorAll(
            'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])',
        )].filter((element) => element.getClientRects().length > 0);

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

    onFloorChange() {
        this.form.ruangan = '';
        this.clearFormError('lantai');
        this.clearFormError('ruangan');
    },

    onCategoryChange() {
        this.form.fasilitas = '';
        this.clearFormError('kategori');
        this.clearFormError('fasilitas');
    },

    clearFormError(key) {
        if (!this.formErrors[key]) {
            return;
        }

        const nextErrors = { ...this.formErrors };
        delete nextErrors[key];
        this.formErrors = nextErrors;
    },

    async handlePhoto(event) {
        const input = event.target;
        const file = input.files?.[0];

        this.clearFormError('foto');

        if (!file) {
            return;
        }

        if (!ALLOWED_PHOTO_TYPES.includes(file.type)) {
            this.formErrors = {
                ...this.formErrors,
                foto: 'Pilih foto berformat JPG, PNG, atau WEBP.',
            };
            this.resetPhotoState();
            input.value = '';
            return;
        }

        if (file.size > MAX_PHOTO_BYTES) {
            this.formErrors = {
                ...this.formErrors,
                foto: 'Ukuran foto maksimal 5 MB sebelum dikecilkan.',
            };
            this.resetPhotoState();
            input.value = '';
            return;
        }

        try {
            const dataUrl = await resizePhoto(file);

            this.form.foto = dataUrl;
            this.form.fotoPreview = dataUrl;
            this.form.fotoName = file.name;
        } catch (error) {
            this.formErrors = {
                ...this.formErrors,
                foto: 'Foto tidak bisa diproses. Coba pilih foto lain.',
            };
            this.resetPhotoState();
            input.value = '';
        }
    },

    resetPhotoState() {
        this.form.foto = null;
        this.form.fotoPreview = '';
        this.form.fotoName = '';
    },

    submitReport() {
        if (this.submitting) {
            return;
        }

        const errors = {};

        if (!this.form.lantai) {
            errors.lantai = 'Pilih lantai lokasi.';
        }

        if (!this.form.ruangan) {
            errors.ruangan = 'Pilih ruangan.';
        }

        if (!this.form.kategori) {
            errors.kategori = 'Pilih kategori fasilitas.';
        }

        if (!this.form.fasilitas) {
            errors.fasilitas = 'Pilih fasilitas yang bermasalah.';
        }

        if (!this.form.foto) {
            errors.foto = 'Unggah foto kerusakan atau kekurangan fasilitas';
        }

        this.formErrors = errors;

        const fieldOrder = ['lantai', 'ruangan', 'kategori', 'fasilitas', 'foto'];
        const focusTargets = {
            lantai: 'report-floor',
            ruangan: 'report-room',
            kategori: 'report-category',
            fasilitas: 'report-facility',
            foto: 'report-photo',
        };
        const firstInvalid = fieldOrder.find((field) => errors[field]);

        if (firstInvalid) {
            this.$nextTick(() => {
                this.focusWhenReady(focusTargets[firstInvalid]);
            });
            return;
        }

        // Cegah klik ganda selama proses pengiriman.
        this.submitting = true;

        const created = createReport({
            gedung: this.selected,
            lantai: this.form.lantai,
            ruangan: this.form.ruangan,
            kategori: this.form.kategori,
            fasilitas: this.form.fasilitas,
            deskripsi: this.form.deskripsi.trim(),
            foto: this.form.foto,
        });

        this.reports = getReportsByLocation(this.selected);
        this.reportCounts = getActiveReportCounts();
        this.statusFilter = '';
        this.categoryFilter = '';
        this.notice = '';
        this.voteNotice = '';
        this.closeForm(false);
        this.showSubmitNotice('Laporan terkirim');

        this.$nextTick(() => {
            document.getElementById(`report-${created.id}`)?.scrollIntoView({
                behavior: 'smooth',
                block: 'center',
            });
            this.focusWhenReady(`report-title-${created.id}`);
        });
    },

    goToDuplicate(report) {
        this.closeForm(false);
        this.statusFilter = '';
        this.categoryFilter = '';
        this.voteNotice = '';

        this.$nextTick(() => {
            document.getElementById(`report-${report.id}`)?.scrollIntoView({
                behavior: 'smooth',
                block: 'center',
            });
            this.focusWhenReady(`vote-${report.id}`);
        });
    },

    showSubmitNotice(message) {
        this.submitNotice = message;

        if (this.submitNoticeTimer) {
            window.clearTimeout(this.submitNoticeTimer);
        }

        this.submitNoticeTimer = window.setTimeout(() => {
            this.submitNotice = '';
            this.submitNoticeTimer = null;
        }, 6000);
    },

    hideSubmitNotice() {
        if (this.submitNoticeTimer) {
            window.clearTimeout(this.submitNoticeTimer);
            this.submitNoticeTimer = null;
        }

        this.submitNotice = '';
    },

    isVoteDisabled(report) {
        return report.status === 'Selesai' || hasVoted(report.id);
    },

    hasReportVoted(report) {
        return hasVoted(report.id);
    },

    voteButtonLabel(report) {
        if (report.status === 'Selesai') {
            return 'Laporan selesai';
        }

        return hasVoted(report.id) ? 'Sudah dinilai' : 'Ini juga urgent';
    },

    voteButtonAria(report) {
        const target = `${report.fasilitas} di ${report.ruangan}`;

        if (report.status === 'Selesai') {
            return `Laporan selesai untuk ${target}; tidak dapat dinilai`;
        }

        return hasVoted(report.id)
            ? `Sudah dinilai untuk ${target}`
            : `Ini juga urgent untuk ${target}`;
    },

    vote(report) {
        if (this.isVoteDisabled(report)) {
            return;
        }

        const updated = voteUrgency(report.id);

        if (!updated) {
            return;
        }

        // Muat ulang dari modul agar nilai yang ditampilkan sama dengan data di modul.
        this.reports = getReportsByLocation(this.selected);
        this.notice = '';
        this.voteNotice = `Terima kasih. Nilai urgensi ${updated.fasilitas} di ${updated.ruangan} sekarang ${updated.nilai_urgensi}.`;

        // Kembalikan fokus ke tombol yang sama setelah daftar diurutkan ulang.
        window.requestAnimationFrame(() => {
            document.getElementById(`vote-${report.id}`)?.focus({ preventScroll: true });
        });
    },

    formatDate(value) {
        const date = new Date(value);

        if (Number.isNaN(date.getTime())) {
            return value;
        }

        return new Intl.DateTimeFormat('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        }).format(date);
    },

    toggleTheme() {
        this.isDark = document.documentElement.classList.toggle('dark');

        try {
            localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
        } catch (error) {
            // Mode tetap berubah untuk sesi ini meskipun preferensi gagal disimpan.
        }
    },
});

window.Alpine = Alpine;
Alpine.start();
