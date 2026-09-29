<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Manajemen Denda</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                <p class="text-xs font-semibold uppercase text-red-700">Total belum lunas</p>
                <p class="mt-1 text-xl font-bold text-red-900">Rp {{ number_format($totalBelumLunas, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                <p class="text-xs font-semibold uppercase text-green-700">Lunas bulan ini</p>
                <p class="mt-1 text-xl font-bold text-green-900">Rp {{ number_format($totalLunasBulanIni, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <form method="GET" class="flex items-center gap-2 border-b border-gray-100 p-4">
                <label for="status" class="text-sm font-medium text-gray-700">Status</label>
                <select id="status" name="status" class="rounded-md border-gray-300 py-2 text-sm shadow-sm focus:border-green-600 focus:ring-green-600" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="belum_lunas" @selected(request('status') === 'belum_lunas')>Belum lunas</option>
                    <option value="lunas" @selected(request('status') === 'lunas')>Lunas</option>
                </select>
            </form>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr><th class="px-4 py-3 font-semibold">Peminjam</th><th class="px-4 py-3 font-semibold">Buku</th><th class="px-4 py-3 text-center font-semibold">Hari telat</th><th class="px-4 py-3 text-right font-semibold">Jumlah</th><th class="px-4 py-3 font-semibold">Status</th><th class="px-4 py-3 text-right font-semibold">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($denda as $d)
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-4 py-3"><div class="font-medium text-gray-900">{{ $d->transaction->user->name }}</div><div class="mt-0.5 font-mono text-xs text-gray-500">{{ $d->transaction->user->nim_nip }}</div></td>
                                <td class="px-4 py-3 text-gray-800">{{ $d->transaction->book->title }}</td>
                                <td class="px-4 py-3 text-center text-gray-600">{{ $d->days_late }} hari</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right font-mono font-medium">Rp {{ number_format($d->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    @if($d->status === 'lunas')
                                        <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-800">Lunas</span>
                                        <div class="mt-1 text-xs text-gray-500">{{ $d->paid_at?->format('d/m/Y H:i') }}</div>
                                    @else
                                        <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">Belum lunas</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($d->status === 'belum_lunas')
                                        <div x-data="{ open: false }" class="inline-block">
                                            <button type="button" @click="open = true" class="rounded-md bg-green-700 px-3 py-2 text-xs font-semibold text-white hover:bg-green-800">Catat pelunasan</button>
                                            <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/50 p-4" @keydown.escape.window="open = false" @click.self="open = false">
                                                <div class="w-full max-w-md rounded-lg bg-white p-5 text-left shadow-xl" @click.stop>
                                                    <div class="flex items-start justify-between gap-4">
                                                        <div><h3 class="text-lg font-semibold text-gray-900">Pelunasan denda</h3><p class="mt-1 text-sm text-gray-600">{{ $d->transaction->user->name }} · {{ $d->days_late }} hari terlambat</p></div>
                                                        <button type="button" @click="open = false" aria-label="Tutup" class="rounded p-1 text-xl leading-none text-gray-500 hover:bg-gray-100">&times;</button>
                                                    </div>
                                                    <form method="POST" action="{{ route('pustakawan.denda.bayar', $d) }}" class="mt-5">
                                                        @csrf
                                                        <label for="paid-amount-{{ $d->id }}" class="block text-sm font-medium text-gray-700">Jumlah dibayar (minimal Rp {{ number_format($d->amount, 0, ',', '.') }})</label>
                                                        <input id="paid-amount-{{ $d->id }}" type="number" name="paid_amount" value="{{ $d->amount }}" min="{{ $d->amount }}" step="0.01" required class="mt-1 w-full rounded-md border-gray-300 font-mono text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
                                                        <div class="mt-5 flex justify-end gap-2">
                                                            <button type="button" @click="open = false" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                                                            <button type="submit" class="rounded-md bg-green-700 px-3 py-2 text-sm font-semibold text-white hover:bg-green-800">Konfirmasi bayar</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-500">Dibayar Rp {{ number_format($d->paid_amount, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500">Belum ada denda tercatat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 px-4 py-3">{{ $denda->links() }}</div>
        </div>
    </div>
</x-app-layout>