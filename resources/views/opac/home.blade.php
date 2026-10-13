<x-app-layout>
    <section class="relative overflow-hidden bg-cover bg-center bg-no-repeat text-white"
             style="background-image: url('{{ asset('images/gedung-c.jpg') }}');">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900/50 via-slate-800/30 to-emerald-900/90"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-9 md:py-14">
            <div class="text-center max-w-3xl mx-auto">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-white/10 p-2 ring-1 ring-white/30 shadow-[0_12px_24px_rgba(0,0,0,0.28),inset_0_1px_0_rgba(255,255,255,0.45)]">
                    <img src="{{ asset('images/logo-unhasy-baru.webp') }}"
                         alt="UNHASY"
                         class="h-12 w-12 object-contain drop-shadow-[0_4px_3px_rgba(0,0,0,0.35)]">
                </div>

                <h1 class="text-2xl md:text-4xl font-bold leading-tight text-white">
                    Perpustakaan Digital UNHASY
                </h1>
                <p class="mt-3 text-sm md:text-base text-gray-100/90">
                    Temukan koleksi buku perpustakaan Universitas Hasyim Asy'ari
                    secara online — cek ketersediaan &amp; lokasi rak sebelum datang.
                </p>

                <form action="{{ route('katalog.index') }}" method="GET"
                      class="mt-6 flex flex-col sm:flex-row gap-2 max-w-2xl mx-auto">
                    <div class="flex-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 p-1 shadow-[0_8px_30px_rgba(0,0,0,0.18)]">
                        <input type="text" name="q"
                               placeholder="Cari judul, pengarang, atau ISBN..."
                               value="{{ request('q') }}"
                               class="w-full rounded-full border-0 bg-white py-4 px-5 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-300">
                    </div>
                    <button type="submit"
                            class="bg-terracotta hover:bg-terracotta/90 text-white font-semibold px-5 py-4 rounded-full shadow-lg whitespace-nowrap transition">
                        🔍 Cari Buku
                    </button>
                </form>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-3xl mx-auto text-center">
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 text-white shadow-xl">
                        <div class="mb-2 flex justify-center text-orange-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6.75A2.75 2.75 0 016.75 4h10.5A2.75 2.75 0 0120 6.75v10.5A2.75 2.75 0 0117.25 20H6.75A2.75 2.75 0 014 17.25V6.75zm2.75-.75a.75.75 0 00-.75.75v10.5c0 .414.336.75.75.75h10.5a.75.75 0 00.75-.75V6.75a.75.75 0 00-.75-.75H6.75zm1.5 3h7.5m-7.5 4h7.5"/>
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-white">{{ $totalBooks }}</div>
                        <div class="text-xs md:text-sm text-gray-100">Total Buku</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 text-white shadow-xl">
                        <div class="mb-2 flex justify-center text-orange-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15l3.75-4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-white">{{ $availableBooks }}</div>
                        <div class="text-xs md:text-sm text-gray-100">Tersedia</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 text-white shadow-xl">
                        <div class="mb-2 flex justify-center text-orange-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 017.5 5.25h9a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-9A2.25 2.25 0 015.25 16.5v-9zm2.25 2.25h9m-9 4.5h9"/>
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-white">{{ $totalCategories }}</div>
                        <div class="text-xs md:text-sm text-gray-100">Kategori</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($categories->count())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Jelajahi Kategori</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($categories as $category)
                    <a href="{{ route('katalog.index', ['kategori' => $category->slug]) }}"
                       class="bg-white border border-gray-300 hover:border-green-600 hover:text-green-700 text-gray-700 text-sm px-4 py-2 rounded-full shadow-sm transition">
                        {{ $category->name }}
                        <span class="text-xs text-gray-400">({{ $category->books_count }})</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="bg-gradient-to-b from-white to-slate-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">Smart circulation</p>
                <h2 class="mt-3 text-2xl md:text-3xl font-bold text-gray-900">Peminjaman Cepat &amp; Pintar</h2>
                <p class="mt-3 text-sm md:text-base text-gray-600">Cukup 3 langkah mudah dengan teknologi QR Code.</p>
            </div>

            <div class="mt-8 grid gap-5 md:grid-cols-3">
                <div class="rounded-2xl border border-gray-100 border-t-4 border-t-emerald-500 bg-white p-6 shadow-lg transition-all duration-300 hover:-translate-y-2 hover:shadow-xl">
                    <div class="mb-4 inline-block rounded-full bg-emerald-100 p-4 text-emerald-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5h3a2.25 2.25 0 012.25 2.25v1.5h1.5A2.25 2.25 0 0119.5 7.5v10.5A2.25 2.25 0 0117.25 20.25H6.75A2.25 2.25 0 014.5 18V7.5A2.25 2.25 0 016.75 5.25h1.5v-1.5A2.25 2.25 0 0110.5 1.5zm3 5.25h-3v1.5h3v-1.5zM8.25 10.5h7.5v1.5h-7.5v-1.5zm0 3h7.5v1.5h-7.5v-1.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">1. Buka E-Card</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Login untuk memunculkan E-Card QR unik Anda di layar HP.</p>
                </div>

                <div class="rounded-2xl border border-gray-100 border-t-4 border-t-emerald-500 bg-white p-6 shadow-lg transition-all duration-300 hover:-translate-y-2 hover:shadow-xl">
                    <div class="mb-4 inline-block rounded-full bg-emerald-100 p-4 text-emerald-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75A2.25 2.25 0 016.75 4.5h10.5A2.25 2.25 0 0119.5 6.75v10.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 17.25V6.75zm3.75 1.5h7.5m-7.5 3h7.5m-7.5 3h5.25"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">2. Ambil Buku</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Cari fisik buku di rak perpustakaan UNHASY sesuai kebutuhan Anda.</p>
                </div>

                <div class="rounded-2xl border border-gray-100 border-t-4 border-t-emerald-500 bg-white p-6 shadow-lg transition-all duration-300 hover:-translate-y-2 hover:shadow-xl">
                    <div class="mb-4 inline-block rounded-full bg-emerald-100 p-4 text-emerald-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.75h9a3 3 0 013 3V19.5l-3.75-2.25L7.5 19.5l-3.75-2.25V6.75a3 3 0 013-3zm1.5 4.5h6m-6 3h6"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">3. Scan &amp; Selesai</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Pustakawan akan memindai HP dan Buku Anda. Selesai dalam hitungan detik.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-slate-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Koleksi Terbaru</h2>
            <a href="{{ route('katalog.index') }}" class="text-sm text-green-700 hover:underline">Lihat semua →</a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach($latestBooks as $book)
                <article class="group cursor-pointer overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-transform duration-300 hover:-translate-y-2 hover:shadow-lg">
                    <div class="relative aspect-[2/3] overflow-hidden bg-gradient-to-br from-emerald-50 to-gray-200 shadow-[4px_4px_10px_rgba(0,0,0,0.1)]">
                        @if ($book->cover_image)
                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="h-full w-full object-cover rounded-t-xl">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-emerald-50 to-gray-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-emerald-700/80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75A2.25 2.25 0 1012 11.25 2.25 2.25 0 0012 6.75zm-5.25 8.25a2.25 2.25 0 014.5 0v.75h-4.5v-.75zm9.75 0a2.25 2.25 0 014.5 0v.75h-4.5v-.75zM4.5 7.5h8.25v10.5H4.5V7.5zm15 0h-6.75v10.5h6.75V7.5z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <div class="p-4">
                        <p class="text-xs font-medium text-emerald-700">{{ $book->category?->name ?? 'Tanpa Kategori' }}</p>
                        <h3 class="mt-2 line-clamp-2 text-sm font-semibold text-gray-900">{{ $book->title }}</h3>
                        <p class="mt-1 text-xs text-gray-500">{{ $book->author }}</p>

                        <div class="mt-4 flex items-center justify-between gap-2">
                            @if ($book->stock > 0)
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-semibold text-emerald-700">Tersedia</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-1 text-[10px] font-semibold text-red-700">Dipinjam</span>
                            @endif
                            <span class="text-[11px] text-gray-500">{{ $book->stock }} stok</span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        </div>
    </section>

    @guest
        <section class="bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 text-center">
                <h3 class="text-xl font-semibold text-gray-800">
                    Mahasiswa UNHASY? Login untuk meminjam buku.
                </h3>
                <p class="text-gray-600 mt-2 text-sm">
                    Cek riwayat peminjaman, jatuh tempo, dan status denda langsung dari dashboard.
                </p>
                <div class="mt-4 flex gap-3 justify-center">
                    <a href="{{ route('login') }}"
                               class="inline-flex items-center justify-center rounded-full bg-terracotta px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-terracotta/90">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center justify-center rounded-full border border-emerald-600 bg-white px-5 py-2.5 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50">
                        Daftar Akun
                    </a>
                </div>
            </div>
        </section>
    @endguest

    <footer class="bg-emerald-900 text-emerald-100">
        <div class="max-w-7xl mx-auto grid gap-6 px-4 py-10 sm:px-6 lg:grid-cols-2 lg:px-8">
            <div>
                <h3 class="text-lg font-bold text-white">Perpustakaan Universitas Hasyim Asy'ari</h3>
                <p class="mt-3 max-w-md text-sm text-emerald-100/80">
                    Jl. Tebuireng No. 1, Jombang, Jawa Timur<br>
                    Kampus Universitas Hasyim Asy'ari
                </p>
                <p class="mt-4 text-sm text-emerald-100/80">
                    Jam Operasional: Senin-Jumat, 08.00 - 16.00 WIB
                </p>
            </div>

            <div class="lg:text-right">
                <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-200">Menu</h4>
                <div class="mt-4 flex flex-col gap-2 text-sm text-emerald-100/80 lg:items-end">
                    <a href="{{ route('katalog.index') }}" class="hover:text-white">Bantuan</a>
                    <a href="{{ route('home') }}" class="hover:text-white">Kontak</a>
                    <a href="{{ route('login') }}" class="hover:text-white">Kebijakan Privasi</a>
                </div>
            </div>
        </div>
    </footer>
</x-app-layout>
