<x-layouts.panel>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Verifikasi Pembayaran Denda</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="space-y-4">
            @forelse($payments as $payment)
                <article class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="grid gap-5 md:grid-cols-[1fr_auto]">
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $payment->user->name }}</h3>
                            <p class="mt-1 text-sm text-gray-600">NIM: {{ $payment->user->nim_nip }} · Metode: {{ strtoupper($payment->method) }}</p>
                            <p class="mt-1 text-sm text-gray-600">Dikirim: {{ $payment->created_at->format('d/m/Y H:i') }}</p>
                            <p class="mt-2 font-mono font-semibold text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                            <ul class="mt-3 list-inside list-disc text-sm text-gray-700">
                                @foreach($payment->fines as $fine)
                                    <li>{{ $fine->transaction?->book?->title ?? 'Buku tidak tersedia' }} — {{ $fine->days_late }} hari terlambat</li>
                                @endforeach
                            </ul>
                            @if($payment->proof_path)
                                <a href="{{ route('pustakawan.pembayaran.bukti', $payment) }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-block">
                                    <img src="{{ route('pustakawan.pembayaran.bukti', $payment) }}" alt="Pratinjau bukti pembayaran dari {{ $payment->user->name }}" class="max-h-64 max-w-full rounded border border-gray-200 object-contain">
                                    <span class="mt-1 block text-sm font-semibold text-green-800 underline">Buka bukti ukuran penuh</span>
                                </a>
                            @else
                                <p class="mt-4 text-sm text-gray-500">Bukti pembayaran tidak tersedia.</p>
                            @endif
                        </div>
                        <div class="flex flex-col gap-3 md:w-72">
                            <form method="POST" action="{{ route('pustakawan.pembayaran.update', $payment) }}">
                                @csrf
                                <input type="hidden" name="action" value="setujui">
                                <button type="submit" class="w-full rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800">Setujui Pembayaran</button>
                            </form>
                            <form method="POST" action="{{ route('pustakawan.pembayaran.update', $payment) }}">
                                @csrf
                                <input type="hidden" name="action" value="tolak">
                                <label for="rejection_note_{{ $payment->id }}" class="block text-sm font-medium text-gray-700">Alasan penolakan</label>
                                <textarea id="rejection_note_{{ $payment->id }}" name="rejection_note" required rows="2" maxlength="2000" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm"></textarea>
                                @error('rejection_note')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                <button type="submit" class="mt-2 w-full rounded-md border border-red-300 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Tolak Pembayaran</button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-gray-200 bg-white px-4 py-12 text-center text-sm text-gray-500">Tidak ada pembayaran yang menunggu verifikasi.</div>
            @endforelse
        </div>
        <div class="mt-4">{{ $payments->links() }}</div>
    </div>
</x-layouts.panel>
