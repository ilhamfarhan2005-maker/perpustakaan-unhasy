<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Catat Peminjaman Baru</h2>
    </x-slot>

    <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
            Masukkan NIM/NIP anggota dan ISBN buku. Tanggal pinjam serta jatuh tempo dihitung otomatis dari pengaturan.
        </div>

        <form method="POST" action="{{ route('pustakawan.peminjaman.store') }}" class="mt-5 rounded-lg border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            @csrf
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="nim_nip" class="block text-sm font-medium text-gray-700">NIM / NIP Anggota</label>
                    <input id="nim_nip" type="text" name="nim_nip" value="{{ old('nim_nip') }}" required autofocus
                           class="mt-1 w-full rounded-md border-gray-300 font-mono text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
                    @error('nim_nip') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="isbn" class="block text-sm font-medium text-gray-700">ISBN Buku</label>
                    <input id="isbn" type="text" name="isbn" value="{{ old('isbn') }}" required
                           class="mt-1 w-full rounded-md border-gray-300 font-mono text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
                    @error('isbn') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            @error('transaction') <p class="mt-4 text-sm text-red-600">{{ $message }}</p> @enderror

            <div class="mt-6 flex flex-wrap gap-2 border-t border-gray-100 pt-5">
                <button type="submit" class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800">Catat Peminjaman</button>
                <a href="{{ route('pustakawan.peminjaman.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>