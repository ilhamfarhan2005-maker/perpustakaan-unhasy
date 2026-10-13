<x-guest-layout>
    <div class="auth-page-heading">
        <h2>Lupa kata sandi?</h2>
        <p>Masukkan email akun Anda. Kami akan mengirimkan tautan untuk membuat kata sandi baru.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="ui-field">
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Kirim tautan pemulihan
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
