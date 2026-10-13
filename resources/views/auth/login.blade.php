<x-guest-layout>
    <div class="w-full">
        <div class="auth-page-heading">
            <h2>Masuk</h2>
            <p>Silakan masuk ke akun Perpustakaan Digital UNHASY.</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div class="ui-field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <div class="ui-field">
                <label for="password">Kata sandi</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 text-sm">
                <label class="inline-flex min-h-11 items-center gap-2 text-gray-700" for="remember_me">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-unhasy-green focus:ring-unhasy-green">
                    <span>Ingat saya</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">Lupa kata sandi?</a>
                @endif
            </div>

            <button class="btn btn--primary btn--md w-full" type="submit">Masuk</button>
        </form>

        <div class="mt-5 text-center text-sm text-gray-600">
            Belum memiliki akun?
            <a href="{{ route('register') }}">Daftar sekarang</a>
        </div>
    </div>
</x-guest-layout>
