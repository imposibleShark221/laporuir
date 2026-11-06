<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Pelaporan fasilitas Fakultas Teknik Universitas Islam Riau.">
    <meta name="theme-color" content="#0F5B3A">
    <title>{{ config('app.name', 'Lapor Fasilitas FT UIR') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    class="page-home min-h-screen bg-ft-paper font-sans text-ft-ink antialiased dark:bg-stone-950 dark:text-stone-100"
    x-data="homePage(@js($formConfig))"
    x-effect="document.body.style.overflow = formOpen ? 'hidden' : ''"
>
    <header class="bg-ft-green text-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <div class="flex min-w-0 items-center gap-3">
                <img
                    src="{{ asset('images/logo-uir.png') }}"
                    alt="Logo Universitas Islam Riau"
                    width="85"
                    height="98"
                    class="h-11 w-auto shrink-0 sm:h-12"
                >
                <h1 class="min-w-0 text-sm font-semibold leading-snug tracking-tight sm:text-base">
                    Lapor Fasilitas Fakultas Teknik UIR
                </h1>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        <div class="mb-6 sm:mb-8">
            <h2 class="font-serif text-[clamp(2rem,5vw,3.25rem)] font-bold leading-[1.02] tracking-[-0.03em] text-ft-green dark:text-emerald-300">
                Pilih lokasi
            </h2>
            <p class="mt-3 max-w-2xl text-base leading-relaxed text-stone-600 dark:text-stone-400">
                Pilih gedung atau area untuk melihat laporan yang sudah ada.
            </p>
        </div>

        <section aria-labelledby="map-heading">
            <h2 id="map-heading" class="sr-only">Lokasi FT UIR</h2>

            <div class="grid grid-cols-2 gap-2 sm:gap-3">
                @foreach ($areas as $area)
                    @php($isArea = $area['building_id'] === 'AREA-FT')
                    <button
                        type="button"
                        class="location-card group relative min-h-11 overflow-hidden text-left {{ $isArea ? 'col-span-2 aspect-[16/5]' : 'aspect-[4/3]' }}"
                        aria-label="Pilih {{ $area['label'] }}"
                        x-bind:aria-label="'Pilih ' + @js($area['label']) + ', ' + (reportCounts[@js($area['kode'])] ?? 0) + ' laporan aktif'"
                        x-bind:aria-pressed="selected === @js($area['kode'])"
                        x-bind:data-selected="selected === @js($area['kode']) ? 'true' : 'false'"
                        x-on:click="select(@js($area['kode']), @js($area['label']))"
                    >
                        @if ($area['image'])
                            <img
                                src="{{ $area['image'] }}"
                                alt=""
                                loading="lazy"
                                class="location-card__photo absolute inset-0 h-full w-full object-cover"
                                style="object-position: {{ $area['foto_posisi'] }};"
                                aria-hidden="true"
                            >
                        @else
                            <span class="location-card__fallback absolute inset-0" aria-hidden="true"></span>
                        @endif

                        <span class="absolute inset-0" style="background-color: rgba(15, 91, 58, 0.18);" aria-hidden="true"></span>
                        <span class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent" aria-hidden="true"></span>

                        <span
                            class="absolute left-2 top-2 bg-ft-brick px-2 py-1 text-[13px] font-semibold leading-none text-white sm:left-3 sm:top-3"
                            x-show="selected === @js($area['kode'])"
                            x-cloak
                        >
                            Dipilih
                        </span>

                        <span class="absolute right-2 top-2 bg-emerald-50 px-2 py-1.5 text-right text-ft-green sm:right-3 sm:top-3">
                            <span class="block" x-show="(reportCounts[@js($area['kode'])] ?? 0) > 0">
                                <span class="block text-2xl font-bold leading-none sm:text-3xl" x-text="reportCounts[@js($area['kode'])] ?? 0">0</span>
                                <span class="mt-1 block text-[13px] leading-none">laporan aktif</span>
                            </span>
                            <span
                                class="block text-[13px] font-semibold leading-tight"
                                x-show="(reportCounts[@js($area['kode'])] ?? 0) === 0"
                                x-cloak
                            >
                                Tidak ada laporan aktif
                            </span>
                        </span>

                        <span class="absolute inset-x-0 bottom-0 p-2 sm:p-4">
                            <span class="block font-serif font-extrabold leading-[0.95] tracking-[-0.02em] text-white {{ $isArea ? 'text-[clamp(1.25rem,4vw,2rem)]' : 'text-[clamp(1.5rem,5vw,2.75rem)]' }}">
                                {{ $area['label'] }}
                            </span>
                        </span>
                    </button>
                @endforeach
            </div>

            <p class="mt-3 text-right text-[13px] text-stone-600 dark:text-stone-400" aria-live="polite">
                <span class="font-semibold">Lokasi terpilih:</span>
                <span x-text="selectedName || 'Belum ada'">Belum ada</span>
            </p>
        </section>

        <section
            x-show="selected"
            x-cloak
            aria-labelledby="reports-heading"
            class="mt-8"
        >
            <div class="rounded-[2rem] border border-ft-green/15 bg-white/60 p-4 shadow-sm dark:border-white/10 dark:bg-stone-900/50 sm:p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-[13px] font-semibold tracking-[0.12em] text-ft-green/80 dark:text-emerald-300/90">
                            Daftar laporan
                        </p>
                        <h2 id="reports-heading" class="mt-1 font-serif text-2xl text-ft-green dark:text-emerald-300 sm:text-3xl">
                            Laporan di <span x-text="selectedName"></span>
                        </h2>
                        <p class="mt-1 text-sm text-stone-600 dark:text-stone-400">
                            <span x-text="reports.length"></span> laporan
                            <span aria-hidden="true">·</span>
                            <span class="font-semibold" x-text="activeReportCount"></span> aktif
                            <span class="text-[13px] text-stone-600 dark:text-stone-400">(status Baru atau Diproses)</span>
                        </p>
                    </div>

                    <button
                        type="button"
                        class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-full bg-ft-green px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-ft-green-deep focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-ft-brick/40 sm:w-auto"
                        x-on:click="openReportForm()"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"></path>
                        </svg>
                        Laporkan di sini
                    </button>
                </div>

                <div class="mt-5 grid gap-3 rounded-2xl border border-stone-200/80 bg-stone-50/80 p-3 dark:border-white/10 dark:bg-stone-950/40 sm:grid-cols-3">
                    <div>
                        <label for="report-status-filter" class="text-[13px] font-semibold text-stone-700 dark:text-stone-300">
                            Status
                        </label>
                        <select
                            id="report-status-filter"
                            x-model="statusFilter"
                            class="mt-1 min-h-11 w-full rounded-xl border border-stone-300 bg-white px-3 py-2 text-sm text-ft-ink shadow-sm focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-amber-300/70 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-100"
                        >
                            <option value="">Semua status</option>
                            <option value="Baru">Baru</option>
                            <option value="Diproses">Diproses</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>

                    <div>
                        <label for="report-category-filter" class="text-[13px] font-semibold text-stone-700 dark:text-stone-300">
                            Kategori
                        </label>
                        <select
                            id="report-category-filter"
                            x-model="categoryFilter"
                            class="mt-1 min-h-11 w-full rounded-xl border border-stone-300 bg-white px-3 py-2 text-sm text-ft-ink shadow-sm focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-amber-300/70 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-100"
                        >
                            <option value="">Semua kategori</option>
                            <template x-for="category in categories" :key="category">
                                <option :value="category" x-text="category"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label for="report-sort" class="text-[13px] font-semibold text-stone-700 dark:text-stone-300">
                            Urutkan
                        </label>
                        <select
                            id="report-sort"
                            x-model="sortBy"
                            class="mt-1 min-h-11 w-full rounded-xl border border-stone-300 bg-white px-3 py-2 text-sm text-ft-ink shadow-sm focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-amber-300/70 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-100"
                        >
                            <option value="urgent">Paling urgent</option>
                            <option value="latest">Terbaru</option>
                        </select>
                    </div>
                </div>

                <p class="sr-only" aria-live="polite" x-text="resultSummary"></p>

                <div
                    x-show="voteNotice"
                    x-cloak
                    role="status"
                    aria-live="polite"
                    class="mt-4 flex items-start gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-50 px-4 py-3 text-sm text-emerald-950 shadow-sm dark:border-emerald-400/25 dark:bg-emerald-950/40 dark:text-emerald-100"
                >
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"></path>
                    </svg>
                    <p x-text="voteNotice"></p>
                </div>

                <template x-if="visibleReports.length === 0">
                    <div class="mt-5 rounded-2xl border-2 border-dashed border-stone-300 px-5 py-10 text-center dark:border-stone-700">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-stone-100 text-stone-500 dark:bg-stone-800 dark:text-stone-400" aria-hidden="true">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-6 4h4M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"></path>
                            </svg>
                        </div>
                        <h3
                            class="mt-3 font-serif text-xl text-ft-green dark:text-emerald-300"
                            x-text="reports.length === 0 ? 'Belum ada laporan di ' + selectedName : 'Tidak ada laporan yang cocok'"
                        ></h3>
                        <p
                            class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-stone-600 dark:text-stone-400"
                            x-text="reports.length === 0 ? 'Jika kamu menemukan kerusakan atau kekurangan fasilitas di sini, buat laporan agar bisa ditindaklanjuti.' : 'Coba ubah filter status atau kategori untuk melihat laporan lain di lokasi ini.'"
                        ></p>
                        <div class="mt-5 flex flex-wrap justify-center gap-2">
                            <button
                                type="button"
                                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-full bg-ft-green px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-ft-green-deep focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-ft-brick/40"
                                x-on:click="openReportForm()"
                            >
                                Laporkan di sini
                            </button>
                            <button
                                type="button"
                                x-show="reports.length > 0"
                                class="inline-flex min-h-11 items-center justify-center rounded-full border border-stone-300 bg-white px-4 py-2.5 text-sm font-semibold text-stone-700 shadow-sm transition hover:bg-stone-50 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-amber-300/70 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-stone-800"
                                x-on:click="statusFilter = ''; categoryFilter = ''"
                            >
                                Reset filter
                            </button>
                        </div>
                    </div>
                </template>

                <ul
                    x-show="visibleReports.length > 0"
                    class="mt-5 space-y-3"
                    aria-label="Daftar laporan lokasi terpilih"
                >
                    <template x-for="report in visibleReports" :key="report.id">
                        <li>
                            <article
                                class="rounded-2xl border border-stone-200/80 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-stone-900 sm:p-5"
                                x-bind:id="'report-' + report.id"
                                x-bind:aria-labelledby="'report-title-' + report.id"
                            >
                                <div class="flex gap-3 sm:gap-4">
                                    <div class="shrink-0">
                                        <template x-if="report.foto">
                                            <img
                                                x-bind:src="report.foto"
                                                x-bind:alt="'Foto ' + report.fasilitas + ' di ' + report.ruangan"
                                                loading="lazy"
                                                class="h-20 w-20 rounded-xl object-cover sm:h-24 sm:w-24"
                                            >
                                        </template>
                                        <template x-if="!report.foto">
                                            <div
                                                class="flex h-20 w-20 flex-col items-center justify-center gap-1 rounded-xl border border-stone-200 bg-stone-100 text-stone-600 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-400 sm:h-24 sm:w-24"
                                                role="img"
                                                aria-label="Foto laporan belum tersedia"
                                            >
                                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h3l1.5-2h7L17 7h3a1 1 0 0 1 1 1v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a1 1 0 0 1 1-1Z"></path>
                                                    <circle cx="12" cy="12.5" r="3.5"></circle>
                                                </svg>
                                                <span class="text-[13px] font-medium">Tanpa foto</span>
                                            </div>
                                        </template>
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-start justify-between gap-x-3 gap-y-2">
                                            <div class="min-w-0">
                                                <h3
                                                    class="rounded-sm font-serif text-lg font-semibold leading-tight text-ft-ink focus:outline-none focus-visible:ring-4 focus-visible:ring-amber-300/70 dark:text-stone-100"
                                                    x-bind:id="'report-title-' + report.id"
                                                    tabindex="-1"
                                                    x-text="report.fasilitas"
                                                ></h3>
                                                <p class="mt-1 text-[13px] font-medium tracking-wide text-stone-600 dark:text-stone-400" x-text="report.kategori"></p>
                                            </div>
                                            <span class="status-badge" x-bind:data-status="report.status" x-text="report.status"></span>
                                        </div>

                                        <dl class="mt-3 grid gap-x-4 gap-y-1 text-sm sm:grid-cols-2">
                                            <div class="flex gap-1.5">
                                                <dt class="text-stone-600 dark:text-stone-400">Ruangan</dt>
                                                <dd class="font-medium text-stone-800 dark:text-stone-200" x-text="report.ruangan"></dd>
                                            </div>
                                            <div class="flex gap-1.5">
                                                <dt class="text-stone-600 dark:text-stone-400">Lantai</dt>
                                                <dd class="font-medium text-stone-800 dark:text-stone-200" x-text="report.lantai"></dd>
                                            </div>
                                        </dl>

                                        <p class="mt-2 inline-flex items-center gap-1.5 text-[13px] text-stone-600 dark:text-stone-400">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <circle cx="12" cy="12" r="9"></circle>
                                                <path stroke-linecap="round" d="M12 7v5l3 2"></path>
                                            </svg>
                                            <time x-bind:datetime="report.dibuat_pada" x-text="formatDate(report.dibuat_pada)"></time>
                                        </p>
                                    </div>
                                </div>

                                <p
                                    x-show="report.deskripsi"
                                    class="mt-4 text-sm leading-relaxed text-stone-700 dark:text-stone-300"
                                    x-text="report.deskripsi"
                                ></p>

                                <div
                                    x-show="report.tindakan_admin"
                                    class="mt-3 rounded-xl border border-emerald-600/15 bg-emerald-50/80 px-3 py-2.5 dark:border-emerald-400/20 dark:bg-emerald-950/30"
                                >
                                    <p class="text-[13px] font-semibold tracking-[0.1em] text-emerald-800 dark:text-emerald-300">
                                        Tindakan admin (staf TU)
                                    </p>
                                    <p class="mt-1 text-sm leading-relaxed text-emerald-950 dark:text-emerald-100" x-text="report.tindakan_admin"></p>
                                </div>

                                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-stone-200/80 pt-3 dark:border-white/10">
                                    <div class="inline-flex items-center gap-2">
                                        <span class="text-[13px] font-semibold tracking-wide text-stone-600 dark:text-stone-400">Urgensi</span>
                                        <span class="inline-flex min-w-9 items-center justify-center rounded-full bg-rose-100 px-2.5 py-1 text-sm font-bold text-rose-800 dark:bg-rose-950/60 dark:text-rose-200" x-text="report.nilai_urgensi"></span>
                                    </div>

                                    <button
                                        type="button"
                                        x-bind:id="'vote-' + report.id"
                                        x-on:click="vote(report)"
                                        x-bind:aria-disabled="isVoteDisabled(report)"
                                        x-bind:aria-label="voteButtonAria(report)"
                                        class="inline-flex min-h-11 items-center justify-center gap-1.5 rounded-full px-4 py-2 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-amber-300/70"
                                        x-bind:class="isVoteDisabled(report)
                                            ? 'cursor-not-allowed border border-stone-300 bg-stone-100 text-stone-500 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-400'
                                            : 'bg-ft-green text-white shadow-sm hover:bg-ft-green-deep'"
                                    >
                                        <svg x-show="!isVoteDisabled(report)" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"></path>
                                        </svg>
                                        <svg x-show="report.status !== 'Selesai' && hasReportVoted(report)" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"></path>
                                        </svg>
                                        <svg x-show="report.status === 'Selesai'" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                                            <path stroke-linecap="round" d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                                        </svg>
                                        <span x-text="voteButtonLabel(report)"></span>
                                    </button>
                                </div>
                            </article>
                        </li>
                    </template>
                </ul>

                <p class="mt-4 text-[13px] leading-relaxed text-stone-600 dark:text-stone-400">
                    Data laporan masih contoh. Laporan baru hanya tersimpan di memori sampai halaman dimuat ulang. Rating urgensi pada tahap ini juga simulasi tampilan; validasi sebenarnya dibuat di server saat backend tersedia.
                </p>
            </div>
        </section>
    </main>

    <div
        x-show="submitNotice"
        x-cloak
        x-transition.opacity
        class="fixed inset-x-4 bottom-4 z-[60] mx-auto flex max-w-md items-start gap-3 rounded-2xl bg-emerald-700 px-4 py-3 text-sm font-medium text-white shadow-lg dark:bg-emerald-600 sm:left-auto sm:right-6 sm:mx-0"
        role="status"
        aria-live="polite"
    >
        <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"></path>
        </svg>
        <p class="min-w-0 flex-1" x-text="submitNotice"></p>
        <button
            type="button"
            class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-emerald-100 transition hover:bg-white/15 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-amber-300/70"
            x-on:click="hideSubmitNotice()"
            aria-label="Tutup notifikasi"
        >
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18"></path>
            </svg>
        </button>
    </div>

    <div
        x-show="formOpen"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        x-on:keydown.escape.window="formOpen && closeForm()"
        x-on:keydown.tab.window="formOpen && trapFocus($event)"
    >
        <div class="fixed inset-0 bg-stone-950/70 backdrop-blur-sm" aria-hidden="true"></div>

        <div class="relative flex min-h-full items-end justify-center p-0 sm:items-center sm:p-6">
            <div
                x-ref="reportDialog"
                role="dialog"
                aria-modal="true"
                aria-labelledby="report-form-title"
                aria-describedby="report-form-description"
                class="relative w-full max-w-2xl overflow-hidden rounded-t-3xl border border-stone-200 bg-white shadow-2xl dark:border-white/10 dark:bg-stone-900 sm:rounded-3xl"
            >
                <div class="max-h-[92vh] overflow-y-auto p-5 sm:p-7">
                    <div>
                        <p class="text-[13px] font-semibold tracking-[0.12em] text-ft-green/80 dark:text-emerald-300/90">
                            Laporan fasilitas
                        </p>
                        <h2 id="report-form-title" class="mt-1 font-serif text-2xl text-ft-green dark:text-emerald-300 sm:text-3xl">
                            Buat laporan
                        </h2>
                        <p id="report-form-description" class="mt-1 text-sm leading-relaxed text-stone-600 dark:text-stone-400">
                            Lokasi: <span class="font-semibold" x-text="selectedName"></span>.
                            Kolom bertanda <span class="font-semibold text-red-700 dark:text-red-300" aria-hidden="true">*</span><span class="sr-only"> bintang</span> wajib diisi.
                        </p>
                    </div>

                    <form class="mt-5" x-on:submit.prevent="submitReport()" novalidate>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="report-building" class="field-label">
                                    Gedung
                                </label>
                                <input
                                    id="report-building"
                                    type="text"
                                    x-bind:value="selectedName"
                                    readonly
                                    aria-readonly="true"
                                    class="field-control"
                                >
                                <p class="mt-1 text-[13px] text-stone-600 dark:text-stone-400">
                                    Terisi otomatis dari area yang dipilih pada pilihan lokasi.
                                </p>
                            </div>

                            <div x-show="!isAreaLocation">
                                <label for="report-floor" class="field-label">
                                    Lantai
                                    <span class="text-red-700 dark:text-red-300" aria-hidden="true">*</span>
                                    <span class="sr-only"> (wajib)</span>
                                </label>
                                <select
                                    id="report-floor"
                                    x-model="form.lantai"
                                    x-on:change="onFloorChange()"
                                    class="field-control"
                                    x-bind:aria-invalid="formErrors.lantai ? 'true' : 'false'"
                                    aria-describedby="report-floor-error"
                                >
                                    <option value="">Pilih lantai</option>
                                    <template x-for="floor in floorOptions" :key="floor.nama">
                                        <option :value="floor.nama" x-text="floor.nama"></option>
                                    </template>
                                </select>
                                <p id="report-floor-error" x-show="formErrors.lantai" x-cloak class="field-error" x-text="formErrors.lantai" role="alert"></p>
                            </div>

                            <div>
                                <label for="report-room" class="field-label">
                                    <span x-text="isAreaLocation ? 'Lokasi' : 'Ruangan'">Ruangan</span>
                                    <span class="text-red-700 dark:text-red-300" aria-hidden="true">*</span>
                                    <span class="sr-only"> (wajib)</span>
                                </label>
                                <select
                                    id="report-room"
                                    x-model="form.ruangan"
                                    x-on:change="clearFormError('ruangan')"
                                    x-bind:disabled="!form.lantai"
                                    class="field-control"
                                    x-bind:aria-invalid="formErrors.ruangan ? 'true' : 'false'"
                                    aria-describedby="report-room-hint report-room-error"
                                >
                                    <option value="" x-text="isAreaLocation ? 'Pilih lokasi' : 'Pilih ruangan'">Pilih ruangan</option>
                                    <template x-for="room in roomOptions" :key="room">
                                        <option :value="room" x-text="room"></option>
                                    </template>
                                    <option x-show="isAreaLocation" x-bind:value="roomOtherValue">Lainnya</option>
                                </select>
                                <div x-show="isAreaLocation && form.ruangan === roomOtherValue" x-cloak class="mt-2">
                                    <label for="report-room-other" class="sr-only">Nama lokasi lainnya</label>
                                    <input
                                        id="report-room-other"
                                        type="text"
                                        maxlength="100"
                                        x-model="form.ruanganLainnya"
                                        x-on:input="clearFormError('ruangan')"
                                        class="field-control"
                                        placeholder="Tulis nama lokasi, misalnya parkiran belakang"
                                        x-bind:aria-invalid="formErrors.ruangan ? 'true' : 'false'"
                                        aria-describedby="report-room-other-hint report-room-error"
                                    >
                                    <p id="report-room-other-hint" class="mt-1 text-[13px] text-stone-600 dark:text-stone-400">
                                        Tulis nama lokasi jika tidak ada di daftar.
                                    </p>
                                </div>
                                <p id="report-room-hint" x-show="!form.lantai && !isAreaLocation" class="mt-1 text-[13px] text-stone-600 dark:text-stone-400">
                                    Pilih lantai dulu untuk melihat pilihan ruangan.
                                </p>
                                <p id="report-room-error" x-show="formErrors.ruangan" x-cloak class="field-error" x-text="formErrors.ruangan" role="alert"></p>
                            </div>

                            <div>
                                <label for="report-category" class="field-label">
                                    Kategori
                                    <span class="text-red-700 dark:text-red-300" aria-hidden="true">*</span>
                                    <span class="sr-only"> (wajib)</span>
                                </label>
                                <select
                                    id="report-category"
                                    x-model="form.kategori"
                                    x-on:change="onCategoryChange()"
                                    class="field-control"
                                    x-bind:aria-invalid="formErrors.kategori ? 'true' : 'false'"
                                    aria-describedby="report-category-error"
                                >
                                    <option value="">Pilih kategori</option>
                                    <template x-for="category in formConfig.categories" :key="category.nama">
                                        <option :value="category.nama" x-text="category.nama"></option>
                                    </template>
                                </select>
                                <p id="report-category-error" x-show="formErrors.kategori" x-cloak class="field-error" x-text="formErrors.kategori" role="alert"></p>
                            </div>

                            <div x-show="!isAreaLocation">
                                <label for="report-facility" class="field-label">
                                    Fasilitas
                                    <span class="text-red-700 dark:text-red-300" aria-hidden="true">*</span>
                                    <span class="sr-only"> (wajib)</span>
                                </label>
                                <select
                                    id="report-facility"
                                    x-model="form.fasilitas"
                                    x-on:change="clearFormError('fasilitas')"
                                    x-bind:disabled="!form.kategori"
                                    class="field-control"
                                    x-bind:aria-invalid="formErrors.fasilitas ? 'true' : 'false'"
                                    aria-describedby="report-facility-hint report-facility-error"
                                >
                                    <option value="">Pilih fasilitas</option>
                                    <template x-for="facility in facilityOptions" :key="facility">
                                        <option :value="facility" x-text="facility"></option>
                                    </template>
                                </select>
                                <p id="report-facility-hint" x-show="!form.kategori" class="mt-1 text-[13px] text-stone-600 dark:text-stone-400">
                                    Pilih kategori dulu untuk melihat pilihan fasilitas.
                                </p>
                                <p id="report-facility-error" x-show="formErrors.fasilitas" x-cloak class="field-error" x-text="formErrors.fasilitas" role="alert"></p>
                            </div>

                            <div
                                x-show="duplicateReports.length > 0"
                                x-cloak
                                x-transition.opacity
                                class="rounded-2xl border border-amber-500/40 bg-amber-50 p-4 text-amber-950 shadow-sm dark:border-amber-400/30 dark:bg-amber-950/40 dark:text-amber-100 sm:col-span-2"
                                role="status"
                                aria-live="polite"
                            >
                                <div class="flex items-start gap-3">
                                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-600 dark:text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"></path>
                                    </svg>
                                    <div class="min-w-0">
                                        <p class="font-semibold">Laporan serupa sudah ada</p>
                                        <p class="mt-1 text-sm leading-relaxed">
                                            <span x-text="duplicateReports.length"></span>
                                            laporan aktif dengan <span x-text="isAreaLocation ? 'lokasi' : 'ruangan'"></span> dan fasilitas yang sama. Beri urgensi pada laporan yang sudah ada jika masalahnya sama.
                                        </p>
                                    </div>
                                </div>

                                <ul class="mt-3 space-y-2">
                                    <template x-for="duplicate in duplicateReports" :key="duplicate.id">
                                        <li class="rounded-xl border border-amber-500/25 bg-white/70 p-3 dark:border-amber-300/20 dark:bg-stone-900/60">
                                            <p class="text-sm font-semibold" x-text="duplicate.fasilitas + ' · ' + duplicate.ruangan"></p>
                                            <p
                                                class="mt-1 text-[13px] text-amber-900/80 dark:text-amber-100/80"
                                                x-text="'Status ' + duplicate.status + ' · urgensi ' + duplicate.nilai_urgensi + ' · ' + formatDate(duplicate.dibuat_pada)"
                                            ></p>
                                            <button
                                                type="button"
                                                class="mt-2 inline-flex min-h-11 w-full items-center justify-center rounded-full bg-ft-brick px-3 py-2 text-[13px] font-semibold text-white transition hover:bg-ft-brick-deep focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-ft-brick/40 sm:w-auto"
                                                x-on:click="goToDuplicate(duplicate)"
                                                x-bind:aria-label="'Beri urgensi pada laporan ' + duplicate.fasilitas + ' di ' + duplicate.ruangan"
                                            >
                                                Beri urgensi pada laporan ini
                                            </button>
                                        </li>
                                    </template>
                                </ul>

                                <p class="mt-3 text-[13px] leading-relaxed text-amber-900/80 dark:text-amber-100/80">
                                    Kamu tetap boleh melanjutkan membuat laporan baru.
                                </p>
                            </div>

                            <div class="sm:col-span-2">
                                <div class="flex items-baseline justify-between gap-2">
                                    <label for="report-description" class="field-label">
                                        Deskripsi
                                        <span x-show="!isAreaLocation" class="font-normal text-stone-600 dark:text-stone-400">(opsional)</span>
                                        <span x-show="isAreaLocation" class="text-red-700 dark:text-red-300" aria-hidden="true">*</span>
                                        <span x-show="isAreaLocation" class="sr-only"> (wajib)</span>
                                    </label>
                                    <span class="text-[13px] text-stone-600 dark:text-stone-400" x-text="form.deskripsi.length + '/500'"></span>
                                </div>
                                <textarea
                                    id="report-description"
                                    rows="4"
                                    maxlength="500"
                                    x-model="form.deskripsi"
                                    x-on:input="clearFormError('deskripsi')"
                                    class="field-control resize-y"
                                    x-bind:aria-invalid="formErrors.deskripsi ? 'true' : 'false'"
                                    x-bind:aria-required="isAreaLocation ? 'true' : 'false'"
                                    aria-describedby="report-description-hint report-description-note report-description-error"
                                ></textarea>
                                <p id="report-description-hint" x-show="!isAreaLocation" class="mt-1 text-[13px] text-stone-600 dark:text-stone-400">
                                    Jelaskan kerusakan atau kekurangan yang ditemukan.
                                </p>
                                <p id="report-description-note" x-show="isAreaLocation" x-cloak class="mt-2 rounded-xl border border-amber-500/30 bg-amber-50 p-3 text-[13px] leading-relaxed text-amber-950 dark:border-amber-400/30 dark:bg-amber-950/40 dark:text-amber-100">
                                    <span class="font-semibold">Catatan:</span>
                                    jelaskan kondisi sedetail mungkin — apa yang rusak, di mana persisnya, sejak kapan, dan dampaknya. Laporan yang jelas lebih cepat diproses.
                                </p>
                                <p id="report-description-error" x-show="formErrors.deskripsi" x-cloak class="field-error" x-text="formErrors.deskripsi" role="alert"></p>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="report-photo" class="field-label">
                                    Foto laporan
                                    <span class="text-red-700 dark:text-red-300" aria-hidden="true">*</span>
                                    <span class="sr-only"> (wajib)</span>
                                </label>
                                <input
                                    id="report-photo"
                                    x-ref="photoInput"
                                    type="file"
                                    accept="image/jpeg,.jpg,.jpeg"
                                    x-on:change="handlePhoto($event)"
                                    class="mt-1 block min-h-11 w-full cursor-pointer rounded-xl border border-stone-300 bg-white text-sm text-stone-700 shadow-sm file:mr-3 file:cursor-pointer file:rounded-l-xl file:border-0 file:bg-ft-green file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-ft-green-deep focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-amber-300/70 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-200 dark:file:bg-emerald-700 dark:hover:file:bg-emerald-600"
                                    x-bind:aria-invalid="formErrors.foto ? 'true' : 'false'"
                                    aria-describedby="report-photo-hint report-photo-error"
                                >
                                <p id="report-photo-hint" class="mt-1 text-[13px] text-stone-600 dark:text-stone-400">
                                    JPG atau JPEG, maksimal 2 MB. Foto otomatis diperkecil di browser.
                                </p>
                                <p id="report-photo-error" x-show="formErrors.foto" x-cloak class="field-error" x-text="formErrors.foto" role="alert"></p>

                                <div
                                    x-show="form.fotoPreview"
                                    x-cloak
                                    class="mt-3 flex flex-col gap-3 rounded-2xl border border-stone-200 bg-white/70 p-3 dark:border-white/10 dark:bg-stone-950/40 sm:flex-row sm:items-center"
                                >
                                    <img
                                        x-bind:src="form.fotoPreview"
                                        alt="Pratinjau foto laporan"
                                        class="h-32 w-full rounded-xl object-cover sm:h-24 sm:w-36"
                                    >
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-stone-800 dark:text-stone-100">Foto siap diunggah</p>
                                        <p class="mt-1 truncate text-[13px] text-stone-600 dark:text-stone-400" x-text="form.fotoName"></p>
                                        <button
                                            type="button"
                                            class="mt-2 inline-flex min-h-11 items-center justify-center rounded-full border border-stone-300 bg-white px-4 py-2 text-[13px] font-semibold text-stone-700 shadow-sm transition hover:bg-stone-50 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-amber-300/70 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-stone-800"
                                            x-on:click="$refs.photoInput.click()"
                                        >
                                            Ganti foto
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col-reverse gap-2 border-t border-stone-300/70 pt-4 dark:border-white/10 sm:flex-row sm:justify-end">
                            <button
                                type="button"
                                class="inline-flex min-h-11 items-center justify-center rounded-full border border-stone-300 bg-white px-5 py-2.5 text-sm font-semibold text-stone-700 shadow-sm transition hover:bg-stone-50 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-amber-300/70 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-stone-800"
                                x-on:click="closeForm()"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                x-bind:disabled="submitting"
                                x-bind:aria-busy="submitting ? 'true' : 'false'"
                                class="inline-flex min-h-11 items-center justify-center rounded-full bg-ft-green px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-ft-green-deep focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-amber-300/70 disabled:cursor-not-allowed disabled:bg-stone-400 dark:disabled:bg-stone-700"
                            >
                                Kirim laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="mx-auto max-w-6xl px-4 pb-10 text-[13px] text-stone-600 dark:text-stone-400 sm:px-6 lg:px-8">
        Prototipe Fase 1 · Data laporan dan lokasi masih contoh.
    </footer>
</body>
</html>
