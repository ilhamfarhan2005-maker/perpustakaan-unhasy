<x-app-layout>
    <section class="bg-gradient-to-br from-green-700 via-green-600 to-emerald-700 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-9 md:py-14">
            <div class="text-center max-w-3xl mx-auto">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-white/10 p-2 ring-1 ring-white/30 shadow-[0_12px_24px_rgba(0,0,0,0.28),inset_0_1px_0_rgba(255,255,255,0.45)]">
                    <img src="{{ asset('images/logo-unhasy-baru.webp') }}"
                         alt="UNHASY"
                         class="h-12 w-12 object-contain drop-shadow-[0_4px_3px_rgba(0,0,0,0.35)]">
                </div>

                <h1 class="text-2xl md:text-4xl font-bold leading-tight">
                    Perpustakaan Digital UNHASY
                </h1>
                <p class="mt-3 text-green-100 text-sm md:text-base">
                    Temukan koleksi buku perpustakaan Universitas Hasyim Asy'ari
                    secara online — cek ketersediaan &amp; lokasi rak sebelum datang.
                </p>

                <form action="{{ route('katalog.index') }}" method="GET"
                      class="mt-6 flex flex-col sm:flex-row gap-2 max-w-2xl mx-auto">
                    <div class="flex-1 relative">
                        <input type="text" name="q"
                               placeholder="Cari judul, pengarang, atau ISBN..."
                               value="{{ request('q') }}"
                               class="w-full rounded-lg py-2.5 px-4 text-gray-800 placeholder-gray-400 focus:ring-2 focus:ring-yellow-400 focus:outline-none shadow-lg">
                    </div>
                    <button type="submit"
                            class="bg-yellow-400 hover:bg-yellow-500 text-green-900 font-semibold px-5 py-2.5 rounded-lg shadow-lg whitespace-nowrap">
                        🔍 Cari Buku
                    </button>
                </form>

                <div class="mt-6 grid grid-cols-3 gap-4 max-w-2xl mx-auto text-center">
                    <div>
                        <div class="text-xl md:text-2xl font-bold text-yellow-300">{{ $totalBuku }}</div>
                        <div class="text-xs md:text-sm text-green-100">Total Buku</div>
                    </div>
                    <div>
                        <div class="text-xl md:text-2xl font-bold text-yellow-300">{{ $totalTersedia }}</div>
                        <div class="text-xs md:text-sm text-green-100">Judul Tersedia</div>
                    </div>
                    <div>
                        <div class="text-xl md:text-2xl font-bold text-yellow-300">{{ $totalKategori }}</div>
                        <div class="text-xs md:text-sm text-green-100">Kategori</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($kategoriList->count())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Jelajahi Kategori</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($kategoriList as $k)
                    <a href="{{ route('katalog.index', ['kategori' => $k->slug]) }}"
                       class="bg-white border border-gray-300 hover:border-green-600 hover:text-green-700 text-gray-700 text-sm px-4 py-2 rounded-full shadow-sm transition">
                        {{ $k->name }}
                        <span class="text-xs text-gray-400">({{ $k->books_count }})</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Koleksi Terbaru</h2>
            <a href="{{ route('katalog.index') }}" class="text-sm text-green-700 hover:underline">Lihat semua →</a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach($bukuTerbaru as $b)
                <x-book-card :buku="$b" />
            @endforeach
        </div>
    </section>

    @guest
        <section class="bg-gray-50 mt-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 text-center">
                <h3 class="text-xl font-semibold text-gray-800">
                    Mahasiswa UNHASY? Login untuk meminjam buku.
                </h3>
                <p class="text-gray-600 mt-2 text-sm">
                    Cek riwayat peminjaman, jatuh tempo, dan status denda langsung dari dashboard.
                </p>
                <div class="mt-4 flex gap-2 justify-center">
                    <a href="{{ route('login') }}"
                       class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg text-sm font-medium">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                       class="bg-white border border-green-600 text-green-700 hover:bg-green-50 px-5 py-2 rounded-lg text-sm font-medium">
                        Daftar Akun
                    </a>
                </div>
            </div>
        </section>
    @endguest

    <footer class="bg-green-800 text-green-100 mt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center text-sm">
            © {{ date('Y') }} Perpustakaan Digital — Universitas Hasyim Asy'ari Tebuireng Jombang
        </div>
    </footer>
</x-app-layout>
