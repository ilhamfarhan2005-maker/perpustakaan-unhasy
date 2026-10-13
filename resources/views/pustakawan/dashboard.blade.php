<x-layouts.panel>
    <x-slot name="header">
        <h1>Meja Layanan</h1>
    </x-slot>

    <section class="dashboard-panel">
        <div class="dashboard-panel__heading">
            <div>
                <h2>Selamat datang, {{ auth()->user()->name }}</h2>
                <p>Kelola sirkulasi dan bantu anggota menemukan koleksi perpustakaan.</p>
            </div>
        </div>
        <p class="dashboard-note">Ringkasan transaksi harian belum tersedia pada dashboard ini. Gunakan pintasan berikut untuk membuka antrean layanan.</p>
    </section>

    <div class="dashboard-grid">
        <section class="dashboard-panel">
            <div class="dashboard-panel__heading">
                <div>
                    <h2>Akses layanan</h2>
                    <p>Mulai pekerjaan sirkulasi dari halaman berikut.</p>
                </div>
            </div>
            <nav class="dashboard-actions" aria-label="Pintasan pustakawan">
                <a class="dashboard-action" href="{{ route('pustakawan.peminjaman.create') }}">Catat peminjaman <x-ui.icon name="loan" /></a>
                <a class="dashboard-action" href="{{ route('pustakawan.pengembalian.index') }}">Proses pengembalian <x-ui.icon name="return" /></a>
                <a class="dashboard-action" href="{{ route('pustakawan.denda.index') }}">Kelola denda <x-ui.icon name="fine" /></a>
            </nav>
        </section>

        <section class="dashboard-panel">
            <div class="dashboard-panel__heading">
                <div>
                    <h2>Koleksi</h2>
                    <p>Perbarui informasi buku dan kategori.</p>
                </div>
            </div>
            <nav class="dashboard-actions" aria-label="Pintasan koleksi">
                <a class="dashboard-action" href="{{ route('pustakawan.buku.index') }}">Data buku <x-ui.icon name="book" /></a>
                <a class="dashboard-action" href="{{ route('pustakawan.kategori.index') }}">Kategori buku <x-ui.icon name="category" /></a>
            </nav>
        </section>
    </div>
</x-layouts.panel>
