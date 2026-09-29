<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Detail Peminjaman</h2>
    </x-slot>

    <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 bg-gray-50 px-5 py-4">
                <p class="text-xs font-medium uppercase text-gray-500">Kode transaksi</p>
                <p class="mt-1 font-mono text-xl font-bold text-green-800">TRX-{{ str_pad($transaksi->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
            <dl class="grid grid-cols-1 gap-x-6 gap-y-4 px-5 py-5 text-sm sm:grid-cols-2">
                <div><dt class="text-gray-500">Peminjam</dt><dd class="mt-1 font-medium text-gray-900">{{ $transaksi->user->name }} ({{ $transaksi->user->nim_nip }})</dd></div>
                <div><dt class="text-gray-500">Buku</dt><dd class="mt-1 font-medium text-gray-900">{{ $transaksi->book->title }}</dd></div>
                <div><dt class="text-gray-500">ISBN</dt><dd class="mt-1 text-gray-900">{{ $transaksi->book->isbn }}</dd></div>
                <div><dt class="text-gray-500">Petugas</dt><dd class="mt-1 text-gray-900">{{ $transaksi->handler->name ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Tanggal pinjam</dt><dd class="mt-1 text-gray-900">{{ $transaksi->borrow_date->translatedFormat('d F Y') }}</dd></div>
                <div><dt class="text-gray-500">Jatuh tempo</dt><dd class="mt-1 font-semibold text-gray-900">{{ $transaksi->due_date->translatedFormat('d F Y') }}</dd></div>
                <div><dt class="text-gray-500">Tanggal kembali</dt><dd class="mt-1 text-gray-900">{{ $transaksi->return_date?->translatedFormat('d F Y') ?? '-' }}</dd></div>
                <div>
                    <dt class="text-gray-500">Status</dt>
                    <dd class="mt-1"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $transaksi->status === 'selesai' ? 'bg-green-50 text-green-800' : ($transaksi->status === 'terlambat' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-800') }}">{{ ucfirst($transaksi->status) }}</span></dd>
                </div>
                <div><dt class="text-gray-500">Perpanjangan</dt><dd class="mt-1 text-gray-900">{{ $transaksi->extension_count }} kali</dd></div>
            </dl>

            @if($transaksi->fine)
                <div class="mx-5 mb-5 rounded-md border border-red-200 bg-red-50 p-4 text-sm">
                    <p class="font-semibold text-red-900">Denda keterlambatan</p>
                    <p class="mt-1 text-red-800">{{ $transaksi->fine->days_late }} hari · Rp {{ number_format($transaksi->fine->amount, 0, ',', '.') }} · {{ $transaksi->fine->status === 'lunas' ? 'Lunas' : 'Belum lunas' }}</p>
                </div>
            @endif

            <div class="border-t border-gray-100 px-5 py-4">
                <a href="{{ route('pustakawan.peminjaman.index') }}" class="text-sm font-medium text-green-800 hover:underline">Kembali ke peminjaman</a>
            </div>
        </div>
    </div>
</x-app-layout>