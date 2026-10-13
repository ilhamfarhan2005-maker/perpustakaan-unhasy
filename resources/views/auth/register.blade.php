<x-guest-layout>
    <div class="auth-page-heading">
        <h2>Daftar anggota</h2>
        <p>Buat akun untuk menjelajahi dan meminjam koleksi perpustakaan.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="ui-field">
            <x-input-label for="name" value="Nama lengkap" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="ui-field mt-4">
            <x-input-label for="nim_nip" value="NIM/NIP" />
            <x-text-input id="nim_nip" class="block mt-1 w-full" type="text" name="nim_nip" :value="old('nim_nip')" required />
            <x-input-error :messages="$errors->get('nim_nip')" class="mt-2" />
        </div>

        <div class="ui-field mt-4">
            <x-input-label for="no_hp" value="No. HP" />
            <x-text-input id="no_hp" class="block mt-1 w-full" type="text" name="no_hp" :value="old('no_hp')" />
            <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
        </div>

        <div class="ui-field mt-4">
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="ui-field mt-4">
            <x-input-label for="password" value="Kata sandi" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="ui-field mt-4">
            <x-input-label for="password_confirmation" value="Konfirmasi kata sandi" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="text-sm text-gray-600 underline hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-unhasy-green" href="{{ route('login') }}">
                Sudah memiliki akun?
            </a>

            <x-primary-button class="ms-4">
                Daftar
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
