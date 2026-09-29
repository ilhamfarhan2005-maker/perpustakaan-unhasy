@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm">Nama *</label>
        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" class="w-full border rounded px-3 py-2" required>
        @error('name')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm">Email *</label>
        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="w-full border rounded px-3 py-2" required>
        @error('email')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm">NIM/NIP</label>
        <input type="text" name="nim_nip" value="{{ old('nim_nip', $user->nim_nip ?? '') }}" class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm">No. HP</label>
        <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp ?? '') }}" class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm">Role *</label>
        <select name="role" class="w-full border rounded px-3 py-2" required>
            @foreach(['admin','pustakawan','mahasiswa','kepala_perpustakaan'] as $role)
                <option value="{{ $role }}" @selected(old('role', $user->role ?? '') == $role)>{{ $role }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm">Status *</label>
        <select name="status" class="w-full border rounded px-3 py-2" required>
            <option value="active" @selected(old('status', $user->status ?? '') == 'active')>Aktif</option>
            <option value="inactive" @selected(old('status', $user->status ?? '') == 'inactive')>Nonaktif</option>
        </select>
    </div>

    <div>
        <label class="block text-sm">Password {{ isset($user) ? '(kosongkan jika tidak diubah)' : '*' }}</label>
        <input type="password" name="password" class="w-full border rounded px-3 py-2" {{ isset($user) ? '' : 'required' }}>
        @error('password')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2">
    </div>
</div>

<div class="mt-6 flex gap-2">
    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
    <a href="{{ route('admin.pengguna.index') }}" class="bg-gray-300 px-4 py-2 rounded">Batal</a>
</div>
