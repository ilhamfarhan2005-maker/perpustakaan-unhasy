<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Proses Pengembalian Buku</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-4 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            Pengembalian menambah stok buku dan menghitung denda otomatis jika melewati tanggal jatuh tempo.
        </div>
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr><th class="px-4 py-3 font-semibold">Peminjam</th><th class="px-4 py-3 font-semibold">Buku</th><th class="px-4 py-3 font-semibold">Jatuh tempo</th><th class="px-4 py-3 font-semibold">Status</th><th class="px-4 py-3 text-right font-semibold">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($transaksi as $t)
                            @php($sisa = $t->sisaHari())
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-4 py-3"><div class="font-medium text-gray-900">{{ $t->user->name }}</div><div class="mt-0.5 font-mono text-xs text-gray-500">{{ $t->user->nim_nip }}</div></td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $t->book->title }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $t->due_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    @if($sisa < 0)<span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">Terlambat {{ abs($sisa) }} hari</span>
                                    @elseif($sisa <= 2)<span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800">{{ $sisa }} hari lagi</span>
                                    @else<span class="text-gray-600">Dalam masa pinjam</span>@endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('pustakawan.pengembalian.store', $t) }}" onsubmit="return confirm('Kembalikan buku {{ addslashes($t->book->title) }}?')">
                                        @csrf
                                        <button type="submit" class="rounded-md bg-blue-700 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-800">Proses kembali</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500">Tidak ada buku yang sedang dipinjam.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 px-4 py-3">{{ $transaksi->links() }}</div>
        </div>
    </div>
</x-app-layout>