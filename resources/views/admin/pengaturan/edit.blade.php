<x-layouts.panel>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Pengaturan</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-semibold text-gray-900">Pembayaran denda melalui QRIS</h3>
            <p class="mt-1 text-sm text-gray-600">Unggah gambar QRIS statis yang akan ditampilkan kepada mahasiswa.</p>

            @if($qrisImagePath)
                <div class="mt-4">
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($qrisImagePath) }}" alt="Kode QRIS pembayaran denda" class="max-h-72 rounded border border-gray-200 p-2">
                </div>
            @endif

            <form method="POST" action="{{ route('admin.pengaturan.update') }}" enctype="multipart/form-data" class="mt-5 max-w-xl">
                @csrf
                <label for="digital_preview_pages" class="block text-sm font-medium text-gray-700">Jumlah halaman pratinjau PDF</label>
                <input id="digital_preview_pages" name="digital_preview_pages" type="number" min="1" max="50" value="{{ old('digital_preview_pages', $previewPages) }}" class="mt-1 block w-full rounded border-gray-300">
                @error('digital_preview_pages')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                <label for="qris_image" class="block text-sm font-medium text-gray-700">Gambar QRIS (JPG, JPEG, atau PNG; maksimal 2 MB)</label>
                <input id="qris_image" name="qris_image" type="file" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="mt-1 block w-full rounded-md border border-gray-300 text-sm shadow-sm">
                @error('qris_image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                <button type="submit" class="mt-4 rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800">Simpan QRIS</button>
            </form>
        </div>
    </div>
</x-layouts.panel>
