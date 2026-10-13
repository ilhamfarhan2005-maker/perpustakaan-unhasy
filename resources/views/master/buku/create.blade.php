<x-layouts.panel>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Tambah Buku</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
            <form method="POST" action="{{ route('pustakawan.buku.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Kategori</label>
                        <select name="category_id" class="mt-1 block w-full border rounded p-2" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategori as $item)
                                <option value="{{ $item->id }}" {{ old('category_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Judul</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="mt-1 block w-full border rounded p-2" required>
                        @error('title')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">ISBN</label>
                        <input type="text" name="isbn" value="{{ old('isbn') }}" class="mt-1 block w-full border rounded p-2" required>
                        @error('isbn')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Format</label>
                        <select id="format" name="format" class="mt-1 block w-full border rounded p-2" required>
                            <option value="fisik" @selected(old('format', 'fisik') === 'fisik')>Fisik</option>
                            <option value="digital" @selected(old('format') === 'digital')>Digital</option>
                            <option value="fisik_digital" @selected(old('format') === 'fisik_digital')>Fisik dan digital</option>
                        </select>
                        @error('format')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kode Inventaris (opsional)</label>
                        <input type="text" name="kode_inventaris" value="{{ old('kode_inventaris') }}" class="mt-1 block w-full border rounded p-2">
                        @error('kode_inventaris')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Penulis</label>
                        <input type="text" name="author" value="{{ old('author') }}" class="mt-1 block w-full border rounded p-2" required>
                        @error('author')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Penerbit</label>
                        <input type="text" name="publisher" value="{{ old('publisher') }}" class="mt-1 block w-full border rounded p-2" required>
                        @error('publisher')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tahun</label>
                        <input type="number" name="year" value="{{ old('year') }}" class="mt-1 block w-full border rounded p-2" required>
                        @error('year')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div id="physical-rack-field">
                        <label class="block text-sm font-medium text-gray-700">Rak</label>
                        <input type="text" name="rak_location" value="{{ old('rak_location') }}" class="mt-1 block w-full border rounded p-2" required>
                        @error('rak_location')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div id="physical-stock-field">
                        <label class="block text-sm font-medium text-gray-700">Stok</label>
                        <input type="number" min="0" name="stock" value="{{ old('stock', 0) }}" class="mt-1 block w-full border rounded p-2" required>
                        @error('stock')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div id="digital-url-field" class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">URL Buku Digital (opsional)</label>
                        <input type="url" name="digital_url" value="{{ old('digital_url') }}" placeholder="https://contoh.ac.id/buku.pdf" class="mt-1 block w-full border rounded p-2">
                        @error('digital_url')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div id="preview-url-field" class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">URL Pratinjau (opsional bila mengunggah PDF)</label>
                        <input type="url" name="preview_url" value="{{ old('preview_url') }}" placeholder="https://contoh.ac.id/pratinjau" class="mt-1 block w-full border rounded p-2">
                        @error('preview_url')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        <label class="block mt-4 text-sm font-medium">File buku PDF (maksimal 20 MB)</label>
                        <input type="file" name="digital_file" accept="application/pdf,.pdf" class="mt-1 block w-full border rounded p-2">
                        <p class="mt-1 text-xs text-gray-500">Untuk Laragon, pastikan upload_max_filesize dan post_max_size di php.ini minimal 20M.</p>
                        @error('digital_file')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Cover Buku</label>
                        <input type="file" name="cover_image" accept="image/*" class="mt-1 block w-full border rounded p-2">
                        @error('cover_image')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <a href="{{ route('pustakawan.buku.index') }}" class="px-4 py-2 border rounded text-gray-700">Batal</a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (() => {
            const format = document.getElementById('format');
            const physicalFields = ['physical-rack-field', 'physical-stock-field'];
            const digitalUrlField = document.getElementById('digital-url-field');
            const previewUrlField = document.getElementById('preview-url-field');

            const updateFormatFields = () => {
                const hasPhysicalCopy = ['fisik', 'fisik_digital'].includes(format.value);
                const hasDigitalCopy = ['digital', 'fisik_digital'].includes(format.value);

                physicalFields.forEach((id) => {
                    const wrapper = document.getElementById(id);
                    const input = wrapper.querySelector('input');
                    wrapper.hidden = !hasPhysicalCopy;
                    input.disabled = !hasPhysicalCopy;
                    input.required = hasPhysicalCopy;
                });

                digitalUrlField.hidden = !hasDigitalCopy;
                previewUrlField.hidden = !hasDigitalCopy;
                digitalUrlField.querySelector('input').disabled = !hasDigitalCopy;
                const previewUrl = previewUrlField.querySelector('input');
                previewUrl.disabled = !hasDigitalCopy;
                previewUrl.required = false;
            };

            format.addEventListener('change', updateFormatFields);
            updateFormatFields();
        })();
    </script>
</x-layouts.panel>
