<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Perpustakaan Digital UNHASY')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,400;6..72,500;6..72,600;6..72,700&family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="public-layout">
    <a class="opac-skip-link" href="#public-content">Lewati ke konten utama</a>
    <x-flash />
    @include('opac.partials.navigation')

    <main id="public-content" class="public-layout__main">
        @yield('content')
    </main>

    <footer class="public-footer">
        <div class="public-footer__inner">
            <p>Perpustakaan Universitas Hasyim Asy'ari<br>Jl. Tebuireng No. 1, Jombang, Jawa Timur<br>© {{ date('Y') }} UNHASY</p>
            <nav class="public-footer__links" aria-label="Tautan footer">
                <a href="{{ route('home') }}">Beranda</a>
                <a href="{{ route('katalog.index') }}">Katalog</a>
                @guest
                    <a href="{{ route('login') }}">Masuk</a>
                    <a href="{{ route('register') }}">Daftar</a>
                @endguest
            </nav>
        </div>
    </footer>
</body>
</html>
