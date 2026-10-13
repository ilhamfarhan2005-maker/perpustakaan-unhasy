@php
    $borrowDurationDays = \App\Models\Setting::get('borrow_duration_days', 7);
    $maxBorrowPerUser = \App\Models\Setting::get('max_borrow_per_user', 3);
    $finePerDay = \App\Models\Setting::get('fine_per_day', 1000);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#F7F4EC">
    <title>Perpustakaan Digital UNHASY</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,400;6..72,500;6..72,600;6..72,700&family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="opac-page">
    <a class="opac-skip-link" href="#main-content">Lewati ke konten utama</a>
    <x-flash />
    @include('opac.partials.navigation')

    <main id="main-content">
        <section class="opac-hero" aria-labelledby="home-heading">
            <div class="opac-hero__inner">
                <div class="opac-hero__content">
                    <p class="opac-eyebrow">Universitas Hasyim Asy'ari</p>
                    <h1 id="home-heading">Ruang ilmu untuk tumbuh dan berkarya.</h1>
                    <p class="opac-hero__summary">
                        Jelajahi koleksi Perpustakaan Digital UNHASY, temukan buku yang Anda perlukan, lalu kunjungi kami untuk membacanya.
                    </p>

                    <form class="opac-search" action="{{ route('katalog.index') }}" method="GET" role="search">
                        <label class="opac-search__field" for="book-search">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <circle cx="10.8" cy="10.8" r="6.8" />
                                <path stroke-linecap="round" d="m16 16 4.2 4.2" />
                            </svg>
                            <input id="book-search" type="search" name="q" value="{{ request('q') }}"
                                   placeholder="Cari judul, pengarang, atau ISBN..." autocomplete="off">
                        </label>
                        <button class="opac-search__submit" type="submit">Cari buku</button>
                    </form>

                    <nav class="opac-quick-links" aria-label="Kategori pilihan">
                        <span class="opac-quick-links__label">Jelajahi:</span>
                        @forelse ($categories->take(4) as $category)
                            <a class="opac-chip" href="{{ route('katalog.index', ['kategori' => $category->slug]) }}">{{ $category->name }}</a>
                        @empty
                            <a class="opac-chip" href="{{ route('katalog.index') }}">Semua koleksi</a>
                        @endforelse
                    </nav>

                    <p class="opac-stat-line">
                        <strong>{{ number_format($totalBooks) }}</strong> judul
                        <span aria-hidden="true">·</span>
                        <strong>{{ number_format($availableBooks) }}</strong> tersedia
                        <span aria-hidden="true">·</span>
                        <strong>{{ number_format($totalCategories) }}</strong> kategori
                    </p>
                </div>
            </div>
        </section>

        <section class="opac-section" aria-labelledby="new-books-heading">
            <div class="opac-container">
                <div class="opac-section__heading">
                    <div>
                        <p class="opac-eyebrow">Dari rak perpustakaan</p>
                        <h2 id="new-books-heading">Baru Ditambahkan</h2>
                        <p>Kenali koleksi yang baru hadir di perpustakaan.</p>
                    </div>
                    <a class="opac-text-link" href="{{ route('katalog.index') }}">Lihat semua koleksi</a>
                </div>
                <div class="opac-book-grid">
                    @forelse ($latestBooks->take(5) as $book)
                        @include('opac.partials.book-card', ['book' => $book])
                    @empty
                        <p class="opac-empty-collection">Koleksi baru akan ditampilkan di sini setelah buku ditambahkan.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="opac-section opac-section--paper" aria-labelledby="popular-books-heading">
            <div class="opac-container">
                <div class="opac-section__heading">
                    <div>
                        <p class="opac-eyebrow">Sering dipinjam</p>
                        <h2 id="popular-books-heading">Terpopuler</h2>
                        <p>Buku yang paling banyak dipilih oleh anggota.</p>
                    </div>
                    <a class="opac-text-link" href="{{ route('katalog.index') }}">Jelajahi katalog</a>
                </div>
                <div class="opac-book-grid">
                    @forelse ($popularBooks as $book)
                        @include('opac.partials.book-card', ['book' => $book])
                    @empty
                        <p class="opac-empty-collection">Belum ada data peminjaman untuk menentukan koleksi terpopuler.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="opac-section" aria-labelledby="categories-heading">
            <div class="opac-container">
                <div class="opac-section__heading">
                    <div>
                        <p class="opac-eyebrow">Temukan bidang minat Anda</p>
                        <h2 id="categories-heading">Jelajahi Kategori</h2>
                    </div>
                    <a class="opac-text-link" href="{{ route('katalog.index') }}">Semua kategori</a>
                </div>
                <div class="opac-categories">
                    @forelse ($categories as $category)
                        <a class="opac-category" href="{{ route('katalog.index', ['kategori' => $category->slug]) }}">
                            {{ $category->name }} <span>{{ number_format($category->books_count) }} buku</span>
                        </a>
                    @empty
                        <p class="opac-empty-collection">Kategori buku akan ditampilkan setelah tersedia.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="opac-section opac-info" aria-labelledby="visit-heading">
            <div class="opac-container opac-info__grid">
                <div>
                    <p class="opac-eyebrow">Berkunjung dan membaca</p>
                    <h2 id="visit-heading">Sampai jumpa di perpustakaan.</h2>
                    <p class="opac-info__intro">Temukan ruang yang tenang untuk belajar, membaca, dan mengembangkan gagasan bersama komunitas kampus.</p>
                    <div class="opac-info__details">
                        <div>
                            <h3>Lokasi</h3>
                            <p>Jl. Tebuireng No. 1, Jombang, Jawa Timur<br>Kampus Universitas Hasyim Asy'ari</p>
                        </div>
                        <div>
                            <h3>Jam layanan</h3>
                            <p>Senin–Jumat<br>08.00–16.00 WIB</p>
                        </div>
                    </div>
                    <p class="opac-info__contact">Informasi layanan: silakan hubungi pustakawan di loket perpustakaan.</p>
                </div>

                <aside class="opac-rules" aria-label="Aturan peminjaman">
                    <h3>Ketentuan peminjaman</h3>
                    <div class="opac-rules__item">
                        <strong>Masa pinjam</strong>
                        <p>Maksimal {{ $borrowDurationDays }} hari per transaksi.</p>
                    </div>
                    <div class="opac-rules__item">
                        <strong>Batas buku</strong>
                        <p>Maksimal {{ $maxBorrowPerUser }} buku per anggota.</p>
                    </div>
                    <div class="opac-rules__item">
                        <strong>Denda keterlambatan</strong>
                        <p>Rp{{ number_format((float) $finePerDay, 0, ',', '.') }} per buku per hari.</p>
                    </div>
                </aside>
            </div>
        </section>
    </main>

    <footer class="opac-footer">
        <div class="opac-container opac-footer__inner">
            <p>Perpustakaan Universitas Hasyim Asy'ari<br>Jl. Tebuireng No. 1, Jombang, Jawa Timur · © {{ date('Y') }} UNHASY</p>
            <nav class="opac-footer__links" aria-label="Tautan footer">
                <a href="{{ route('katalog.index') }}">Katalog</a>
                <a href="{{ route('login') }}">Masuk</a>
            </nav>
        </div>
    </footer>
</body>
</html>
