<x-layouts.panel>
    <x-slot name="header">
        <h1>Dashboard Mahasiswa</h1>
    </x-slot>

    <section class="dashboard-summary" aria-label="Ringkasan keanggotaan">
        <div class="dashboard-summary__item">
            <span class="dashboard-summary__label">Peminjaman Aktif</span>
            <strong class="dashboard-summary__value">{{ number_format($pinjamanAktif) }}</strong>
        </div>
        <div class="dashboard-summary__item">
            <span class="dashboard-summary__label">Denda Belum Lunas</span>
            <strong class="dashboard-summary__value">Rp {{ number_format((float) $dendaBelumLunas, 0, ',', '.') }}</strong>
        </div>
        <div class="dashboard-summary__item">
            <span class="dashboard-summary__label">Riwayat Peminjaman</span>
            <strong class="dashboard-summary__value">{{ number_format($riwayatCount) }}</strong>
        </div>
    </section>

    <div class="dashboard-grid">
        <section class="dashboard-panel">
            <div class="dashboard-panel__heading">
                <div>
                    <h2>Aktivitas peminjaman</h2>
                    <p>Periksa status dan tanggal jatuh tempo buku Anda.</p>
                </div>
                <a class="text-sm font-semibold text-unhasy-green-dark underline underline-offset-2" href="{{ route('mahasiswa.riwayat') }}">Buka riwayat</a>
            </div>

            @if ($peminjaman->isEmpty())
                <div class="dashboard-empty">
                    <x-ui.icon name="book" />
                    <p>Belum ada transaksi peminjaman. Jelajahi katalog untuk menemukan buku.</p>
                    <a class="btn btn--secondary btn--sm mt-4" href="{{ route('katalog.index') }}">Jelajahi katalog</a>
                </div>
            @else
                <div class="ui-table-wrap">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Judul buku</th>
                                <th>Status</th>
                                <th>Jatuh tempo</th>
                                <th>Sisa waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($peminjaman as $item)
                                @php
                                    $daysRemaining = $item->tanggal_kembali
                                        ? (int) now()->startOfDay()->diffInDays($item->tanggal_kembali, false)
                                        : null;
                                    $isUrgent = $item->status === 'dipinjam' && $daysRemaining !== null && $daysRemaining < 2;
                                    $statusBadge = match ($item->status) {
                                        'pending' => 'badge--pending',
                                        'dipinjam' => $isUrgent ? 'badge--late' : 'badge--pending',
                                        'dikembalikan' => 'badge--complete',
                                        'ditolak' => 'badge--warning',
                                        default => 'badge--warning',
                                    };
                                    $workflowStatus = $item->workflow_status ?? match ($item->status) {
                                        'pending' => 'Diajukan',
                                        'dipinjam' => 'Dipinjam',
                                        'dikembalikan' => 'Selesai',
                                        'ditolak' => 'Ditolak',
                                        default => ucfirst($item->status),
                                    };
                                    if ($workflowStatus === 'Menunggu Konfirmasi Kembali') {
                                        $statusBadge = 'badge--pending';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $item->buku?->title ?? 'Buku tidak tersedia' }}</strong>
                                        <span class="dashboard-table__muted block">{{ $item->buku?->author ?? '' }}</span>
                                    </td>
                                    <td><span class="badge {{ $statusBadge }}">{{ $workflowStatus }}</span></td>
                                    <td class="dashboard-table__muted">{{ optional($item->tanggal_kembali)->translatedFormat('d M Y') ?? '-' }}</td>
                                    <td>
                                        @if ($workflowStatus === 'Diajukan')
                                            <span class="dashboard-table__muted">Menunggu persetujuan</span>
                                        @elseif ($workflowStatus === 'Menunggu Konfirmasi Kembali')
                                            <span class="dashboard-table__muted">Menunggu konfirmasi pustakawan</span>
                                        @elseif ($item->status === 'dipinjam' && $daysRemaining !== null && $daysRemaining < 0)
                                            <span class="badge badge--late">Lewat {{ abs($daysRemaining) }} hari</span>
                                        @elseif ($item->status === 'dipinjam' && $daysRemaining === 0)
                                            <span class="badge badge--late">Jatuh tempo hari ini</span>
                                        @elseif ($item->status === 'dipinjam')
                                            <span class="{{ $isUrgent ? 'text-red-700' : 'text-unhasy-green-dark' }} text-sm font-semibold">
                                                {{ $daysRemaining === null ? 'Sedang dipinjam' : $daysRemaining.' hari lagi' }}
                                            </span>
                                        @elseif ($item->status === 'ditolak')
                                            <span class="dashboard-table__muted">Pengajuan ditolak</span>
                                        @else
                                            <span class="dashboard-table__muted">Selesai</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <aside class="dashboard-stack">
            <section class="dashboard-panel">
                <div class="dashboard-panel__heading">
                    <div>
                        <h2>Denda</h2>
                        <p>Jumlah tagihan yang belum dibayar.</p>
                    </div>
                    <x-ui.icon name="fine" class="h-5 w-5 text-unhasy-green-dark" />
                </div>
                <strong class="dashboard-summary__value">Rp {{ number_format((float) $dendaBelumLunas, 0, ',', '.') }}</strong>
                @if ((float) $dendaBelumLunas > 0)
                    <p class="dashboard-note mt-4">Periksa rincian denda dan kirim bukti pembayaran melalui QRIS.</p>
                @else
                    <p class="mt-3 text-sm text-gray-600">Tidak ada denda yang belum dilunasi.</p>
                @endif
                <a class="dashboard-action mt-4" href="{{ route('mahasiswa.denda.index') }}">Lihat dan bayar denda <x-ui.icon name="fine" /></a>
            </section>

            <section class="dashboard-panel">
                <div class="dashboard-panel__heading">
                    <div>
                        <h2>Akses cepat</h2>
                        <p>Lanjutkan kebutuhan perpustakaan Anda.</p>
                    </div>
                </div>
                <nav class="dashboard-actions" aria-label="Pintasan mahasiswa">
                    <a class="dashboard-action" href="{{ route('katalog.index') }}">Cari buku <x-ui.icon name="catalog" /></a>
                    <a class="dashboard-action" href="{{ route('mahasiswa.riwayat') }}">Lihat riwayat <x-ui.icon name="history" /></a>
                    <a class="dashboard-action" href="{{ route('mahasiswa.denda.index') }}">Denda Saya <x-ui.icon name="fine" /></a>
                    <a class="dashboard-action" href="{{ route('mahasiswa.kartu') }}">Kartu anggota <x-ui.icon name="profile" /></a>
                </nav>
            </section>
        </aside>
    </div>
</x-layouts.panel>
