<x-guest-layout>
    <div class="auth-page-heading">
        <h2>Verifikasi email</h2>
        <p>Periksa email Anda dan klik tautan verifikasi untuk mengaktifkan akun. Jika belum menerima email, kami dapat mengirim ulang tautannya.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert--success mb-4" role="status">
            Tautan verifikasi baru telah dikirim ke alamat email Anda.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="btn btn--secondary btn--sm">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>
