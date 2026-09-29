<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Tambah Pengguna</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
            <form method="POST" action="{{ route('admin.pengguna.store') }}">
                @include('admin.pengguna._form')
            </form>
        </div>
    </div>
</x-app-layout>
