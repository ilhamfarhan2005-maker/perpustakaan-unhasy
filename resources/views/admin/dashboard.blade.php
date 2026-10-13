<x-layouts.panel>
    <x-slot name="header">
        <h1>Ringkasan Administrasi</h1>
    </x-slot>

    <section class="dashboard-summary" aria-label="Ringkasan sistem">
        <div class="dashboard-summary__item">
            <span class="dashboard-summary__label">Total pengguna</span>
            <strong class="dashboard-summary__value">{{ number_format($totalUsers) }}</strong>
        </div>
        <div class="dashboard-summary__item">
            <span class="dashboard-summary__label">Mahasiswa</span>
            <strong class="dashboard-summary__value">{{ number_format($totalMahasiswa) }}</strong>
        </div>
        <div class="dashboard-summary__item">
            <span class="dashboard-summary__label">Stok buku</span>
            <strong class="dashboard-summary__value">{{ number_format($totalStock) }}</strong>
        </div>
        <div class="dashboard-summary__item">
            <span class="dashboard-summary__label">Peminjaman aktif</span>
            <strong class="dashboard-summary__value">{{ number_format($aktif) }}</strong>
        </div>
    </section>

    <div class="dashboard-grid">
        <section class="dashboard-panel">
            <div class="dashboard-panel__heading">
                <div>
                    <h2>Aktivitas terbaru</h2>
                    <p>Lima transaksi terakhir di perpustakaan.</p>
                </div>
                <a class="text-sm font-semibold text-unhasy-green-dark underline underline-offset-2" href="{{ route('pustakawan.peminjaman.index') }}">Lihat transaksi</a>
            </div>

            @if ($peminjamanTerbaru->isEmpty())
                <div class="dashboard-empty">
                    <x-ui.icon name="history" />
                    <p>Belum ada transaksi untuk ditampilkan.</p>
                </div>
            @else
                <div class="ui-table-wrap">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Anggota</th>
                                <th>Buku</th>
                                <th>Status</th>
                                <th>Tanggal pinjam</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($peminjamanTerbaru as $transaksi)
                                <tr>
                                    <td>{{ $transaksi->user?->name ?? 'Anggota' }}</td>
                                    <td>{{ $transaksi->book?->title ?? 'Buku tidak tersedia' }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match ($transaksi->status) {
                                                'dipinjam' => 'badge--pending',
                                                'terlambat' => 'badge--late',
                                                'selesai' => 'badge--complete',
                                                default => 'badge--warning',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ ucfirst($transaksi->status) }}</span>
                                    </td>
                                    <td class="dashboard-table__muted">{{ $transaksi->borrow_date?->translatedFormat('d M Y') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <div class="dashboard-stack">
            <section class="dashboard-panel">
                <div class="dashboard-panel__heading">
                    <div>
                        <h2>Keuangan hari ini</h2>
                        <p>{{ number_format($dendaCount) }} denda tercatat hari ini.</p>
                    </div>
                    <x-ui.icon name="fine" class="h-5 w-5 text-unhasy-green-dark" />
                </div>
                <p class="dashboard-summary__value">Rp{{ number_format($dendaHariIni, 0, ',', '.') }}</p>
                <p class="mt-2 text-sm text-gray-600">Denda belum lunas: Rp{{ number_format($totalDendaBelumLunas, 0, ',', '.') }}.</p>
            </section>

            <section class="dashboard-panel">
                <div class="dashboard-panel__heading">
                    <div>
                        <h2>Buku terfavorit bulan ini</h2>
                        <p>{{ number_format($pinjamBulanIni) }} peminjaman bulan ini.</p>
                    </div>
                </div>
                @if ($bukuTerfavorit?->book)
                    <p class="text-lg font-semibold text-gray-900">{{ $bukuTerfavorit->book->title }}</p>
                    <p class="mt-1 text-sm text-gray-600">{{ number_format($bukuTerfavorit->total) }} kali dipinjam</p>
                @else
                    <p class="text-sm leading-6 text-gray-600">Belum ada peminjaman bulan ini untuk menentukan buku terfavorit.</p>
                @endif
            </section>

            <section class="dashboard-panel">
                <div class="dashboard-panel__heading">
                    <div>
                        <h2>Pintasan</h2>
                        <p>Akses pengelolaan yang sering digunakan.</p>
                    </div>
                </div>
                <nav class="dashboard-actions" aria-label="Pintasan administrasi">
                    <a class="dashboard-action" href="{{ route('admin.pengguna.index') }}">Kelola pengguna <x-ui.icon name="users" /></a>
                    <a class="dashboard-action" href="{{ route('pustakawan.buku.index') }}">Kelola koleksi buku <x-ui.icon name="book" /></a>
                    <a class="dashboard-action" href="{{ route('admin.pengaturan.edit') }}">Pengaturan sistem <x-ui.icon name="settings" /></a>
                </nav>
                <p class="dashboard-note mt-4">Status cadangan tidak tersedia dari data dashboard saat ini.</p>
            </section>
        </div>
    </div>
</x-layouts.panel>
