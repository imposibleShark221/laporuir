{{-- Panel detail laporan. Dipakai semua halaman admin agar tombol "Buka" konsisten. --}}
<div
    x-cloak
    x-show="drawerOpen"
    class="admin-drawer-backdrop"
    x-on:click="closeDetail()"
    aria-hidden="true"
></div>

<aside
    x-cloak
    x-show="drawerOpen"
    x-ref="detailPanel"
    class="admin-drawer"
    role="dialog"
    aria-modal="true"
    aria-labelledby="detail-title"
    x-on:keydown.tab="trapFocus($event, 'detailPanel')"
    x-on:keydown.escape="closeDetail()"
>
    <div class="admin-drawer__head">
        <div class="min-w-0">
            <p class="text-[0.62rem] font-semibold uppercase tracking-[0.18em] text-stone-500 dark:text-stone-400">
                Detail laporan
            </p>
            <h2 id="detail-title" class="mt-1 truncate font-serif text-xl text-ft-green dark:text-emerald-200" x-text="detail ? `${detail.fasilitas} · ${detail.ruangan}` : 'Detail laporan'"></h2>
            <p class="mt-1 text-xs text-stone-500 dark:text-stone-400">
                <span x-text="detail?.id"></span>
                <span aria-hidden="true"> · </span>
                <span x-text="detail ? formatDateTime(detail.dibuat_pada) : ''"></span>
            </p>
        </div>

        <button type="button" id="detail-close" class="admin-icon-btn shrink-0" x-on:click="closeDetail()" aria-label="Tutup detail laporan">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"></path>
            </svg>
        </button>
    </div>

    <div class="admin-drawer__body" x-show="detail">
        <div class="admin-drawer__photo">
            <template x-if="detail?.foto">
                <img x-bind:src="detail.foto" x-bind:alt="`Foto ${detail.fasilitas} di ${detail.ruangan}`">
            </template>
            <div x-show="!detail?.foto" class="admin-photo-placeholder">
                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                    <circle cx="9" cy="10" r="1.6"></circle>
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4 17 5-4 3 2 3-3 5 5"></path>
                </svg>
                <span>Foto belum tersedia pada data contoh</span>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-2">
            <span class="status-badge" x-bind:data-status="detail?.status" x-text="detail?.status"></span>
            <span class="admin-meta-pill" x-text="detail ? `${buildingName(detail.gedung)} · ${detail.lantai}` : ''"></span>
            <span class="admin-meta-pill" x-text="detail ? `${voteCount(detail)} vote urgensi` : ''"></span>
        </div>

        <dl class="admin-detail-grid mt-5">
            <div>
                <dt>Gedung</dt>
                <dd x-text="detail ? buildingName(detail.gedung) : ''"></dd>
            </div>
            <div>
                <dt>Lantai</dt>
                <dd x-text="detail?.lantai"></dd>
            </div>
            <div>
                <dt>Ruangan</dt>
                <dd x-text="detail?.ruangan"></dd>
            </div>
            <div>
                <dt>Kategori</dt>
                <dd x-text="detail?.kategori"></dd>
            </div>
            <div>
                <dt>Fasilitas</dt>
                <dd x-text="detail?.fasilitas"></dd>
            </div>
            <div>
                <dt>Nilai urgensi</dt>
                <dd>
                    <span x-text="detail?.nilai_urgensi"></span>
                    <span class="text-stone-500 dark:text-stone-400">
                        (<span x-text="detail ? urgencyLevel(detail.nilai_urgensi).label : ''"></span>)
                    </span>
                </dd>
            </div>
            <div class="sm:col-span-2">
                <dt>Deskripsi pelapor</dt>
                <dd class="whitespace-pre-line" x-text="detail?.deskripsi || 'Tidak ada deskripsi.'"></dd>
            </div>
            <div class="sm:col-span-2">
                <dt>Tindakan yang sudah tercatat</dt>
                <dd class="whitespace-pre-line" x-text="detail?.tindakan_admin || 'Belum ada tindakan yang dicatat.'"></dd>
            </div>
        </dl>

        <div class="mt-6 border-t border-stone-200 pt-5 dark:border-stone-800">
            <p class="field-label">Ubah status</p>
            <div class="admin-segment mt-2" role="group" aria-label="Ubah status laporan">
                <button
                    type="button"
                    class="admin-segment__btn"
                    x-bind:aria-pressed="draft.status === 'Baru' ? 'true' : 'false'"
                    x-bind:data-active="draft.status === 'Baru' ? 'true' : 'false'"
                    x-on:click="draft.status = 'Baru'"
                >Baru</button>
                <button
                    type="button"
                    class="admin-segment__btn"
                    x-bind:aria-pressed="draft.status === 'Diproses' ? 'true' : 'false'"
                    x-bind:data-active="draft.status === 'Diproses' ? 'true' : 'false'"
                    x-on:click="draft.status = 'Diproses'"
                >Diproses</button>
                <button
                    type="button"
                    class="admin-segment__btn"
                    x-bind:aria-pressed="draft.status === 'Selesai' ? 'true' : 'false'"
                    x-bind:data-active="draft.status === 'Selesai' ? 'true' : 'false'"
                    x-on:click="draft.status = 'Selesai'"
                >Selesai</button>
            </div>
            <p class="mt-2 text-xs text-stone-500 dark:text-stone-400">
                Status Baru, Diproses, dan Selesai hanya diubah oleh admin (staf TU).
            </p>
        </div>

        <div class="mt-6">
            <label for="detail-tindakan" class="field-label">Tindakan yang diambil</label>
            <p class="mt-1 text-xs text-stone-500 dark:text-stone-400">
                Tuliskan tindakan nyata yang sudah atau akan dilakukan. Teks ini tampil di sisi pengguna.
            </p>
            <textarea
                id="detail-tindakan"
                rows="5"
                maxlength="1000"
                class="field-control mt-1.5"
                x-model="draft.tindakan_admin"
                placeholder="Contoh: Teknisi mengecek unit pada 27 Oktober dan menunggu suku cadang."
            ></textarea>
            <div class="mt-2 flex flex-wrap gap-2">
                <template x-for="template in treatmentTemplates" :key="template">
                    <button
                        type="button"
                        class="admin-chip admin-chip--action"
                        x-on:click="applyTemplate(template)"
                        x-bind:aria-label="`Tambahkan templat: ${template}`"
                    >
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" d="M12 5v14M5 12h14"></path>
                        </svg>
                        <span x-text="template"></span>
                    </button>
                </template>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <button type="button" id="detail-save" class="admin-btn admin-btn--primary" x-on:click="saveDetail()">
                Simpan perubahan
            </button>
            <span class="text-xs text-stone-500 dark:text-stone-400" x-show="detailChanged" x-cloak>
                Ada perubahan yang belum disimpan.
            </span>
        </div>

        <div class="mt-6 border-t border-stone-200 pt-5 dark:border-stone-800">
            <h3 class="admin-card__title">Riwayat singkat</h3>
            <ol class="mt-3 space-y-3">
                <template x-for="item in detailHistory" :key="item.id">
                    <li class="flex gap-3 text-sm">
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-ft-green dark:bg-emerald-400" aria-hidden="true"></span>
                        <span>
                            <span class="block" x-text="item.text"></span>
                            <span class="block text-xs text-stone-500 dark:text-stone-400" x-text="formatDateTime(item.at)"></span>
                        </span>
                    </li>
                </template>
            </ol>
            <p x-show="detailHistory.length === 0" x-cloak class="mt-3 text-sm text-stone-500 dark:text-stone-400">
                Belum ada riwayat perubahan untuk laporan ini.
            </p>
        </div>
    </div>
