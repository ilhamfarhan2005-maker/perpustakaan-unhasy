<x-layouts.panel>
    <x-slot name="header">Riwayat Saya</x-slot>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Riwayat Peminjaman</h2>
                <p class="text-sm text-gray-500">Semua transaksi peminjaman Anda</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-gray-200 text-gray-600">
                    <tr>
                        <th class="pb-3 font-medium">Judul Buku</th>
                        <th class="pb-3 font-medium">Tanggal Pinjam</th>
                        <th class="pb-3 font-medium">Jatuh Tempo</th>
                        <th class="pb-3 font-medium">Tanggal Kembali</th>
                        <th class="pb-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pengajuan as $item)
                        <tr class="border-b border-gray-100 last:border-0">
                            <td class="py-3 pr-4 font-medium text-gray-800">{{ $item->buku?->title ?? '—' }}</td>
                            <td class="py-3 pr-4 text-gray-600">{{ optional($item->tanggal_pinjam)->translatedFormat('d M Y') ?? '-' }}</td>
                            <td class="py-3 pr-4 text-gray-600">-</td>
                            <td class="py-3 pr-4 text-gray-600">{{ optional($item->tanggal_kembali)->translatedFormat('d M Y') ?? '-' }}</td>
                            <td class="py-3">
                                @php
                                    $workflowStatus = $item->workflow_status ?? match ($item->status) {
                                        'pending' => 'Diajukan',
                                        'dipinjam' => 'Dipinjam',
                                        'dikembalikan' => 'Selesai',
                                        'ditolak' => 'Ditolak',
                                        default => ucfirst($item->status),
                                    };
                                    $statusClass = [
                                        'Diajukan' => 'bg-yellow-100 text-yellow-800',
                                        'Dipinjam' => 'bg-yellow-100 text-yellow-800',
                                        'Menunggu Konfirmasi Kembali' => 'bg-blue-100 text-blue-800',
                                        'Selesai' => 'bg-green-100 text-green-800',
                                        'Ditolak' => 'bg-red-100 text-red-800',
                                        'Kedaluwarsa' => 'bg-gray-100 text-gray-700',
                                    ][$workflowStatus] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                    {{ $workflowStatus }}
                                </span>
                                @if ($item->status === 'pending')
                                    <form method="POST" action="{{ route('mahasiswa.peminjaman.destroy', $item) }}" class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50">Batalkan Pengajuan</button>
                                    </form>
                                @elseif ($item->rejection_reason)
                                    <p class="mt-2 text-xs text-red-700">Alasan: {{ $item->rejection_reason }}</p>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @foreach ($riwayat as $item)
                        <tr class="border-b border-gray-100 last:border-0">
                            <td class="py-3 pr-4 font-medium text-gray-800">{{ $item->book?->title ?? '—' }}</td>
                            <td class="py-3 pr-4 text-gray-600">{{ optional($item->borrow_date)->translatedFormat('d M Y') ?? '-' }}</td>
                            <td class="py-3 pr-4 text-gray-600">{{ optional($item->due_date)->translatedFormat('d M Y') ?? '-' }}</td>
                            <td class="py-3 pr-4 text-gray-600">{{ optional($item->return_date)->translatedFormat('d M Y') ?? '-' }}</td>
                            <td class="py-3">
                                @php
                                    $statusClass = [
                                        'dipinjam' => 'bg-yellow-100 text-yellow-800',
                                        'terlambat' => 'bg-red-100 text-red-800',
                                        'selesai' => 'bg-green-100 text-green-800',
                                    ][$item->status] ?? 'bg-gray-100 text-gray-700';
                                    $workflowStatus = $item->workflow_status
                                        ?? ($item->status === 'selesai' ? 'Selesai' : ucfirst($item->status));
                                    if ($item->status === 'terlambat' && $workflowStatus === 'Dipinjam') {
                                        $workflowStatus = 'Terlambat';
                                    }
                                    if ($workflowStatus === 'Menunggu Konfirmasi Kembali') {
                                        $statusClass = 'bg-blue-100 text-blue-800';
                                    }
                                @endphp
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                    {{ $workflowStatus }}
                                </span>
                                @if (in_array($item->status, ['dipinjam', 'terlambat'], true) && ! $item->return_requested_at)
                                    <form method="POST" action="{{ route('mahasiswa.pengembalian.store', $item) }}" class="mt-2">
                                        @csrf
                                        <button type="submit" class="rounded-md border border-blue-200 px-3 py-1.5 text-xs font-semibold text-blue-800 hover:bg-blue-50">
                                            Ajukan Pengembalian
                                        </button>
                                    </form>
                                @elseif ($item->return_requested_at && in_array($item->status, ['dipinjam', 'terlambat'], true))
                                    <p class="mt-2 text-xs text-gray-500">Menunggu konfirmasi pustakawan.</p>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @if ($pengajuan->isEmpty() && $riwayat->isEmpty())
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">Belum ada riwayat peminjaman.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if ($riwayat->hasPages())
            <div class="mt-4">
                {{ $riwayat->links() }}
            </div>
        @endif
        @if ($pengajuan->hasPages())
            <div class="mt-4">
                {{ $pengajuan->links() }}
            </div>
        @endif
    </div>
</x-layouts.panel>
