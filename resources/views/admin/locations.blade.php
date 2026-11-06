@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 class="font-serif text-lg text-ft-green dark:text-emerald-200">Ringkasan per gedung</h2>
                <p class="mt-1 text-sm text-stone-600 dark:text-stone-400">
                    Jumlah laporan aktif, laporan paling urgent, dan ruangan yang paling sering dilaporkan.
                </p>
            </div>
            <a href="{{ route('admin.reports') }}" class="admin-btn admin-btn--quiet admin-btn--small">
                Lihat semua laporan
            </a>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <template x-for="card in locationCards" :key="card.kode">
                <a
                    x-bind:href="`{{ route('admin.reports') }}?gedung=${card.kode}`"
                    class="admin-card admin-location"
                    x-bind:aria-label="`Lihat laporan ${card.nama}: ${card.active} laporan aktif`"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[0.62rem] font-semibold uppercase tracking-[0.18em] text-stone-500 dark:text-stone-400">
                                Gedung
                            </p>
                            <h3 class="mt-1 font-serif text-xl text-ft-green dark:text-emerald-200" x-text="card.nama"></h3>
                        </div>
                        <span class="admin-count-pill admin-count-pill--large" x-text="card.active"></span>
                    </div>

                    <p class="mt-3 text-sm text-stone-600 dark:text-stone-400">
                        <span class="font-semibold text-ft-ink dark:text-stone-100" x-text="card.active"></span> laporan aktif
                        · <span x-text="card.total"></span> total
                        · <span x-text="card.selesai"></span> selesai
                    </p>

                    <div class="mt-4 border-t border-stone-200 pt-4 dark:border-stone-800">
                        <p class="text-[0.62rem] font-semibold uppercase tracking-[0.16em] text-stone-500 dark:text-stone-400">
                            Paling urgent
                        </p>
                        <template x-if="card.urgent">
                            <div class="mt-2">
                                <p class="flex flex-wrap items-center gap-2">
                                    <span class="font-semibold" x-text="card.urgent.fasilitas"></span>
                                    <span class="text-stone-500 dark:text-stone-400" x-text="card.urgent.ruangan"></span>
                                    <span class="status-badge" x-bind:data-status="card.urgent.status" x-text="card.urgent.status"></span>
                                </p>
                                <div class="urgency mt-2">
                                    <span class="urgency__value" x-text="card.urgent.nilai_urgensi"></span>
                                    <span class="urgency__track" aria-hidden="true">
                                        <span
                                            class="urgency__fill"
                                            x-bind:data-tone="urgencyLevel(card.urgent.nilai_urgensi).tone"
                                            x-bind:style="`width: ${urgencyWidth(card.urgent.nilai_urgensi)}`"
                                        ></span>
                                    </span>
                                    <span class="urgency__label" x-text="urgencyLevel(card.urgent.nilai_urgensi).label"></span>
                                </div>
                            </div>
                        </template>
                        <p x-show="!card.urgent" x-cloak class="mt-2 text-sm text-stone-500 dark:text-stone-400">
                            Tidak ada laporan aktif.
                        </p>
                    </div>

                    <div class="mt-4 border-t border-stone-200 pt-4 dark:border-stone-800">
                        <p class="text-[0.62rem] font-semibold uppercase tracking-[0.16em] text-stone-500 dark:text-stone-400">
                            Ruangan paling sering dilaporkan
                        </p>
                        <ul class="mt-2 space-y-1.5">
                            <template x-for="room in card.topRooms" :key="room.name">
                                <li class="flex items-center justify-between gap-3 text-sm">
                                    <span class="truncate" x-text="room.name"></span>
                                    <span class="shrink-0 text-xs font-semibold text-stone-600 dark:text-stone-300">
                                        <span x-text="room.count"></span> laporan
                                    </span>
                                </li>
                            </template>
                        </ul>
                        <p x-show="card.topRooms.length === 0" x-cloak class="mt-2 text-sm text-stone-500 dark:text-stone-400">
                            Belum ada laporan.
                        </p>
                    </div>

                    <p class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-ft-green dark:text-emerald-300">
                        Lihat laporan gedung ini
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"></path>
                        </svg>
                    </p>
                </a>
            </template>
        </div>
    </div>
@endsection
