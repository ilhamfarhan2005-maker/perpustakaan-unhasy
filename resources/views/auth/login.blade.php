<x-guest-layout>
    <div class="w-full max-w-md mx-auto">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-green-700 to-emerald-600 px-6 py-5 text-white text-center">
                <img src="{{ asset('images/logo-unhasy-baru.webp') }}" alt="UNHASY" class="mx-auto h-14 w-14 object-contain mb-2">
                <h2 class="text-2xl font-bold">Perpustakaan UNHASY</h2>
                <p class="text-sm text-green-100">Silakan masuk ke akun Anda</p>
            </div>

            <div class="p-6 sm:p-8">
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-200">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-200">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label for="remember_me" class="inline-flex items-center text-gray-600">
                            <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="ml-2">Ingat saya</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-green-700 hover:underline">
                                Lupa password?
                            </a>
                        @endif
                    </div>

                    <button type="submit"
                            class="w-full rounded-lg bg-green-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Masuk
                    </button>
                </form>

                <div class="mt-5 text-center text-sm text-gray-600">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="font-medium text-green-700 hover:underline">Daftar sekarang</a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
