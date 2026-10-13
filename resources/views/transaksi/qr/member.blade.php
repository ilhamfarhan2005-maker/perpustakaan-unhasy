<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kartu Anggota Perpustakaan UNHASY</title>
    <style>
        .member-card { width: 340px; border: 1px solid #d1d5db; border-radius: 12px; padding: 24px; }
        .member-card img { display: block; width: 150px; height: 150px; margin: 18px auto; }
        @media print { .print-actions { display: none; } }
    </style>
</head>
<body class="bg-white p-6 font-sans text-gray-900">
    <div class="print-actions mb-5 flex gap-3">
        <button type="button" onclick="window.print()" class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white">Cetak Kartu Anggota</button>
        <a href="{{ url()->previous() }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700">Kembali</a>
    </div>
    <main class="member-card">
        <h1 class="text-center text-lg font-bold">Perpustakaan Digital UNHASY</h1>
        <img src="data:image/svg+xml;base64,{{ $qrImage }}" alt="QR anggota {{ $anggota->nim_nip }}">
        <p class="text-center font-semibold">{{ $anggota->name }}</p>
        <p class="mt-1 text-center font-mono">{{ $anggota->nim_nip }}</p>
    </main>
</body>
</html>
