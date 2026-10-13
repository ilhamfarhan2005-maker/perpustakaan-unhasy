# Progress Proyek

## Tahap D — Kartu Anggota Digital Mahasiswa

Status: selesai.

- Kartu anggota mahasiswa berisi logo, nama, NIM, status, dan QR NIM dari generator QR yang sudah digunakan sistem.
- Tersedia tombol cetak ukuran kartu serta layar penuh untuk pemindaian.

## Tahap C — Buku Digital Berupa File + Pratinjau

Status: selesai.

- File PDF asli tersimpan privat, dilayani hanya kepada admin/pustakawan; mahasiswa hanya dapat membuka route pratinjau yang terautentikasi.
- Upload memeriksa MIME, ukuran 20 MB, dan signature PDF; form mendukung file atau URL pratinjau eksternal.
- Generator pratinjau menggunakan FPDI 2.6.8 dan FPDF untuk menyusun halaman awal PDF. Bila sebuah PDF tidak bisa diproses, file asli tetap tersimpan privat dan admin menerima pesan yang jelas.

## Tahap B — Denda, QR Pembayaran, dan Verifikasi

Status: selesai.

- Pembayaran QRIS multi-denda, bukti privat, verifikasi/tolak oleh petugas, dan pembayaran tunai telah tersedia.
- Antrean verifikasi kini diurutkan dari yang paling lama; QRIS yang belum tersedia menampilkan instruksi bayar tunai.

## Tahap A — Mahasiswa Pinjam & Kembali

Status: selesai.

- Pengajuan peminjaman mahasiswa untuk buku fisik/fisik-digital tersedia dari detail katalog; stok hanya berkurang saat pustakawan menyetujui.
- Validasi mencakup mahasiswa aktif, denda belum lunas, duplikasi, batas gabungan pinjaman/pengajuan, ketersediaan stok, dan larangan buku digital murni.
- Mahasiswa dapat membatalkan pengajuan yang masih berstatus Diajukan serta mengajukan pengembalian. Pustakawan dapat menyetujui/menolak pengajuan (alasan penolakan wajib) dan mengonfirmasi pengembalian.
- Command `peminjaman:expire-requests` mengedaluwarsakan pengajuan yang berumur lebih dari satu hari dan dijadwalkan setiap hari.

### File utama diubah/dibuat

- `app/Http/Controllers/Mahasiswa/PeminjamanController.php`
- `app/Http/Controllers/Transaksi/PeminjamanController.php`
- `app/Http/Requests/Transaksi/ProsesPengajuanRequest.php`
- `app/Models/Peminjaman.php`
- `app/Console/Commands/ExpireBorrowingRequests.php`
- `routes/web.php`, `routes/console.php`
- `resources/views/mahasiswa/riwayat.blade.php`
- `resources/views/transaksi/peminjaman/index.blade.php`
- `tests/Feature/StudentLoanRequestsTest.php`
- `tests/Feature/LibrarianCirculationQrTest.php`

### Migration baru

- `2026_10_12_000004_add_rejection_reason_to_peminjamans.php` — menyimpan alasan penolakan tanpa mengubah kolom atau enum lama.

### Verifikasi

- `php artisan test`: lulus, 57 tes dan 301 assertion.

### Masalah tersisa / pemeriksaan manual

- Tahap B, C, dan D belum dikerjakan sesuai instruksi bertahap.
- Periksa sendiri tombol pengajuan/pembatalan di detail buku dan riwayat mahasiswa, antrean persetujuan pustakawan, serta eksekusi scheduler pada server.
