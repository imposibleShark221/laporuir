@extends('layouts.admin')

@section('content')
    <div class="space-y-5">
        <section class="admin-card" aria-labelledby="activity-filter-heading">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 id="activity-filter-heading" class="admin-card__title">Riwayat aktivitas</h2>
                    <p class="admin-card__hint">
                        Urut dari yang terbaru. Aktivitas baru dari sesi ini muncul paling atas.
                    </p>
                </div>

                <div class="w-full sm:w-64">
                    <label for="activity-type" class="field-label">Jenis aktivitas</label>
                    <select id="activity-type" class="field-control mt-1.5" x-model="activityType">
                        <template x-for="type in activityTypes" :key="type.value">
                            <option x-bind:value="type.value" x-text="`${type.label} (${countActivitiesOfType(type.value)})`"></option>
                        </template>
                    </select>
                </div>
            </div>
        </section>

        <div class="flex flex-wrap items-center justify-between gap-2">
            <p class="text-sm text-stone-600 dark:text-stone-400" aria-live="polite">
                <span class="font-semibold text-ft-ink dark:text-stone-100" x-text="filteredActivities.length"></span>
                aktivitas ditampilkan
            </p>
            <button
                type="button"
                class="admin-btn admin-btn--quiet admin-btn--small"
                x-show="activityType !== ''"
                x-cloak
                x-on:click="activityType = ''"
            >
                Tampilkan semua aktivitas
            </button>
        </div>

        <div class="space-y-5">
            <template x-for="group in activityGroups" :key="group.label">
                <section>
                    <h2 class="mb-2 text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-stone-500 dark:text-stone-400" x-text="group.label"></h2>
                    <ol class="admin-card admin-activity-list">
                        <template x-for="item in group.items" :key="item.id">
                            <li class="admin-activity">
                                <span class="admin-activity__icon" x-bind:data-type="activityIcon(item.type)" aria-hidden="true">
                                    <svg x-show="item.type === 'laporan_baru'" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"></path>
                                    </svg>
                                    <svg x-show="item.type === 'status'" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h5l2-5 3 10 2-5h4"></path>
                                    </svg>
                                    <svg x-show="item.type === 'tindakan'" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 19V5h9l1.5 3H19v11h-9l-1.5-3H5Z"></path>
                                    </svg>
                                </span>

                                <div class="min-w-0 flex-1">
                                    <p class="text-sm" x-text="item.text"></p>
                                    <p class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-stone-500 dark:text-stone-400">
                                        <span class="admin-type-badge" x-text="activityLabel(item.type)"></span>
                                        <span aria-hidden="true">·</span>
                                        <span x-text="formatDateTime(item.at)"></span>
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    class="admin-btn admin-btn--small admin-btn--quiet shrink-0"
                                    x-show="item.reportId"
                                    x-cloak
                                    x-on:click="openDetail(item.reportId)"
                                    x-bind:aria-label="`Buka laporan untuk aktivitas: ${item.text}`"
                                >
                                    Buka laporan
                                </button>
                            </li>
                        </template>
                    </ol>
                </section>
            </template>
        </div>

        <div class="admin-empty" x-show="filteredActivities.length === 0" x-cloak>
            <h3 class="font-serif text-xl text-ft-green dark:text-emerald-200">Tidak ada aktivitas untuk jenis ini</h3>
            <p class="mx-auto mt-2 max-w-md text-sm text-stone-600 dark:text-stone-400">
                Pilih jenis aktivitas lain atau tampilkan semua aktivitas.
            </p>
            <button type="button" class="admin-btn admin-btn--primary mt-4" x-on:click="activityType = ''">
                Tampilkan semua aktivitas
            </button>
        </div>
    </div>
@endsection
