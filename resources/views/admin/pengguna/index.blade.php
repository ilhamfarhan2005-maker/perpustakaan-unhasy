<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Manajemen Pengguna</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
            <div class="flex justify-between mb-4">
                <form method="GET" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama/email/NIM..."
                           class="border rounded px-3 py-2 text-sm w-64">
                    <select name="role" class="border rounded px-3 py-2 text-sm">
                        <option value="">-- Semua Role --</option>
                        @foreach(['admin','pustakawan','mahasiswa','kepala_perpustakaan'] as $role)
                            <option value="{{ $role }}" @selected(request('role') == $role)>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                        @endforeach
                    </select>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded text-sm">Filter</button>
                </form>

                <a href="{{ route('admin.pengguna.create') }}" class="bg-green-600 text-white px-4 py-2 rounded text-sm">+ Tambah</a>
            </div>

            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-left">Nama</th>
                        <th class="p-2 text-left">Email</th>
                        <th class="p-2 text-left">NIM/NIP</th>
                        <th class="p-2 text-left">Role</th>
                        <th class="p-2 text-left">Status</th>
                        <th class="p-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr class="border-b">
                        <td class="p-2">{{ $user->name }}</td>
                        <td class="p-2">{{ $user->email }}</td>
                        <td class="p-2">{{ $user->nim_nip ?? '-' }}</td>
                        <td class="p-2">{{ $user->role }}</td>
                        <td class="p-2">
                            <span class="px-2 py-1 rounded text-xs {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->status }}
                            </span>
                        </td>
                        <td class="p-2 space-x-2">
                            <a href="{{ route('admin.pengguna.edit', $user) }}" class="text-blue-600">Edit</a>
                            @if($user->status === 'active' && $user->id !== auth()->id())
                                <form action="{{ route('admin.pengguna.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Nonaktifkan akun {{ $user->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600">Nonaktifkan</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center p-4 text-gray-500">Belum ada pengguna.</td></tr>
                @endforelse
                </tbody>
            </table>

            <div class="mt-4">{{ $users->links() }}</div>
        </div>
    </div>
</x-app-layout>
