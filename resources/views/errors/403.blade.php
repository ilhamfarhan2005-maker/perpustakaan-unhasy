<x-app-layout>
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-red-600">403</h1>
            <p class="text-xl mt-2">Akses Ditolak</p>
            <p class="text-gray-600 mt-2">{{ $exception->getMessage() ?: 'Anda tidak punya izin.' }}</p>
            <a href="{{ url('/') }}" class="inline-block mt-4 bg-green-600 text-white px-4 py-2 rounded">Kembali ke Beranda</a>
        </div>
    </div>
</x-app-layout>
