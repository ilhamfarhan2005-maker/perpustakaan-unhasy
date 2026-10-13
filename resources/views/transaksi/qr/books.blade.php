<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Label QR Buku</title>
    <style>
        .label-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
        .label { border: 1px solid #d1d5db; border-radius: 8px; padding: 12px; text-align: center; break-inside: avoid; }
        .label img { display: block; width: 110px; height: 110px; margin: 8px auto; }
        @media print {
            .print-actions { display: none; }
            .label-grid { gap: 8px; }
            .label { border-color: #9ca3af; }
        }
    </style>
</head>
<body class="bg-white p-6 font-sans text-gray-900">
    <div class="print-actions mb-5 flex gap-3">
        <button type="button" onclick="window.print()" class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white">Cetak Label</button>
        <a href="{{ route('pustakawan.buku.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700">Kembali</a>
    </div>
    <main class="label-grid">
        @foreach($books as $book)
            <article class="label">
                <h1 class="text-sm font-semibold">{{ $book->title }}</h1>
                <p class="mt-1 text-xs">{{ $book->author }}</p>
                <img src="data:image/svg+xml;base64,{{ $qrImages[$book->id] }}" alt="QR {{ $book->kode_inventaris }}">
                <p class="font-mono text-xs">{{ $book->kode_inventaris }}</p>
                <p class="text-xs">ISBN {{ $book->isbn }}</p>
            </article>
        @endforeach
    </main>
</body>
</html>
