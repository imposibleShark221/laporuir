<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Panel admin Lapor Fasilitas Fakultas Teknik Universitas Islam Riau.">
    <meta name="theme-color" content="#0F5B3A">
    <meta name="robots" content="noindex">
    @php
        $pageTitles = [
            'dashboard' => 'Dashboard',
            'reports' => 'Laporan',
            'locations' => 'Lokasi',
            'activity' => 'Log Aktivitas',
        ];
        $pageDescriptions = [
            'dashboard' => 'Ringkasan laporan fasilitas Fakultas Teknik UIR.',
            'reports' => 'Kelola status dan tindakan seluruh laporan fasilitas.',
            'locations' => 'Ringkasan laporan aktif per gedung dan ruangan.',
            'activity' => 'Riwayat perubahan status dan tindakan admin.',
        ];
        $pageTitle = $pageTitles[$page] ?? 'Admin';
    @endphp
    <title>{{ $pageTitle }} · Admin Lapor Fasilitas FT UIR</title>

    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body
    class="admin-shell min-h-screen bg-ft-cream font-sans text-ft-ink antialiased dark:bg-stone-950 dark:text-stone-100"
    x-data="adminApp(@js([
        'page' => $page,
        'buildings' => $buildings,
        'categories' => $categories,
    ]))"
    x-effect="document.body.style.overflow = (drawerOpen || confirmOpen) ? 'hidden' : ''"
