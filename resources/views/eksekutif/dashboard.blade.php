<x-layouts.panel>
    <x-slot name="header">
        <h1>Ikhtisar Perpustakaan</h1>
    </x-slot>

    <section class="dashboard-panel">
        <div class="dashboard-panel__heading">
            <div>
                <h2>Ikhtisar kinerja</h2>
                <p>Ringkasan untuk memantau koleksi dan layanan perpustakaan.</p>
            </div>
        </div>
        <div class="dashboard-empty">
            <x-ui.icon name="chart" />
            <p>Data transaksi dan peminjaman belum disediakan untuk dashboard ini. Grafik tren dan daftar buku terpopuler akan tampil setelah data analitik tersedia.</p>
        </div>
    </section>

    <div class="dashboard-grid">
        <section class="dashboard-panel">
            <div class="dashboard-panel__heading">
                <div>
                    <h2>Fokus evaluasi</h2>
                    <p>Indikator yang dapat ditinjau dari laporan operasional.</p>
                </div>
            </div>
            <ul class="list-disc space-y-2 pl-5 text-sm leading-6 text-gray-700">
                <li>Tren peminjaman dan pengembalian.</li>
                <li>Ketersediaan koleksi per kategori.</li>
                <li>Keterlambatan dan penyelesaian denda.</li>
            </ul>
        </section>

        <aside class="dashboard-panel">
            <div class="dashboard-panel__heading">
                <div>
                    <h2>Akses koleksi</h2>
                    <p>Jelajahi buku yang tersedia bagi anggota.</p>
                </div>
            </div>
            <a class="dashboard-action" href="{{ route('katalog.index') }}">Buka katalog <x-ui.icon name="catalog" /></a>
        </aside>
    </div>
</x-layouts.panel>
