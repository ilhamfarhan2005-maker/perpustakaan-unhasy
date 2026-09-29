<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Detail Buku</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded shadow p-6">
            <div class="flex gap-6 flex-col md:flex-row">
                <div class="flex-shrink-0">
                    @if($buku->cover_image)
                        <img src="{{ asset('storage/' . $buku->cover_image) }}" alt="{{ $buku->title }}" class="w-40 h-56 object-cover rounded border">
                    @else
                        <div class="w-40 h-56 bg-gray-200 border rounded flex items-center justify-center text-gray-500">No Image</div>
                    @endif
                </div>

                <div class="flex-1 space-y-3 text-sm">
                    <div><span class="font-semibold text-gray-600">Judul:</span> {{ $buku->title }}</div>
                    <div><span class="font-semibold text-gray-600">Kategori:</span> {{ $buku->category->name ?? '-' }}</div>
                    <div><span class="font-semibold text-gray-600">Penulis:</span> {{ $buku->author }}</div>
                    <div><span class="font-semibold text-gray-600">Penerbit:</span> {{ $buku->publisher }}</div>
                    <div><span class="font-semibold text-gray-600">ISBN:</span> {{ $buku->isbn }}</div>
                    <div><span class="font-semibold text-gray-600">Tahun:</span> {{ $buku->year }}</div>
                    <div><span class="font-semibold text-gray-600">Rak:</span> {{ $buku->rak_location }}</div>
                    <div><span class="font-semibold text-gray-600">Stok:</span> {{ $buku->stock }}</div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <a href="{{ route('pustakawan.buku.edit', $buku) }}" class="bg-blue-600 text-white px-4 py-2 rounded">Edit</a>
                <a href="{{ route('pustakawan.buku.index') }}" class="border px-4 py-2 rounded text-gray-700">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
