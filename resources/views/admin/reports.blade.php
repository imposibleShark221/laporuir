@extends('layouts.admin')

@section('content')
    <div class="space-y-5">
        <section class="admin-card" aria-labelledby="filter-heading">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 id="filter-heading" class="admin-card__title">Cari dan filter laporan</h2>
                    <p class="admin-card__hint">Filter berlaku langsung. Tekan tombol hapus pada chip untuk membatalkan satu filter.</p>
                </div>
                <button
                    type="button"
                    class="admin-btn admin-btn--quiet admin-btn--small"
                    x-show="hasFilters"
                    x-cloak
                    x-on:click="resetFilters()"
                >
                    Reset filter
                </button>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <div class="sm:col-span-2 xl:col-span-2">
                    <label for="report-search" class="field-label">Cari laporan</label>
                    <input
                        id="report-search"
                        type="search"
                        class="field-control mt-1.5"
                        placeholder="Fasilitas, ruangan, kategori, atau ID"
                        x-model="filters.q"
                        x-on:input="resetPage()"
                        autocomplete="off"
                    >
                </div>

                <div>
                    <label for="filter-building" class="field-label">Gedung</label>
                    <select id="filter-building" class="field-control mt-1.5" x-model="filters.gedung" x-on:change="resetPage()">
                        <option value="">Semua gedung</option>
                        <template x-for="building in buildings" :key="building.kode">
                            <option x-bind:value="building.kode" x-text="building.nama"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label for="filter-category" class="field-label">Kategori</label>
                    <select id="filter-category" class="field-control mt-1.5" x-model="filters.kategori" x-on:change="resetPage()">
                        <option value="">Semua kategori</option>
                        <template x-for="category in categories" :key="category">
                            <option x-bind:value="category" x-text="category"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label for="filter-status" class="field-label">Status</label>
                    <select id="filter-status" class="field-control mt-1.5" x-model="filters.status" x-on:change="resetPage()">
                        <option value="">Semua status</option>
                        <option value="Baru">Baru</option>
                        <option value="Diproses">Diproses</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>

                <div>
                    <label for="filter-sort" class="field-label">Urutkan</label>
                    <select id="filter-sort" class="field-control mt-1.5" x-model="filters.sort" x-on:change="resetPage()">
                        <option value="urgent">Urgensi tertinggi</option>
                        <option value="terbaru">Terbaru</option>
                        <option value="terlama">Terlama</option>
                    </select>
                </div>

                <div>
                    <label for="filter-from" class="field-label">Dari tanggal</label>
                    <input id="filter-from" type="date" class="field-control mt-1.5" x-model="filters.dari" x-on:input="resetPage()">
                </div>

                <div>
                    <label for="filter-to" class="field-label">Sampai tanggal</label>
                    <input id="filter-to" type="date" class="field-control mt-1.5" x-model="filters.sampai" x-on:input="resetPage()">
                </div>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-2" x-show="hasFilters" x-cloak aria-label="Filter aktif">
                <template x-for="chip in activeFilterChips" :key="chip.key">
                    <span class="admin-chip">
                        <span x-text="chip.label"></span>
                        <button
                            type="button"
                            class="admin-chip__remove"
                            x-on:click="clearFilter(chip.key)"
                            x-bind:aria-label="`Hapus filter ${chip.label}`"
                        >
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"></path>
                            </svg>
                        </button>
                    </span>
                </template>
            </div>
        </section>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <p id="reports-result-summary" class="text-sm text-stone-600 dark:text-stone-400" tabindex="-1" aria-live="polite">
                <span class="font-semibold text-ft-ink dark:text-stone-100" x-text="resultRange"></span>
            </p>
            <p class="text-xs text-stone-500 dark:text-stone-400 lg:hidden">
                Geser tabel ke samping untuk melihat semua kolom.
            </p>
        </div>

        <div class="admin-bulk-bar" x-show="selectedIds.length > 0" x-cloak>
            <p class="text-sm font-semibold">
                <span x-text="selectedIds.length"></span> laporan dipilih
            </p>
            <div class="flex flex-wrap gap-2">
                <button type="button" class="admin-btn admin-btn--small admin-btn--primary" x-on:click="openBulkConfirm('Diproses')">
                    Tandai Diproses
                </button>
                <button type="button" class="admin-btn admin-btn--small admin-btn--primary" x-on:click="openBulkConfirm('Selesai')">
                    Tandai Selesai
                </button>
                <button type="button" class="admin-btn admin-btn--small admin-btn--quiet" x-on:click="clearSelection()">
                    Batalkan pilihan
                </button>
            </div>
        </div>

        <section class="admin-card admin-card--flush" aria-labelledby="table-heading">
            <h2 id="table-heading" class="sr-only">Tabel semua laporan</h2>

            <div class="admin-table-scroll">
                <table class="admin-table">
                    <caption class="sr-only">Daftar seluruh laporan fasilitas FT UIR beserta status dan tindakannya</caption>
                    <thead>
                        <tr>
                            <th scope="col" class="admin-table__check">
                                <input
                                    type="checkbox"
                                    class="admin-checkbox"
                                    x-bind:checked="allOnPageSelected"
                                    x-on:change="toggleAllOnPage()"
                                    aria-label="Pilih semua laporan pada halaman ini"
                                >
                            </th>
                            <th scope="col"><span class="sr-only">Foto</span></th>
                            <th scope="col">Fasilitas</th>
                            <th scope="col">Lokasi</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Urgensi</th>
                            <th scope="col">Status</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="report in pagedReports" :key="report.id">
                            <tr
                                class="admin-table__row"
                                x-bind:data-selected="isSelected(report.id) ? 'true' : 'false'"
                                x-on:click="openDetail(report.id)"
                            >
                                <td class="admin-table__check" x-on:click.stop>
                                    <input
                                        type="checkbox"
                                        class="admin-checkbox"
                                        x-bind:checked="isSelected(report.id)"
                                        x-on:change="toggleSelect(report.id)"
                                        x-bind:aria-label="`Pilih ${report.fasilitas} ${report.ruangan}`"
                                    >
                                </td>
                                <td>
                                    <template x-if="report.foto">
                                        <img class="report-thumb" x-bind:src="report.foto" x-bind:alt="`Foto ${report.fasilitas} di ${report.ruangan}`" loading="lazy">
                                    </template>
                                    <div x-show="!report.foto" class="report-thumb report-thumb--empty" aria-hidden="true">
                                        <span x-text="report.fasilitas.slice(0, 1)"></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="block font-semibold text-ft-ink dark:text-stone-100" x-text="report.fasilitas"></span>
                                    <span class="mt-0.5 block text-xs text-stone-500 dark:text-stone-400" x-text="report.id"></span>
                                </td>
                                <td>
                                    <span class="block" x-text="report.ruangan"></span>
                                    <span class="mt-0.5 block text-xs text-stone-500 dark:text-stone-400">
                                        <span x-text="buildingName(report.gedung)"></span>
                                        · <span x-text="report.lantai"></span>
                                    </span>
                                </td>
                                <td class="text-sm" x-text="report.kategori"></td>
                                <td>
                                    <div class="urgency">
                                        <span class="urgency__value" x-text="report.nilai_urgensi"></span>
                                        <span class="urgency__track" aria-hidden="true">
                                            <span
                                                class="urgency__fill"
                                                x-bind:data-tone="urgencyLevel(report.nilai_urgensi).tone"
                                                x-bind:style="`width: ${urgencyWidth(report.nilai_urgensi)}`"
                                            ></span>
                                        </span>
                                        <span class="urgency__label" x-text="urgencyLevel(report.nilai_urgensi).label"></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge" x-bind:data-status="report.status" x-text="report.status"></span>
                                </td>
                                <td class="whitespace-nowrap text-sm" x-text="formatDate(report.dibuat_pada)"></td>
                                <td x-on:click.stop>
                                    <button
                                        type="button"
                                        class="admin-btn admin-btn--small admin-btn--quiet"
                                        x-on:click="openDetail(report.id)"
                                        x-bind:aria-label="`Buka detail ${report.fasilitas} ${report.ruangan}`"
                                    >
                                        Buka
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="admin-empty" x-show="reports.length === 0" x-cloak>
                <h3 class="font-serif text-xl text-ft-green dark:text-emerald-200">Belum ada laporan</h3>
                <p class="mx-auto mt-2 max-w-md text-sm text-stone-600 dark:text-stone-400">
                    Laporan dari pengguna akan muncul di sini setelah dikirim.
                </p>
            </div>

            <div class="admin-empty" x-show="reports.length > 0 && filteredReports.length === 0" x-cloak>
                <h3 class="font-serif text-xl text-ft-green dark:text-emerald-200">Tidak ada laporan yang cocok</h3>
                <p class="mx-auto mt-2 max-w-md text-sm text-stone-600 dark:text-stone-400">
                    Coba hapus sebagian filter atau ubah kata kunci pencarian.
                </p>
                <button type="button" class="admin-btn admin-btn--primary mt-4" x-on:click="resetFilters()">
                    Reset filter
                </button>
            </div>
        </section>

        <nav class="flex flex-wrap items-center justify-between gap-3" aria-label="Paginasi laporan" x-show="filteredReports.length > 0" x-cloak>
            <p class="text-sm text-stone-600 dark:text-stone-400" x-text="resultRange"></p>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    class="admin-btn admin-btn--small admin-btn--quiet"
                    x-on:click="goToPage(pageIndex - 1)"
                    x-bind:disabled="pageIndex <= 1"
                >
                    Sebelumnya
                </button>
                <p class="px-2 text-sm font-medium" aria-live="polite">
                    Halaman <span x-text="pageIndex"></span> dari <span x-text="pageCount"></span>
                </p>
                <button
                    type="button"
                    class="admin-btn admin-btn--small admin-btn--quiet"
                    x-on:click="goToPage(pageIndex + 1)"
                    x-bind:disabled="pageIndex >= pageCount"
                >
                    Berikutnya
                </button>
            </div>
        </nav>
    </div>
@endsection
