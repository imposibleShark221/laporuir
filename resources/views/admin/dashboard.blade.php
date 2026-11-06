@extends('layouts.admin')

@section('content')
    <div class="space-y-8">
        <section aria-labelledby="ringkasan-heading">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 id="ringkasan-heading" class="font-serif text-lg text-ft-green dark:text-emerald-200">
                        Ringkasan laporan
                    </h2>
                    <p class="mt-1 text-sm text-stone-600 dark:text-stone-400">
                        Data contoh 18–26 Oktober 2026. Angka berubah saat status laporan diperbarui.
                    </p>
                </div>
                <a href="{{ route('admin.reports') }}" class="admin-btn admin-btn--quiet admin-btn--small">
                    Buka semua laporan
                </a>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                <article class="admin-stat" data-tone="green">
                    <p class="admin-stat__label">Total laporan</p>
                    <p class="admin-stat__value" x-text="stats.total">0</p>
                    <p class="admin-stat__meta">
                        <span x-text="`+${stats.createdLast7} dalam 7 hari terakhir`"></span>
                    </p>
                </article>

                <article class="admin-stat" data-tone="brick">
                    <p class="admin-stat__label">Baru</p>
                    <p class="admin-stat__value" x-text="stats.baru">0</p>
                    <p class="admin-stat__meta">
                        <span x-text="`${stats.baru} belum ditangani`"></span>
                    </p>
                </article>

                <article class="admin-stat" data-tone="amber">
                    <p class="admin-stat__label">Diproses</p>
                    <p class="admin-stat__value" x-text="stats.diproses">0</p>
                    <p class="admin-stat__meta">
                        <span x-text="`${stats.diproses} sedang ditangani`"></span>
                    </p>
                </article>

                <article class="admin-stat" data-tone="green">
                    <p class="admin-stat__label">Selesai</p>
                    <p class="admin-stat__value" x-text="stats.selesai">0</p>
                    <p class="admin-stat__meta">
                        <span x-text="stats.total === 0 ? 'Belum ada laporan' : `${stats.selesai} dari ${stats.total} laporan`"></span>
                    </p>
                </article>
            </div>
        </section>

        <section aria-labelledby="grafik-heading" class="space-y-4">
            <h2 id="grafik-heading" class="font-serif text-lg text-ft-green dark:text-emerald-200">
                Analitik laporan
            </h2>

            <div class="grid gap-5 xl:grid-cols-2 xl:items-start">
                <figure class="admin-card admin-chart xl:col-span-2">
                    <figcaption>
                        <h3 class="admin-card__title">Distribusi laporan masuk</h3>
                        <p class="admin-card__hint">
                            Total <span class="font-semibold" x-text="stats.total"></span> laporan.
                        </p>
                    </figcaption>

                    <div class="bar-chart" x-show="stats.total > 0" aria-hidden="true">
                        <template x-for="entry in buildingBars" :key="entry.code">
                            <div class="bar-chart__row">
                                <span class="bar-chart__label">
                                    <span class="bar-chart__name" x-text="entry.label"></span>
                                </span>
                                <div class="bar-chart__plot">
                                    <div class="bar-chart__grid" aria-hidden="true">
                                        <template x-for="tick in buildingAxis.ticks" :key="`grid-${tick}`">
                                            <span class="bar-chart__grid-line"></span>
                                        </template>
                                    </div>
                                    <div
                                        class="bar-chart__bar"
                                        x-bind:style="`width: ${entry.widthPct}%`"
                                        x-bind:title="`${entry.label}: ${entry.total} laporan`"
                                    >
                                        <span class="bar-chart__count" x-text="entry.total"></span>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="bar-chart__axis-row" aria-hidden="true">
                            <span class="bar-chart__axis-spacer"></span>
                            <div class="bar-chart__axis">
                                <template x-for="tick in buildingAxis.ticks" :key="`axis-${tick}`">
                                    <span x-text="tick"></span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <p x-show="stats.total === 0" x-cloak class="mt-4 rounded-lg border border-dashed border-stone-300 p-4 text-sm text-stone-600 dark:border-stone-700 dark:text-stone-400">
                        Belum ada laporan masuk.
                    </p>

                    <table class="sr-only">
                        <caption>Jumlah laporan masuk per gedung</caption>
                        <thead>
                            <tr>
                                <th scope="col">Gedung</th>
                                <th scope="col">Jumlah laporan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="entry in buildingBars" :key="`sr-${entry.code}`">
                                <tr>
                                    <th scope="row" x-text="entry.label"></th>
                                    <td x-text="entry.total"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </figure>

                <figure class="admin-card admin-chart">
                    <figcaption>
                        <h3 class="admin-card__title">Laporan per kategori</h3>
                        <p class="admin-card__hint">
                            Jumlah laporan pada tiap kategori fasilitas.
                        </p>
                    </figcaption>

                    <div class="chart-hbars" aria-hidden="true">
                        <template x-for="entry in categoryBars" :key="entry.name">
                            <div class="chart-hbar">
                                <div class="chart-hbar__head">
                                    <span x-text="entry.name"></span>
                                    <span class="chart-hbar__value" x-text="`${entry.count} · ${entry.sharePct}%`"></span>
                                </div>
                                <div class="chart-hbar__track">
                                    <div class="chart-hbar__fill" x-bind:style="`width: ${entry.widthPct}%`"></div>
                                </div>
                            </div>
                        </template>

                        <p x-show="categoryBars.length === 0" x-cloak class="text-sm text-stone-600 dark:text-stone-400">
                            Belum ada data kategori.
                        </p>
                    </div>

                    <table class="sr-only">
                        <caption>Jumlah laporan per kategori</caption>
                        <thead>
                            <tr>
                                <th scope="col">Kategori</th>
                                <th scope="col">Jumlah</th>
                                <th scope="col">Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="entry in categoryBars" :key="`sr-${entry.name}`">
                                <tr>
                                    <th scope="row" x-text="entry.name"></th>
                                    <td x-text="entry.count"></td>
                                    <td x-text="`${entry.sharePct}%`"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </figure>

                <section class="admin-card" aria-labelledby="perhatian-heading">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 id="perhatian-heading" class="admin-card__title">Perlu perhatian</h3>
                            <p class="admin-card__hint">Lima laporan aktif dengan urgensi tertinggi.</p>
                        </div>
                        <span class="admin-count-pill" x-text="needAttention.length"></span>
                    </div>

                    <ul class="mt-4 divide-y divide-stone-200 dark:divide-stone-800">
                        <template x-for="report in needAttention" :key="report.id">
                            <li class="admin-attention">
                                <div class="min-w-0 flex-1">
                                    <p class="flex flex-wrap items-center gap-2">
                                        <span class="font-semibold" x-text="report.fasilitas"></span>
                                        <span class="text-stone-500 dark:text-stone-400" x-text="report.ruangan"></span>
                                        <span class="status-badge" x-bind:data-status="report.status" x-text="report.status"></span>
                                    </p>
                                    <p class="mt-1 text-xs text-stone-600 dark:text-stone-400">
                                        <span x-text="buildingName(report.gedung)"></span>
                                        · <span x-text="formatDate(report.dibuat_pada)"></span>
                                    </p>
                                    <div class="urgency mt-2">
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
                                </div>
                                <div class="flex shrink-0 flex-col gap-1.5">
                                    <button
                                        type="button"
                                        class="admin-btn admin-btn--small admin-btn--primary"
                                        x-on:click="markProcessed(report.id)"
                                        x-bind:disabled="report.status !== 'Baru'"
                                        x-text="report.status === 'Baru' ? 'Proses' : 'Sudah diproses'"
                                        x-bind:aria-label="`Tandai ${report.fasilitas} ${report.ruangan} sebagai Diproses`"
                                    ></button>
                                    <button
                                        type="button"
                                        class="admin-btn admin-btn--small admin-btn--quiet"
                                        x-on:click="openDetail(report.id)"
                                        x-bind:aria-label="`Buka detail ${report.fasilitas} ${report.ruangan}`"
                                    >
                                        Buka
                                    </button>
                                </div>
                            </li>
                        </template>
                    </ul>

                    <p
                        x-show="needAttention.length === 0"
                        x-cloak
                        class="mt-4 rounded-lg border border-dashed border-stone-300 p-4 text-sm text-stone-600 dark:border-stone-700 dark:text-stone-400"
                    >
                        Tidak ada laporan aktif. Semua laporan sudah selesai.
                    </p>
                </section>
            </div>
        </section>
    </div>
@endsection
