<?php

use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Mahasiswa\DashboardController;
use App\Http\Controllers\Mahasiswa\DendaSayaController;
use App\Http\Controllers\Mahasiswa\RiwayatController;
use App\Http\Controllers\Mahasiswa\MemberCardController;
use App\Http\Controllers\Master\BukuController;
use App\Http\Controllers\Master\KategoriController;
use App\Http\Controllers\Opac\HomeController;
use App\Http\Controllers\Opac\KatalogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DigitalBookController;
use App\Http\Controllers\Transaksi\BuktiPembayaranController;
use App\Http\Controllers\Transaksi\DendaController;
use App\Http\Controllers\Transaksi\PeminjamanController;
use App\Http\Controllers\Transaksi\PengembalianController;
use App\Http\Controllers\Transaksi\PerpanjanganController;
use App\Http\Controllers\Transaksi\QrPrintController;
use App\Http\Controllers\Transaksi\VerifikasiPembayaranController;
use Illuminate\Support\Facades\Route;

// ───── PUBLIK (TANPA LOGIN) ─────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/katalog/{buku}', [KatalogController::class, 'show'])->name('katalog.show');

Route::middleware('auth')->group(function () {
    Route::get('/katalog/{buku}/pratinjau', [DigitalBookController::class, 'reader'])->name('katalog.preview');
    Route::get('/katalog/{buku}/pratinjau/file', [DigitalBookController::class, 'preview'])->name('katalog.preview.file');
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'pustakawan' => redirect()->route('pustakawan.dashboard'),
            'kepala_perpustakaan' => redirect()->route('eksekutif.dashboard'),
            default => redirect()->route('mahasiswa.dashboard'),
        };
    })->name('dashboard');

    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
        Route::get('/kartu-anggota', [MemberCardController::class, 'show'])->name('kartu');
        Route::get('/denda', [DendaSayaController::class, 'index'])->name('denda.index');
        Route::post('/denda/pembayaran', [DendaSayaController::class, 'storePayment'])->name('denda.pembayaran.store');
        Route::get('/pembayaran/{payment}/bukti', [BuktiPembayaranController::class, 'show'])->name('pembayaran.bukti');
        Route::post('/peminjaman/{buku}', [App\Http\Controllers\Mahasiswa\PeminjamanController::class, 'store'])->name('peminjaman.store');
        Route::delete('/peminjaman/{pengajuan}', [App\Http\Controllers\Mahasiswa\PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');
        Route::post('/pengembalian/{transaksi}', [App\Http\Controllers\Mahasiswa\PeminjamanController::class, 'requestReturn'])->name('pengembalian.store');
    });

    Route::middleware('role:pustakawan,admin')->prefix('pustakawan')->name('pustakawan.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Pustakawan\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
        Route::get('/peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
        Route::get('/peminjaman/identifikasi', [PeminjamanController::class, 'identify'])->name('peminjaman.identifikasi');
        Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
        Route::get('/peminjaman/{transaksi}', [PeminjamanController::class, 'show'])->name('peminjaman.show');
        Route::post('/pengajuan/{pengajuan}', [PeminjamanController::class, 'processRequest'])->name('pengajuan.proses');
        Route::get('/pengembalian', [PengembalianController::class, 'index'])->name('pengembalian.index');
        Route::post('/pengembalian/{transaksi}', [PengembalianController::class, 'store'])->name('pengembalian.store');
        Route::post('/perpanjangan/{transaksi}', [PerpanjanganController::class, 'store'])->name('perpanjangan.store');
        Route::get('/denda', [DendaController::class, 'index'])->name('denda.index');
        Route::post('/denda/{fine}/bayar', [DendaController::class, 'bayar'])->name('denda.bayar');
        Route::get('/pembayaran-denda', [VerifikasiPembayaranController::class, 'index'])->name('pembayaran.index');
        Route::post('/pembayaran-denda/{payment}', [VerifikasiPembayaranController::class, 'update'])->name('pembayaran.update');
        Route::get('/pembayaran-denda/{payment}/bukti', [BuktiPembayaranController::class, 'show'])->name('pembayaran.bukti');
        Route::post('/buku/label-qr', [QrPrintController::class, 'books'])->name('buku.label-qr.batch');
        Route::get('/buku/{buku}/label-qr', [QrPrintController::class, 'book'])->name('buku.label-qr');
        Route::get('/anggota/{anggota}/kartu-qr', [QrPrintController::class, 'member'])->name('anggota.kartu-qr');
        Route::resource('kategori', KategoriController::class)->except(['show'])->parameters(['kategori' => 'kategori']);
        Route::resource('buku', BukuController::class)->parameters(['buku' => 'buku']);
        Route::get('/buku/{buku}/file-asli', [DigitalBookController::class, 'original'])->name('buku.file-asli');
    });

    Route::middleware('role:kepala_perpustakaan')->prefix('eksekutif')->name('eksekutif.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Eksekutif\DashboardController::class, 'index'])->name('dashboard');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('pengguna', UserController::class)->except(['show']);
        Route::get('/pengaturan', [SettingController::class, 'edit'])->name('pengaturan.edit');
        Route::post('/pengaturan', [SettingController::class, 'update'])->name('pengaturan.update');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
