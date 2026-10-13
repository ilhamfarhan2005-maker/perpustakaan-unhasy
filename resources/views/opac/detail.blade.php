<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="text-xs text-gray-500 mb-4">
            <a href="{{ route('home') }}" class="hover:underline">Beranda</a>
            <span class="mx-1">/</span>
            <a href="{{ route('katalog.index') }}" class="hover:underline">Katalog</a>
            <span class="mx-1">/</span>
            <span class="text-gray-700">{{ $buku->title }}</span>
        </nav>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    @if($buku->cover_image)
                        <img src="{{ asset('storage/' . $buku->cover_image) }}"
                             alt="{{ $buku->title }}"
                             class="w-full aspect-[3/4] object-cover">
                    @else
                        <div class="aspect-[3/4] bg-gray-100 flex items-center justify-center text-gray-400">
                            <div class="text-center">
                                <div class="text-6xl">📕</div>
                                <p class="text-xs mt-2">Cover tidak tersedia</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h1 class="text-2xl font-bold text-gray-800 leading-tight">
                        {{ $buku->title }}
                    </h1>
                    <p class="text-gray-600 mt-1">oleh <strong>{{ $buku->author }}</strong></p>

                    <div class="mt-3 flex flex-wrap gap-2">
                        <span class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full font-medium">
                            {{ ['fisik' => 'Fisik', 'digital' => 'Digital', 'fisik_digital' => 'Fisik dan digital'][$buku->format] ?? 'Fisik' }}
                        </span>
                        @if($buku->hasPhysicalCopy())
                            @if($buku->stock > 0)
                                <span class="bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full font-medium">
                                    ✓ Tersedia ({{ $buku->stock }} eksemplar)
                                </span>
                            @else
                                <span class="bg-red-100 text-red-800 text-sm px-3 py-1 rounded-full font-medium">
                                    ✗ Sedang Dipinjam Semua
                                </span>
                            @endif

                            <span class="bg-yellow-100 text-yellow-800 text-sm px-3 py-1 rounded-full">
                                📍 Rak {{ $buku->rak_location }}
                            </span>
                        @endif

                        @if($buku->category)
                            <a href="{{ route('katalog.index', ['kategori' => $buku->category->slug]) }}"
                               class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full hover:bg-blue-200">
                                {{ $buku->category->name }}
                            </a>
                        @endif
                    </div>

                    <dl class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div class="flex justify-between border-b border-dashed py-1.5">
                            <dt class="text-gray-500">ISBN</dt>
                            <dd class="font-medium">{{ $buku->isbn }}</dd>
                        </div>
                        <div class="flex justify-between border-b border-dashed py-1.5">
                            <dt class="text-gray-500">Penerbit</dt>
                            <dd class="font-medium text-right">{{ $buku->publisher }}</dd>
                        </div>
                        <div class="flex justify-between border-b border-dashed py-1.5">
                            <dt class="text-gray-500">Tahun Terbit</dt>
                            <dd class="font-medium">{{ $buku->year }}</dd>
                        </div>
                        <div class="flex justify-between border-b border-dashed py-1.5">
                            <dt class="text-gray-500">Kategori</dt>
                            <dd class="font-medium">{{ $buku->category->name ?? '-' }}</dd>
                        </div>
                        @if($buku->hasPhysicalCopy())
                            <div class="flex justify-between border-b border-dashed py-1.5">
                                <dt class="text-gray-500">Stok</dt>
                                <dd class="font-medium">{{ $buku->stock }} eksemplar</dd>
                            </div>
                            <div class="flex justify-between border-b border-dashed py-1.5">
                                <dt class="text-gray-500">Lokasi Rak</dt>
                                <dd class="font-medium">{{ $buku->rak_location }}</dd>
                            </div>
                        @endif
                    </dl>

                    @if($buku->preview_path)
                        <a href="{{ route('katalog.preview', $buku) }}" class="inline-flex rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Baca Pratinjau</a>
                    @elseif($buku->preview_url)
                        <a href="{{ $buku->preview_url }}" target="_blank" rel="noopener noreferrer"
                           class="inline-block mt-5 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg font-medium">
                            Baca Pratinjau
                        </a>
                    @endif

                    <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg text-sm">
                        @guest
                            <p class="text-green-900">
                                @if($buku->hasPhysicalCopy())
                                    💡 Ingin meminjam buku ini? Silakan
                                    <a href="{{ route('login') }}" class="font-semibold underline">login</a>
                                    atau
                                    <a href="{{ route('register') }}" class="font-semibold underline">daftar</a>
                                    sebagai mahasiswa, lalu tunjukkan NIM Anda ke pustakawan.
                                @else
                                    Buku digital dapat dibaca melalui tautan pratinjau di atas.
                                @endif
                            </p>
                        @else
                            @if(auth()->user()->isMahasiswa())
                                @if(!$buku->hasPhysicalCopy())
                                    <p class="text-green-900">Buku digital tidak dapat dipinjam secara fisik.</p>
                                @elseif(session('success'))
                                    <p class="text-green-900">{{ session('success') }}</p>
                                @elseif($errors->has('buku'))
                                    <p class="text-red-700">{{ $errors->first('buku') }}</p>
                                @elseif($pengajuanAktif)
                                    <p class="text-green-900">
                                        Pengajuan Anda untuk buku ini berstatus <strong>{{ $pengajuanAktif->status }}</strong>.
                                    </p>
                                @elseif($buku->stock > 0)
                                    <p class="text-green-900 mb-3">
                                        Ajukan peminjaman menggunakan akun mahasiswa Anda.
                                    </p>
                                    <form method="POST" action="{{ route('mahasiswa.peminjaman.store', $buku) }}">
                                        @csrf
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg font-medium">
                                            Ajukan Peminjaman
                                        </button>
                                    </form>
                                @else
                                    <p class="text-green-900">Buku belum tersedia untuk dipinjam.</p>
                                @endif
                            @else
                                <p class="text-green-900">
                                    Anda login sebagai <strong>{{ auth()->user()->role }}</strong>.
                                </p>
                            @endif
                        @endguest
                    </div>
                </div>
            </div>
        </div>

        @if($serupa->count())
            <section class="mt-10">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    Buku Lain di Kategori {{ $buku->category->name ?? '' }}
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach($serupa as $item)
                        <x-book-card :buku="$item" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-app-layout>
