<nav x-data="{ open: false }" class="bg-unhasy-green text-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <img src="{{ asset('images/logo-unhasy-baru.webp') }}" alt="UNHASY" class="h-10 w-10">
                        <span class="hidden font-bold text-white sm:block">Perpustakaan Digital UNHASY</span>
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-sm text-white/90 hover:text-white">Dashboard</a>
                        <a href="{{ route('admin.pengguna.index') }}" class="text-sm text-white/90 hover:text-white">Pengguna</a>
                        <a href="{{ route('admin.pengaturan.edit') }}" class="text-sm text-white/90 hover:text-white">Pengaturan</a>
                    @elseif(auth()->user()->isPustakawan())
                        <a href="{{ route('pustakawan.dashboard') }}" class="text-sm text-white/90 hover:text-white">Dashboard</a>
                        <a href="{{ route('pustakawan.peminjaman.index') }}" class="text-sm text-white/90 hover:text-white">Peminjaman</a>
                        <a href="{{ route('pustakawan.pengembalian.index') }}" class="text-sm text-white/90 hover:text-white">Pengembalian</a>
                        <a href="{{ route('pustakawan.denda.index') }}" class="text-sm text-white/90 hover:text-white">Denda</a>
                        <a href="{{ route('pustakawan.pembayaran.index') }}" class="text-sm text-white/90 hover:text-white">Verifikasi Pembayaran</a>
                    @elseif(auth()->user()->isKepalaPerpustakaan())
                        <a href="{{ route('eksekutif.dashboard') }}" class="text-sm text-white/90 hover:text-white">Dashboard</a>
                    @else
                        <a href="{{ route('mahasiswa.dashboard') }}" class="text-sm text-white/90 hover:text-white">Dashboard</a>
                        <a href="{{ route('mahasiswa.riwayat') }}" class="text-sm text-white/90 hover:text-white">Riwayat</a>
                        <a href="{{ route('mahasiswa.kartu') }}" class="text-sm text-white/90 hover:text-white">Kartu Anggota</a>
                        <a href="{{ route('mahasiswa.denda.index') }}" class="text-sm text-white/90 hover:text-white">Denda Saya</a>
                    @endif

                    <a href="{{ route('katalog.index') }}" class="text-sm text-white/90 hover:text-white">Katalog</a>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="text-sm text-white/90 hover:text-white">{{ auth()->user()->name }}</button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">Profil</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">Logout</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="hidden sm:flex sm:items-center sm:gap-4">
                        <a href="{{ route('katalog.index') }}" class="text-sm font-medium text-white/90 hover:text-white">Katalog</a>
                        <a href="{{ route('login') }}" class="rounded-full bg-terracotta px-6 py-2 font-semibold text-white transition hover:bg-terracotta/90">Login</a>
                        <a href="{{ route('register') }}" class="rounded-full border border-white/70 px-6 py-2 font-semibold text-white transition hover:bg-white/10">Daftar</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>
