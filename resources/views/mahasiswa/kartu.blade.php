<x-layouts.panel>
    <x-slot name="header"><h2 class="text-xl font-semibold text-gray-800">Kartu Anggota Saya</h2></x-slot>
    <div class="mx-auto max-w-2xl px-4 py-6">
        <div id="member-card" class="rounded-xl bg-unhasy-green p-6 text-white shadow-lg">
            <div class="flex items-start justify-between gap-6">
                <div><img src="{{ asset('images/logo-unhasy-baru.webp') }}" alt="Logo UNHASY" class="h-14 w-14"><p class="mt-3 text-sm font-semibold">PERPUSTAKAAN DIGITAL UNHASY</p></div>
                <img src="data:image/svg+xml;base64,{{ $qrImage }}" alt="QR NIM {{ $anggota->nim_nip }}" class="h-32 w-32 rounded bg-white p-2">
            </div>
            <div class="mt-6"><p class="text-xs text-white/75">NAMA</p><p class="text-xl font-bold">{{ $anggota->name }}</p><p class="mt-3 text-xs text-white/75">NIM</p><p class="font-mono text-lg">{{ $anggota->nim_nip }}</p><p class="mt-3 text-xs text-white/75">STATUS KEANGGOTAAN</p><p class="font-semibold">{{ $anggota->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}</p></div>
        </div>
        <div class="mt-4 flex gap-3"><button type="button" onclick="window.print()" class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white">Cetak</button><button type="button" onclick="document.getElementById('member-card').requestFullscreen()" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">Layar Penuh</button></div>
    </div>
    <style>@media print { body * { visibility:hidden } #member-card,#member-card * { visibility:visible } #member-card { position:absolute;left:0;top:0;width:85.6mm;min-height:54mm } }</style>
</x-layouts.panel>