>
    <a href="#admin-content" class="admin-skip-link">Lewati ke konten utama</a>

    {{-- Sidebar: hijau tua, ikon garis sederhana, menjadi drawer di layar kecil. --}}
    <div
        x-cloak
        x-show="sidebarOpen"
        class="fixed inset-0 z-30 bg-stone-950/60 lg:hidden"
        x-on:click="closeSidebar(false)"
        aria-hidden="true"
    ></div>

    <aside
        id="admin-sidebar"
        class="admin-sidebar"
        x-bind:data-open="sidebarOpen ? 'true' : 'false'"
        x-on:keydown.escape="closeSidebar()"
        aria-label="Panel navigasi admin"
    >
        <div class="flex items-start justify-between gap-3 px-5 pb-6 pt-5">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-lg">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-white p-1">
                    <img
                        src="{{ asset('images/logo-uir.png') }}"
                        alt="Logo Universitas Islam Riau"
                        width="85"
                        height="98"
                        class="h-full w-auto"
                    >
                </span>
                <span class="min-w-0">
                    <span class="block text-[0.62rem] font-semibold uppercase tracking-[0.2em] text-emerald-100/75">
                        Fakultas Teknik · UIR
                    </span>
                    <span class="mt-0.5 block font-serif text-base leading-tight text-white">
                        Panel Admin
                    </span>
                </span>
            </a>

            <button
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-white/25 text-white transition hover:bg-white/10 lg:hidden"
                x-on:click="closeSidebar()"
                x-ref="sidebarClose"
                aria-label="Tutup menu navigasi"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"></path>
                </svg>
            </button>
        </div>

        @php
            $navItems = [
                [
                    'key' => 'dashboard',
                    'label' => 'Dashboard',
                    'route' => 'admin.dashboard',
                    'icon' => '<rect x="3.5" y="3.5" width="7" height="7" rx="1.2"></rect><rect x="13.5" y="3.5" width="7" height="7" rx="1.2"></rect><rect x="3.5" y="13.5" width="7" height="7" rx="1.2"></rect><rect x="13.5" y="13.5" width="7" height="7" rx="1.2"></rect>',
                ],
                [
                    'key' => 'reports',
                    'label' => 'Laporan',
                    'route' => 'admin.reports',
                    'icon' => '<path d="M8 3.5h8a2 2 0 0 1 2 2v15H6v-15a2 2 0 0 1 2-2Z"></path><path stroke-linecap="round" d="M9.5 8.5h5M9.5 12h5M9.5 15.5h3"></path>',
                ],
                [
                    'key' => 'locations',
                    'label' => 'Lokasi',
                    'route' => 'admin.locations',
                    'icon' => '<path d="M4 20V6.5L12 3l8 3.5V20"></path><path stroke-linecap="round" d="M9.5 20v-5h5v5M8 9h1.5M14.5 9H16M8 12.5h1.5M14.5 12.5H16"></path>',
                ],
                [
                    'key' => 'activity',
                    'label' => 'Log Aktivitas',
                    'route' => 'admin.activity',
                    'icon' => '<circle cx="12" cy="12" r="8.5"></circle><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5V12l3 2"></path>',
                ],
            ];
        @endphp

        <nav class="flex flex-1 flex-col gap-1 px-3" aria-label="Navigasi admin">
            <p class="px-2 pb-2 text-[0.62rem] font-semibold uppercase tracking-[0.18em] text-emerald-100/60">
                Menu utama
            </p>

            @foreach ($navItems as $item)
                <a
                    href="{{ route($item['route']) }}"
                    class="admin-sidebar__link"
                    @if ($page === $item['key']) aria-current="page" @endif
                >
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                        {!! $item['icon'] !!}
                    </svg>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="mt-6 border-t border-white/15 px-5 py-4">
            <p class="text-xs font-semibold text-white">Admin (staf TU)</p>
            <p class="mt-0.5 text-xs text-emerald-50/70">Tata Usaha FT</p>
            <p class="mt-3 text-xs leading-relaxed text-emerald-50/70">
                Tampilan dengan data contoh. Belum tersambung ke basis data.
            </p>
            <p class="mt-2 text-[0.62rem] font-semibold uppercase tracking-[0.16em] text-emerald-100/50">
                Prototipe Fase 1
            </p>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-header">
            <div class="flex min-w-0 items-center gap-3">
                <button
                    type="button"
                    class="admin-icon-btn lg:hidden"
                    x-on:click="openSidebar()"
                    x-ref="menuButton"
                    aria-controls="admin-sidebar"
                    x-bind:aria-expanded="sidebarOpen ? 'true' : 'false'"
                    aria-label="Buka menu navigasi"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"></path>
                    </svg>
                </button>

                <div class="min-w-0">
                    <p class="hidden text-[0.62rem] font-semibold uppercase tracking-[0.18em] text-stone-500 dark:text-stone-400 sm:block">
                        Panel Admin
                    </p>
                    <h1 class="truncate font-serif text-xl leading-tight text-ft-green dark:text-emerald-200 sm:text-2xl">
                        {{ $pageTitle }}
                    </h1>
                    <p class="hidden truncate text-sm text-stone-600 dark:text-stone-400 md:block">
                        {{ $pageDescriptions[$page] ?? '' }}
                    </p>
                </div>
            </div>

            <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                <a href="{{ route('home') }}" class="admin-btn admin-btn--quiet hidden sm:inline-flex">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5h5v5M19 5l-7 7"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 14v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4"></path>
                    </svg>
                    Lihat situs
                </a>
                <a href="{{ route('home') }}" class="admin-icon-btn sm:hidden" aria-label="Lihat situs pengguna">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5h5v5M19 5l-7 7"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 14v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4"></path>
                    </svg>
                </a>

                <div class="hidden items-center gap-2 border-l border-stone-300 pl-3 dark:border-stone-700 md:flex">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-ft-green text-xs font-bold text-white dark:bg-emerald-800">
                        TU
                    </span>
                    <span class="leading-tight">
                        <span class="block text-sm font-semibold">Admin (staf TU)</span>
                        <span class="block text-xs text-stone-500 dark:text-stone-400">Tata Usaha FT</span>
                    </span>
                </div>
            </div>
        </header>

        <div class="admin-preview-banner" role="note">
            <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.5 21 20H3L12 3.5Z"></path>
                <path stroke-linecap="round" d="M12 9.5v4M12 16.8h.01"></path>
            </svg>
            <p>
                <span class="font-semibold">Mode pratinjau: akses admin belum dibatasi.</span>
                Panel ini memakai data contoh. Pembatasan peran dan login menyusul di fase berikutnya.
            </p>
        </div>

        <main id="admin-content" class="admin-content" tabindex="-1">
            @yield('content')
        </main>

        <footer class="border-t border-stone-300/70 px-4 py-5 text-xs text-stone-500 dark:border-stone-800 dark:text-stone-400 sm:px-6 lg:px-8">
            Lapor Fasilitas FT UIR · Panel admin masih berupa tampilan (UI) dengan data contoh.
        </footer>
    </div>

    @include('admin.partials.detail-drawer')

    <div class="admin-toast-region" aria-live="polite" aria-atomic="true">
        <div x-show="toast" x-cloak x-transition.opacity class="admin-toast">
            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m5 12.5 4.5 4.5L19 7"></path>
            </svg>
            <span x-text="toast"></span>
        </div>
    </div>
</body>
</html>
