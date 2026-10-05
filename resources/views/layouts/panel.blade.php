@php
    $user = auth()->user();

    $groups = match ($user->role) {
        'admin' => [
            [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => '&#8962;'],
                ['label' => 'Pengguna', 'route' => 'admin.pengguna.index', 'active' => 'admin.pengguna.*', 'icon' => '&#9787;'],
                ['label' => 'Pengaturan', 'route' => 'admin.pengaturan.edit', 'active' => 'admin.pengaturan.*', 'icon' => '&#9881;'],
            ],
            [
                ['heading' => 'Master Data'],
                ['label' => 'Buku', 'route' => 'pustakawan.buku.index', 'active' => 'pustakawan.buku.*', 'icon' => '&#9638;'],
                ['label' => 'Kategori', 'route' => 'pustakawan.kategori.index', 'active' => 'pustakawan.kategori.*', 'icon' => '&#9776;'],
            ],
            [
                ['heading' => 'Transaksi'],
                ['label' => 'Peminjaman', 'route' => 'pustakawan.peminjaman.index', 'active' => 'pustakawan.peminjaman.*', 'icon' => '&#8644;'],
                ['label' => 'Pengembalian', 'route' => 'pustakawan.pengembalian.index', 'active' => 'pustakawan.pengembalian.*', 'icon' => '&#8635;'],
                ['label' => 'Denda', 'route' => 'pustakawan.denda.index', 'active' => 'pustakawan.denda.*', 'icon' => '&#36;'],
            ],
        ],
        'pustakawan' => [
            [
                ['label' => 'Dashboard', 'route' => 'pustakawan.dashboard', 'active' => 'pustakawan.dashboard', 'icon' => '&#8962;'],
            ],
            [
                ['heading' => 'Master Data'],
                ['label' => 'Buku', 'route' => 'pustakawan.buku.index', 'active' => 'pustakawan.buku.*', 'icon' => '&#9638;'],
                ['label' => 'Kategori', 'route' => 'pustakawan.kategori.index', 'active' => 'pustakawan.kategori.*', 'icon' => '&#9776;'],
            ],
            [
                ['heading' => 'Transaksi'],
                ['label' => 'Peminjaman', 'route' => 'pustakawan.peminjaman.index', 'active' => 'pustakawan.peminjaman.*', 'icon' => '&#8644;'],
                ['label' => 'Pengembalian', 'route' => 'pustakawan.pengembalian.index', 'active' => 'pustakawan.pengembalian.*', 'icon' => '&#8635;'],
                ['label' => 'Denda', 'route' => 'pustakawan.denda.index', 'active' => 'pustakawan.denda.*', 'icon' => '&#36;'],
            ],
        ],
        'kepala_perpustakaan' => [[
            ['label' => 'Dashboard Analitik', 'route' => 'eksekutif.dashboard', 'active' => 'eksekutif.dashboard', 'icon' => '&#9636;'],
            ['label' => 'Laporan', 'route' => 'eksekutif.laporan.index', 'active' => 'eksekutif.laporan.*', 'icon' => '&#9776;'],
        ]],
        default => [[
            ['label' => 'Dashboard', 'route' => 'mahasiswa.dashboard', 'active' => 'mahasiswa.dashboard', 'icon' => '&#8962;'],
            ['label' => 'Riwayat Saya', 'route' => 'mahasiswa.riwayat', 'active' => 'mahasiswa.riwayat', 'icon' => '&#8635;'],
        ]],
    };

    $groups[] = [[
        'label' => 'Katalog',
        'route' => 'katalog.index',
        'active' => 'katalog.*',
        'icon' => '&#128218;',
    ]];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Perpustakaan Digital UNHASY') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-800">
    <x-flash />

    <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-gray-100">
        <button
            x-cloak
            x-show="sidebarOpen"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-30 bg-gray-950/50 lg:hidden"
            aria-label="Tutup menu"
        ></button>

        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col bg-gradient-to-b from-green-800 to-green-900 text-white transition-transform duration-200 ease-out lg:translate-x-0"
        >
            <div class="flex h-20 shrink-0 items-center gap-3 border-b border-white/10 px-6">
                <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3">
                    <img src="{{ asset('images/logo-unhasy-baru.webp') }}" alt="Logo UNHASY" class="h-11 w-11 shrink-0 rounded-full bg-white object-contain p-1">
                    <span class="text-sm font-semibold leading-tight">Perpustakaan Digital<br>UNHASY</span>
                </a>
                <button @click="sidebarOpen = false" class="ml-auto text-2xl leading-none text-white/80 lg:hidden" aria-label="Tutup menu">&times;</button>
            </div>

            <nav class="flex-1 space-y-5 overflow-y-auto px-3 py-5" aria-label="Navigasi utama">
                @foreach ($groups as $group)
                    <div class="space-y-1">
                        @foreach ($group as $item)
                            @if (isset($item['heading']))
                                <p class="px-3 pb-1 pt-2 text-[11px] font-semibold uppercase tracking-wider text-green-200/70">{{ $item['heading'] }}</p>
                            @else
                                @php($active = request()->routeIs($item['active']))
                                @if (Route::has($item['route']))
                                    <a
                                        href="{{ route($item['route']) }}"
                                        @if ($active) aria-current="page" @endif
                                        class="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm transition {{ $active ? 'bg-white text-green-900 shadow-sm' : 'text-green-50 hover:bg-white/10 hover:text-white' }}"
                                    >
                                        <span class="w-5 text-center text-lg leading-none" aria-hidden="true">{!! $item['icon'] !!}</span>
                                        <span>{{ $item['label'] }}</span>
                                    </a>
                                @else
                                    <span class="flex cursor-not-allowed items-center gap-3 rounded-md px-3 py-2.5 text-sm text-green-100/50" aria-disabled="true" title="Segera tersedia">
                                        <span class="w-5 text-center text-lg leading-none" aria-hidden="true">{!! $item['icon'] !!}</span>
                                        <span>{{ $item['label'] }}</span>
                                    </span>
                                @endif
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </nav>

            <div class="border-t border-white/10 p-4">
                <div class="mb-3 flex min-w-0 items-center gap-3 px-2">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-green-100 text-sm font-bold uppercase text-green-900">{{ mb_substr($user->name, 0, 1) }}</span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium">{{ $user->name }}</p>
                        <p class="truncate text-xs capitalize text-green-100/70">{{ str_replace('_', ' ', $user->role) }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-md px-3 py-2 text-left text-sm text-green-50 hover:bg-white/10">Keluar</button>
                </form>
            </div>
        </aside>

        <div class="min-h-screen lg:pl-72">
            <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 shadow-sm sm:px-6">
                <div class="flex min-w-0 items-center gap-3">
                    <button @click="sidebarOpen = true" class="-ml-2 rounded-md p-2 text-gray-600 hover:bg-gray-100 lg:hidden" aria-label="Buka menu">&#9776;</button>
                    <div class="truncate text-base font-semibold text-gray-900">{{ $header ?? 'Dashboard' }}</div>
                </div>
                <a href="{{ route('profile.edit') }}" class="flex shrink-0 items-center gap-2 rounded-md px-2 py-1.5 hover:bg-gray-50" aria-label="Profil {{ $user->name }}">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 text-sm font-semibold uppercase text-green-800">{{ mb_substr($user->name, 0, 1) }}</span>
                    <span class="hidden max-w-40 truncate text-sm font-medium text-gray-700 sm:block">{{ $user->name }}</span>
                </a>
            </header>

            <main class="mx-auto w-full max-w-7xl p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>