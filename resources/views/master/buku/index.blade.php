<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Manajemen Buku</h2>
            <a href="{{ route('pustakawan.buku.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">+ Tambah Buku</a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
            <form method="GET" class="mb-4 flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul/penulis/ISBN" class="border rounded px-3 py-2 text-sm w-64">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Kategori</label>
                    <select name="category_id" class="border rounded px-3 py-2 text-sm">
                        <option value="">Semua Kategori</option>
                        @foreach($kategori as $kat)
                            <option value="{{ $kat->id }}" {{ request('category_id') == $kat->id ? 'selected' : '' }}>{{ $kat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded text-sm">Filter</button>
                </div>
                @if(request()->hasAny(['search','category_id']))
                    <div>
                        <a href="{{ route('pustakawan.buku.index') }}" class="text-sm text-gray-600">Reset</a>
                    </div>
                @endif
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left">Cover</th>
                            <th class="p-2 text-left">Judul</th>
                            <th class="p-2 text-left">Kategori</th>
                            <th class="p-2 text-left">Penulis</th>
                            <th class="p-2 text-left">ISBN</th>
                            <th class="p-2 text-center">Stok</th>
                            <th class="p-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($buku as $item)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-2">
                                @if($item->cover_image)
                                    <img src="{{ asset('storage/' . $item->cover_image) }}" alt="{{ $item->title }}" class="h-16 w-12 object-cover rounded border">
                                @else
                                    <div class="h-16 w-12 bg-gray-200 rounded border flex items-center justify-center text-[10px] text-gray-500">No img</div>
                                @endif
                            </td>
                            <td class="p-2 font-medium">{{ $item->title }}</td>
                            <td class="p-2">{{ $item->category->name ?? '-' }}</td>
                            <td class="p-2">{{ $item->author }}</td>
                            <td class="p-2">{{ $item->isbn }}</td>
                            <td class="p-2 text-center">{{ $item->stock }}</td>
                            <td class="p-2 text-center space-x-2">
                                <a href="{{ route('pustakawan.buku.show', $item) }}" class="text-gray-700 hover:underline">Detail</a>
                                <a href="{{ route('pustakawan.buku.edit', $item) }}" class="text-blue-600 hover:underline">Edit</a>
                                <form action="{{ route('pustakawan.buku.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus buku {{ $item->title }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-500">Belum ada buku.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $buku->links() }}</div>
        </div>
    </div>
</x-app-layout>
