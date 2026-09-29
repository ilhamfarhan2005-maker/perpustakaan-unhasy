<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <img src="{{ asset('images/logo-unhasy-baru.webp') }}" alt="UNHASY" class="h-10 w-10">
                        <span class="font-bold text-green-800 hidden sm:block">Perpustakaan Digital UNHASY</span>
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-700 hover:text-green-700">Dashboard</a>
                        <a href="{{ route('admin.pengguna.index') }}" class="text-sm text-gray-700 hover:text-green-700">Pengguna</a>
                        <a href="{{ route('admin.pengaturan.edit') }}" class="text-sm text-gray-700 hover:text-green-700">Pengaturan</a>
                    @elseif(auth()->user()->isPustakawan())
                        <a href="{{ route('pustakawan.dashboard') }}" class="text-sm text-gray-700 hover:text-green-700">Dashboard</a>
                        <a href="{{ route('pustakawan.peminjaman.index') }}" class="text-sm text-gray-700 hover:text-green-700">Peminjaman</a>
                        <a href="{{ route('pustakawan.pengembalian.index') }}" class="text-sm text-gray-700 hover:text-green-700">Pengembalian</a>
                        <a href="{{ route('pustakawan.denda.index') }}" class="text-sm text-gray-700 hover:text-green-700">Denda</a>
                    @elseif(auth()->user()->isKepalaPerpustakaan())
                        <a href="{{ route('eksekutif.dashboard') }}" class="text-sm text-gray-700 hover:text-green-700">Dashboard</a>
                    @else
                        <a href="{{ route('mahasiswa.dashboard') }}" class="text-sm text-gray-700 hover:text-green-700">Dashboard</a>
                        <a href="{{ route('mahasiswa.riwayat') }}" class="text-sm text-gray-700 hover:text-green-700">Riwayat</a>
                    @endif

                    <a href="{{ route('katalog.index') }}" class="text-sm text-gray-700 hover:text-green-700">Katalog</a>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="text-sm text-gray-700 hover:text-gray-900">{{ auth()->user()->name }}</button>
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
                    <a href="{{ route('katalog.index') }}" class="text-sm text-gray-700 hover:text-green-700">Katalog</a>
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-green-700">Login</a>
                    <a href="{{ route('register') }}" class="text-sm text-gray-700 hover:text-green-700">Daftar</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
