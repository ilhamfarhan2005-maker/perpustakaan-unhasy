<x-layouts.panel>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-800">Peminjaman Aktif</h2>
            <a href="{{ route('pustakawan.peminjaman.create') }}" class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800">+ Catat Peminjaman</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        @if($pengajuanPinjam->isNotEmpty())
            <section class="mb-6 overflow-hidden rounded-lg border border-amber-200 bg-white shadow-sm">
                <div class="border-b border-amber-100 bg-amber-50 px-4 py-3">
                    <h3 class="font-semibold text-amber-900">Pengajuan dari Mahasiswa</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[700px] text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-4 py-3">Mahasiswa</th><th class="px-4 py-3">Buku</th><th class="px-4 py-3">Tanggal pengajuan</th><th class="px-4 py-3 text-right">Aksi</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($pengajuanPinjam as $pengajuan)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $pengajuan->user->name }}</div>
                                        <div class="font-mono text-xs text-gray-500">{{ $pengajuan->user->nim_nip }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $pengajuan->buku->title }}</div>
                                        <div class="text-xs text-gray-500">Stok fisik: {{ $pengajuan->buku->stock }}</div>
                                    </td>
                                    <td class="px-4 py-3">{{ $pengajuan->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="inline-flex gap-2">
                                            <form method="POST" action="{{ route('pustakawan.pengajuan.proses', $pengajuan) }}">
                                                @csrf
                                                <input type="hidden" name="action" value="setujui">
                                                <button class="rounded-md bg-green-700 px-3 py-2 text-xs font-semibold text-white hover:bg-green-800">Setujui</button>
                                            </form>
                                            <form method="POST" action="{{ route('pustakawan.pengajuan.proses', $pengajuan) }}" class="flex gap-2">
                                                @csrf
                                                <input type="hidden" name="action" value="tolak">
                                                <label class="sr-only" for="alasan-{{ $pengajuan->id }}">Alasan penolakan</label>
                                                <input id="alasan-{{ $pengajuan->id }}" name="alasan_penolakan" required maxlength="1000" placeholder="Alasan penolakan" class="w-36 rounded-md border-gray-300 text-xs">
                                                <button class="rounded-md border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-50">Tolak</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        @if($pengajuanKembali->isNotEmpty())
            <section class="mb-6 overflow-hidden rounded-lg border border-blue-200 bg-white shadow-sm">
                <div class="border-b border-blue-100 bg-blue-50 px-4 py-3">
                    <h3 class="font-semibold text-blue-900">Pengajuan Pengembalian dari Mahasiswa</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[700px] text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-4 py-3">Mahasiswa</th><th class="px-4 py-3">Buku</th><th class="px-4 py-3">Waktu pengajuan</th><th class="px-4 py-3 text-right">Aksi</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($pengajuanKembali as $pengajuan)
                                <tr>
                                    <td class="px-4 py-3">{{ $pengajuan->user->name }} <span class="font-mono text-xs text-gray-500">({{ $pengajuan->user->nim_nip }})</span></td>
                                    <td class="px-4 py-3">{{ $pengajuan->book->title }}</td>
                                    <td class="px-4 py-3">{{ $pengajuan->return_requested_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <form method="POST" action="{{ route('pustakawan.pengembalian.store', $pengajuan) }}" onsubmit="return confirm('Konfirmasi pengembalian buku ini?')">
                                            @csrf
                                            <button class="rounded-md bg-blue-700 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-800">Konfirmasi</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <form method="GET" class="flex flex-wrap gap-2 border-b border-gray-100 p-4">
                <label for="search" class="sr-only">Cari peminjaman</label>
                <input id="search" type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIM/NIP, atau judul buku"
                       class="min-w-0 flex-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-green-600 focus:ring-green-600 sm:max-w-md">
                <button type="submit" class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-900">Cari</button>
                @if(request('search'))
                    <a href="{{ route('pustakawan.peminjaman.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Reset</a>
                @endif
            </form>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[780px] text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Peminjam</th>
                            <th class="px-4 py-3 font-semibold">Buku</th>
                            <th class="px-4 py-3 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 font-semibold">Jatuh tempo</th>
                            <th class="px-4 py-3 font-semibold">Sisa waktu</th>
                            <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($transaksi as $t)
                            @php($sisa = $t->sisaHari())
                            <tr class="align-top hover:bg-gray-50/70">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $t->user->name }}</div>
                                    <div class="mt-0.5 font-mono text-xs text-gray-500">{{ $t->user->nim_nip }}</div>
                                </td>
                                <td class="max-w-xs px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $t->book->title }}</div>
                                    <div class="mt-0.5 text-xs text-gray-500">ISBN {{ $t->book->isbn }}</div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $t->borrow_date->format('d/m/Y') }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $t->due_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    @if($sisa < 0)
                                        <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">Terlambat {{ abs($sisa) }} hari</span>
                                    @elseif($sisa <= 2)
                                        <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800">{{ $sisa }} hari lagi</span>
                                    @else
                                        <span class="text-gray-600">{{ $sisa }} hari</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('pustakawan.peminjaman.show', $t) }}" class="text-xs font-medium text-green-800 hover:underline">Detail</a>
                                        <form method="POST" action="{{ route('pustakawan.pengembalian.store', $t) }}" onsubmit="return confirm('Proses pengembalian buku ini?')">
                                            @csrf
                                            <button type="submit" class="rounded border border-blue-200 px-2.5 py-1.5 text-xs font-medium text-blue-800 hover:bg-blue-50">Kembalikan</button>
                                        </form>
                                        @if($sisa >= 0 && $t->status === 'dipinjam')
                                            <form method="POST" action="{{ route('pustakawan.perpanjangan.store', $t) }}" onsubmit="return confirm('Perpanjang masa pinjam?')">
                                                @csrf
                                                <button type="submit" class="rounded border border-amber-200 px-2.5 py-1.5 text-xs font-medium text-amber-800 hover:bg-amber-50">Perpanjang</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500">Belum ada peminjaman aktif.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 px-4 py-3">{{ $transaksi->links() }}</div>
        </div>
    </div>
</x-layouts.panel>
