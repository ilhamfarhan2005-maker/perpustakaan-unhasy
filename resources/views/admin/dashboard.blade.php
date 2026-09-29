<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard Admin</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded shadow p-6">
                <div class="text-sm text-gray-500">Total Pengguna</div>
                <div class="text-3xl font-bold mt-2">{{ \App\Models\User::count() }}</div>
            </div>
            <div class="bg-white rounded shadow p-6">
                <div class="text-sm text-gray-500">Buku</div>
                <div class="text-3xl font-bold mt-2">{{ \App\Models\Book::count() }}</div>
            </div>
            <div class="bg-white rounded shadow p-6">
                <div class="text-sm text-gray-500">Denda Hari Ini</div>
                <div class="text-3xl font-bold mt-2">Rp {{ number_format((float) \App\Models\Setting::get('fine_per_day', 0), 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
