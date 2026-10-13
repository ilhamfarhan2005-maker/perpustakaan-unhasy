<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Perpustakaan Digital UNHASY') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,400;6..72,500;6..72,600;6..72,700&family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <x-flash />
    <div class="guest-shell">
        <aside class="guest-brand-panel">
            <div class="guest-brand-content">
                <img src="{{ asset('images/logo-unhasy-baru.webp') }}" alt="Logo Universitas Hasyim Asy'ari">
                <h1>Perpustakaan Digital UNHASY</h1>
                <p>Ruang ilmu untuk belajar, meneliti, dan bertumbuh bersama.</p>
            </div>
        </aside>
        <main class="guest-form-panel">
            <div class="guest-form-content">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
