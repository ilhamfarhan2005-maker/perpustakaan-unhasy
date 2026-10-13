<x-layouts.panel>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Denda Saya</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4">
            <p class="text-xs font-semibold uppercase text-red-700">Total denda belum lunas</p>
            <p class="mt-1 text-xl font-bold text-red-900">Rp {{ number_format($totalBelumDibayar, 0, ',', '.') }}</p>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Buku</th>
                            <th class="px-4 py-3 text-center font-semibold">Hari terlambat</th>
                            <th class="px-4 py-3 text-right font-semibold">Nominal</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                            <th class="px-4 py-3 font-semibold">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($fines as $fine)
                            @php
                                $status = $fine->isLunas() ? 'lunas' : ($fine->payment_status ?? 'belum_dibayar');
                                $statusLabel = [
                                    'belum_dibayar' => 'Belum Dibayar',
                                    'menunggu_verifikasi' => 'Menunggu Verifikasi',
                                    'lunas' => 'Lunas',
                                    'ditolak' => 'Ditolak',
                                ][$status] ?? 'Belum Dibayar';
                            @endphp
                            <tr>
                                <td class="px-4 py-3 text-gray-800">{{ $fine->transaction?->book?->title ?? 'Buku tidak tersedia' }}</td>
                                <td class="px-4 py-3 text-center text-gray-600">{{ $fine->days_late }} hari</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right font-mono font-medium">Rp {{ number_format($fine->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $status === 'lunas' ? 'bg-green-50 text-green-800' : ($status === 'menunggu_verifikasi' ? 'bg-amber-50 text-amber-800' : ($status === 'ditolak' ? 'bg-red-50 text-red-700' : 'bg-gray-100 text-gray-700')) }}">{{ $statusLabel }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    @foreach($fine->payments->where('user_id', auth()->id())->whereNotNull('proof_path') as $payment)
                                        <a href="{{ route('mahasiswa.pembayaran.bukti', $payment) }}" target="_blank" rel="noopener noreferrer" class="block text-sm font-semibold text-green-800 underline">Lihat bukti pembayaran</a>
                                    @endforeach
                                    @foreach($fine->payments->where('status', 'ditolak') as $payment)
                                        <p>{{ $payment->rejection_note }}</p>
                                    @endforeach
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500">Anda belum memiliki denda.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($availableFines->isNotEmpty())
            <section class="mt-6 rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900">Pembayaran melalui QRIS</h3>
                <p class="mt-1 text-sm text-gray-600">Pilih denda yang dibayar, transfer sesuai nominal yang ditampilkan, lalu unggah bukti pembayaran.</p>
                @if($qrisImagePath)
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($qrisImagePath) }}" alt="Kode QRIS pembayaran denda" class="mt-4 max-h-72 rounded border border-gray-200 p-2">
                    <form method="POST" action="{{ route('mahasiswa.denda.pembayaran.store') }}" enctype="multipart/form-data" class="mt-5 max-w-2xl">
                        @csrf
                        <fieldset>
                            <legend class="text-sm font-semibold text-gray-700">Pilih denda</legend>
                            <div class="mt-2 space-y-2">
                                @foreach($availableFines as $fine)
                                    <label class="flex items-center justify-between gap-4 rounded-md border border-gray-200 p-3">
                                        <span class="flex items-center gap-3">
                                            <input type="checkbox" name="fines[]" value="{{ $fine->id }}" data-amount="{{ $fine->amount }}" @checked(in_array($fine->id, old('fines', []))) class="rounded border-gray-300 text-green-700 focus:ring-green-600">
                                            <span class="text-sm text-gray-800">{{ $fine->transaction?->book?->title ?? 'Buku tidak tersedia' }}</span>
                                        </span>
                                        <span class="whitespace-nowrap font-mono text-sm">Rp {{ number_format($fine->amount, 0, ',', '.') }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('fines')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            @error('fines.*')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </fieldset>
                        <p class="mt-4 text-base font-semibold text-gray-900">Nominal yang harus ditransfer: <span id="selected-fines-total">Rp 0</span></p>
                        <label for="proof" class="mt-4 block text-sm font-medium text-gray-700">Bukti pembayaran (JPG, JPEG, atau PNG; maksimal 2 MB)</label>
                        <input id="proof" name="proof" type="file" required accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="mt-1 block w-full rounded-md border border-gray-300 text-sm shadow-sm">
                        @error('proof')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        <p class="mt-2 text-sm text-gray-600">Pastikan nominal transfer sesuai. Bukti akan diperiksa oleh pustakawan.</p>
                        <button type="submit" class="mt-4 rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800">Kirim Bukti Pembayaran</button>
                    </form>
                    <script>
                        document.querySelectorAll('input[name="fines[]"]').forEach((input) => {
                            input.addEventListener('change', () => {
                                const total = [...document.querySelectorAll('input[name="fines[]"]:checked')]
                                    .reduce((sum, item) => sum + Number(item.dataset.amount), 0);
                                document.getElementById('selected-fines-total').textContent =
                                    'Rp ' + new Intl.NumberFormat('id-ID').format(total);
                            });
                        });
                        document.querySelectorAll('input[name="fines[]"]:checked').forEach((input) => input.dispatchEvent(new Event('change')));
                    </script>
                @else
                    <p class="mt-4 rounded-md bg-amber-50 p-3 text-sm text-amber-800">QRIS belum tersedia. Silakan bayar tunai ke pustakawan.</p>
                @endif
            </section>
        @endif
    </div>
</x-layouts.panel>
