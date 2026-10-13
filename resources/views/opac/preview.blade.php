<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-xl font-bold text-gray-800">Pratinjau: {{ $buku->title }}</h1>
        <p class="mt-1 text-sm text-gray-600">Pratinjau {{ $buku->preview_pages }} halaman pertama.</p>
        <iframe src="{{ route('katalog.preview.file', $buku) }}#toolbar=0&navpanes=0" title="Pratinjau {{ $buku->title }}" class="mt-4 h-[75vh] w-full rounded border border-gray-200"></iframe>
    </div>
</x-app-layout>
