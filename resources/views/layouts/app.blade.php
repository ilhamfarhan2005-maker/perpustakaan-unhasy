@php
    $user = auth()->user();
    $groups = [];

    if ($user?->isAdmin()) {
        $groups = [
            [
                ['heading' => 'Utama'],
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'home'],
            ],
            [
                ['heading' => 'Master Data'],
                ['label' => 'Buku', 'route' => 'pustakawan.buku.index', 'active' => 'pustakawan.buku.*', 'icon' => 'book'],
                ['label' => 'Kategori', 'route' => 'pustakawan.kategori.index', 'active' => 'pustakawan.kategori.*', 'icon' => 'category'],
                ['label' => 'Pengguna', 'route' => 'admin.pengguna.index', 'active' => 'admin.pengguna.*', 'icon' => 'users'],
            ],
            [
                ['heading' => 'Transaksi'],
                ['label' => 'Peminjaman', 'route' => 'pustakawan.peminjaman.index', 'active' => 'pustakawan.peminjaman.*', 'icon' => 'loan'],
                ['label' => 'Pengembalian', 'route' => 'pustakawan.pengembalian.index', 'active' => 'pustakawan.pengembalian.*', 'icon' => 'return'],
                ['label' => 'Denda', 'route' => 'pustakawan.denda.index', 'active' => 'pustakawan.denda.*', 'icon' => 'fine'],
                ['label' => 'Verifikasi Pembayaran', 'route' => 'pustakawan.pembayaran.index', 'active' => 'pustakawan.pembayaran.*', 'icon' => 'fine'],
            ],
            [
                ['heading' => 'Pengaturan'],
                ['label' => 'Pengaturan Sistem', 'route' => 'admin.pengaturan.edit', 'active' => 'admin.pengaturan.*', 'icon' => 'settings'],
            ],
        ];
    } elseif ($user?->isPustakawan()) {
        $groups = [
            [
                ['heading' => 'Utama'],
                ['label' => 'Dashboard', 'route' => 'pustakawan.dashboard', 'active' => 'pustakawan.dashboard', 'icon' => 'home'],
            ],
            [
                ['heading' => 'Master Data'],
                ['label' => 'Buku', 'route' => 'pustakawan.buku.index', 'active' => 'pustakawan.buku.*', 'icon' => 'book'],
                ['label' => 'Kategori', 'route' => 'pustakawan.kategori.index', 'active' => 'pustakawan.kategori.*', 'icon' => 'category'],
            ],
            [
                ['heading' => 'Transaksi'],
                ['label' => 'Peminjaman', 'route' => 'pustakawan.peminjaman.index', 'active' => 'pustakawan.peminjaman.*', 'icon' => 'loan'],
                ['label' => 'Pengembalian', 'route' => 'pustakawan.pengembalian.index', 'active' => 'pustakawan.pengembalian.*', 'icon' => 'return'],
                ['label' => 'Denda', 'route' => 'pustakawan.denda.index', 'active' => 'pustakawan.denda.*', 'icon' => 'fine'],
                ['label' => 'Verifikasi Pembayaran', 'route' => 'pustakawan.pembayaran.index', 'active' => 'pustakawan.pembayaran.*', 'icon' => 'fine'],
            ],
        ];
    } elseif ($user?->isKepalaPerpustakaan()) {
        $groups = [[
            ['heading' => 'Laporan'],
            ['label' => 'Dashboard Analitik', 'route' => 'eksekutif.dashboard', 'active' => 'eksekutif.dashboard', 'icon' => 'chart'],
        ]];
    } elseif ($user) {
        $groups = [[
            ['heading' => 'Keanggotaan'],
            ['label' => 'Dashboard', 'route' => 'mahasiswa.dashboard', 'active' => 'mahasiswa.dashboard', 'icon' => 'home'],
            ['label' => 'Riwayat', 'route' => 'mahasiswa.riwayat', 'active' => 'mahasiswa.riwayat', 'icon' => 'history'],
            ['label' => 'Denda Saya', 'route' => 'mahasiswa.denda.index', 'active' => 'mahasiswa.denda.*', 'icon' => 'fine'],
        ]];
    }

    if ($user) {
        $groups[] = [
            ['heading' => 'Koleksi'],
            ['label' => 'Katalog', 'route' => 'katalog.index', 'active' => 'katalog.*', 'icon' => 'catalog'],
        ];
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Perpustakaan Digital UNHASY') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,400;6..72,500;6..72,600;6..72,700&family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <x-flash />

    @auth
        <div class="app-shell" x-data="{ sidebarOpen: false }">
            <button class="app-shell__backdrop" :class="{ 'is-open': sidebarOpen }"
                    x-cloak x-show="sidebarOpen" @click="sidebarOpen = false"
                    aria-label="Tutup navigasi"></button>

            <aside class="app-sidebar" :class="{ 'is-open': sidebarOpen }" aria-label="Navigasi area anggota">
                <a href="{{ route('home') }}" class="app-sidebar__brand">
                    <img src="{{ asset('images/logo-unhasy-baru.webp') }}" alt="Logo UNHASY">
                    <span>Perpustakaan Digital<br>UNHASY</span>
                </a>

                <nav class="app-sidebar__nav" aria-label="Menu utama">
                    @foreach ($groups as $group)
                        <div class="app-sidebar__group">
                            @foreach ($group as $item)
                                @if (isset($item['heading']))
                                    <p class="app-sidebar__heading">{{ $item['heading'] }}</p>
                                @elseif (Route::has($item['route']))
                                    <a href="{{ route($item['route']) }}"
                                       class="app-sidebar__link {{ request()->routeIs($item['active']) ? 'is-active' : '' }}"
                                       @if (request()->routeIs($item['active'])) aria-current="page" @endif>
                                        <x-ui.icon :name="$item['icon']" />
                                        <span>{{ $item['label'] }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </nav>

                <div class="app-sidebar__footer">
                    <p class="app-sidebar__user">{{ $user->name }}<small class="app-sidebar__role">{{ str_replace('_', ' ', $user->role) }}</small></p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="app-sidebar__logout">
                            <x-ui.icon name="logout" />
                            Keluar
                        </button>
                    </form>
                </div>
            </aside>

            <div class="app-shell__main">
                <header class="app-topbar">
                    <div class="app-topbar__start">
                        <button type="button" class="app-shell__menu-toggle" @click="sidebarOpen = true"
                                aria-label="Buka navigasi">
                            <x-ui.icon name="menu" />
                        </button>
                        <div class="app-topbar__page">
                            <nav class="breadcrumb" aria-label="Breadcrumb">
                                <a href="{{ route('home') }}">Beranda</a>
                                <span aria-hidden="true">/</span>
                                <span>Area anggota</span>
                            </nav>
                            <div class="app-topbar__title">{{ $header ?? 'Dashboard' }}</div>
                        </div>
                    </div>
                    <div class="app-topbar__actions" x-data="{ profileOpen: false }">
                        <button type="button" class="app-topbar__identity" @click="profileOpen = !profileOpen"
                                :aria-expanded="profileOpen.toString()" aria-haspopup="true">
                            <span class="app-topbar__avatar">{{ mb_substr($user->name, 0, 1) }}</span>
                            <span>{{ $user->name }}</span>
                            <x-ui.icon name="chevron" class="h-4 w-4" />
                        </button>
                        <div class="app-topbar__menu" x-cloak x-show="profileOpen" @click.outside="profileOpen = false">
                            <a href="{{ route('profile.edit') }}"><x-ui.icon name="profile" class="h-4 w-4" />Profil</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"><x-ui.icon name="logout" class="h-4 w-4" />Keluar</button>
                            </form>
                        </div>
                    </div>
                </header>

                <main class="app-shell__content">
                    {{ $slot }}
                </main>
            </div>
        </div>
    @else
        <div class="public-layout">
            @include('opac.partials.navigation')
            <main class="public-layout__main">{{ $slot }}</main>
        </div>
    @endauth
</body>
</html>
