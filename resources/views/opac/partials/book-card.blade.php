<a href="{{ route('katalog.show', $book) }}" class="opac-book" aria-label="Lihat {{ $book->title }}">
    <div class="opac-book__cover">
        @if ($book->cover_image)
            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Sampul {{ $book->title }}" loading="lazy" decoding="async">
        @else
            <div class="opac-book__placeholder" role="img" aria-label="Sampul belum tersedia: {{ $book->title }}">
                <span>{{ $book->title }}</span>
            </div>
        @endif
    </div>

    <div class="opac-book__details">
        <h3 class="opac-book__title">{{ $book->title }}</h3>
        <p class="opac-book__author">{{ $book->author }}</p>
        <div class="opac-book__meta">
            @if ($book->stock > 0)
                <span class="opac-book__badge opac-book__badge--available">Tersedia</span>
            @else
                <span class="opac-book__badge opac-book__badge--borrowed">Sedang Dipinjam</span>
            @endif
        </div>
        <p class="opac-book__location">Rak {{ $book->rak_location }}</p>
        @isset($book->transactions_count)
            <p class="opac-book__popular-count">{{ number_format($book->transactions_count) }} kali dipinjam</p>
        @endisset
    </div>
</a>
