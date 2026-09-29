<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Manajemen Kategori</h2>
            <a href="{{ route('pustakawan.kategori.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">+ Tambah Kategori</a>
        </div>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
            <form method="GET" class="mb-4">
                <div class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kategori..." class="border rounded px-3 py-2 text-sm w-72">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded text-sm">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('pustakawan.kategori.index') }}" class="text-sm text-gray-600 self-center">Reset</a>
                    @endif
                </div>
            </form>

            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-left w-12">#</th>
                        <th class="p-2 text-left">Nama Kategori</th>
                        <th class="p-2 text-left">Slug</th>
                        <th class="p-2 text-center w-24">Jumlah Buku</th>
                        <th class="p-2 text-center w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($kategori as $i => $k)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-2">{{ $kategori->firstItem() + $i }}</td>
                        <td class="p-2 font-medium">{{ $k->name }}</td>
                        <td class="p-2 text-gray-500">{{ $k->slug }}</td>
                        <td class="p-2 text-center"><span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">{{ $k->books_count }}</span></td>
                        <td class="p-2 text-center space-x-2">
                            <a href="{{ route('pustakawan.kategori.edit', $k) }}" class="text-blue-600 hover:underline">Edit</a>
                            <form action="{{ route('pustakawan.kategori.destroy', $k) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kategori {{ $k->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500">Belum ada kategori.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div class="mt-4">{{ $kategori->links() }}</div>
        </div>
    </div>
</x-app-layout>