</aside>

{{-- Dialog konfirmasi aksi massal. --}}
<div x-cloak x-show="confirmOpen" class="admin-modal-backdrop" aria-hidden="true"></div>

<div
    x-cloak
    x-show="confirmOpen"
    x-ref="confirmDialog"
    class="admin-modal"
    role="alertdialog"
    aria-modal="true"
    aria-labelledby="confirm-title"
    aria-describedby="confirm-desc"
    x-on:keydown.tab="trapFocus($event, 'confirmDialog')"
    x-on:keydown.escape="closeBulkConfirm()"
>
    <h2 id="confirm-title" class="font-serif text-xl text-ft-green dark:text-emerald-200">
        Konfirmasi perubahan status
    </h2>
    <p id="confirm-desc" class="mt-2 text-sm text-stone-600 dark:text-stone-400" x-text="confirmMessage"></p>

    <div class="mt-5 flex flex-wrap justify-end gap-2">
        <button type="button" id="confirm-cancel" class="admin-btn admin-btn--quiet" x-on:click="closeBulkConfirm()">
            Batal
        </button>
        <button type="button" class="admin-btn admin-btn--primary" x-on:click="runBulkUpdate()">
            <span x-text="`Ya, tandai ${confirmStatus}`"></span>
        </button>
    </div>
</div>
