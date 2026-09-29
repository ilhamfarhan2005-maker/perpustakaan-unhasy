@props(['buku'])

<a href="{{ route('katalog.show', $buku) }}"
   class="group bg-white rounded-xl shadow-sm hover:shadow-md border border-gray-100 overflow-hidden transition flex flex-col">
    <div class="aspect-[3/4] bg-gray-100 relative overflow-hidden">
        @if($buku->cover_image)
            <img src="{{ asset('storage/' . $buku->cover_image) }}"
                 alt="{{ $buku->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
        @else
            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="text-xs mt-1">No Cover</span>
            </div>
        @endif

        <div class="absolute top-2 right-2">
            @if($buku->stock > 0)
                <span class="bg-green-500 text-white text-[10px] font-semibold px-2 py-1 rounded-full shadow">
                    Tersedia ({{ $buku->stock }})
                </span>
            @else
                <span class="bg-red-500 text-white text-[10px] font-semibold px-2 py-1 rounded-full shadow">
                    Habis
                </span>
            @endif
        </div>
    </div>

    <div class="p-3 flex-1 flex flex-col">
        <h3 class="text-sm font-semibold text-gray-800 line-clamp-2 leading-snug" title="{{ $buku->title }}">
            {{ $buku->title }}
        </h3>
        <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $buku->author }}</p>

        <div class="mt-auto pt-2 flex items-center justify-between text-[11px]">
            <span class="text-gray-500">{{ $buku->category->name ?? '-' }}</span>
            <span class="bg-yellow-100 text-yellow-800 px-1.5 py-0.5 rounded font-mono">
                📍 {{ $buku->rak_location }}
            </span>
        </div>
    </div>
</a>
