<header class="opac-nav" x-data="{ menuOpen: false, userOpen: false }">
    <div class="opac-nav__inner">
        <a href="{{ route('home') }}" class="opac-brand" aria-label="Beranda Perpustakaan Digital UNHASY">
            <img src="{{ asset('images/logo-unhasy-baru.webp') }}" alt="Logo UNHASY">
            <span class="opac-brand__name">Perpustakaan Digital<br>UNHASY</span>
        </a>

        <button type="button" class="opac-nav__toggle" @click="menuOpen = !menuOpen"
                :aria-expanded="menuOpen.toString()" aria-controls="opac-main-menu"
                :aria-label="menuOpen ? 'Tutup menu' : 'Buka menu'">
            <svg x-show="!menuOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
            </svg>
            <svg x-cloak x-show="menuOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" d="m6 6 12 12M18 6 6 18" />
            </svg>
        </button>

        <div id="opac-main-menu" class="opac-nav__content" :class="{ 'is-open': menuOpen }">
            <nav class="opac-nav__links" aria-label="Navigasi utama">
                <a href="{{ route('home') }}" class="opac-nav__link {{ request()->routeIs('home') ? 'is-active' : '' }}" @if (request()->routeIs('home')) aria-current="page" @endif>Beranda</a>
                <a href="{{ route('katalog.index') }}" class="opac-nav__link {{ request()->routeIs('katalog.*') ? 'is-active' : '' }}" @if (request()->routeIs('katalog.*')) aria-current="page" @endif>Katalog</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="opac-nav__link">Dashboard</a>
                    @if (auth()->user()->isMahasiswa())
                        <a href="{{ route('mahasiswa.riwayat') }}" class="opac-nav__link {{ request()->routeIs('mahasiswa.riwayat') ? 'is-active' : '' }}" @if (request()->routeIs('mahasiswa.riwayat')) aria-current="page" @endif>Riwayat</a>
                    @endif
                @endauth
            </nav>

            <div class="opac-nav__actions">
                @auth
                    <div class="opac-user">
                        <button type="button" class="opac-user__trigger" @click="userOpen = !userOpen"
                                :aria-expanded="userOpen.toString()" aria-haspopup="true">
                            <span>{{ auth()->user()->name }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 7.5 5 5 5-5" />
                            </svg>
                        </button>
                        <div x-cloak x-show="userOpen" @click.outside="userOpen = false" class="opac-user__menu">
                            <a href="{{ route('profile.edit') }}">Profil</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit">Keluar</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="opac-button opac-button--quiet">Masuk</a>
                    <a href="{{ route('register') }}" class="opac-button">Daftar</a>
                @endauth
            </div>
        </div>
    </div>
</header>
