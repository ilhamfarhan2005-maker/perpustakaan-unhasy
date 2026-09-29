<x-app-layout>
    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-2xl font-bold text-gray-800">Katalog Buku</h1>
            <p class="text-sm text-gray-500 mt-1">
                Cari koleksi perpustakaan berdasarkan judul, pengarang, ISBN, atau kategori.
            </p>

            <form method="GET" action="{{ route('katalog.index') }}"
                  class="mt-4 grid grid-cols-1 md:grid-cols-12 gap-2">
                <div class="md:col-span-6">
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="🔍 Cari judul / pengarang / ISBN..."
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>

                <div class="md:col-span-3">
                    <select name="kategori"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-green-500">
                        <option value="">-- Semua Kategori --</option>
                        @foreach($kategoriList as $k)
                            <option value="{{ $k->slug }}" @selected(request('kategori') == $k->slug)>
                                {{ $k->name }} ({{ $k->books_count }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2 flex items-center">
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" name="tersedia" value="1" @checked(request('tersedia') === '1') class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        Hanya tersedia
                    </label>
                </div>

                <div class="md:col-span-1 flex gap-2">
                    <button type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white rounded-lg px-4 py-2.5 text-sm font-medium">
                        Cari
                    </button>
                </div>
            </form>

            @if(request('q') || request('kategori') || request('tersedia'))
                <div class="mt-2 text-sm text-gray-500">
                    Menampilkan hasil untuk
                    @if(request('q')) kata kunci "<strong>{{ request('q') }}</strong>" @endif
                    @if(request('kategori'))
                        · kategori <strong>{{ $kategoriList->firstWhere('slug', request('kategori'))?->name }}</strong>
                    @endif
                    — <a href="{{ route('katalog.index') }}" class="text-green-700 hover:underline">reset filter</a>
                </div>
            @endif
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if($buku->count())
            <div class="text-sm text-gray-500 mb-3">
                Ditemukan <strong>{{ $buku->total() }}</strong> buku
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-4">
                @foreach($buku as $item)
                    <x-book-card :buku="$item" />
                @endforeach
            </div>

            <div class="mt-6">
                {{ $buku->links() }}
            </div>
        @else
            <div class="bg-white rounded-xl border border-dashed border-gray-300 p-10 text-center">
                <div class="text-5xl mb-3">🔍</div>
                <h3 class="text-lg font-semibold text-gray-700">Buku tidak ditemukan</h3>
                <p class="text-sm text-gray-500 mt-1">
                    Coba kata kunci lain atau
                    <a href="{{ route('katalog.index') }}" class="text-green-700 hover:underline">lihat semua koleksi</a>.
                </p>
            </div>
        @endif
    </div>
</x-app-layout>
