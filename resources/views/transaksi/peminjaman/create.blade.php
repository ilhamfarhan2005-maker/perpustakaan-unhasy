<x-layouts.panel>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Catat Peminjaman Baru</h2>
    </x-slot>

    <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
            Masukkan NIM anggota dan ISBN atau kode inventaris buku. Tanggal pinjam dan jatuh tempo dihitung otomatis dari pengaturan.
        </div>

        <form method="POST" action="{{ route('pustakawan.peminjaman.store') }}" class="mt-5 rounded-lg border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            @csrf
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="nim_nip" class="block text-sm font-medium text-gray-700">NIM / NIP Anggota</label>
                    <input id="nim_nip" type="text" name="nim_nip" value="{{ old('nim_nip') }}" required autofocus
                           class="mt-1 w-full rounded-md border-gray-300 font-mono text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
                    <button type="button" data-scan-target="nim_nip" data-scan-id="member-qr-reader"
                            class="mt-2 rounded-md border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">Pindai QR NIM</button>
                    @error('nim_nip') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="isbn" class="block text-sm font-medium text-gray-700">ISBN / Kode Inventaris Buku</label>
                    <input id="isbn" type="text" name="isbn" value="{{ old('isbn') }}" required
                           class="mt-1 w-full rounded-md border-gray-300 font-mono text-sm shadow-sm focus:border-green-600 focus:ring-green-600">
                    <button type="button" data-scan-target="isbn" data-scan-id="book-qr-reader"
                            class="mt-2 rounded-md border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">Pindai QR Buku</button>
                    @error('isbn') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div id="member-qr-reader" class="mt-3 hidden max-w-sm rounded-md border border-gray-200 p-2"></div>
            <div id="book-qr-reader" class="mt-3 hidden max-w-sm rounded-md border border-gray-200 p-2"></div>
            <p id="scanner-message" class="mt-3 text-sm text-amber-800">
                Kamera memerlukan HTTPS atau localhost. Gunakan http://localhost atau aktifkan SSL Laragon; input manual dan pemindai barcode USB tetap tersedia.
            </p>

            <div class="mt-5 grid grid-cols-1 gap-3 md:grid-cols-2">
                <section id="member-preview" class="hidden rounded-md border border-gray-200 bg-gray-50 p-4 text-sm" aria-live="polite">
                    <h3 class="font-semibold text-gray-800">Data anggota</h3>
                    <p class="mt-2">Nama: <span data-member="name">-</span></p>
                    <p>NIM: <span data-member="nim_nip">-</span></p>
                    <p>Pinjaman aktif: <span data-member="active_loans">-</span></p>
                    <p>Status denda: <span data-member="unpaid_fine">-</span></p>
                </section>
                <section id="book-preview" class="hidden rounded-md border border-gray-200 bg-gray-50 p-4 text-sm" aria-live="polite">
                    <h3 class="font-semibold text-gray-800">Data buku</h3>
                    <p class="mt-2">Judul: <span data-book="title">-</span></p>
                    <p>ISBN: <span data-book="isbn">-</span></p>
                    <p>Kode inventaris: <span data-book="kode_inventaris">-</span></p>
                    <p>Stok fisik: <span data-book="stock">-</span></p>
                    <p>Format: <span data-book="format">-</span></p>
                </section>
            </div>
            <p id="lookup-error" class="mt-3 hidden text-sm text-red-600" role="alert"></p>

            @error('transaction') <p class="mt-4 text-sm text-red-600">{{ $message }}</p> @enderror

            <div class="mt-6 flex flex-wrap gap-2 border-t border-gray-100 pt-5">
                <button type="submit" class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800">Catat Peminjaman</button>
                <a href="{{ route('pustakawan.peminjaman.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</a>
            </div>
        </form>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        (() => {
            const nim = document.getElementById('nim_nip');
            const isbn = document.getElementById('isbn');
            const message = document.getElementById('lookup-error');
            let runningScanner = null;
            let lookupTimer;

            const lookup = async (field, value) => {
                if (!value) return;
                const url = new URL(@json(route('pustakawan.peminjaman.identifikasi')), window.location.origin);
                url.searchParams.set(field, value);
                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                if (!response.ok) throw new Error('Data tidak dapat diperiksa.');
                const data = await response.json();
                const key = field === 'nim_nip' ? 'member' : 'book';
                const card = document.getElementById(`${key}-preview`);
                if (!data[key]) {
                    card.classList.add('hidden');
                    message.textContent = field === 'nim_nip' ? 'Anggota tidak ditemukan.' : 'Buku tidak ditemukan.';
                    message.classList.remove('hidden');
                    return;
                }
                message.classList.add('hidden');
                card.classList.remove('hidden');
                Object.entries(data[key]).forEach(([name, value]) => {
                    const output = card.querySelector(`[data-${key}="${name}"]`);
                    if (output) output.textContent = name === 'unpaid_fine'
                        ? (value ? 'Ada denda belum lunas' : 'Tidak ada denda belum lunas')
                        : (value ?? 'Tidak dapat dipinjam');
                });
            };

            const scheduleLookup = (field, value) => {
                clearTimeout(lookupTimer);
                lookupTimer = setTimeout(() => lookup(field, value).catch((error) => {
                    message.textContent = error.message;
                    message.classList.remove('hidden');
                }), 250);
            };

            nim.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' && nim.value.trim()) {
                    event.preventDefault();
                    scheduleLookup('nim_nip', nim.value.trim());
                    isbn.focus();
                }
            });
            nim.addEventListener('change', () => scheduleLookup('nim_nip', nim.value.trim()));
            nim.addEventListener('blur', () => scheduleLookup('nim_nip', nim.value.trim()));
            isbn.addEventListener('change', () => scheduleLookup('isbn', isbn.value.trim()));
            isbn.addEventListener('blur', () => scheduleLookup('isbn', isbn.value.trim()));

            document.querySelectorAll('[data-scan-target]').forEach((button) => {
                button.addEventListener('click', async () => {
                    if (!window.isSecureContext || !navigator.mediaDevices?.getUserMedia) {
                        message.textContent = 'Kamera hanya dapat digunakan melalui HTTPS atau localhost. Silakan gunakan input manual atau pemindai barcode USB.';
                        message.classList.remove('hidden');
                        return;
                    }
                    if (typeof Html5Qrcode === 'undefined') {
                        message.textContent = 'Pustaka pemindai QR tidak dapat dimuat. Gunakan input manual atau pemindai barcode USB.';
                        message.classList.remove('hidden');
                        return;
                    }
                    if (runningScanner) {
                        await runningScanner.stop();
                        runningScanner.clear();
                        runningScanner = null;
                    }
                    const readerId = button.dataset.scanId;
                    const reader = document.getElementById(readerId);
                    reader.classList.remove('hidden');
                    const target = document.getElementById(button.dataset.scanTarget);
                    runningScanner = new Html5Qrcode(readerId);
                    try {
                        await runningScanner.start(
                            { facingMode: 'environment' },
                            { fps: 10, qrbox: { width: 220, height: 160 } },
                            async (decodedText) => {
                                target.value = decodedText;
                                target.dispatchEvent(new Event('change', { bubbles: true }));
                                (target === nim ? isbn : target).focus();
                                await runningScanner.stop();
                                runningScanner.clear();
                                runningScanner = null;
                                reader.classList.add('hidden');
                            },
                            () => {},
                        );
                    } catch (error) {
                        message.textContent = 'Kamera tidak dapat diaktifkan. Periksa izin kamera atau gunakan input manual.';
                        message.classList.remove('hidden');
                    }
                });
            });
        })();
    </script>
</x-layouts.panel>