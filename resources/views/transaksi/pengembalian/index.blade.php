<x-layouts.panel>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Proses Pengembalian Buku</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-4 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            Pengembalian menambah stok buku dan menghitung denda otomatis jika melewati tanggal jatuh tempo.
        </div>
        @if($pengajuanKembali->isNotEmpty())
            <section class="mb-6 overflow-hidden rounded-lg border border-blue-200 bg-white shadow-sm">
                <div class="border-b border-blue-100 bg-blue-50 px-4 py-3">
                    <h3 class="font-semibold text-blue-900">Pengajuan Pengembalian dari Mahasiswa</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[650px] text-left text-sm">
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
                                            <button class="rounded-md bg-blue-700 px-3 py-2 text-xs font-semibold text-white">Konfirmasi</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
        <form method="GET" action="{{ route('pustakawan.pengembalian.index') }}" class="mb-4 flex flex-wrap items-end gap-2 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="min-w-0 flex-1">
                <label for="return-search" class="block text-sm font-medium text-gray-700">Cari transaksi pengembalian</label>
                <input id="return-search" type="search" name="search" value="{{ $search }}" placeholder="Kode transaksi, NIM, ISBN, atau kode inventaris"
                       class="mt-1 w-full rounded-md border-gray-300 font-mono text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
            </div>
            <button type="button" data-return-scan class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Pindai QR</button>
            <button type="submit" class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-900">Cari</button>
            @if($search !== '')
                <a href="{{ route('pustakawan.pengembalian.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700">Reset</a>
            @endif
            <div id="return-qr-reader" class="hidden w-full max-w-sm rounded-md border border-gray-200 p-2"></div>
            <p id="return-scan-message" class="hidden w-full text-sm text-red-600" role="alert"></p>
            <p class="w-full text-xs text-amber-800">Pemindai kamera memerlukan HTTPS atau localhost. Anda juga dapat memasukkan kode manual atau menggunakan pemindai barcode USB lalu menekan Enter.</p>
        </form>
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
                            <tr><td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500">{{ $search !== '' ? 'Tidak ditemukan transaksi aktif yang sesuai.' : 'Tidak ada buku yang sedang dipinjam.' }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 px-4 py-3">{{ $transaksi->links() }}</div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        (() => {
            const button = document.querySelector('[data-return-scan]');
            const reader = document.getElementById('return-qr-reader');
            const input = document.getElementById('return-search');
            const message = document.getElementById('return-scan-message');
            let scanner = null;

            button.addEventListener('click', async () => {
                if (!window.isSecureContext || !navigator.mediaDevices?.getUserMedia || typeof Html5Qrcode === 'undefined') {
                    message.textContent = 'Kamera memerlukan HTTPS atau localhost dan pustaka pemindai harus dapat dimuat. Gunakan input manual atau pemindai barcode USB.';
                    message.classList.remove('hidden');
                    return;
                }
                if (scanner) {
                    await scanner.stop();
                    scanner.clear();
                    scanner = null;
                }
                reader.classList.remove('hidden');
                scanner = new Html5Qrcode('return-qr-reader');
                try {
                    await scanner.start({ facingMode: 'environment' }, { fps: 10, qrbox: { width: 220, height: 160 } }, async (value) => {
                        input.value = value;
                        await scanner.stop();
                        scanner.clear();
                        scanner = null;
                        reader.classList.add('hidden');
                        input.form.requestSubmit();
                    }, () => {});
                } catch (error) {
                    message.textContent = 'Kamera tidak dapat diaktifkan. Periksa izin kamera atau gunakan input manual.';
                    message.classList.remove('hidden');
                }
            });
        })();
    </script>
</x-layouts.panel>